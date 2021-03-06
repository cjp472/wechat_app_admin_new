<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Tools\ImageUtils;
use Closure;

/**
 * 图片压缩中间件
 * Class ImageDealMiddleware
 * @package App\Http\Middleware
 */
class ImageDealMiddleware
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

	/**
	 * request->image_list 图片列表
	 *
	 * @param $request
	 * @param $response
	 */
	public function terminate ($request, $response)
	{
		$img_list = $request->image_list;

		if (!empty($img_list) && is_array($img_list) && count($img_list) > 0) {
			foreach ($img_list as $item) {
				$imageObj = $item;

				if (!empty($imageObj) && is_object($imageObj)) {
					$table_name     = $imageObj->table_name;
					$app_id         = $imageObj->app_id;
					$id             = $imageObj->id;
					$image_url      = $imageObj->image_url;
					$compress_field = $imageObj->compress_field;
					$image_width    = $imageObj->image_width;
					$image_height   = $imageObj->image_height;
					$image_quality  = $imageObj->image_quality;
					$query_id       = $imageObj->query_id;

					if (empty($query_id)) {
						$query_id = 'id';
					}

					ImageUtils::SingleImageCompress($table_name, $app_id, $id, $image_url, $compress_field, $image_width,
						$image_height, $image_quality, $query_id);
				}
			}
		}
	}
}
