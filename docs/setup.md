# دليل التشغيل والإعداد

## المتطلبات

- PHP 8.3 أو أحدث ضمن المتطلبات المحددة في المشروع.
- Composer.
- MySQL يعمل على الجهاز أو خادم متاح.
- Node.js وnpm اختياريان عند الحاجة إلى بناء أصول Vite.
- Git اختياري لإدارة الإصدارات.

## التثبيت الأولي

من جذر المشروع:

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

على Linux/macOS:

```bash
composer install
test -f .env || cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## إعداد قاعدة البيانات

عدّل `.env` حسب MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_hr_management
DB_USERNAME=root
DB_PASSWORD=
```

يجب إنشاء قاعدة البيانات قبل تشغيل `migrate` إذا لم تكن موجودة.

تحقق من نوع الاتصال المحمّل دون عرض بيانات الاعتماد:

```bash
php artisan config:show database.default
```

## التشغيل المحلي

```bash
php artisan serve
```

العنوان الافتراضي:

```text
http://127.0.0.1:8000
```

للتطوير الكامل مع queue وVite:

```bash
composer run dev
```

## أوامر Laravel المهمة

```bash
php artisan route:list
php artisan migrate:status
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
php artisan db:seed
php artisan db:seed --class=OrganizationPermissionSeeder
php artisan config:clear
php artisan cache:clear
php artisan test --compact
vendor/bin/pint --dirty --format agent
```

`migrate:re` ليس أمرًا كاملًا؛ استخدم `migrate:refresh` أو `migrate:reset`.

`HrSeeder` ينشئ أقسامًا ووظائف وموظفين تجريبيين في كل تشغيل. شغّل `db:seed` مرة واحدة على قاعدة تطوير جديدة فقط؛ لإضافة دور مدير القسم إلى قاعدة موجودة استخدم `OrganizationPermissionSeeder` وحده.

## إعادة بناء قاعدة البيانات محليًا

هذا يحذف الجداول ويعيد إنشاءها، لذلك لا يستخدم على الإنتاج:

```bash
php artisan migrate:fresh --seed
```

هذا الأمر يحذف كل البيانات، ولا يستخدم إلا على قاعدة محلية مؤقتة.

## بيانات الدخول المحلية

بعد `db:seed`:

```text
Email: admin@example.com
Password: password
```

هذه بيانات محلية تجريبية وليست بيانات إنتاج.

## مشاكل شائعة

### MySQL connection refused

تحقق من أن MySQL يعمل، وأن `DB_HOST` و`DB_PORT` واسم قاعدة البيانات صحيحون.

### JSON default error

حقل `settings.weekend_days` لا يملك default في قاعدة البيانات بسبب توافق MySQL. يجب تمرير قيمة مثل `["Friday", "Saturday"]` عند إنشاء الإعداد.

### Migration غير مكتشفة

لا تنقل ملفات migration إلى مجلد جديد دون تسجيل المسار في `AppServiceProvider::boot()` باستخدام `loadMigrationsFrom`.

### Vite manifest error

شغّل:

```bash
npm run build
```

أو أثناء التطوير:

```bash
npm run dev
```

## التشغيل الإنتاجي

- استخدم `APP_ENV=production` و`APP_DEBUG=false` وHTTPS، وغيّر كلمة مرور Seeder.
- اضبط `SANCTUM_EXPIRATION` بالدقائق؛ القيمة الافتراضية 10080 (7 أيام).
- شغّل Laravel Scheduler كل دقيقة لتنظيف الرموز المنتهية.
- عيّن نسخًا احتياطيًا لقاعدة البيانات وسياسة احتفاظ بملفات المستندات.
