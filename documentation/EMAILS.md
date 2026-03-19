# Leave Request Email System Documentation

## Overview

The Leave Sync system sends automated emails at various stages of the leave request lifecycle. This document outlines when each email is triggered, who receives it, and what information it contains.

---

## Email Status Codes

Leave requests have an `is_confirmed` status that determines which email is sent:

| Status | Value | Meaning |
|--------|-------|---------|
| Pending | 0 | Awaiting approval from manager |
| Declined | 1 | Manager has declined the request |
| Approved | 2 | Manager has approved the request |

---

## Email Types

### 1. LeaveRequestNotification

**When Triggered:**
- When a new leave request is **created** (unless it's auto-confirmed)
- When an existing leave request is **updated** (only the update version is sent in this case)
- Does NOT trigger for auto-confirmed leave types (leave type 6)

**Files:**
- Class: `app/Applications/LeaveRequest/Mail/LeaveRequestNotification.php`
- Template: `resources/views/emails/leave_request_notification.blade.php`

**Recipients:**
- Manager who will review the request (`request_to` user)

**Email Subject Format:**
```
Requested: [Employee Name]: [Leave Type]: [Number] days: [Start Date] to [End Date]
```

**Content Includes:**
- Employee name
- Leave type
- Number of days
- Start date
- End date (if applicable)
- Reason for leave (if provided)
- Link to the admin dashboard to review/approve

---

### 2. LeaveRequestNotificationUpdate

**When Triggered:**
- When an existing leave request is **updated** (before confirmation/decision is made)
- Similar to LeaveRequestNotification but indicates the request has been modified

**Files:**
- Class: `app/Applications/LeaveRequest/Mail/LeaveRequestNotificationUpdate.php`
- Template: `resources/views/emails/leave_request_notification.blade.php` (reuses notification template)

**Recipients:**
- Manager who will review the request (`request_to` user)

**Email Subject Format:**
```
[Employee Name]: [Leave Type]: [Number] days: [Start Date] to [End Date] (Update)
```

**Content Includes:**
- Same as LeaveRequestNotification
- Badge indicating "Update"

---

### 3. LeaveRequestConfirmation

**When Triggered:**
- When a manager **approves** a leave request (`is_confirmed = 2`)
- Sent to the employee confirming approval
- Does NOT trigger on subsequent updates to an approved request

**Files:**
- Class: `app/Applications/LeaveRequest/Mail/LeaveRequestConfirmation.php`
- Template: `resources/views/emails/leave_request_confirmation.blade.php`

**Recipients:**
- Employee who requested the leave
- Manager who approved it (reply-to address)
- All Admin users
- **CC'd:** All collaborators (accounting team) if leave type is Vacation (3) or Sick Leave (4)

**Email Subject Format:**
```
[Employee Name]: [Leave Type]: [Number] days: [Start Date] to [End Date] (Approved)
```

**Content Includes:**
- Employee name
- Leave type
- Number of days
- Start and end dates
- If applicable: PDF document attachment (for vacation/sick leave)

**Special Handling:**
- If leave type is **Vacation (3)** or **Sick Leave (4)**, a PDF certificate is generated and attached
- Collaborators are CC'd on the email

---

### 4. LeaveRequestDeclining

**When Triggered:**
- When a manager **declines** a leave request (`is_confirmed = 1`)

**Files:**
- Class: `app/Applications/LeaveRequest/Mail/LeaveRequestDeclining.php`
- Template: `resources/views/emails/leave_request_decline.blade.php`

**Recipients:**
- Employee who requested the leave

**Email Subject Format:**
```
[Leave Type]: [Start Date] to [End Date] (Declined)
```

**Content Includes:**
- Clear indication that the request was declined
- Instruction to contact manager for more information
- Link to view the full request details in the admin dashboard

---

### 5. LeaveRequestCancelation

**When Triggered:**
- When an **approved leave request is deleted** (is_confirmed = 2)
- Manager cancels a previously approved leave

**Files:**
- Class: `app/Applications/LeaveRequest/Mail/LeaveRequestCancelation.php`
- Template: `resources/views/emails/leave_request_cancelation.blade.php`

**Recipients:**
- Employee who requested the leave
- Manager who is canceling it
- All Admin users

**Email Subject Format:**
```
[Employee Name]: [Leave Type]: [Number] days: [Start Date] to [End Date] (Canceled)
```

**Content Includes:**
- Employee name
- Leave type
- Number of days
- Start and end dates
- Reason for cancellation (if provided)

**Special Handling:**
- If leave type is **Paid Leave (3)**, the days are refunded back to the employee's `paid_leaves_left` balance

---

### 6. LeaveRequestConfirmationUpdate

**When Triggered:**
- When an **already approved leave request is updated** (`is_confirmed = 2` and `isUpdate = true`)
- Manager modifies details of an approved request

**Files:**
- Class: `app/Applications/LeaveRequest/Mail/LeaveRequestConfirmationUpdate.php`
- Template: `resources/views/emails/leave_request_confirmation_update.blade.php`

**Recipients:**
- Employee who requested the leave
- Manager who made the update (reply-to address)
- All Admin users

**Email Subject Format:**
```
[Employee Name]: [Leave Type]: [Number] days: [Start Date] to [End Date] (Approved - UPDATE)
```

**Content Includes:**
- Details of what was changed
- Updated leave dates and duration
- If applicable: Updated PDF document attachment (for vacation/sick leave)

**Special Handling:**
- Sent whenever an approved request is modified
- PDF is regenerated if leave type is Vacation (3) or Sick Leave (4)

---

### 7. LeaveRequestConfirmationPDF

**When Triggered:**
- When a leave request is **approved** (`is_confirmed = 2`) AND the employee's country is **Macedonia (country_id = 1)** or **Bulgaria**
- Only sent for approved requests, not updates

**Files:**
- Class: `app/Applications/LeaveRequest/Mail/LeaveRequestConfirmationPDF.php`
- Template: `resources/views/emails/leave_request_confirmation_pdf.blade.php`

**Recipients:**
- Accounting team / Collaborators (`User::role(User::COLLABORATOR)`) in the same country as the employee

**Email Subject:**
```
ESOF PDF
```

**Content Includes:**
- Attached PDF document (if available) - country-specific format
- Generic accounting notification

**Special Handling:**
- This is a separate email from LeaveRequestConfirmation
- Currently only triggered for Macedonia (country = 1) - can be extended for Bulgaria
- Different PDF templates are generated based on employee's country:
  - **Macedonia**: Uses MK_template_paid.pdf or MK_template_unpaid.pdf
  - **Bulgaria**: Uses BG_template_paid.pdf or BG_template_unpaid.pdf
- Specifically targets the accounting/collaboration team in the employee's country

---

## recipient Logic

The `getRecipients()` method determines who receives the email based on the request status:

```php
private function getRecipients(LeaveRequest $leaveRequest)
{
    if ($leaveRequest->is_confirmed == 2) {
        // Approved: Send to employee, manager, and all admins
        return [$leaveRequest->user->email, $leaveRequest->requestToUser->email, ...Admin emails];
    } else if ($leaveRequest->is_confirmed == 1) {
        // Declined: Send to employee only
        return $leaveRequest->user->email;
    } else if ($leaveRequest->is_confirmed == 0) {
        // Pending: Send to manager only
        return $leaveRequest->requestToUser->email;
    }
}
```

---

## Leave Type Handling

Different leave types have special behavior:

| Leave Type ID | Type Name | Special Behavior |
|---------------|-----------|-----------------|
| 3 | Paid Leave | Generates country-specific PDF certificate (MK or BG), deducts from `paid_leaves_left` balance |
| 4 | Sick Leave | Generates country-specific PDF certificate (MK or BG), sent to collaborators |
| 6 | Auto-Confirm | Auto-approved on creation, no notification email sent initially |

**PDF Generation:**
- When approved (status = 2), a PDF is automatically generated based on the employee's country:
  - **Macedonia (country_id = 1)**: Uses Macedonian template (MK_template_paid.pdf or MK_template_unpaid.pdf)
  - **Bulgaria (country_id = 2)**: Uses Bulgarian template (BG_template_paid.pdf or BG_template_unpaid.pdf)
- PDFs are attached to LeaveRequestConfirmation emails and LeaveRequestConfirmationPDF emails

---

## Key Methods Explained

### `create(LeaveRequestDTO $leaveRequestDTO): LeaveRequest`
1. Creates the leave request record
2. If leave type is 6: Auto-confirms with status 2
3. Otherwise: Sends LeaveRequestNotification to manager

### `update(int $leaveRequestId, LeaveRequestDTO $leaveRequestData): LeaveRequest`
1. Updates the leave request
2. Sends LeaveRequestNotificationUpdate to manager

### `confirm(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed, bool $isUpdate): LeaveRequest`
1. Sets the confirmation status (1 = declined, 2 = approved)
2. Sends LeaveRequestConfirmationEmail (which dispatches either Confirmation or Declining email)
3. If approved and leave type is 3 or 4: Generates country-specific PDF certificate (Macedonian or Bulgarian template)
4. If country is Macedonia: Also sends LeaveRequestConfirmationPDF to accounting team (can be extended for Bulgaria)
5. Updates paid leave balance if applicable

### `delete(int $id)`
1. Deletes the leave request (soft delete)
2. If was approved and type is 3: Refunds days to employee
3. Sends LeaveRequestCancelation email

---

## Email Flow Diagram

```
CREATE REQUEST
├─ If leave type = 6 (Auto-confirm)
│  └─ Auto-confirm → sendRequestConfirmationEmail
│     ├─ LeaveRequestConfirmation (to employee, manager, admins)
│     ├─ Generate country-specific PDF (MK or BG)
│     └─ LeaveRequestConfirmationPDF (to accountants if country = 1) *currently Macedonia only*
│
└─ If leave type ≠ 6 (Needs approval)
   └─ LeaveRequestNotification (to manager)

UPDATE REQUEST
├─ If status is still pending (0)
│  └─ LeaveRequestNotificationUpdate (to manager)
│
└─ If status is already confirmed (1 or 2)
   └─ sendRequestConfirmationEmail
      └─ LeaveRequestConfirmationUpdate (to employee, manager, admins)
         └─ Regenerate country-specific PDF if type is 3 or 4

CONFIRM REQUEST (Approve or Decline)
└─ sendRequestConfirmationEmail
   ├─ If is_confirmed = 2 (Approved)
   │  ├─ LeaveRequestConfirmation (to employee, manager, admins)
   │  ├─ CC collaborators if type is 3 or 4
   │  ├─ Generate country-specific PDF if type is 3 or 4 (MK or BG)
   │  └─ LeaveRequestConfirmationPDF (to accountants if country = 1) *currently Macedonia only*
   │     └─ Attach generated country-specific PDF
   │
   └─ If is_confirmed = 1 (Declined)
      └─ LeaveRequestDeclining (to employee)

DELETE APPROVED REQUEST
└─ LeaveRequestCancelation (to employee, manager, admins)
```

---

## Greeting and Templates

All emails use a **generic "Hello,"** greeting to avoid confusion about who the recipient is. This was changed from personalized greetings which could be misleading when the same request triggers multiple emails to different recipients.

**Template Structure:**
- Header with ESOF logo and color-coded title
- Generic greeting
- Key information (employee, leave type, dates, duration)
- Call-to-action buttons (View Request or other actions)
- Footer with disclaimer

---

## Testing Emails

To test the email system:

1. **Create a new leave request** - Triggers LeaveRequestNotification to manager
2. **Update a pending request** - Triggers LeaveRequestNotificationUpdate to manager
3. **Approve a request** - Triggers LeaveRequestConfirmation (and possibly LeaveRequestConfirmationPDF)
4. **Decline a request** - Triggers LeaveRequestDeclining
5. **Update an approved request** - Triggers LeaveRequestConfirmationUpdate
6. **Delete an approved request** - Triggers LeaveRequestCancelation

---

## Configuration

Email configuration is handled in:
- `.env` file - MAIL_* settings
- `config/mail.php` - Laravel mail configuration

Make sure `MAIL_FROM_ADDRESS` is set to a valid email address.

---

## Troubleshooting

| Issue | Cause | Solution |
|-------|-------|----------|
| Emails not sending | Mail configuration incorrect | Check `.env` and `config/mail.php` |
| Wrong recipient | Incorrect user relationship | Verify `request_to` and `user_id` fields in leave_requests table |
| Missing PDF | PDF template not found | Check `public_path()` for PDF template files (MK_template_paid.pdf, MK_template_unpaid.pdf, BG_template_paid.pdf, BG_template_unpaid.pdf) |
| Wrong greeting name | Using wrong variable in template | Ensure templates use `$leaveRequest->user` for requester |
| Accounting email not sent | Country not set correctly | Verify employee's `country` field in users table (currently only 1 = Macedonia triggers email) |
| Different PDF in email | Country-specific template required | System automatically selects MK or BG template based on employee's country |

---

## Future Enhancements

### Bulgaria Support for LeaveRequestConfirmationPDF

The PDF generation infrastructure already supports both Macedonia and Bulgaria with different templates:
```php
if ($userCountry === 1) {
    // MK TEMPLATE
    $templatePath = public_path($leaveRequest->leave_type_id == 3 ? 'MK_template_paid.pdf' : 'MK_template_unpaid.pdf');
} else {
    // BG TEMPLATE
    $templatePath = public_path($leaveRequest->leave_type_id == 3 ? 'BG_template_paid.pdf' : 'BG_template_unpaid.pdf');
}
```

Currently, `LeaveRequestConfirmationPDF` is only sent when `country == 1` (Macedonia). To enable Bulgaria support, modify the `sendConfirmationAccountentsEmail` method to remove or expand the country condition:

```php
// Current condition:
if ($leaveRequestUser->country == 1) {
    $this->sendConfirmationAccountentsEmail($leaveRequest);
}

// Proposed change (to support both Macedonia and Bulgaria):
if (in_array($leaveRequestUser->country, [1, 2])) {  // 1 = Macedonia, 2 = Bulgaria
    $this->sendConfirmationAccountentsEmail($leaveRequest);
}
```

