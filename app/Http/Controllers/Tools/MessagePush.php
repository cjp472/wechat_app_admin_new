<?php
/**
 * Created by PhpStorm.
 * User: marcus
 * Date: 2017/03/15
 * Time: 16:05
 */

namespace App\Http\Controllers\Tools;

use DB;

class MessagePush
{

	//用户服务号所属行业设置（教育/培训）（获得模板id）
	public static function getIndustry ($app_id)
	{
		$url = env("TEMPLATE_ID") . $app_id;
		@$result = file_get_contents($url);

		$result = json_decode($result);
		if ($result) {
			if ($result->code === 0) {
				return json_encode(['ret' => 0]);
			} else {
				return json_encode(['ret' => -1]);
			}
		} else {
			return json_encode(['ret' => -1]);
		}
	}

	//用户服务号所属行业设置（教育/培训）（获得模板id）

	/**
	 * 判断是否已经设置过模板,未设置进行添加
	 *
	 * @param $app_id
	 *
	 * @return int
	 */
	public static function isHadSetTemp ($app_id)
	{
		$url = env("TEMPLATE_ID") . $app_id;
		@$result = file_get_contents($url);
		$result = json_decode($result);
		if ($result) {
			if ($result->code === 0) {
				return 0;
			} else {
				return 1;
			}
		} else {
			return 1;
		}
	}

	/**
	 * 检测用户是否开启消息通知
	 *
	 * @param $app_id
	 *
	 * @return bool
	 */
	public static function checkMessagePush ($app_id)
	{
		$use_collection = AppUtils::getCollection();
		if ($use_collection === 1) {
			// 个人模式查询是否开启模板消息推送
			$is_person_message_push = DB::connection('mysql_config')->table('t_app_module')->where('app_id', $app_id)->value('is_person_message_push');
			if (!$is_person_message_push) return false;
		}

		return true;
	}
}