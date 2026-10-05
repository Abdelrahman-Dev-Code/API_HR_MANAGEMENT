# مرجع API المختصر

## Headers

للمسارات المحمية:

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer TOKEN
```

## Authentication

| Method | URI                | Auth | Response        |
| ------ | ------------------ | ---- | --------------- |
| POST   | `/api/auth/login`  | لا   | Token ومستخدم   |
| GET    | `/api/auth/me`     | نعم  | المستخدم الحالي |
| POST   | `/api/auth/logout` | نعم  | 204             |

## Resources

المسارات تتطلب token وصلاحية المورد الموضحة في Gates. العمليات المتاحة:

| Resource       | URI                   | Auth                  | ملاحظات                                               |
| -------------- | --------------------- | --------------------- | ----------------------------------------------------- |
| Employees      | `/api/employees`      | حسب المورد            | GET/POST/PATCH/DELETE؛ نطاق حسب الدور                 |
| Departments    | `/api/departments`    | حسب المورد            | GET/POST/PATCH/DELETE؛ يدعم القسم الرئيسي             |
| Positions      | `/api/positions`      | حسب المورد            | GET/POST/PATCH/DELETE؛ الاسم والرمز فريدان            |
| Documents      | `/api/documents`      | حسب المورد            | GET/POST/PATCH/DELETE؛ رفع خاص حتى 10 MiB وتنزيل محمي |
| Holidays       | `/api/holidays`       | حسب المورد            | GET/POST/PATCH/DELETE؛ التاريخ فريد                   |
| Attendances    | `/api/attendances`    | حسب المورد            | GET/POST/PATCH؛ لا يوجد DELETE                        |
| Leaves         | `/api/leaves`         | حسب المورد            | GET/POST/PATCH/DELETE؛ قرار منفصل                     |
| Leave balances | `/api/leave-balances` | حسب المورد            | GET/POST/PATCH/DELETE؛ الموظف/السنة/النوع فريد        |
| Salaries       | `/api/salaries`       | حسب المورد            | GET/POST/PATCH/DELETE للمسودات؛ قرار ودفع منفصلان     |
| Roles          | `/api/roles`          | `roles.manage`        | CRUD ويدعم `permission_ids`                           |
| Permissions    | `/api/permissions`    | `permissions.manage`  | CRUD؛ اسم الصلاحية فريد                               |
| Organization   | `/api/organization`   | `organization.manage` | GET وPATCH فقط                                        |

المؤسسة ليست مورد CRUD. `GET` و`PATCH /api/organization` يعملان على سجل إعداد واحد، ويتطلبان Gate `manage-organization`. تحديث المؤسسة محدود إلى 30 طلبًا في الدقيقة.

## Resource operation matrix

| Operation      | Method    | Example                      |
| -------------- | --------- | ---------------------------- |
| List           | GET       | `/api/employees?per_page=15` |
| Create         | POST      | `/api/employees`             |
| Read one       | GET       | `/api/employees/1`           |
| Replace/Update | PUT/PATCH | `/api/employees/1`           |
| Delete         | DELETE    | `/api/employees/1`           |

هذه أمثلة عامة: الحضور لا يقبل DELETE، والرواتب المراجعة لا تقبل التعديل أو الحذف.

## Employee fields

| Field           | Required on create | Values/format              |
| --------------- | -----------------: | -------------------------- |
| `user_id`       |                نعم | existing users, unique     |
| `department_id` |                نعم | existing departments       |
| `position_id`   |                نعم | existing positions         |
| `employee_code` |                نعم | unique string              |
| `name`          |                نعم | string                     |
| `email`         |                نعم | valid unique email         |
| `basic_salary`  |                 لا | number >= 0                |
| `hire_date`     |                 لا | date                       |
| `birth_date`    |                 لا | date before today          |
| `gender`        |                 لا | male/female                |
| `status`        |                 لا | active/inactive/terminated |
| `work_shift`    |                 لا | day/night                  |

## Attendance fields

- `attendance_date`: تاريخ بداية الوردية.
- `shift`: `day` أو `night`.
- `check_in_at` و`check_out_at`: datetime كامل، ويمكن أن يكون الخروج في اليوم التالي.
- `status`: `present`, `absent`, `leave`, `holiday`, `late`.
- `late_minutes` و`overtime_minutes`: أعداد غير سالبة.
- يجب أن تطابق `shift` وردية الموظف؛ تاريخ الدخول هو `attendance_date` والخروج يمكن أن يقع في اليوم التالي.

## Leave fields

- `leave_type`: `sick`, `casual`, `earned`, `maternity`, `paternity`, `bereavement`, `unpaid`.
- `status`: `pending`, `approved`, `rejected`.
- `start_date` و`end_date`: النهاية لا تسبق البداية.
- لا يرسل العميل `status`, `approved_by`, أو `total_days` عند الإنشاء أو التحديث.
- يحسب الخادم أيام العمل مستبعدًا عطلة نهاية الأسبوع والعطل الرسمية، ويرفض التداخل أو نقص الرصيد عند الاعتماد.
- الرصيد المستهلك لا يقبل تعديلًا مباشرًا عبر leave-balance API.

## Salary fields

- `year`: سنة الراتب.
- `month`: من 1 إلى 12.
- الأيام والمبالغ: غير سالبة.
- `status`: `pending`, `approved`, `rejected`.
- `net_amount`, `status`, `paid_at` لا يقبلها API كمدخلات من العميل.
- يمر الراتب بمسودّة ثم اعتماد ثم دفع، مع حفظ هوية المعتمد والدافع.

## Errors

- `401`: يلزم تسجيل الدخول.
- `403`: الحساب غير نشط أو الصلاحية غير ممنوحة.
- `404`: المورد غير موجود أو خارج نطاق المستخدم.
- `409`: تعارض متزامن مع قيد فريد.
- `422`: فشل التحقق؛ راجع `errors`.
- `429`: تجاوز حد الطلبات.
- أرسل `Accept-Language: en` لرسائل إنجليزية؛ الافتراضي عربي.

## Organization permissions

- القراءة والتعديل تتطلبان مستخدمًا نشطًا لديه الدور الذي يحمل صلاحية `organization.manage`.
- طلب `PATCH` محدود بـ 30 طلبًا في الدقيقة.
- لا يوجد إنشاء أو حذف عام لسجل المؤسسة.

## Source of truth

- Routes: `routes/api.php` و`routes/api/*.php`.
- Validation: `app/Http/Requests`.
- Response shape: `app/Http/Resources`.
- Data access: `app/Repositories`.
- Database rules: `database/migrations`.
