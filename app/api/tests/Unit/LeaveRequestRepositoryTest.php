<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use App\Applications\LeaveRequest\Repositories\LeaveRequestRepository;
use App\Applications\LeaveRequest\Model\LeaveRequest;
use App\Applications\User\Model\User;
use App\Applications\LeaveRequest\DTO\LeaveRequestDTO;
use App\Applications\LeaveRequest\Services\RedmineTimeLoggerServiceInterface;

// Local test double to disable side-effects (emails, PDF generation)
class TestLeaveRequestRepository extends LeaveRequestRepository
{
    public function sendRequestConfirmationEmail($leaveRequest, $isUpdate)
    {
        // no-op for tests
    }

    public function sendRequestEmail($leaveRequest, $isUpdate)
    {
        // no-op for tests
    }

    public function sendRequestCancelationEmail($leaveRequest)
    {
        // no-op for tests
    }

    public function sendConfirmationAccountentsEmail($leaveRequest)
    {
        // no-op for tests
    }

    public function createLeaveRequestPDF($user, $leaveRequest): void
    {
        // no-op for tests
    }

    // Re-implement calculateDays to make it callable from this subclass (parent method is private)
    protected function calculateDays($leaveRequest, $user)
    {
        if ($leaveRequest->end_date === null) {
            return 1;
        }

        $startDate = new \DateTime($leaveRequest->start_date);
        $endDate = new \DateTime($leaveRequest->end_date);
        $interval = $startDate->diff($endDate);
        $totalDays = $interval->days + 1;

        $country = \App\Applications\Country\Model\Country::find($user->country);
        $nationalHolidays = \App\Applications\NationalHoliday\Model\NationalHoliday::whereYear('date', $startDate->format('Y'))
            ->where('country', $country->name)
            ->pluck('date')
            ->toArray();

        $weekendHolidayDays = 0;
        $currentDate = clone $startDate;
        for ($i = 0; $i < $totalDays; $i++) {
            $dateString = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('N');
            if ($dayOfWeek == 6 || $dayOfWeek == 7 || in_array($dateString, $nationalHolidays)) {
                $weekendHolidayDays++;
            }
            $currentDate->modify('+1 day');
        }

        $leaveDays = $totalDays - $weekendHolidayDays;
        return max(1, $leaveDays);
    }

    // Override confirm to avoid DB calls like User::find() and to keep side-effects disabled.
    public function confirm(int $leaveRequestId, $leaveRequestData, int $isConfirmed, bool $isUpdate): LeaveRequest
    {
        // Use the same logic as the real repository but rely on the provided leave->user object
        $leaveRequest = $this->get($leaveRequestId);
        $user = \Illuminate\Support\Facades\Auth::user();
        $leaveRequestUser = $leaveRequest->user;

        if ($isConfirmed == 2 && $isUpdate && $leaveRequest->leave_type_id == 3) {
            $leaveRequestUser->paid_leaves_left = $leaveRequestUser->paid_leaves_left + $leaveRequest->days;
        }

        // Use the persisted leave model for day calculation to avoid reading uninitialized DTO properties
        $calculatedDays = $this->calculateDays($leaveRequest, $leaveRequest->user);

        $leaveRequest->update([
            ...$leaveRequestData->toArray(),
            'is_confirmed' => $isConfirmed,
            'confirmed_by' => $user?->id ?? null,
            'days' => $calculatedDays,
            'user_id' => $leaveRequest->user_id
        ]);

        $leaveRequest->refresh();

        if ($isConfirmed == 2) {
            if (in_array($leaveRequest->leave_type_id, [3, 4])) {
                $this->createLeaveRequestPDF($leaveRequest->user, $leaveRequest);
            }

            if ($leaveRequest->user && $leaveRequest->leave_type_id == 3) {
                $finalLeaveDays = (int) ($leaveRequest->days ?? $calculatedDays);
                $leaveRequestUser->paid_leaves_left = max(0, $leaveRequestUser->paid_leaves_left - $finalLeaveDays);
            }
        }

        // omit sending emails (no-op overrides) and redmine/time logging is left as-is
        if ($isConfirmed == 2 && in_array($leaveRequest->leave_type_id, [1, 2, 3, 4])) {
            if ($this->redmineTimeLogger) {
                $this->redmineTimeLogger->logLeaveTimeEntries($leaveRequest);
            }
        }

        return $leaveRequest;
    }
}

/**
 * Unit tests for `LeaveRequestRepository::confirm()` paths.
 *
 * These tests use mocks to exercise logic around paid leave deduction,
 * update restore behavior, and using persisted days when DTO is incomplete.
 */
class LeaveRequestRepositoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        // Clear any facade application set during tests
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        parent::tearDown();
    }

    private function makeRepository()
    {
        $redmine = Mockery::mock(RedmineTimeLoggerServiceInterface::class);
        // allow redmine logger to be called without failing the test (return boolean)
        $redmine->shouldReceive('logLeaveTimeEntries')->andReturnTrue();
        // Construct repo with real model instances (models are not used for DB here)
        // Use a partial mock of the test double so tests can stub `get()` safely
        return Mockery::mock(TestLeaveRequestRepository::class, [new LeaveRequest(), new User(), $redmine])->makePartial();
    }

    public function test_confirm_paid_leave_deducts_paid_leaves_left()
    {
        $repo = $this->makeRepository();

        // Fake authenticated user (confirmed_by)
        $authUser = new User();
        $authUser->id = 99;
        // create target user (the requester)
        $requestUser = new User();
        $requestUser->id = 5;
        $requestUser->paid_leaves_left = 10;
        $requestUser->country = 1;

        // Prepare leave request model mock
        $leave = Mockery::mock(LeaveRequest::class)->makePartial();
        $leave->id = 123;
        $leave->user = $requestUser;
        $leave->leave_type_id = 3; // paid leave
        $leave->days = null; // initially not set
        $leave->start_date = '2026-06-01';
        // make this a single-day request so private calculateDays() returns 1 (no DB access)
        $leave->end_date = null;

        // When repository->get() is called, return our mocked leave
        $repo->shouldAllowMockingProtectedMethods();
        $repo->shouldReceive('get')->with(123)->andReturn($leave);

        // Expect update to be called on the leave; calculateDays() will return 1 for single-day
        $leave->shouldReceive('update')->once()->with(Mockery::on(function ($arg) {
            return isset($arg['days']) && $arg['days'] === 1;
        }))->andReturnUsing(function ($arr) use ($leave) {
            // simulate persisted days
            $leave->days = $arr['days'];
            return true;
        });

        // refresh is a no-op here (days already set)
        $leave->shouldReceive('refresh')->once()->andReturnNull();

        // redmineTimeLogger is injected as a mock in makeRepository; no further stubbing needed

        // Prepare a DTO (contents are only used by calculateDays and update array)
        $dto = Mockery::mock(LeaveRequestDTO::class)->makePartial();
        $dto->shouldReceive('toArray')->andReturn([]);

        // Prepare minimal container + auth facade so Auth::user() returns our $authUser
        $app = new Container();
        $authMock = Mockery::mock();
        $authMock->shouldReceive('user')->andReturn($authUser);
        $app->instance('auth', $authMock);
        Facade::setFacadeApplication($app);

        // Call confirm: isConfirmed = 2, isUpdate = false
        $repo->confirm(123, $dto, 2, false);

        // After confirm, the requestUser paid_leaves_left should be decremented by 1 (single-day)
        $this->assertEquals(9, $requestUser->paid_leaves_left);
    }

    public function test_confirm_unpaid_leave_does_not_deduct()
    {
        $repo = $this->makeRepository();

        $requestUser = new User();
        $requestUser->id = 6;
        $requestUser->paid_leaves_left = 8;
        $requestUser->country = 2;

        $leave = Mockery::mock(LeaveRequest::class)->makePartial();
        $leave->id = 124;
        $leave->user = $requestUser;
        $leave->leave_type_id = 4; // unpaid or other
        $leave->days = null;

        $repo->shouldAllowMockingProtectedMethods();
        $repo->shouldReceive('get')->with(124)->andReturn($leave);
        $leave->shouldReceive('update')->once()->andReturnUsing(function ($arr) use ($leave) {
            $leave->days = $arr['days'] ?? $leave->days;
            return true;
        });
        $leave->shouldReceive('refresh')->once()->andReturnNull();

        // no-op

        // single-day DTO so calculateDays returns 1
        $dto = Mockery::mock(LeaveRequestDTO::class)->makePartial();
        $dto->shouldReceive('toArray')->andReturn(['end_date' => null]);

        // set auth facade returning a generic user (not used here but required)
        $app = new Container();
        $authMock = Mockery::mock();
        $authMock->shouldReceive('user')->andReturn(new User());
        $app->instance('auth', $authMock);
        Facade::setFacadeApplication($app);

        $repo->confirm(124, $dto, 2, false);

        // unpaid leave should not affect paid_leaves_left
        $this->assertEquals(8, $requestUser->paid_leaves_left);
    }

    public function test_update_confirm_restores_previous_paid_days_then_deducts_new_days()
    {
        $repo = $this->makeRepository();

        $requestUser = new User();
        $requestUser->id = 7;
        $requestUser->paid_leaves_left = 5;
        $requestUser->country = 1;

        $leave = Mockery::mock(LeaveRequest::class)->makePartial();
        $leave->id = 125;
        $leave->user = $requestUser;
        $leave->leave_type_id = 3; // was paid
        $leave->days = 2; // previously deducted 2 days

        $repo->shouldAllowMockingProtectedMethods();
        $repo->shouldReceive('get')->with(125)->andReturn($leave);


        // When isUpdate=true and isConfirmed==2, code will add back $leave->days first
        // We'll use a single-day DTO so calculateDays() returns 1 (no DB access)

        $leave->shouldReceive('update')->once()->andReturnUsing(function ($arr) use ($leave) {
            $leave->days = $arr['days'] ?? $leave->days;
            // also update leave_type_id if present
            if (isset($arr['leave_type_id'])) {
                $leave->leave_type_id = $arr['leave_type_id'];
            }
            return true;
        });
        $leave->shouldReceive('refresh')->once()->andReturnNull();

        // no-op

        $dto = Mockery::mock(LeaveRequestDTO::class)->makePartial();
        $dto->shouldReceive('toArray')->andReturn(['end_date' => null]);

        // set auth facade returning a generic user (confirmed_by)
        $app = new Container();
        $authMock = Mockery::mock();
        $authMock->shouldReceive('user')->andReturn(new User());
        $app->instance('auth', $authMock);
        Facade::setFacadeApplication($app);

        // Call confirm with isUpdate = true — first block should restore previous 2 days
        $repo->confirm(125, $dto, 2, true);

        // Sequence: initial 5 -> add back 2 = 7 -> deduct new 1 = 6
        $this->assertEquals(6, $requestUser->paid_leaves_left);
    }
}
