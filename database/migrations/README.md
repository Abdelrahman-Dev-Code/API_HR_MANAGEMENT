# Migration Organization

تم تنظيم ملفات migrations في مجلدات فرعية. يسجل `AppServiceProvider` كل مسار باستخدام `loadMigrationsFrom`، لذلك تستمر أوامر Artisan في اكتشاف الملفات.

## 1. Laravel Foundation

- `01-foundation/0001_01_01_000000_create_users_table.php`
    - `users`
    - `password_reset_tokens`
    - `sessions`
- `01-foundation/0001_01_01_000001_create_cache_table.php`
    - `cache`
    - `cache_locks`
- `01-foundation/0001_01_01_000002_create_jobs_table.php`
    - `jobs`
    - `job_batches`
    - `failed_jobs`

## 2. Authentication And Authorization

- `02-authorization/2026_09_26_213130_create_personal_access_tokens_table.php`
    - `personal_access_tokens`
- `02-authorization/2026_09_26_221103_create_roles_table.php`
    - `roles`
- `02-authorization/2026_09_26_222032_create_permissions_table.php`
    - `permissions`
- `02-authorization/2026_09_26_223002_create_role_user_table.php`
    - `role_user`
- `02-authorization/2026_09_26_223312_create_permission_role_table.php`
    - `permission_role`

## 3. Company Structure

- `03-company-structure/2026_09_26_223646_create_settings_table.php`
    - `settings`
- `03-company-structure/2026_09_26_225124_create_departments_table.php`
    - `departments`
- `03-company-structure/2026_09_26_225916_create_positions_table.php`
    - `positions`
- `03-company-structure/2026_09_26_230152_create_employees_table.php`
    - `employees`

## 4. Employee Records

- `04-employee-records/2026_09_26_233619_create_documents_table.php`
    - `documents`
- `04-employee-records/2026_09_26_234005_create_holidays_table.php`
    - `holidays`
- `04-employee-records/2026_09_26_234254_create_attendances_table.php`
    - `attendances`

## 5. Leave Management

- `05-leave-management/2026_09_27_001334_create_leaves_table.php`
    - `leaves`
- `05-leave-management/2026_09_27_001942_create_leave_balances_table.php`
    - `leave_balances`

## 6. Payroll And Notifications

- `06-payroll-notifications/2026_09_27_002458_create_salaries_table.php`
    - `salaries`
- `06-payroll-notifications/2026_09_27_003450_create_notifications_table.php`
    - `notifications`

## Dependency Order

```text
users
  -> roles / permissions
  -> role_user / permission_role
  -> departments / positions
  -> employees
  -> documents / holidays / attendances / leaves / leave_balances / salaries
  -> notifications
```

## Important Rules

- Do not rename or move a migration after it has run in a shared or production database.
- Add future schema changes as new migrations with a newer timestamp.
- Keep one database concern per migration when adding future tables.
- The current timestamp order is already the correct dependency order.
