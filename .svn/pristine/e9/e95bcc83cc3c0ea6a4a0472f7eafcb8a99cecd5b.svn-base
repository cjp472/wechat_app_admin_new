<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 14/06/2017
 * Time: 20:22
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class QCloudController extends Controller
{
	const EVENT_TYPE_TRANS = 'TranscodeComplete';  //转码完成

	/***
	 * 腾讯云视频上传回调方法
	 */
	public function qCloudCallback ()
	{
		$params = Input::all();

		if (empty($params) || count($params) == 0) {
			return;
		}

		$file_id = $params['data']['fileId'];
		//判断视频属于视频还是直播
		$table_name = $this->isVideoOrAlive($file_id);
		if ($table_name == 'other' || empty($file_id)) {
			$this->insertMiddleVideo($file_id, $params);

			return;
		}

		if ($table_name == 't_video') {
			$this->videoTransCode($file_id, $params, $table_name);
		} else if ($table_name == 't_alive') {
			$this->aliveTransCode($file_id, $params, $table_name);
		}

	}

	/**
	 * 根据file_id判断是否视频或直播
	 *
	 * @param $file_id
	 *
	 * @return string
	 */
	function isVideoOrAlive ($file_id)
	{
		$videoInfo = $this->findVideoByFileId($file_id);
		if ($videoInfo) {
			return 't_video';
		}
		$aliveInfo = $this->findAliveByFileId($file_id);
		if ($aliveInfo) {
			return 't_alive';
		}

		return 'other';
	}

	/**
	 * 通过fileId查找视频信息
	 *
	 * @param $file_id
	 *
	 * @return
	 */
	function findVideoByFileId ($file_id)
	{
		$result = DB::table('t_video')
			->select('app_id', 'id', 'file_id')
			->where('file_id', $file_id)
			->first();

		return $result;
	}

	/**
	 * 通过fileID查找直播信息
	 *
	 * @param $file_id
	 *
	 * @return
	 */
	function findAliveByFileId ($file_id)
	{
		$result = DB::table('t_alive')
			->select('app_id', 'id', 'file_id')
			->where('file_id', $file_id)
			->first();

		return $result;
	}

	/**
	 * 插入中间表视频
	 *
	 * @param $file_id
	 * @param $source_type 0视频;1-直播
	 * @param $params
	 */
	function insertMiddleVideo ($file_id, $params, $source_type = 0)
	{
		$video_length = $params['data']['duration'];

		$data                 = [];
		$data['video_length'] = $video_length;
		$playSet              = $params['data']['playSet'];
		foreach ($playSet as $item) {
			$url        = $item['url'];
			$definition = $item['definition'];
			$vbitrate   = $item['vbitrate'];

			$videoSize = $vbitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024
			if ($definition === 0) {    //原视频
				$data['video_url']  = $url;
				$data['video_size'] = $videoSize;
			} else if ($definition === 20) {    //标清MP4
				$data['video_mp4']          = $url;
				$data['video_mp4_size']     = $videoSize;
				$data['video_mp4_vbitrate'] = $videoSize;
			} else if ($definition === 30) {    //高清MP4
				$data['video_mp4_high']          = $url;
				$data['video_mp4_high_size']     = $videoSize;
				$data['video_mp4_high_vbitrate'] = $videoSize;
			} else if ($definition === 230) {   //高清M3u8
				$data['video_hls'] = $url;
				$data['m3u8url']   = $url;
			}
		}
		$insertData                = $data;
		$insertData['file_id']     = $file_id;
		$insertData['source_type'] = $source_type;
		Utils::insertRecordVideoTranscode($insertData);
	}

	/**
	 * 视频转码处理
	 *
	 * @param $file_id
	 * @param $params
	 * @param $table_name
	 */
	function videoTransCode ($file_id, $params, $table_name)
	{

		if ($params['version'] == '4.0'
			&& $params['eventType'] == QCloudController::EVENT_TYPE_TRANS) {

			$status = $params['data']['status'];    //0成功;其他失败
			if ($status != 0) { //转码失败
				$updateResult = DB::table($table_name)
					->where('file_id', '=', $file_id)
					->where('video_state', '!=', 2)
					->limit(1)
					->update([
						'video_state'  => 1,
						'is_transcode' => 2,
					]);

				return;
			}
			$this->insertMiddleVideo($file_id, $params, 0);

			$video_length = $params['data']['duration'];

			$data                 = [];
			$data['video_length'] = $video_length;
			$playSet              = $params['data']['playSet'];
			foreach ($playSet as $item) {
				$url        = $item['url'];
				$definition = $item['definition'];
				$vbitrate   = $item['vbitrate'];

				$videoSize = $vbitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024
				if ($definition === 0) {    //原视频
					$data['video_url']  = $url;
					$data['video_size'] = $videoSize;
				} else if ($definition === 20) {    //标清MP4
					$data['video_mp4']          = $url;
					$data['video_mp4_size']     = $videoSize;
					$data['video_mp4_vbitrate'] = $videoSize;
				} else if ($definition === 30) {    //高清MP4
					$data['video_mp4_high']          = $url;
					$data['video_mp4_high_size']     = $videoSize;
					$data['video_mp4_high_vbitrate'] = $videoSize;
				} else if ($definition === 230) {   //高清M3u8
					$data['video_hls'] = $url;
				}
			}

			$data['video_state']  = 0;
			$data['is_transcode'] = 1;

			$updateResult = DB::table($table_name)
				->where('file_id', '=', $file_id)
				->where('video_state', '!=', 2)
				->limit(1)
				->update($data);

			//成功后更新专栏
			$videoInfo = DB::table($table_name)
				->select('app_id', 'payment_type', 'product_id')
				->where('file_id', '=', $file_id)
				->first();
			if ($videoInfo && $videoInfo->payment_type == 3) {
				$update = DB::update('
update t_pay_products set resource_count = resource_count + 1 where app_id = ? and id = ? limit 1
', [$videoInfo->app_id, $videoInfo->product_id]);
			}

		} else {
			return;
		}
	}

	/**
	 * 直播转码处理
	 *
	 * @param $file_id
	 * @param $params
	 * @param $table_name
	 */
	function aliveTransCode ($file_id, $params, $table_name)
	{

		if ($params['version'] == '4.0'
			&& $params['eventType'] == QCloudController::EVENT_TYPE_TRANS) {
			$status = $params['data']['status'];    //0成功;其他失败

			if ($status != 0) {
				$updateResult = DB::table($table_name)
					->where('file_id', '=', $file_id)
					->limit(1)
					->update([
						'is_transcode' => 2,
					]);

				return;
			}

			$this->insertMiddleVideo($file_id, $params, 1);

			$video_length = $params['data']['duration'];

			$data                 = [];
			$data['video_length'] = $video_length;
			$playSet              = $params['data']['playSet'];
			foreach ($playSet as $item) {
				$url        = $item['url'];
				$definition = $item['definition'];
				$vbitrate   = $item['vbitrate'];

				$videoSize = $vbitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024
				if ($definition === 0) {    //原视频
					$data['video_url']  = $url;
					$data['video_size'] = $videoSize;
				} else if ($definition === 230) {   //高清M3u8
					$data['video_hls']                = $url;
					$data['alive_m3u8_high_vbitrate'] = $vbitrate;
					$data['alive_m3u8_high_size']     = $videoSize;
				}
			}

			//如果转码失败更新数据库
			if ($status != 0 || !array_key_exists('video_hls', $data) || empty($data['video_hls'])) {
				$updateResult = DB::table($table_name)
					->where('file_id', '=', $file_id)
					->limit(1)
					->update([
						'is_transcode' => 2,
					]);

				return;
			} else {    //转码成功

				$listNew = Utils::getAliveList($data['video_hls']);

				$updateData['state']             = 0;
				$updateData['is_transcode']      = 1;
				$updateData['list_file_content'] = $listNew;
				$updateData['video_length']      = $video_length;
				if (array_key_exists('alive_m3u8_high_vbitrate', $data) && array_key_exists('alive_m3u8_high_size', $data)) {
					$updateData['alive_m3u8_high_vbitrate'] = $data['alive_m3u8_high_vbitrate'];
					$updateData['alive_m3u8_high_size']     = $data['alive_m3u8_high_size'];
				}

				$updateResult = DB::table($table_name)
					->where('file_id', '=', $file_id)
					->where('state', '!=', 2)
					->limit(1)
					->update($updateData);

				//成功后更新专栏
				$videoInfo = DB::table($table_name)
					->select('app_id', 'payment_type', 'product_id')
					->where('file_id', '=', $file_id)
					->first();
				if ($videoInfo && $videoInfo->payment_type == 3) {
					$update = DB::update('
update t_pay_products set resource_count = resource_count + 1 where app_id = ? and id = ? limit 1
', [$videoInfo->app_id, $videoInfo->product_id]);
				}
			}

		} else {
			return;
		}
	}
}