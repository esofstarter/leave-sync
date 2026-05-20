# Redmine Time Logging Integration - Setup Guide

## Overview
This integration automatically logs time entries in Redmine when leave requests are confirmed. Each working day of the leave period gets logged as an 8-hour time entry against a pre-configured Redmine task.

## What Was Implemented

### 1. Database Changes
- **New table**: `redmine_task_mappings` - Maps leave types to Redmine task IDs
- **New column**: `redmine_logged` (boolean) added to `leave_requests` table

### 2. New Files Created
- `RedmineTaskMapping` model
- `RedmineTimeLoggerService` service class
- Migrations for database changes
- Seeder for initial task mappings

### 3. Modified Files
- `LeaveRequestRepository` - Integrated time logging in the `confirm()` method

---

## Setup Instructions

### Step 1: Run Migrations
```bash
cd app/api
php artisan migrate
```

This will create the `redmine_task_mappings` table and add the `redmine_logged` column.

### Step 2: Configure Environment Variables
Add these to your `.env` file:

```env
# Redmine API Configuration
REDMINE_API_URL=https://your-redmine-instance.com
REDMINE_API_KEY=your_api_key_here

# Dedicated bot user ID for logging time
REDMINE_BOT_USER_ID=999

# Default activity ID (can be changed later)
REDMINE_DEFAULT_ACTIVITY_ID=9
```

**How to get these values:**
- `REDMINE_API_URL`: Your Redmine instance URL (without trailing slash)
- `REDMINE_API_KEY`: Create a dedicated bot user in Redmine → Go to "My account" → "API access key"
- `REDMINE_BOT_USER_ID`: The user ID of your bot user (visible in Redmine user URL)
- `REDMINE_DEFAULT_ACTIVITY_ID`: Activity ID for time entries (e.g., 9 for Development)

### Step 3: Create Redmine Tasks Manually
In your Redmine instance, create 4-5 tasks for tracking leaves:

1. Task for **Sick Leave (Unpaid)** - Get the task ID (e.g., #1234)
2. Task for **Sick Leave (Paid)** - Get the task ID (e.g., #1235)
3. Task for **Vacation (Paid)** - Get the task ID (e.g., #1236)
4. Task for **Vacation (Unpaid)** - Get the task ID (e.g., #1237)

Note the task IDs - you'll need them in the next step.

### Step 4: Seed Initial Mappings
```bash
php artisan db:seed --class=RedmineTaskMappingSeeder
```

This creates placeholder mappings with `redmine_task_id = 0`.

### Step 5: Update Task Mappings
Update the mappings with your actual Redmine task IDs using SQL or a database tool:

```sql
UPDATE redmine_task_mappings SET redmine_task_id = 1234 WHERE leave_type_id = 1; -- Sick (unpaid)
UPDATE redmine_task_mappings SET redmine_task_id = 1235 WHERE leave_type_id = 2; -- Sick (paid)
UPDATE redmine_task_mappings SET redmine_task_id = 1236 WHERE leave_type_id = 3; -- Vacation (paid)
UPDATE redmine_task_mappings SET redmine_task_id = 1237 WHERE leave_type_id = 4; -- Vacation (unpaid)
```

**Alternative**: Access the database via phpMyAdmin, TablePlus, or directly:
```bash
php artisan tinker
>>> DB::table('redmine_task_mappings')->where('leave_type_id', 1)->update(['redmine_task_id' => 1234]);
```

---

## How It Works

### Automatic Time Logging
When a leave request is **confirmed** (status = 2), the system:

1. ✅ Checks if the leave type is eligible (IDs: 1, 2, 3, 4)
2. ✅ Finds the corresponding Redmine task ID from mappings
3. ✅ Calculates working days (excludes weekends & national holidays)
4. ✅ Creates a time entry for each working day:
   - **Hours**: 8 hours per day
   - **User**: The bot user configured in `.env`
   - **Comment**: Format: `"John Doe - Sick Leave (Paid)"`
5. ✅ Marks the leave request as `redmine_logged = true`

### Leave Types Mapped
- **1** = Sick leave (unpaid)
- **2** = Sick leave (paid)
- **3** = Vacation (paid)
- **4** = Vacation (unpaid)

### Example Redmine Time Entry
```
Issue: #1236 (Vacation Tracking Task)
Date: 2026-05-21
Hours: 8
User: Bot User
Activity: Development (ID: 9)
Comment: John Doe - Vacation (Paid)
```

---

## Admin Management (Optional - Future Enhancement)

For easier management, you can create an admin UI to manage mappings:

**Manual Update via Database:**
```sql
-- View current mappings
SELECT * FROM redmine_task_mappings;

-- Update a mapping
UPDATE redmine_task_mappings 
SET redmine_task_id = 5678, is_active = true 
WHERE leave_type_id = 3;

-- Disable a mapping
UPDATE redmine_task_mappings SET is_active = false WHERE leave_type_id = 4;
```

---

## Troubleshooting

### Time entries not being created?

1. **Check logs**: `storage/logs/laravel.log`
2. **Verify API credentials**: Test your Redmine API key
3. **Check mappings**: Ensure `redmine_task_id` is set (not 0)
4. **Verify task exists**: Ensure the Redmine task ID is valid and active

### Testing the Integration

1. Create a test leave request
2. Confirm it (status = approved)
3. Check Redmine for time entries
4. Check database: `SELECT * FROM leave_requests WHERE redmine_logged = 1;`

### Common Issues

| Issue | Solution |
|-------|----------|
| "No Redmine task mapping found" | Update `redmine_task_mappings` with actual task IDs |
| "Redmine API credentials not configured" | Add `REDMINE_API_URL` and `REDMINE_API_KEY` to `.env` |
| HTTP 422/403 errors | Check bot user permissions in Redmine |
| Time entries duplicated | Check if `redmine_logged` flag is working |

---

## Future Enhancements

### Delete Time Entries (Placeholder)
The `deleteLeaveTimeEntries()` method is included but not implemented. To add this:

1. Query Redmine API to find time entries by comment pattern
2. Delete each entry via `DELETE /time_entries/{id}.json`
3. Call it in the `delete()` method if needed

### Admin UI for Mappings
Create a CRUD interface:
- Controller: `RedmineTaskMappingController`
- Views for listing/editing mappings
- Routes in `routes/web.php`

---

## Testing Checklist

- [ ] Migrations ran successfully
- [ ] Environment variables configured
- [ ] Redmine tasks created manually
- [ ] Seeder executed
- [ ] Task mappings updated with real IDs
- [ ] Test leave request confirmed
- [ ] Time entries visible in Redmine
- [ ] Log file checked for errors

---

## Questions?

Refer to the implementation files:
- Service: `app/Applications/LeaveRequest/Services/RedmineTimeLoggerService.php`
- Model: `app/Applications/RedmineTaskMapping/Model/RedmineTaskMapping.php`
- Repository: `app/Applications/LeaveRequest/Repositories/LeaveRequestRepository.php` (line ~178)
