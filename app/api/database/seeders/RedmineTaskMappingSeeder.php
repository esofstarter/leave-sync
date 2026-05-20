<?php

namespace Database\Seeders;

use App\Applications\RedmineTaskMapping\Model\RedmineTaskMapping;
use Illuminate\Database\Seeder;

class RedmineTaskMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates initial mappings for leave types to Redmine tasks.
     * The redmine_task_id values are placeholders and should be updated
     * with actual Redmine task IDs after manual task creation.
     */
    public function run(): void
    {
        $mappings = [
            [
                'leave_type_id' => 1, // Sick leave (unpaid)
                'redmine_task_id' => 0, // Update with actual Redmine task ID
                'is_active' => true,
            ],
            [
                'leave_type_id' => 2, // Sick leave (paid)
                'redmine_task_id' => 0, // Update with actual Redmine task ID
                'is_active' => true,
            ],
            [
                'leave_type_id' => 3, // Vacation (paid)
                'redmine_task_id' => 0, // Update with actual Redmine task ID
                'is_active' => true,
            ],
            [
                'leave_type_id' => 4, // Vacation (unpaid)
                'redmine_task_id' => 0, // Update with actual Redmine task ID
                'is_active' => true,
            ],
        ];

        foreach ($mappings as $mapping) {
            RedmineTaskMapping::updateOrCreate(
                ['leave_type_id' => $mapping['leave_type_id']],
                $mapping
            );
        }

        $this->command->info('Redmine task mappings seeded successfully!');
        $this->command->warn('Remember to update the redmine_task_id values with actual Redmine task IDs.');
    }
}
