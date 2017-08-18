<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Closure;
use Illuminate\Support\Facades\DB;

class CouponPlanMiddleware
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
		return $next($request);
	}

	public function terminate ($request, $response)
	{

		DB::disableQueryLog(); // 禁掉sql缓存
		// 获得响应的原始数据
		$content = $response->original;
		$content = json_decode($content, true);
		// 获得一次性session中的发放计划id
		$id = $request->session()->get('plan_id', '');
		if ($content['code'] == 0 && $id) {
			$app_id = AppUtils::getAppID();

			// 查发放记录
			$plan   = DB::connection('mysql')->table('t_coupon_plan')
				->where('app_id', $app_id)->where('id', $id)->first();
			$coupon = DB::connection('mysql')->table('t_coupon')->where('app_id', $app_id)->where('id', $plan->cou_id)->first();

			// 查用户订购记录 并进行批量插入
			// 一次 1000
			$info = DB::connection('mysql')->table('t_purchase')
				->select('user_id')
				->where('app_id', $app_id)
				->where('is_deleted', 0)
				->where('payment_type', 3)
				->where('product_id', $plan->resource_id)
				->where('expire_at', '>', $plan->created_at)
				->where('created_at', '<', $plan->created_at)
				->orderBy('created_at')
				->chunk(1000, function($result) use ($app_id, $plan, $coupon) {
					$data = [];
					foreach ($result as $v) {
						$item = [
							'id'         => uniqid("cu_", false) . '-' . Utils::generateRandomCode(6, 'ALL'),
							'app_id'     => $app_id,
							'cou_id'     => $plan->cou_id,
							'user_id'    => $v->user_id,
							'get_type'   => 2,
							'receive_at' => date('Y-m-d H:i:s'),
							'invalid_at' => $coupon->invalid_at,
							'has_prompt' => 1,
							'created_at' => date('Y-m-d H:i:s'),
						];
						dump($item['id']);

						$data[] = $item;
					}
					exit;
					$insert = DB::connection('mysql')->table('t_coupon_user')->insert($data);
					//                    Utils::logFrom("plan{$i}:" . time(),"coupon.log");
					//                    Utils::logFrom("cplan{$i}:" . memory_get_usage()/1024/1024,"coupon.log");
					if (!$insert) return false;  // 中断闭包
				});

			if ($info) {
				DB::connection('mysql')->table('t_coupon')->where('app_id', $app_id)->where('id', $plan->cou_id)->increment('has_received', $plan->count);
				DB::connection('mysql')->table('t_coupon_plan')
					->where('app_id', $app_id)->where('id', $id)->update([
						'send_state' => 1,
						'send_at'    => date('Y-m-d H:i:s'),
					]);
			}
		}

	}
}
