<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\LogUtils;
use App\Http\Controllers\Tools\SuperUtils;
use Closure;

class SuperAdminMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle ($request, Closure $next)
	{
		if (!SuperUtils::checkIsAdmin(AppUtils::getSuperOpenId())) {
			abort(404);
		}

		LogUtils::insertOperateLog($request);

		return $next($request);
	}
}
