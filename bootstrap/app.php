<?php

use App\Http\Middleware\SetApiLocale;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [SetApiLocale::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (UniqueConstraintViolationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'تعارضت البيانات مع سجل موجود بالفعل. راجع القيم وأعد المحاولة.',
                ], 409);
            }
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'يجب تسجيل الدخول للوصول إلى هذا المسار.',
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'لا تملك الصلاحية المطلوبة لتنفيذ هذا الطلب.',
                ], 403);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = $exception->getStatusCode();
            $message = match ($status) {
                403 => 'لا تملك الصلاحية المطلوبة لتنفيذ هذا الطلب.',
                404 => 'المورد غير موجود أو خارج نطاق صلاحيتك.',
                405 => 'طريقة الطلب غير مدعومة لهذا المسار.',
                419 => 'انتهت صلاحية الطلب. أعد المحاولة.',
                429 => 'تجاوزت عدد الطلبات المسموح. يرجى المحاولة لاحقًا.',
                default => null,
            };

            if ($message === null) {
                return null;
            }

            return response()->json(['message' => $message], $status, $exception->getHeaders());
        });
    })->create();
