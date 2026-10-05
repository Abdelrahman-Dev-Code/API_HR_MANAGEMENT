<!--
  File Name: database-migrations.md
  Description:m
  Developer: Abdelrahman-Dev-Code
  Created Date: 2026-09-27
  Last Modified: 2026-09-28
-->

# توثيق جداول قاعدة بيانات HR System

هذا الملف يوثق جميع الجداول التي تنشئها migrations الحالية في المشروع. يتضمن التوثيق الغرض، الأعمدة، أنواع البيانات، القيود، وأمثلة عملية.

## ترتيب الاعتماد بين الجداول

```text
users
  -> role_user -> roles
  -> employees -> departments / positions
                    -> documents
                    -> attendances
                    -> leaves -> leave_balances
                    -> salaries

permissions -> permission_role -> roles
users -> personal_access_tokens
users -> notifications
```

---

# Migration: users

## الغرض من الجدول

تخزين حسابات المستخدمين الذين يسجلون الدخول إلى النظام. الموظف يرتبط بحساب مستخدم عبر `employees.user_id`.

## الأعمدة

| العمود                     | النوع               | الوصف                   | لماذا؟                                               |
| -------------------------- | ------------------- | ----------------------- | ---------------------------------------------------- |
| `id`                       | `BIGINT UNSIGNED`   | المعرف الأساسي          | مناسب لمعظم أعداد المستخدمين ويدعم الزيادة التلقائية |
| `name`                     | `VARCHAR`           | اسم المستخدم            | نص قصير للعرض                                        |
| `email`                    | `VARCHAR` فريد      | البريد الإلكتروني       | يستخدم للدخول ويمنع التكرار                          |
| `email_verified_at`        | `TIMESTAMP NULL`    | وقت تأكيد البريد        | `NULL` قبل التأكيد                                   |
| `password`                 | `VARCHAR`           | كلمة المرور المشفرة     | Laravel يخزن hash وليس كلمة المرور الأصلية           |
| `remember_token`           | `VARCHAR(100) NULL` | تذكر جلسة الدخول        | تستخدمه Laravel Auth                                 |
| `is_active`                | `BOOLEAN`           | حالة الحساب             | الافتراضي `true` لتعطيل الحساب دون حذفه              |
| `created_at`, `updated_at` | `TIMESTAMP`         | تواريخ الإنشاء والتعديل | تتبع دورة حياة السجل                                 |

## أمثلة

```text
id: 1
name: Ahmed Ali
email: ahmed@example.com
is_active: true
```

## ملاحظات

- `email` فريد.
- لا يوجد `softDeletes` للمستخدم.
- حذف المستخدم يحذف الموظف المرتبط به بسبب علاقة `employees.user_id`.

---

# Migration: password_reset_tokens

## الغرض من الجدول

تخزين رموز إعادة تعيين كلمات المرور.

## الأعمدة

| العمود       | النوع                 | الوصف             | لماذا؟                       |
| ------------ | --------------------- | ----------------- | ---------------------------- |
| `email`      | `VARCHAR` مفتاح أساسي | بريد صاحب الطلب   | يحدد طلب إعادة التعيين       |
| `token`      | `VARCHAR`             | رمز إعادة التعيين | يستخدم للتحقق من الطلب       |
| `created_at` | `TIMESTAMP NULL`      | وقت إنشاء الرمز   | يسمح بانتهاء الرمز حسب الزمن |

## أمثلة

```text
email: ahmed@example.com
 token: hashed-reset-token
created_at: 2026-09-27 10:00:00
```

## ملاحظات

- يوجد رمز واحد فعال لكل بريد بسبب كون `email` مفتاحًا أساسيًا.
- يجب أن يكون انتهاء الرمز ومعالجته ضمن إعدادات Laravel أو منطق التطبيق.

---

# Migration: sessions

## الغرض من الجدول

تخزين جلسات المستخدمين عند استخدام database session driver.

## الأعمدة

| العمود          | النوع                  | الوصف                         | لماذا؟                         |
| --------------- | ---------------------- | ----------------------------- | ------------------------------ |
| `id`            | `VARCHAR` مفتاح أساسي  | معرف الجلسة                   | معرف نصي قادم من المتصفح       |
| `user_id`       | `BIGINT UNSIGNED NULL` | المستخدم صاحب الجلسة          | `NULL` للجلسات غير المسجلة     |
| `ip_address`    | `VARCHAR(45) NULL`     | عنوان IP                      | 45 يدعم IPv4 وIPv6             |
| `user_agent`    | `TEXT NULL`            | معلومات المتصفح               | قد تكون طويلة                  |
| `payload`       | `LONGTEXT`             | بيانات الجلسة                 | قد تحتوي بيانات كثيرة          |
| `last_activity` | `INTEGER` مفهرس        | آخر نشاط بصيغة Unix timestamp | البحث عن الجلسات القديمة بسرعة |

## ملاحظات

- `user_id` مفهرس لكنه لا يحتوي على foreign key في migration القياسية.
- تنظيف الجلسات القديمة مسؤولية Laravel scheduler أو الأمر المناسب.

---

# Migration: cache

## الغرض من الجدول

تخزين قيم cache عند استخدام database cache driver.

## الأعمدة

| العمود       | النوع                 | الوصف          | لماذا؟                      |
| ------------ | --------------------- | -------------- | --------------------------- |
| `key`        | `VARCHAR` مفتاح أساسي | مفتاح القيمة   | يمنع وجود نفس المفتاح مرتين |
| `value`      | `MEDIUMTEXT`          | القيمة المخزنة | يدعم بيانات أكبر من `TEXT`  |
| `expiration` | `BIGINT` مفهرس        | وقت الانتهاء   | يسهل حذف القيم المنتهية     |

## جدول cache_locks

| العمود       | النوع                 | الوصف            | لماذا؟                         |
| ------------ | --------------------- | ---------------- | ------------------------------ |
| `key`        | `VARCHAR` مفتاح أساسي | مفتاح القفل      | قفل واحد لكل مورد              |
| `owner`      | `VARCHAR`             | مالك القفل       | معرفة العملية التي أنشأت القفل |
| `expiration` | `BIGINT` مفهرس        | وقت انتهاء القفل | منع القفل الدائم               |

## ملاحظات

هذه جداول بنية Laravel وليست جداول أعمال HR مباشرة.

---

# Migration: jobs

## الغرض من الجدول

تخزين المهام التي تعمل في الخلفية، مثل إرسال إشعارات أو معالجة ملفات.

## جدول jobs

| العمود         | النوع               | الوصف           | لماذا؟                        |
| -------------- | ------------------- | --------------- | ----------------------------- |
| `id`           | `BIGINT UNSIGNED`   | معرف المهمة     | معرف فريد للمهمة              |
| `queue`        | `VARCHAR` مفهرس     | اسم الطابور     | توزيع المهام بين queues       |
| `payload`      | `LONGTEXT`          | بيانات المهمة   | قد تحتوي serialized job كبيرة |
| `attempts`     | `UNSIGNED SMALLINT` | عدد المحاولات   | يمنع إعادة المحاولة بلا حدود  |
| `reserved_at`  | `UNSIGNED INT NULL` | وقت حجز المهمة  | `NULL` قبل استلام worker لها  |
| `available_at` | `UNSIGNED INT`      | وقت توفر المهمة | يدعم التأخير                  |
| `created_at`   | `UNSIGNED INT`      | وقت الإنشاء     | Unix timestamp                |

## جدول job_batches

| العمود           | النوع                 | الوصف                 | لماذا؟                   |
| ---------------- | --------------------- | --------------------- | ------------------------ |
| `id`             | `VARCHAR` مفتاح أساسي | معرف batch            | يحدد مجموعة المهام       |
| `name`           | `VARCHAR`             | اسم المجموعة          | عرض المجموعة للمستخدم    |
| `total_jobs`     | `INTEGER`             | إجمالي المهام         | متابعة التقدم            |
| `pending_jobs`   | `INTEGER`             | المهام المتبقية       | معرفة حالة المجموعة      |
| `failed_jobs`    | `INTEGER`             | المهام الفاشلة        | التقارير وإعادة المحاولة |
| `failed_job_ids` | `LONGTEXT`            | معرفات المهام الفاشلة | تخزين القائمة            |
| `options`        | `MEDIUMTEXT NULL`     | خيارات المجموعة       | قد لا توجد خيارات        |
| `cancelled_at`   | `INTEGER NULL`        | وقت الإلغاء           | `NULL` إذا لم تلغَ       |
| `created_at`     | `INTEGER`             | وقت الإنشاء           | Unix timestamp           |
| `finished_at`    | `INTEGER NULL`        | وقت الانتهاء          | `NULL` أثناء التنفيذ     |

## جدول failed_jobs

| العمود       | النوع             | الوصف               | لماذا؟                      |
| ------------ | ----------------- | ------------------- | --------------------------- |
| `id`         | `BIGINT UNSIGNED` | معرف السجل          | معرف داخلي                  |
| `uuid`       | `VARCHAR` فريد    | معرف المهمة الفاشلة | تتبع المهمة دون تكرار       |
| `connection` | `VARCHAR`         | اتصال queue         | معرفة driver المستخدم       |
| `queue`      | `VARCHAR`         | اسم queue           | تحديد مصدر المهمة           |
| `payload`    | `LONGTEXT`        | بيانات المهمة       | إعادة تحليل أو تشغيل المهمة |
| `exception`  | `LONGTEXT`        | تفاصيل الخطأ        | التشخيص                     |
| `failed_at`  | `TIMESTAMP`       | وقت الفشل           | الافتراضي الوقت الحالي      |

## ملاحظات

هذه الجداول تعتمد على Laravel Queue، ويجب مراقبتها في الإنتاج.

---

# Migration: personal_access_tokens

## الغرض من الجدول

تخزين رموز Sanctum للوصول إلى API.

## الأعمدة

| العمود                     | النوع                  | الوصف                 | لماذا؟                              |
| -------------------------- | ---------------------- | --------------------- | ----------------------------------- |
| `id`                       | `BIGINT UNSIGNED`      | معرف الرمز            | معرف داخلي                          |
| `tokenable_type`           | `VARCHAR`              | نوع الكيان صاحب الرمز | يدعم User أو نموذجًا آخر عبر morphs |
| `tokenable_id`             | `BIGINT UNSIGNED`      | معرف صاحب الرمز       | الجزء الرقمي للعلاقة polymorphic    |
| `name`                     | `TEXT`                 | اسم الجهاز أو التطبيق | تمييز الرموز                        |
| `token`                    | `VARCHAR(64)` فريد     | قيمة الرمز المشفرة    | يمنع التكرار ويحفظ الحجم            |
| `abilities`                | `TEXT NULL`            | صلاحيات الرمز         | `NULL` عند عدم تحديدها              |
| `last_used_at`             | `TIMESTAMP NULL`       | آخر استخدام           | للمراجعة الأمنية                    |
| `expires_at`               | `TIMESTAMP NULL` مفهرس | وقت انتهاء الرمز      | تسريع البحث عن المنتهي              |
| `created_at`, `updated_at` | `TIMESTAMP`            | تواريخ الرمز          | التتبع                              |

## ملاحظات

`morphs('tokenable')` ينشئ علاقة polymorphic وفهرسًا مركبًا. لا يستخدم foreign key تقليديًا لأن النوع قد يكون أكثر من Model.

---

# Migration: roles

## الغرض من الجدول

تعريف أدوار المستخدمين مثل مدير النظام أو مدير الموارد البشرية.

## الأعمدة

| العمود                     | النوع              | الوصف         | لماذا؟               |
| -------------------------- | ------------------ | ------------- | -------------------- |
| `id`                       | `BIGINT UNSIGNED`  | المعرف        | مفتاح أساسي          |
| `role_name`                | `VARCHAR(50)` فريد | الاسم البرمجي | يستخدم في الصلاحيات  |
| `display_name`             | `VARCHAR(100)`     | الاسم الظاهر  | مناسب للواجهة        |
| `description`              | `TEXT NULL`        | شرح الدور     | اختياري              |
| `is_active`                | `BOOLEAN`          | تفعيل الدور   | تعطيل الدور دون حذفه |
| `created_at`, `updated_at` | `TIMESTAMP`        | التواريخ      | التتبع               |

## مثال

```text
role_name: hr_manager
display_name: مدير الموارد البشرية
is_active: true
```

---

# Migration: permissions

## الغرض من الجدول

تعريف الصلاحيات الدقيقة التي يمكن ربطها بالأدوار.

## الأعمدة

| العمود                     | النوع              | الوصف           | لماذا؟                   |
| -------------------------- | ------------------ | --------------- | ------------------------ |
| `id`                       | `BIGINT UNSIGNED`  | المعرف          | مفتاح أساسي              |
| `permission_name`          | `VARCHAR(50)` فريد | الاسم البرمجي   | مثل `employees.create`   |
| `display_name`             | `VARCHAR(100)`     | الاسم الظاهر    | للوحة التحكم             |
| `description`              | `TEXT NULL`        | شرح الصلاحية    | توثيق اختياري            |
| `group`                    | `VARCHAR(50)`      | مجموعة الصلاحية | مثل employees أو payroll |
| `created_at`, `updated_at` | `TIMESTAMP`        | التواريخ        | التتبع                   |

## ملاحظات

`permission_name` فريد، أما `group` فليس فريدًا لأن المجموعة تحتوي عدة صلاحيات.

---

# Migration: role_user

## الغرض من الجدول

جدول وسيط لعلاقة many-to-many بين المستخدمين والأدوار.

## الأعمدة

| العمود                     | النوع                | الوصف        | لماذا؟              |
| -------------------------- | -------------------- | ------------ | ------------------- |
| `id`                       | `BIGINT UNSIGNED`    | معرف السجل   | مفتاح داخلي         |
| `user_id`                  | `BIGINT UNSIGNED` FK | المستخدم     | يرتبط بـ `users.id` |
| `role_id`                  | `BIGINT UNSIGNED` FK | الدور        | يرتبط بـ `roles.id` |
| `created_at`, `updated_at` | `TIMESTAMP`          | تواريخ الربط | التتبع              |

## القيود والأمثلة

- يوجد `unique(user_id, role_id)` لمنع ربط الدور نفسه بالمستخدم مرتين.
- الحذف المتسلسل يحذف الربط عند حذف المستخدم أو الدور.

```text
user_id: 1, role_id: 2
```

---

# Migration: permission_role

## الغرض من الجدول

جدول وسيط لعلاقة many-to-many بين الأدوار والصلاحيات.

## الأعمدة

| العمود                     | النوع                | الوصف        | لماذا؟                    |
| -------------------------- | -------------------- | ------------ | ------------------------- |
| `id`                       | `BIGINT UNSIGNED`    | معرف الربط   | مفتاح داخلي               |
| `permission_id`            | `BIGINT UNSIGNED` FK | الصلاحية     | يرتبط بـ `permissions.id` |
| `role_id`                  | `BIGINT UNSIGNED` FK | الدور        | يرتبط بـ `roles.id`       |
| `created_at`, `updated_at` | `TIMESTAMP`          | تواريخ الربط | التتبع                    |

## ملاحظات

`unique(permission_id, role_id)` يمنع تكرار نفس الصلاحية في الدور نفسه، و`cascade` يحذف الربط التابع.

---

# Migration: settings

## الغرض من الجدول

تخزين إعدادات الشركة المستخدمة في الحضور والخصومات والإجازات.

## الأعمدة

| العمود                     | النوع              | الوصف                    | لماذا؟                                          |
| -------------------------- | ------------------ | ------------------------ | ----------------------------------------------- |
| `id`                       | `BIGINT UNSIGNED`  | المعرف                   | مفتاح أساسي                                     |
| `setting_key`              | `VARCHAR(50)` فريد | مفتاح الإعداد            | الافتراضي `company` ويمنع سجلات الشركة المتكررة |
| `company_name`             | `VARCHAR(100)`     | اسم الشركة               | للعرض والتقارير                                 |
| `company_logo`             | `VARCHAR NULL`     | مسار الشعار              | يخزن المسار وليس الملف نفسه                     |
| `timezone`                 | `VARCHAR`          | المنطقة الزمنية          | الافتراضي `Asia/Riyadh`                         |
| `work_start_time`          | `TIME`             | بداية الدوام العام       | مناسب لوقت يومي ثابت                            |
| `work_end_time`            | `TIME`             | نهاية الدوام العام       | مناسب لوقت يومي ثابت                            |
| `weekend_days`             | `JSON`             | أيام العطلة الأسبوعية    | يسمح بقائمة مثل Friday وSaturday                |
| `late_deduction_rate`      | `DECIMAL(5,2)`     | خصم التأخير              | دقة عشرية للنسبة                                |
| `absent_deduction_rate`    | `DECIMAL(5,2)`     | خصم الغياب               | نسبة أو معامل حسب منطق التطبيق                  |
| `overtime_rate`            | `DECIMAL(5,2)`     | معامل العمل الإضافي      | مثل 1.5                                         |
| `annual_leave_days`        | `INTEGER`          | عدد أيام الإجازة السنوية | قيمة افتراضية 21                                |
| `created_at`, `updated_at` | `TIMESTAMP`        | التواريخ                 | التتبع                                          |

## مثال

```text
setting_key: company
company_name: شركة النور
timezone: Asia/Riyadh
work_start_time: 08:00:00
work_end_time: 17:00:00
weekend_days: ["Friday", "Saturday"]
overtime_rate: 1.50
annual_leave_days: 21
```

## ملاحظات

- `annual_leave_days` من نوع unsigned لمنع القيم السالبة، أما النسب فيجب التحقق منها في Form Request أو Service.
- أوقات الدوام العامة لا تكفي وحدها لو كان للنهاري والليلي أوقات مختلفة؛ يجب إضافة إعدادات ورديات مستقلة أو جدول shifts.
- `weekend_days` لا يملك default بسبب توافق MySQL؛ يجب تمرير قيمته عند إنشاء إعداد الشركة.

---

# Migration: departments

## الغرض من الجدول

تخزين أقسام الشركة، مع دعم الأقسام الفرعية.

## الأعمدة

| العمود                     | النوع                     | الوصف         | لماذا؟               |
| -------------------------- | ------------------------- | ------------- | -------------------- |
| `id`                       | `BIGINT UNSIGNED`         | المعرف        | مفتاح أساسي          |
| `department_name`          | `VARCHAR(100)` فريد       | اسم القسم     | منع تكرار اسم القسم  |
| `description`              | `TEXT NULL`               | وصف القسم     | تفاصيل اختيارية      |
| `parent_department_id`     | `BIGINT UNSIGNED NULL` FK | القسم الأب    | يدعم شجرة الأقسام    |
| `is_active`                | `BOOLEAN`                 | حالة القسم    | تعطيل دون حذف        |
| `code`                     | `VARCHAR` فريد            | كود القسم     | استخدامه في التقارير |
| `location`                 | `VARCHAR NULL`            | موقع القسم    | فرع أو مبنى اختياري  |
| `created_at`, `updated_at` | `TIMESTAMP`               | التواريخ      | التتبع               |
| `deleted_at`               | `TIMESTAMP NULL`          | الحذف المنطقي | يدعم `softDeletes`   |

## ملاحظات

- `parent_department_id` يحذف إلى `NULL` عند حذف القسم الأب.
- حذف القسم ممنوع إذا كان له موظفون بسبب `restrictOnDelete` في employees.
- القيم الفريدة تبقى محجوزة حتى بعد soft delete، وهذا قرار مناسب إذا كانت الأكواد تاريخية وغير قابلة لإعادة الاستخدام.

---

# Migration: positions

## الغرض من الجدول

تخزين المسميات الوظيفية المتاحة في الشركة.

## الأعمدة

| العمود                     | النوع               | الوصف        | لماذا؟                         |
| -------------------------- | ------------------- | ------------ | ------------------------------ |
| `id`                       | `BIGINT UNSIGNED`   | المعرف       | مفتاح أساسي                    |
| `position_name`            | `VARCHAR(100)` فريد | اسم الوظيفة  | منع تكرار المسمى               |
| `description`              | `TEXT NULL`         | وصف الوظيفة  | المهام أو المتطلبات            |
| `code`                     | `VARCHAR` فريد      | كود الوظيفة  | التقارير والربط البرمجي        |
| `is_active`                | `BOOLEAN`           | حالة الوظيفة | إيقاف استخدامها للموظفين الجدد |
| `created_at`, `updated_at` | `TIMESTAMP`         | التواريخ     | التتبع                         |

## مثال

```text
position_name: Software Engineer
code: SE-001
is_active: true
```

## ملاحظات

حذف الوظيفة ممنوع إذا كان هناك موظفون مرتبطون بها بسبب `restrictOnDelete`.

---

# Migration: employees

## الغرض من الجدول

تخزين الملف الوظيفي للموظف وربطه بحساب المستخدم والقسم والمسمى الوظيفي.

## الأعمدة

| العمود                     | النوع                     | الوصف                      | لماذا؟                     |
| -------------------------- | ------------------------- | -------------------------- | -------------------------- |
| `id`                       | `BIGINT UNSIGNED`         | المعرف                     | مفتاح أساسي                |
| `user_id`                  | `BIGINT UNSIGNED` فريد FK | حساب الدخول                | موظف واحد لكل مستخدم       |
| `department_id`            | `BIGINT UNSIGNED` FK      | القسم                      | ربط الموظف بالهيكل الإداري |
| `position_id`              | `BIGINT UNSIGNED` FK      | المسمى الوظيفي             | تحديد الوظيفة              |
| `employee_code`            | `VARCHAR` فريد            | الرقم الوظيفي              | معرف عملي ثابت             |
| `name`                     | `VARCHAR`                 | اسم الموظف                 | نسخة للعرض والتقارير       |
| `email`                    | `VARCHAR` فريد            | بريد الموظف                | بيانات اتصال               |
| `phone`                    | `VARCHAR NULL`            | الهاتف                     | قد لا يتوفر                |
| `basic_salary`             | `DECIMAL(10,2) NULL`      | الراتب الأساسي             | دقة مالية من منزلتين       |
| `hire_date`                | `DATE NULL`               | تاريخ التوظيف              | حساب الخدمة                |
| `birth_date`               | `DATE NULL`               | تاريخ الميلاد              | البيانات الشخصية           |
| `address`                  | `VARCHAR NULL`            | العنوان                    | بيانات اتصال               |
| `gender`                   | `ENUM NULL`               | الجنس                      | القيم الحالية male/female  |
| `profile_photo`            | `VARCHAR NULL`            | مسار الصورة                | لا يخزن الملف داخل DB      |
| `status`                   | `ENUM`                    | active/inactive/terminated | إدارة دورة الموظف          |
| `work_shift`               | `ENUM`                    | day/night                  | الوردية الأساسية للموظف    |
| `created_at`, `updated_at` | `TIMESTAMP`               | التواريخ                   | التتبع                     |
| `deleted_at`               | `TIMESTAMP NULL`          | الحذف المنطقي              | الحفاظ على التاريخ         |

## مثال

```text
employee_code: EMP-0001
name: Ahmed Ali
basic_salary: 8500.00
status: active
work_shift: night
```

## ملاحظات

- `work_shift` يحدد الوردية الأساسية، بينما `attendances.shift` يسجل الوردية الفعلية.
- `user_id` فريد لعلاقة واحد إلى واحد.
- حذف المستخدم يحذف الموظف فعليًا بسبب `cascade`، لذلك يجب الحذر مع التاريخ الوظيفي.
- `email` مكرر منطقيًا مع users.email؛ يجب على التطبيق الحفاظ على تطابقهما أو إلغاء أحد الحقلين.

---

# Migration: documents

## الغرض من الجدول

تخزين مستندات الموظفين مثل الهوية والعقد والشهادات.

## الأعمدة

| العمود                     | النوع                | الوصف          | لماذا؟                        |
| -------------------------- | -------------------- | -------------- | ----------------------------- |
| `id`                       | `BIGINT UNSIGNED`    | المعرف         | مفتاح أساسي                   |
| `document_name`            | `VARCHAR`            | اسم المستند    | مثل عقد العمل                 |
| `document_type`            | `VARCHAR`            | نوع المستند    | مرن لإضافة أنواع جديدة        |
| `file_path`                | `VARCHAR`            | مسار الملف     | فصل التخزين عن قاعدة البيانات |
| `file_size`                | `UNSIGNED INTEGER`   | حجم الملف      | يمنع الحجم السالب             |
| `expiry_date`              | `DATE NULL`          | تاريخ الانتهاء | مستندات قد لا تنتهي           |
| `notes`                    | `TEXT NULL`          | ملاحظات        | تفاصيل إضافية                 |
| `employee_id`              | `BIGINT UNSIGNED` FK | صاحب المستند   | ربطه بالموظف                  |
| `created_at`, `updated_at` | `TIMESTAMP`          | التواريخ       | التتبع                        |

## ملاحظات

- حذف الموظف يحذف مستنداته بسبب `cascade`.
- يفضل إضافة فهرس لـ `expiry_date` عند بناء شاشة المستندات المنتهية.
- يفضل استخدام validation للتحقق من MIME type وحجم الملف.

---

# Migration: holidays

## الغرض من الجدول

تخزين العطلات الرسمية أو الخاصة بالشركة.

## الأعمدة

| العمود                     | النوع             | الوصف         | لماذا؟                 |
| -------------------------- | ----------------- | ------------- | ---------------------- |
| `id`                       | `BIGINT UNSIGNED` | المعرف        | مفتاح أساسي            |
| `holiday_name`             | `VARCHAR(100)`    | اسم العطلة    | العرض والتقارير        |
| `holiday_date`             | `DATE` فريد       | تاريخ العطلة  | منع تكرار التاريخ      |
| `description`              | `TEXT NULL`       | تفاصيل العطلة | اختياري                |
| `is_recurring`             | `BOOLEAN`         | هل تتكرر؟     | تمييز العطلات المتكررة |
| `created_at`, `updated_at` | `TIMESTAMP`       | التواريخ      | التتبع                 |

## مثال

```text
holiday_name: اليوم الوطني
holiday_date: 2026-09-23
is_recurring: true
```

## ملاحظات

مع `holiday_date` الفريد يجب إنشاء سجل لكل سنة للعطلة المتكررة. إذا كان المطلوب تخزين الشهر واليوم مرة واحدة، يلزم تصميم مختلف.

---

# Migration: attendances

## الغرض من الجدول

تسجيل حضور وغياب الموظف حسب التاريخ والوردية، مع دعم الوردية الليلية الممتدة لليوم التالي.

## الأعمدة

| العمود                     | النوع                | الوصف                             | لماذا؟                                    |
| -------------------------- | -------------------- | --------------------------------- | ----------------------------------------- |
| `id`                       | `BIGINT UNSIGNED`    | المعرف                            | مفتاح أساسي                               |
| `employee_id`              | `BIGINT UNSIGNED` FK | الموظف                            | صاحب سجل الحضور                           |
| `attendance_date`          | `DATE`               | تاريخ بداية الوردية               | ثابت حتى لو انتهت الوردية في اليوم التالي |
| `shift`                    | `ENUM`               | day/night                         | تحديد الوردية                             |
| `check_in_at`              | `TIMESTAMP NULL`     | وقت الدخول الكامل                 | يدعم التاريخ والوقت للوردية الليلية       |
| `check_out_at`             | `TIMESTAMP NULL`     | وقت الخروج الكامل                 | يمكن أن يكون في اليوم التالي              |
| `status`                   | `ENUM`               | present/absent/leave/holiday/late | الحالة التشغيلية                          |
| `late_minutes`             | `INTEGER`            | دقائق التأخير                     | تستخدم في الحسابات                        |
| `overtime_minutes`         | `INTEGER`            | دقائق العمل الإضافي               | تستخدم في الرواتب                         |
| `source`                   | `VARCHAR`            | مصدر التسجيل                      | مثل web أو mobile أو biometric            |
| `ip_address`               | `VARCHAR NULL`       | IP التسجيل                        | التدقيق الأمني                            |
| `location`                 | `VARCHAR NULL`       | الموقع                            | فرع أو وصف الموقع                         |
| `metadata`                 | `JSON NULL`          | بيانات إضافية                     | مرونة للمصادر المختلفة                    |
| `notes`                    | `VARCHAR NULL`       | ملاحظات                           | سبب التأخير أو التعديل                    |
| `created_at`, `updated_at` | `TIMESTAMP`          | التواريخ                          | التتبع                                    |

## أمثلة

```text
employee_id: 7
attendance_date: 2026-09-27
shift: night
check_in_at: 2026-09-27 23:00:00
check_out_at: 2026-09-28 07:00:00
status: present
```

## القيود والملاحظات

- `unique(employee_id, attendance_date, shift)` يسمح بسجل نهاري وسجل ليلي للموظف في اليوم نفسه، ويمنع تكرار الوردية.
- يجب أن يتحقق التطبيق من أن الخروج ليس أقدم من الدخول.
- `late_minutes` و`overtime_minutes` من نوع unsigned لمنع القيم السالبة.
- يجب أن يتحقق التطبيق من توافق `shift` مع `employees.work_shift` إذا لم يكن تبديل الورديات مسموحًا.

---

# Migration: leaves

## الغرض من الجدول

تسجيل طلبات إجازة الموظفين ومراحل اعتمادها.

## الأعمدة

| العمود                     | النوع                     | الوصف                     | لماذا؟                                       |
| -------------------------- | ------------------------- | ------------------------- | -------------------------------------------- |
| `id`                       | `BIGINT UNSIGNED`         | المعرف                    | مفتاح أساسي                                  |
| `employee_id`              | `BIGINT UNSIGNED` FK      | طالب الإجازة              | ربط الطلب بالموظف                            |
| `approved_by`              | `BIGINT UNSIGNED NULL` FK | الموظف المعتمد            | يبقى فارغًا قبل الاعتماد                     |
| `leave_type`               | `ENUM`                    | نوع الإجازة               | يمنع أنواعًا غير معرفة                       |
| `start_date`               | `DATE`                    | بداية الإجازة             | تاريخ بدون وقت                               |
| `end_date`                 | `DATE`                    | نهاية الإجازة             | تاريخ بدون وقت                               |
| `reason`                   | `TEXT NULL`               | سبب الطلب                 | قد يكون طويلًا                               |
| `status`                   | `ENUM`                    | pending/approved/rejected | دورة الموافقة                                |
| `total_days`               | `UNSIGNED INTEGER`        | عدد الأيام                | يمنع القيم السالبة، ويجب منع الصفر في التحقق |
| `rejection_reason`         | `TEXT NULL`               | سبب الرفض                 | مطلوب منطقيًا عند الرفض                      |
| `created_at`, `updated_at` | `TIMESTAMP`               | التواريخ                  | التتبع                                       |

## مثال

```text
employee_id: 7
leave_type: sick
start_date: 2026-10-01
end_date: 2026-10-03
total_days: 3
status: pending
approved_by: null
```

## القيود والملاحظات

- `approved_by` يستخدم `nullOnDelete` حتى لا يختفي الطلب إذا حذف المعتمد.
- يجب أن يضمن التحقق في التطبيق أن `end_date >= start_date`.
- يوجد فهرس على الموظف والحالة، وفهرس على التاريخين.
- الإجازة `unpaid` موجودة لكنها لا تحتاج عادة إلى رصيد في `leave_balances`.
- منع تداخل إجازتين معتمدتين لنفس الموظف يجب تنفيذه في Form Request أو Service قبل الاعتماد.
- يجب التحقق أن `total_days` يطابق عدد الأيام حسب سياسة احتساب عطلة نهاية الأسبوع.

---

# Migration: leave_balances

## الغرض من الجدول

تخزين رصيد كل نوع إجازة لكل موظف ولكل سنة.

## الأعمدة

| العمود                     | النوع                | الوصف            | لماذا؟                         |
| -------------------------- | -------------------- | ---------------- | ------------------------------ |
| `id`                       | `BIGINT UNSIGNED`    | المعرف           | مفتاح أساسي                    |
| `employee_id`              | `BIGINT UNSIGNED` FK | الموظف           | صاحب الرصيد                    |
| `year`                     | `YEAR`               | سنة الرصيد       | فصل أرصدة السنوات              |
| `leave_type`               | `ENUM`               | نوع الرصيد       | مطابق لأنواع الإجازات المدفوعة |
| `allocated_days`           | `UNSIGNED INTEGER`   | الأيام المخصصة   | يمنع القيم السالبة             |
| `used_days`                | `UNSIGNED INTEGER`   | الأيام المستخدمة | يمنع القيم السالبة             |
| `created_at`, `updated_at` | `TIMESTAMP`          | التواريخ         | التتبع                         |

## مثال

```text
employee_id: 7
year: 2026
leave_type: earned
allocated_days: 21
used_days: 5
```

## ملاحظات

- القيد `unique(employee_id, year, leave_type)` يمنع تكرار رصيد النوع نفسه.
- أنواع هذا الجدول يجب أن تتطابق مع أنواع الإجازات التي لها رصيد. حاليًا `unpaid` مستبعد لأنه بلا رصيد.
- يجب على التطبيق منع `used_days` من تجاوز `allocated_days` عند اعتماد الطلب.

---

# Migration: salaries

## الغرض من الجدول

تخزين ملخص راتب الموظف الشهري ومكوناته.

## الأعمدة

| العمود                     | النوع                | الوصف                     | لماذا؟                                                |
| -------------------------- | -------------------- | ------------------------- | ----------------------------------------------------- |
| `id`                       | `BIGINT UNSIGNED`    | المعرف                    | مفتاح أساسي                                           |
| `employee_id`              | `BIGINT UNSIGNED` FK | الموظف                    | صاحب الراتب                                           |
| `year`                     | `YEAR`               | سنة الراتب                | جزء من فترة الراتب                                    |
| `month`                    | `UNSIGNED TINYINT`   | شهر الراتب                | صغير وموجب، ويجب التحقق من المجال 1 إلى 12 في التطبيق |
| `working_days`             | `UNSIGNED INTEGER`   | أيام العمل                | لا تقبل السالب                                        |
| `absent_days`              | `UNSIGNED INTEGER`   | أيام الغياب               | لا تقبل السالب                                        |
| `holiday_days`             | `UNSIGNED INTEGER`   | أيام العطل                | لا تقبل السالب                                        |
| `basic_amount`             | `DECIMAL(10,2)`      | الراتب الأساسي للشهر      | دقة مالية                                             |
| `deductions`               | `DECIMAL(10,2)`      | الخصومات                  | قيمة مالية غير سالبة                                  |
| `bonus`                    | `DECIMAL(10,2)`      | المكافآت                  | قيمة مالية غير سالبة                                  |
| `overtime_amount`          | `DECIMAL(10,2)`      | مبلغ الإضافي              | قيمة مالية غير سالبة                                  |
| `net_amount`               | `DECIMAL(10,2)`      | الصافي                    | الناتج النهائي                                        |
| `status`                   | `ENUM`               | pending/approved/rejected | حالة المعالجة                                         |
| `paid_at`                  | `TIMESTAMP NULL`     | وقت الدفع                 | فارغ قبل الدفع                                        |
| `created_at`, `updated_at` | `TIMESTAMP`          | التواريخ                  | التتبع                                                |

## مثال

```text
employee_id: 7
year: 2026
month: 9
basic_amount: 8500.00
deductions: 250.00
bonus: 500.00
overtime_amount: 300.00
net_amount: 9050.00
status: approved
paid_at: 2026-10-01 09:00:00
```

## القيود والملاحظات

- `unique(employee_id, year, month)` يمنع إنشاء راتبين لنفس الموظف والفترة.
- أنواع الأيام تمنع القيم السالبة، ويجب التحقق من مجال الشهر وقيم المبالغ في التطبيق.
- يفضل في منطق التطبيق فرض أن `paid_at` لا يمتلئ إلا عند حالة مدفوعة، وقد تحتاج إضافة `paid` إلى enum أو فصل حالة الدفع عن حالة الموافقة.
- يجب حساب `net_amount` في Service موثوق وعدم الاعتماد على قيمة يرسلها المستخدم.
- حذف الموظف يحذف سجلات الرواتب بسبب `cascade`؛ هذا غير مناسب للتدقيق المالي طويل الأجل إذا كان الحذف الفعلي مسموحًا.

---

# Migration: notifications

## الغرض من الجدول

تخزين إشعارات Laravel للمستخدمين، مثل اعتماد إجازة أو تنبيه انتهاء مستند.

## الأعمدة

| العمود                     | النوع              | الوصف           | لماذا؟                           |
| -------------------------- | ------------------ | --------------- | -------------------------------- |
| `id`                       | `UUID` مفتاح أساسي | معرف الإشعار    | تنسيق Laravel القياسي للإشعارات  |
| `type`                     | `VARCHAR`          | اسم نوع الإشعار | يحدد Notification class أو النوع |
| `notifiable_type`          | `VARCHAR`          | نوع المستلم     | يدعم User أو نموذجًا آخر         |
| `notifiable_id`            | `BIGINT UNSIGNED`  | معرف المستلم    | الجزء الرقمي لعلاقة polymorphic  |
| `data`                     | `TEXT`             | بيانات الإشعار  | غالبًا JSON serialized           |
| `read_at`                  | `TIMESTAMP NULL`   | وقت القراءة     | `NULL` يعني غير مقروء            |
| `created_at`, `updated_at` | `TIMESTAMP`        | التواريخ        | التتبع                           |

## مثال

```text
id: 550e8400-e29b-41d4-a716-446655440000
type: LeaveApprovedNotification
notifiable_type: App\\Models\\User
notifiable_id: 1
data: {"leave_id": 15, "message": "تم اعتماد الإجازة"}
read_at: null
```

## ملاحظات

- `morphs('notifiable')` ينشئ العلاقة والفهرس المركب.
- يجب أن يكون `data` متوافقًا مع شكل البيانات الذي يقرأه Notification class.
- لا يوجد حقل `read`؛ يتم تحديد القراءة من خلال `read_at`.

---

# ملاحظات تصميم عامة

1. الجداول الأساسية مناسبة لنظام شركة واحدة، ويمكن لاحقًا إضافة `company_id` إلى الجداول التشغيلية عند التحول إلى SaaS.
2. يجب إنشاء Models وعلاقات Eloquent مقابلة للجداول HR، مع casts للـ JSON والتواريخ والأرقام المنطقية.
3. يجب تنفيذ التحقق التشغيلي في Form Requests وServices، خصوصًا تداخل الإجازات، مطابقة الراتب، وعدم تجاوز الرصيد.
4. إذا كانت migrations قد نُفذت سابقًا، يجب إنشاء migrations تعديل جديدة بدل تعديل migrations القديمة في بيئة الإنتاج.
5. يجب اختبار migrations على MySQL الفعلي، خصوصًا أعمدة JSON، مع تنفيذ قواعد الأعمال في Form Requests وServices.
