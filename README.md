# HR Management System

واجهة API لنظام إدارة موارد بشرية مبنية على Laravel 13 وPHP 8.3 وMySQL، ومخصصة حاليًا لشركة واحدة.

## التوثيق

ابدأ من [دليل التوثيق الكامل](docs/README.md)، ويشمل:

- التشغيل والإعداد.
- المعمارية وبنية المجلدات.
- API والمصادقة عبر Sanctum.
- قاعدة البيانات والـ migrations.
- Factories وSeeders.
- الأمان وقواعد التطوير.
- [مرجع API المختصر](docs/api-reference.md).
- [حالة المشروع والفجوات المعروفة](docs/project-status.md).
- [مجموعة Postman](docs/postman/HR-Management.postman_collection.json).

## الحالة الحالية

- API JSON محمي بـ `auth:sanctum`.
- Repository Pattern مع `BaseRepository` وInterfaces خاصة.
- Form Requests للتحقق ورسائل الأخطاء.
- API Resources لتوحيد JSON responses.
- دعم الموظفين النهاريين والليليين والحضور العابر لمنتصف الليل.

## تشغيل محلي سريع

```bash
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

أنشئ قاعدة MySQL المحددة في `.env` قبل تشغيل migrations. لا تعِد تشغيل `db:seed` على قاعدة مستخدمة؛ Seeder الموارد يضيف بيانات تجريبية جديدة عند كل تشغيل.

بيانات الدخول المحلية بعد Seeder:

```text
admin@example.com / password
```

> لا تستخدم بيانات الدخول هذه في الإنتاج.

عنوان API المحلي: `http://127.0.0.1:8000/api`. ابدأ بـ `POST /auth/login` ثم أرسل الرمز في `Authorization: Bearer <token>`. راجع [دليل API](docs/api.md) لطلبات Postman.

## الاختبارات والتنسيق

```bash
php artisan test --compact
vendor/bin/pint --dirty --format agent
```

---

## Laravel Base Information

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
