<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	/**
	 * The application's global HTTP middleware stack.
	 * These middleware are run during every request to your application.
	 * @var array
	 */
	protected $middleware = [
		\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
		\App\Http\Middleware\EncryptCookies::class,
		\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
		\Illuminate\Session\Middleware\StartSession::class,
		\Illuminate\View\Middleware\ShareErrorsFromSession::class,
	];

	/**
	 * The application's route middleware groups.
	 * @var array
	 */
	protected $middlewareGroups = [
		'web' => [
			\App\Http\Middleware\EncryptCookies::class,
			\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
			\Illuminate\Session\Middleware\StartSession::class,
			\Illuminate\View\Middleware\ShareErrorsFromSession::class,
			\App\Http\Middleware\VerifyCsrfToken::class,
		],

		'api'         => [
			'throttle:60,1',
		],
		'wechatPage'  => [
			\Overtrue\LaravelWechat\Middleware\OAuthAuthenticate::class,//微信登录认证

		],
		'super_admin' => [
			\App\Http\Middleware\SuperAdminMiddleware::class,//超级管理员认证

		],
		'ImageDeal'   => [         //图片处理
			\App\Http\Middleware\ImageDealMiddleware::class,    //图片处理
		],
		'AudioDeal'   => [         //音频处理
			\App\Http\Middleware\AudioDealMiddleware::class,    //音频处理
		],
	];

	/**
	 *定义admin中间件和菜单栏中间件
	 */
	protected $routeMiddleware =
		[
			//        'wechatPage' => \Overtrue\LaravelWechat\Middleware\OAuthAuthenticate::class,
			'auth'       => \App\Http\Middleware\Authenticate::class,
			'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
			'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
			'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,

			'admin'         => \App\Http\Middleware\AdminMiddleware::class,
			'APIMiddleware' => \App\Http\Middleware\APIMiddleware::class,
		];
}
