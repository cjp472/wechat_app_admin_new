<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Tools\AudioUtils;
use App\Http\Controllers\Tools\Utils;
use Closure;

/**
 * 音频压缩中间件
 * Class ImageDealMiddleware
 * @package App\Http\Middleware
 */
class AudioDealMiddleware
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
	 * request->audio_list 音频列表
	 *
	 * @param $request
	 * @param $response
	 */
	public function terminate ($request, $response)
	{
		include "../vendor/getid3/getid3.php";

		$audio_list = $request->audio_list;
		if (!empty($audio_list) && is_array($audio_list) && count($audio_list) > 0) {
			Utils::logFrom("audio count=" . count($audio_list), "AudioDealMiddleware.log");
			foreach ($audio_list as $item) {
				$audioObj = $item;

				if (!empty($audioObj) && is_object($audioObj)) {
					$table_name   = $audioObj->table_name;
					$app_id       = $audioObj->app_id;
					$id           = $audioObj->id;
					$audio_url    = $audioObj->audio_url;
					$audio_length = $audioObj->audio_length;
					Utils::logFrom($table_name . " " . $app_id . " " . $id, "AudioDealMiddleware.log");

					AudioUtils::SingleAudioCompress($table_name, $app_id, $id, $audio_url, $audio_length);
				}
			}
		}
	}
}
