<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class noticeController extends Controller
{

	public function topBarNotice ()
	{
		$show_all = Input::get('show_all', '');
		$app_id   = AppUtils::getAppID();
		$now_time = Utils::getTime();
		// $app_created_at='1=1';
		$app_created_at = \DB::connection('mysql_config')->table('t_app_conf')
			->where('app_id', $app_id)
			->where('wx_app_type', 1)
			->select('created_at')
			->first();
		// dd($app_created_at);
		//        $startChargeTime = explode(' ',microtime());
		$notice_app_id_list = \DB::connection('mysql_notice')->table('t_app_notice')
			->where('app_id', $app_id)
			->select('notice_id')
			->get();

		foreach ($notice_app_id_list as $key => $value) {
			$notice_app_id_list[ $key ] = $value->notice_id;
		}
		//     通知基础表
		$notice_basic_id_list = \DB::connection('mysql_notice')->table('t_notice')
			->where('state', '=', '0')
			->whereRaw("(notice_type=0 or target_user='$app_id')")
			->where('notice_time', '<=', Utils::getTime())
			->where('notice_time', '>', $app_created_at->created_at)
			->orwhere('id', 'n_590fd187cfc5a_vcwATIuX')
			->select('id')
			->get();

		foreach ($notice_basic_id_list as $key => $value) {
			$notice_basic_id_list[ $key ] = $value->id;
		}

		for ($i = 0; $i < sizeof($notice_basic_id_list); $i++) {
			//            如果通知基础表中的通知不在通知关系表中
			if (!in_array($notice_basic_id_list[ $i ], $notice_app_id_list)) {
				$notice_id_list[0]['notice_id']  = $notice_basic_id_list[ $i ];
				$notice_id_list[0]['app_id']     = $app_id;
				$notice_id_list[0]['created_at'] = Utils::getTime();
				//             向关系表中插入通知
				$ret = \DB::connection('mysql_notice')->table('t_app_notice')
					->insert($notice_id_list);
			}
		}
		//        若是列表获取，则加上分页
		if ($show_all == 1) {
			$notice_list = \DB::connection('mysql_notice')->table('t_app_notice')
				->where('app_id', $app_id)
				->leftJoin('t_notice', 'notice_id', '=', 't_notice.id')
				->where('state', '=', '0')
				->whereRaw("(t_notice.notice_time > '$app_created_at->created_at' and t_notice.notice_time < '$now_time' or t_notice.id = 'n_590fd187cfc5a_vcwATIuX')")
				->orderBy('t_notice.notice_time', 'desc')
				->orderBy('t_app_notice.created_at', 'desc')
				->paginate(10);

			return view('admin.notice_list', compact('notice_list'));
		} //        若是顶部通知ajax,则正常取
		else {
			$notice_list       = \DB::connection('mysql_notice')->table('t_app_notice')
				->where('app_id', $app_id)
				->leftJoin('t_notice', 'notice_id', '=', 't_notice.id')
				->where('state', '=', '0')
				->whereRaw("(t_notice.notice_time > '$app_created_at->created_at' and t_notice.notice_time < '$now_time' or t_notice.id = 'n_590fd187cfc5a_vcwATIuX')")
				->orderBy('t_notice.notice_time', 'desc')
				->orderBy('t_app_notice.created_at', 'desc')
				->get();
			$notice_unread_num = 0;
			$notice_arr        = [];
			foreach ($notice_list as $key => $value) {
				$notice_arr[] = $value;
				if ($notice_list[ $key ]->view_state == 0) {
					$notice_unread_num++;
				}
			}
			// dump($notice_list);
			//            dump($notice_unread_num);
			return json_encode(['notice_list' => $notice_list, 'notice_unread_num' => $notice_unread_num]);
		}
		//        $endChargeTime = explode(' ',microtime());
		//        $expenseTime = $endChargeTime[0]+$endChargeTime[1]-($startChargeTime[0]+$startChargeTime[1]);
		//        dump($expenseTime);

	}

	public function changeNoticeState ()
	{
		$notice_id = Input::get('notice_id');
		$app_id    = AppUtils::getAppID();
		$ret       = \DB::connection('mysql_notice')->table('t_app_notice')
			->where('app_id', $app_id)
			->where('notice_id', $notice_id)
			->update(['view_state' => 1]);
		if ($ret) {
			return response()->json(['code' => '0', 'msg' => '更新成功！']);
		} else {
			return response()->json(['code' => '-1', 'msg' => '更新失败！']);
		}
	}

	public function templateNotice ()
	{
		//        $wholeUrl = "http://118.89.49.65:45678/open/template.stat/1.0";
		$wholeUrl = env("NOTICE_ADDR");
		//发包
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $wholeUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret    = curl_exec($ch);
		$result = json_decode($ret, true);
		//        dump($result);
		//        exit;
		if (empty($result)) {
			return response()->json(['code' => -1, 'msg' => '内网请求有误']);
		} else if (array_key_exists('code', $result) && $result['code'] == 0) {
			if (array_key_exists('data', $result) && !empty($result['data'])) {
				//存在模板通知内容，

				foreach ($result['data'] as $k => $v) {
					$app_id     = $k;
					$res_id_arr = [];
					foreach ($result['data'] as $key => $value) {
						if ($key == $app_id)
							$res_id_arr = $value;
					}

					$params                = [];
					$params['id']          = Utils::getUniId('n_');
					$params['title']       = date('m月d日', time() - 86400) . '  服务号通知结果';
					$params['created_at']  = Utils::getTime();
					$params['notice_type'] = 1;
					$params['link_name']   = '';
					$params['target_user'] = $app_id;
					$notice_detail         = '';
					$params['notice_time'] = date('Y-m-d H:i:s');
					$insertResult          = true;//入表结果

					foreach ($res_id_arr as $k1 => $v1) {
						$res_id = $k1;
						$sql    = "
            SELECT
            *
        FROM
            (
                        SELECT
                            id,title
                        FROM
                            t_audio
                        WHERE
                            app_id='{$app_id} 'AND id ='{$res_id}'
                        AND audio_state = 0
                UNION ALL
                    (
                        SELECT
                            id,title
                        FROM
                            t_video
                        WHERE
                            app_id='{$app_id} ' AND id ='{$res_id}'
                        AND video_state = 0
                    )
                UNION ALL
                    (
                        SELECT
                            id,title
                        FROM
                            t_image_text
                        WHERE
                            app_id='{$app_id} ' AND id ='{$res_id}'
                        AND display_state = 0
                    )
                UNION ALL
                    (
                        SELECT
                            id,title
                        FROM
                            t_alive
                        WHERE
                            app_id='{$app_id} ' AND id ='{$res_id}'
                        AND state = 0
                    )
            )v1 ";

						$resource_info = DB::select($sql);
						//                        dump($resource_info);
						if (!empty($resource_info)) {
							if (array_key_exists('success', $v1) && array_key_exists('failed', $v1)) {
								$successCount = $v1['success'];
								$failCount    = $v1['failed'];
								$failInfo     = '';
								$i            = 1;
								if (count($v1['failed_info']) == 1) {
									$failInfo = $v1['failed_info'][0]['msg'];
								} else {
									foreach ($v1['failed_info'] as $k2 => $v3) {
										$failInfo .= "$i" . "." . $v3['msg'] . " ";
										$i++;
									}

								}

								$notice_detail .= "<tr><td width='30%'>《{$resource_info[0]->title}》</td><td width='30%'>通知成功{$successCount}人/失败{$failCount}人</td><td width='30%'>失败原因：{$failInfo}</td></tr>";
							}
							if (array_key_exists('success', $v1) && !array_key_exists('failed', $v1)) {
								$successCount  = $v1['success'];
								$failCount     = 0;
								$failInfo      = '';
								$notice_detail .= "<tr><td width='30%'>《{$resource_info[0]->title}》</td><td width='30%'>通知成功{$successCount}人/失败{$failCount}人</td></tr>";
							}
							if (!array_key_exists('success', $v1) && array_key_exists('failed', $v1)) {
								$successCount = 0;
								$failCount    = $v1['failed'];
								$failInfo     = '';
								$i            = 1;
								if (count($v1['failed_info']) == 1) {
									$failInfo = $v1['failed_info'][0]['msg'];
								} else {
									foreach ($v1['failed_info'] as $k2 => $v3) {
										$failInfo .= "$i" . "." . $v3['msg'] . " ";
										$i++;
									}

								}
								$notice_detail .= "<tr><td width='30%'>《{$resource_info[0]->title}》</td><td width='30%'>通知成功{$successCount}人/失败{$failCount}人</td><td width='30%'>失败原因：{$failInfo}</td></tr>";
							}
						} else {
							$notice_detail .= '';
						}

					}
					if (!empty($notice_detail)) {
						$params['notice_detail'] = "<table border='0'>" . $notice_detail . "</table>";
						$ret                     = \DB::connection('mysql_notice')->table('t_notice')->insert($params);
						$insertResult = $ret and $insertResult;
					}

				}//第一层foreach

				if ($insertResult) {
					return response()->json(['code' => 0, 'msg' => '模板推送通知入表成功']);
				} else {
					return response()->json(['code' => -2, 'msg' => '模板推送通知入表失败']);
				}

			} else {//不存在模板推送内容
				return response()->json(['code' => -3, 'msg' => '模板推送通知内容为空']);
			}

		} else {
			return response()->json(['code' => -1, 'msg' => '内网请求有误']);
		}

	}

}
