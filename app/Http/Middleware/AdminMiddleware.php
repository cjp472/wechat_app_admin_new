<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\LogUtils;
use Closure;
use Illuminate\Support\Facades\DB;
use Route;
use Session;

class AdminMiddleware
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

		$open_id = AppUtils::getOpenId();
		if (empty($open_id)) {
			AppUtils::clearLoginStatus();

			return redirect()->guest('/login');
		} else {
			$loginResult = DB::select("select login_id from db_ex_config.t_mgr_login where openid = '$open_id'");
			if (empty($loginResult) || count($loginResult) == 0) {
				AppUtils::clearLoginStatus();

				return redirect()->guest('/login');
			}
		}

		/**
		 * 访问日志
		 */
		LogUtils::insertOperateLog($request);

		/**
		 * 权限
		 */
		$access = Session::get('access', []);
		// 没有拿到权限，跳转login
		if (!$access || count($access) < 10) return redirect('/login');

		$name = Route::currentRouteName();
		if ($name) {
			$permission = explode('.', $name);
		} else {
			$permission = [];
		}

		if ($permission && count($permission) > 0) {
			$key = $permission[0];
			if ($key && $access[ $key ] < 1) return redirect()->back()->with('accessError', '您的账号没有相关操作权限，如有疑问请联系店铺管理员');

			if (count($permission) > 1) {
				if ($permission[1]) $key = $permission[1];
				//                if ($key && $access[$key] < 1) return redirect();
				if ($key && $access[ $key ] < 1) return redirect()->back()->with('accessError', '您的账号没有相关操作权限，如有疑问请联系店铺管理员');
			}

		}

		return $next($request);
	}
}
