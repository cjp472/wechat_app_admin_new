<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 09/08/2017
 * Time: 11:48
 */

namespace App\Http\Controllers\Tools;

use Illuminate\Support\Facades\DB;

class UserUtils
{
	const MD5_EXPIRED_IMAGE = [
		"fee9458c29cdccf10af7ec01155dc7f0",
		//        "d41d8cd98f00b204e9800998ecf8427e"
	];   //过期图片的md5值

	/**
	 * 获取需要更新用户图像的用户列表
	 *
	 * @param     $start_time
	 * @param     $end_time
	 * @param int $offset
	 *
	 * @return
	 */
	public static function getOldWxAvatarUsers ($start_time, $end_time, $offset = 0)
	{
		Utils::logFrom($start_time . "~" . $end_time . " " . $offset, "UpdateUserWxAvatar.log");
		$userList = DB::select("
SELECT app_id, user_id, wx_avatar, wx_open_id, collection_open_id from t_users
where wx_avatar REGEXP 'wx.qlogo.cn' and need_update_avatar = 1 and created_at >= ? and created_at < ?
 ORDER BY created_at limit 10000 offset ?
", [$start_time, $end_time, $offset * 10000]);

		return $userList;
	}

	/**
	 * 更新数据库未过期用户图像
	 *
	 * @param $user_list
	 */
	public static function updateDbUserAvatar ($user_list)
	{
		//		$temp_list = [];
		foreach ($user_list as $item) {
			$app_id  = $item->app_id;
			$user_id = $item->user_id;

			//			Utils::logFrom("start updateDbUserAvatar app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");

			//1.获取数据库中用户图像
			$wx_avatar = $item->wx_avatar;
			//2.下载用户头像
			$imageData = self::curlDownloadImage($wx_avatar, $md5);
			//			Utils::logFrom("1 updateDbUserAvatar app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");

			//3.校验用户头像md5值是否已过期
			//            $fileSize = filesize($src_image);
			//4.如果过期,需要去微信重新拉用户头像信息,存入过期用户头像队列,继续验证下一个用户
			if (in_array($md5, UserUtils::MD5_EXPIRED_IMAGE)) {
				DB::table('t_users')
					->where('app_id', '=', $app_id)
					->where('user_id', '=', $user_id)
					->update([
						'need_update_avatar' => 2,
					]);
				//				$temp_list[ $app_id ][] = $item;
				//				Utils::logFrom("expired updateDbUserAvatar app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");
				continue;
			}
			//            Utils::logFrom("fileSize=".$fileSize, "UpdateUserWxAvatar.log");
			//5.压缩用户头像
			//			Utils::logFrom("2 updateDbUserAvatar app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");
			$dst_image = Utils::imageCompressRemote($imageData, $user_id, 120, 120);
			//			Utils::logFrom("3 updateDbUserAvatar app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");
			if (empty($dst_image)) {
				DB::table('t_users')
					->where('app_id', '=', $app_id)
					->where('user_id', '=', $user_id)
					->update([
						'need_update_avatar' => 2,
					]);
				//				$temp_list[ $app_id ][] = $item;
				//				Utils::logFrom("null updateDbUserAvatar app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");
				continue;
			}
			//6.上传用户头像
			$file_url = Utils::uploadCompressImage($dst_image, $app_id);

			//7.更新数据库中用户头像
			$result = DB::table('t_users')
				->where('app_id', '=', $app_id)
				->where('user_id', '=', $user_id)
				->update([
					'need_update_avatar' => 0,
					'wx_avatar'          => $file_url,
				]);
			if ($result) {
				Utils::logFrom("updateDbUserAvatar SUCCESS app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");
			} else {
				Utils::logFrom("updateDbUserAvatar FAILED app_id=" . $app_id . " user_id=" . $user_id, "UpdateUserWxAvatar.log");
			}

			//            if (is_file($src_image)) @unlink($src_image);
			if (is_file($dst_image)) @unlink($dst_image);
		}
		//		$expire_list = $temp_list;
	}

	/**
	 * 用curl下载图片,md5值不会发生改变
	 *
	 * @param $src_image
	 * @param $user_id
	 * @param $md5
	 *
	 * @return mixed
	 */
	public static function curlDownloadImage ($src_image, &$md5)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $src_image);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$imageData = curl_exec($ch);
		curl_close($ch);

		$md5 = md5($imageData);

		return $imageData;
	}

	/**
	 * 更新过期头像为微信新拉去图像
	 *
	 * @param $expire_list
	 */
	public static function updateWxUserAvatar ($expire_list)
	{
		foreach ($expire_list as $key => $value) {
			$app_id = $key;
			//1.按app_id分组 批量获取用户信息包括头像

			$collection_conf = AppUtils::getCollectionToken();
			$wx_access_token = $collection_conf->value;

			$wx_url    = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=" . $wx_access_token;
			$open_list = [];
			foreach ($value as $value_item) {
				$openid      = $value_item->collection_open_id;
				$open_list[] = [
					'openid' => $openid,
					'lang'   => 'zh-CN',
				];
			}
			$userBatch = array_chunk($open_list, 100);
			foreach ($userBatch as $item) {
				$post_data  = json_encode(['user_list' => $item]);
				$wx_result  = Utils::send_post($wx_url, $post_data);//调用微信接口
				$content    = Utils::filterUtf8($wx_result);
				$wx_results = json_decode($content, true);
				if ($wx_results && is_array($wx_results) && !array_key_exists('errcode', $wx_results)) {
					$wx_user_list = $wx_results['user_info_list']; //得到微信返回的用户信息的数组
					if ($wx_user_list && count($wx_user_list) > 0) {
						//                        Utils::logFrom($wx_user_list, "UpdateUserWxAvatar.log");
						foreach ($wx_user_list as $wx_item) {
							//                            Utils::logFrom($wx_item, "UpdateUserWxAvatar.log");

							if ($wx_item['subscribe'] === 0) {
								$result = DB::table('t_users')
									->where('app_id', '=', $app_id)
									->where('collection_open_id', '=', $wx_item['openid'])
									->limit(1)
									->update([
										'need_update_avatar' => 0,
									]);
								Utils::logFrom("subscribe 0 updateWxUserAvatar app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
								continue;
							}
							$headimgurl = $wx_item['headimgurl'];
							//2.下载用户头像
							$imageData = self::curlDownloadImage($headimgurl, $md5);
							//3.压缩用户头像
							$dst_image = Utils::imageCompressRemote($imageData, $wx_item['openid'], 120, 120);
							if (empty($dst_image)) {
								$result = DB::table('t_users')
									->where('app_id', '=', $app_id)
									->where('collection_open_id', '=', $wx_item['openid'])
									->limit(1)
									->update([
										'need_update_avatar' => 0,
									]);
								Utils::logFrom("image null updateWxUserAvatar app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
								continue;
							}
							//4.上传用户头像
							$file_url = Utils::uploadCompressImage($dst_image, $app_id);
							//5.更新数据库中用户头像
							$result = DB::table('t_users')
								->where('app_id', '=', $app_id)
								->where('collection_open_id', '=', $wx_item['openid'])
								->limit(1)
								->update([
									'need_update_avatar' => 0,
									'wx_avatar'          => $file_url,
								]);

							if ($result) {
								Utils::logFrom("updateWxUserAvatar SUCCESS app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
							} else {
								Utils::logFrom("updateWxUserAvatar FAILED app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
							}

							//                            if (is_file($src_image)) @unlink($src_image);
							if (is_file($dst_image)) @unlink($dst_image);
						}
					}
				} else {
					Utils::logFrom($app_id, "UpdateUserWxAvatar.log");
					Utils::logFrom($wx_results, "UpdateUserWxAvatar.log");
				}
			}
		}
	}

	/**
	 * 更新过期头像为微信新拉去图像
	 *
	 * @param $expire_list
	 */
	public static function updateWxUserAvatarSingle ($expire_list)
	{

		$collection_conf    = AppUtils::getCollectionToken();
		$wx_access_token_co = $collection_conf->value;

		foreach ($expire_list as $key => $value) {
			$app_id = $key;
			//1.按app_id分组 批量获取用户信息包括头像
			$wx_access_token = self::getAppToken($app_id, $use_collection);
			if (empty($wx_access_token)) {
				$result = DB::table('t_users')
					->where('app_id', '=', $app_id)
					->update([
						'need_update_avatar' => 0,
					]);
				Utils::logFrom("token null updateWxUserAvatar app_id=" . $app_id, "UpdateUserWxAvatar.log");
				continue;
			}

			foreach ($value as $value_item) {
				$use_collection  = !empty($value_item->collection_open_id) ? 1 : 0;
				$openid          = !empty($value_item->collection_open_id) ? $value_item->collection_open_id : $value_item->wx_open_id;
				$wx_access_token = !empty($value_item->collection_open_id) ? $wx_access_token_co : $wx_access_token;

				$wx_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $wx_access_token
					. "&openid=" . $openid . "&lang=zh_CN ";

				$wx_results = self::curl_get_json($wx_url);
				//				$content    = Utils::filterUtf8($wx_result);
				if ($wx_results && is_array($wx_results) && !array_key_exists('errcode', $wx_results)) {
					$wx_item = $wx_results;
					if ($wx_item['subscribe'] === 0) {
						$result = DB::table('t_users')
							->where('app_id', '=', $app_id)
							->where('collection_open_id', '=', $wx_item['openid'])
							->limit(1)
							->update([
								'need_update_avatar' => 0,
							]);
						Utils::logFrom("subscribe 0 updateWxUserAvatar app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
						continue;
					}
					$headimgurl = $wx_item['headimgurl'];
					//2.下载用户头像
					$imageData = self::curlDownloadImage($headimgurl, $md5);
					//3.压缩用户头像
					$dst_image = Utils::imageCompressRemote($imageData, $wx_item['openid'], 120, 120);
					if (empty($dst_image)) {
						$result = DB::table('t_users')
							->where('app_id', '=', $app_id)
							->where('collection_open_id', '=', $wx_item['openid'])
							->limit(1)
							->update([
								'need_update_avatar' => 0,
							]);
						Utils::logFrom("image null updateWxUserAvatar app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
						continue;
					}
					//4.上传用户头像
					$file_url = Utils::uploadCompressImage($dst_image, $app_id);
					//5.更新数据库中用户头像
					$result = DB::table('t_users')
						->where('app_id', '=', $app_id)
						->where('collection_open_id', '=', $wx_item['openid'])
						->limit(1)
						->update([
							'need_update_avatar' => 0,
							'wx_avatar'          => $file_url,
						]);

					if ($result) {
						Utils::logFrom("updateWxUserAvatar SUCCESS app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
					} else {
						Utils::logFrom("updateWxUserAvatar FAILED app_id=" . $app_id . " openid=" . $wx_item['openid'], "UpdateUserWxAvatar.log");
					}

					//                            if (is_file($src_image)) @unlink($src_image);
					if (is_file($dst_image)) @unlink($dst_image);
				} else {
					Utils::logFrom($app_id, "UpdateUserWxAvatar.log");
					Utils::logFrom($wx_results, "UpdateUserWxAvatar.log");
				}
			}
		}
	}

	public static function curl_get_json ($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		return json_decode($output, true);
	}

	/**
	 * 根据app_i获取token
	 *
	 * @param $app_id
	 * @param $use_collection
	 *
	 * @return mixed
	 */
	public static function getAppToken ($app_id, &$use_collection)
	{
		$app_conf = AppUtils::getAppConfInfo($app_id);
		if ($app_conf && count($app_conf) > 0) {
			$use_collection = $app_conf->use_collection;
			if ($use_collection == 0) {
				$wx_access_token = $app_conf->wx_access_token;
				if (!self::isValidByTime($app_conf->wx_access_token_refresh_at)) {
					// $accessToken过期了,去刷新下
					$remote_url = env('BUZ_HOST') . '/require/refresh_access_token/' . $app_id;
					$curl       = Utils::curl_file_post_contents($remote_url);
					if ($curl) {
						$appInfo         = AppUtils::getAppConfInfo($app_id);
						$wx_access_token = $appInfo->wx_access_token;
					}
				}
			} else {
				$collection_conf = AppUtils::getCollectionToken();
				$wx_access_token = $collection_conf->value;
				if (!self::isValidByTime($collection_conf->token_time)) {
					$remote_url = env('BUZ_HOST') . '/require/refresh_collection_access_token';
					$curl       = Utils::curl_file_post_contents($remote_url);
					if ($curl) {
						$collection_conf = AppUtils::getCollectionToken();
						$wx_access_token = $collection_conf->value;
					}
				}
			}

			return $wx_access_token;
		} else {
			return null;
		}
	}

	/** 判断是否过期
	 *
	 * @param     $updatedAt
	 * @param int $seconds
	 *
	 * @return bool
	 */
	public static function isValidByTime ($updatedAt, $seconds = 6000)
	{
		if (Utils::isValidTimeInDB($updatedAt) && Utils::getTimestamp($updatedAt) + $seconds > time()) {
			return true;
		} else {
			return false;
		}
	}
}