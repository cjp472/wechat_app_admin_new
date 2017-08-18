<?php
/**
 * Created by PhpStorm.
 * User: fuhaiwen
 * Date: 2017/2/22
 * Time: 20:07
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AccountSystem;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\Input;

class chargeShellController extends Controller
{

	public function resourceRecord ()
	{
		$end_date = Input::get('end_date', '');

		$start_date = $this->get_yestoday($end_date);
		//        $end_date = Utils::getTime();
		//        $app_id = AppUtils::getAppID();
		//        $charge_date = date("Y-m-d",strtotime("-1 day"));
		//        $d = new Datetime($end_date);

		$charge_date = date("Y-m-d", strtotime($end_date . "-1 day"));
		$table_date  = date("Y_m_d", strtotime($end_date . "-1 day"));
		//统计每个资源的昨天流量大小、播放次数、资源类型
		for ($i = 0; $i <= 9; ++$i) {
			$yp_flow_result = \DB::select("select resource_id,resource_type,resource_name,app_id, size,size_compressed,img_size_total,size_total,  count(*) num_play, sum(size_total) size_yp_flow, (sum(size_total)/1024)*0.9 bal_yp_flow from db_ex_finance.t_resource_uv_$i where  created_at>='$start_date' and created_at<'$end_date'   group by  resource_id");
			if ($yp_flow_result) {
				foreach ($yp_flow_result as $key => $single_audio) {
					//将资源存储明细放入t_resource_record
					$params                             = [];
					$params['app_id']                   = $single_audio->app_id;
					$params['resource_id']              = $single_audio->resource_id;
					$params['resource_name']            = $single_audio->resource_name;
					$params['resource_size']            = $single_audio->size;
					$params['resource_size_compressed'] = $single_audio->size_compressed ? $single_audio->size_compressed : $single_audio->size;
					$params['img_size_total']           = $single_audio->img_size_total ? $single_audio->img_size_total : 0;
					$params['size_total']               = $single_audio->size_total ? $single_audio->size_total : ($params['resource_size_compressed'] + $params['img_size_total']);
					$params['resource_type']            = $single_audio->resource_type;//资源类型
					$params['detail_type']              = 2;//流量明细
					$params['day_datause']              = $single_audio->size_yp_flow;//单位:兆
					$params['day_viewcount']            = $single_audio->num_play;
					$params['fee']                      = $single_audio->bal_yp_flow * 100;//单位:分
					$params['created_at']               = Utils::getTime();
					$params['charge_at']                = $charge_date;
					$params['charge_rate']              = 1;

					$result_insert = \DB::table('db_ex_finance.t_resource_record')->insert($params);
				}
			}
		}
	}

	//脚本1-1:生成中间表（扣费记录）

	private function get_yestoday ($end_date)
	{
		//        $end_date = Utils::getTime();
		//
		//        $end_arr = explode("-", $end_date);
		//
		//        $start_year = intval($end_arr[0]);
		//        $start_month = $end_arr[1];
		//        $start_day = '01';
		$start_date = date("Y-m-d H:i:s", strtotime($end_date . "-1 day"));

		return $start_date;
	}

	//脚本1-2:生成流量记录

	public function createMiddleRecords ()
	{
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '-1');

		$end_date = Input::get('end_date', '');
		//        echo $end_date;
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));
		//        $statr_date = $this->get_yestoday($end_date);
		//        echo $end_date.'start_date:'.$statr_date;
		//        return 1;
		$result0 = $this->gen_resource_uv_appid($end_date);
		if ($result0) {
		} else {
		}
	}

	//脚本1-3:生成存储记录

	/**
	 * 生成业务流量表的数据,从每天的流量记录表中通过app_id最后一位判断
	 */
	private function gen_resource_uv_appid ($time)
	{
		//        $start_time = $time.' 00:00:00';
		//        $end_time = $time.' 23:59:59';
		//        set_time_limit(180);

		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '-1');

		$table_date = date("Y_m_d", strtotime($time . "-1 day"));
		$result     = 1;

		//脚本开始运行时间
		$startChargeTime = explode(' ', microtime());

		//从t_data_usage_$time中取出记录

		$count = 0;

		$total_count = \DB::select("select count(*) as sum from (select concat(app_id,user_id,resource_id,resource_type) from db_ex_flows.t_data_usage_$table_date group by concat(app_id,user_id,resource_id,resource_type)) t");
		$i           = $total_count[0]->sum / 10000;
		for ($limit_index = 0; $limit_index < $i; $limit_index++) {
			$start_limit = $limit_index * 10000;

			$result_data_uasage = \DB::select("select * from db_ex_flows.t_data_usage_$table_date group by concat(app_id,user_id,resource_id,resource_type) limit $start_limit,10000");
			if ($result_data_uasage) {
				foreach ($result_data_uasage as $key => $data_usage) {
					$app_id = $data_usage->app_id;
					$num    = ord(substr($app_id, -1)) % 10;

					$params['app_id']          = $app_id;
					$params['user_id']         = $data_usage->user_id;
					$params['resource_id']     = $data_usage->resource_id;
					$params['resource_type']   = $data_usage->resource_type;
					$params['resource_name']   = $data_usage->resource_name;
					$params['size']            = $data_usage->size;
					$params['size_compressed'] = $data_usage->size_compressed ? $data_usage->size_compressed : $data_usage->size;
					$params['img_size_total']  = $data_usage->img_size_total ? $data_usage->img_size_total : 0;
					$params['size_total']      = $data_usage->size_total ? $data_usage->size_total : ($params['size_compressed'] + $params['img_size_total']);
					//                $params['wx_app_type'] = $app_id;
					$params['way']        = $data_usage->way;
					$params['created_at'] = $data_usage->created_at;
					//检查是否存在该记录
					//                    $is_exist = \DB::table("db_ex_finance.t_resource_uv_$num")
					//                        ->where('app_id','=',$app_id)
					//                        ->where('user_id','=',$data_usage->user_id)
					//                        ->where('resource_id','=',$data_usage->resource_id)
					//                        ->where('resource_type','=',$data_usage->resource_type)
					//                        ->first();
					try {
						$result = \DB::table("db_ex_finance.t_resource_uv_$num")->insert($params);
					} catch (\Exception $e) {

					}

					$count++;

				}
			}

			$before_unset_memory = memory_get_usage();
			unset($result_data_uasage);
			//            $result_data_uasage = null;
			//使用后内存大小
			$memory_2 = memory_get_usage();
			//脚本运行10000条数据的耗时
			$endChargeTime = explode(' ', microtime());
			$expenseTime   = $endChargeTime[0] + $endChargeTime[1] - ($startChargeTime[0] + $startChargeTime[1]);
			//            system('sync && echo 3 > /proc/sys/vm/drop_caches');
		}

		//脚本运行结束时间
		$endChargeTime = explode(' ', microtime());
		$expenseTime   = $endChargeTime[0] + $endChargeTime[1] - ($startChargeTime[0] + $startChargeTime[1]);

		//        system('sync && echo 3 > /proc/sys/vm/drop_caches');
		return $result;
	}

	//脚本一:生成扣费记录

	public function createFlowRecords ()
	{
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '-1');

		$end_date = Input::get('end_date', '');
		//        echo $end_date;
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));
		$result3  = $this->get_yestoday_flow_data_new($end_date);

		if ($result3) {
		} else {
		}
	}

	//脚本:短信扣费、提成扣费

	private function get_yestoday_flow_data_new ($end_date)
	{
		//        return 1;
		$start_date = $this->get_yestoday($end_date);
		//        $end_date = Utils::getTime();
		//        $app_id = AppUtils::getAppID();
		//        $charge_date = date("Y-m-d",strtotime("-1 day"));
		//        $d = new Datetime($end_date);

		$charge_date   = date("Y-m-d", strtotime($end_date . "-1 day"));
		$table_date    = date("Y_m_d", strtotime($end_date . "-1 day"));
		$result_insert = 1;

		//昨天音频流量
		$yestoday_flow_app = [];
		$count             = 0;
		$audio_rate        = env('AUDIO_RATE');
		for ($i = 0; $i <= 9; ++$i) {
			$yp_flow_result = \DB::select("select app_id,  count(*) num_yp_flow, sum(size_total) size_yp_flow, (sum(size_total)/1024)*0.9 bal_yp_flow from db_ex_finance.t_resource_uv_$i where created_at>='$start_date' and created_at<'$end_date'  group by  app_id ");
			if ($yp_flow_result) {
				foreach ($yp_flow_result as $key => $yp_flow_app) {
					if ($yp_flow_app->size_yp_flow) {
						$yestoday_flow_app[ $count ]['app_id']       = $yp_flow_app->app_id;
						$yestoday_flow_app[ $count ]['size_yp_flow'] = $yp_flow_app->size_yp_flow;
						$yestoday_flow_app[ $count ]['bal_yp_flow']  = $yp_flow_app->bal_yp_flow;
						$count++;
					}
				}
			}
		}

		//将该组记录写入流水表t_balance_charge
		if (count($yestoday_flow_app) > 0) {
			foreach ($yestoday_flow_app as $key => $yestoday_flow) {
				if (!Utils::isEmptyString($yestoday_flow['app_id'])) {

					//插入流水
					$params                    = [];
					$params['app_id']          = $yestoday_flow['app_id'];
					$params['extra']           = $yestoday_flow['size_yp_flow']; //单位:M
					$params['fee']             = $yestoday_flow['bal_yp_flow'] * 100;//单位:分
					$params['account_balance'] = 0;
					$params['created_at']      = Utils::getTime();
					$params['charge_at']       = $charge_date;
					$params['state']           = 1;//未扣费
					$params['charge_type']     = 202;//累积流量扣费
					$params['id']              = Utils::getOrderId();

					$result_insert = \DB::table('db_ex_finance.t_balance_charge')->insert($params);
				}
			}
		}

		//统计每个资源的昨天流量大小、播放次数、资源类型
		for ($i = 0; $i <= 9; ++$i) {
			$yp_flow_result = \DB::select("select resource_id,resource_type,resource_name,app_id, size,size_compressed,img_size_total,size_total,  count(*) num_play, sum(size_total) size_yp_flow, (sum(size_total)/1024)*0.9 bal_yp_flow from db_ex_finance.t_resource_uv_$i where  created_at>='$start_date' and created_at<'$end_date'   group by  resource_id");
			if ($yp_flow_result) {
				foreach ($yp_flow_result as $key => $single_audio) {
					//将资源存储明细放入t_resource_record
					$params                             = [];
					$params['app_id']                   = $single_audio->app_id;
					$params['resource_id']              = $single_audio->resource_id;
					$params['resource_name']            = $single_audio->resource_name;
					$params['resource_size']            = $single_audio->size;
					$params['resource_size_compressed'] = $single_audio->size_compressed ? $single_audio->size_compressed : $single_audio->size;
					$params['img_size_total']           = $single_audio->img_size_total ? $single_audio->img_size_total : 0;
					$params['size_total']               = $single_audio->size_total ? $single_audio->size_total : ($params['resource_size_compressed'] + $params['img_size_total']);
					$params['resource_type']            = $single_audio->resource_type;//资源类型
					$params['detail_type']              = 2;//流量明细
					$params['day_datause']              = $single_audio->size_yp_flow;//单位:兆
					$params['day_viewcount']            = $single_audio->num_play;
					$params['fee']                      = $single_audio->bal_yp_flow * 100;//单位:分
					$params['created_at']               = Utils::getTime();
					$params['charge_at']                = $charge_date;
					$params['charge_rate']              = $audio_rate;

					$result_insert = \DB::table('db_ex_finance.t_resource_record')->insert($params);
				}
			}
		}

		return $result_insert;
	}

	//脚本二:系统扣费

	public function createStorageRecords ()
	{
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '-1');

		$end_date = Input::get('end_date', '');
		//        echo $end_date;
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));

		$result1 = $this->get_yestoday_storage($end_date);
		if ($result1) {
		}
	}

	//生成老用户的基础版赠送50元

	private function get_yestoday_storage ($end_date)
	{

		$start_date = $this->get_yestoday($end_date);
		//        $end_date = Utils::getTime();

		//        $d = new Datetime($end_date);
		$charge_date = date("Y-m-d", strtotime($end_date . "-1 day"));
		//        $charge_date = date("Y-m-d",$end_date.strtotime("-1 day"));
		//        $app_id = AppUtils::getAppID();
		$result_insert = 1;
		$audio_rate    = env('AUDIO_RATE');
		$video_rate    = env('VIDEO_RATE');

		//        return 1;
		//昨天音频存储空间明细
		$yestoday_space_app = [];
		$count              = 0;
		$yp_storage_total   = \DB::select("select app_id, sum(audio_size+img_size_total) as size_yp_space,sum(audio_size+img_size_total)/1024*0.9/30 as bal_yp_space  from db_ex_business.t_audio where created_at <= '$end_date' and audio_state!=2 group by app_id  order by size_yp_space desc");
		if ($yp_storage_total) {
			foreach ($yp_storage_total as $key => $yp_storage_app) {
				$yestoday_space_app[ $count ]['app_id']        = $yp_storage_app->app_id;
				$yestoday_space_app[ $count ]['size_yp_space'] = $yp_storage_app->size_yp_space;
				$yestoday_space_app[ $count ]['bal_yp_space']  = $yp_storage_app->bal_yp_space;
				$count++;
			}
		}

		$count_yestoday = count($yestoday_space_app);

		//昨天图文存储空间总量
		$sp_storage_total = \DB::select("select app_id,sum(img_size_total) size_sp_space, (sum(img_size_total)/1024)*0.9/30 bal_sp_space from db_ex_business.t_image_text where created_at<='$end_date'  group by app_id order by size_sp_space desc");
		if ($sp_storage_total) {
			foreach ($sp_storage_total as $key => $sp_storage_app) {

				$is_added = false;

				if ($count_yestoday > 0 && !Utils::isEmptyString($sp_storage_app->app_id)) {
					foreach ($yestoday_space_app as $key3 => $yestoday_space) {
						if ($yestoday_space['app_id'] == $sp_storage_app->app_id) {
							$yestoday_space_app[ $key3 ]['size_yp_space'] += $sp_storage_app->size_sp_space;
							$yestoday_space_app[ $key3 ]['bal_yp_space']  += $sp_storage_app->bal_sp_space;
							$is_added                                     = true;
							//                        break;
						}
					}
				}

				if ($is_added == false && !Utils::isEmptyString($sp_storage_app->app_id)) {
					$yestoday_space_app[ $count ]['app_id']        = $sp_storage_app->app_id;
					$yestoday_space_app[ $count ]['size_yp_space'] = $sp_storage_app->size_sp_space;
					$yestoday_space_app[ $count ]['bal_yp_space']  = $sp_storage_app->bal_sp_space;
					$count++;
				}
			}
		}

		$count_yestoday = count($yestoday_space_app);

		//昨天视频存储空间总量
		$sp_storage_total = \DB::select("select app_id,sum(video_size+img_size_total) size_sp_space, (sum(video_size+img_size_total)/1024)*0.9/30 bal_sp_space from db_ex_business.t_video where created_at<='$end_date' and video_state!=2 group by app_id order by size_sp_space desc");
		if ($sp_storage_total) {
			foreach ($sp_storage_total as $key => $sp_storage_app) {

				$is_added = false;

				if ($count_yestoday > 0 && !Utils::isEmptyString($sp_storage_app->app_id)) {
					foreach ($yestoday_space_app as $key2 => $yestoday_space) {
						if ($yestoday_space['app_id'] == $sp_storage_app->app_id) {
							$yestoday_space_app[ $key2 ]['size_yp_space'] += $sp_storage_app->size_sp_space;
							$yestoday_space_app[ $key2 ]['bal_yp_space']  += $sp_storage_app->bal_sp_space;
							$is_added                                     = true;
							//                        break;
						}
					}
				}

				if ($is_added == false && !Utils::isEmptyString($sp_storage_app->app_id)) {
					$yestoday_space_app[ $count ]['app_id']        = $sp_storage_app->app_id;
					$yestoday_space_app[ $count ]['size_yp_space'] = $sp_storage_app->size_sp_space;
					$yestoday_space_app[ $count ]['bal_yp_space']  = $sp_storage_app->bal_sp_space;
					$count++;
				}
			}
		}

		//将该组记录写入流水表t_balance_charge
		if (count($yestoday_space_app) > 0) {
			foreach ($yestoday_space_app as $key => $yestoday_space) {
				//插入流水
				if (!Utils::isEmptyString($yestoday_space['app_id'])) {

					//                    \DB::beginTransaction();

					//                    $app_id = $yestoday_space['app_id'];
					//                    $fee = $yestoday_space['bal_yp_space'] * 100;//单位:分

					//更新账户余额
					//                    $result = \DB::update("update db_ex_config.t_app_conf set balance = balance - '$fee' where app_id = '$app_id' and wx_app_type = 1");
					//                    if($result){
					$params           = [];
					$params['app_id'] = $yestoday_space['app_id'];
					$params['extra']  = $yestoday_space['size_yp_space'];//单位:M
					$params['fee']    = $yestoday_space['bal_yp_space'] * 100;//单位:分
					//                        $params['account_balance'] = AccountSystem::query_account_money($app_id);
					$params['account_balance'] = 0;
					$params['created_at']      = Utils::getTime();
					$params['charge_at']       = $charge_date;
					$params['state']           = 1;//未扣费

					$params['charge_type'] = 203;//存储空间扣费
					$params['id']          = Utils::getOrderId();

					$result_insert = \DB::table('db_ex_finance.t_balance_charge')->insert($params);
					//                        if($result_insert){
					////                            \DB::commit();
					////                            return 1;
					//                        }else{
					////                            \DB::rollBack();
					////                            return 0;
					//                        }
					//                    }else{
					////                        \DB::rollBack();
					////                        return 0;
					//                    }

				}

			}
		}

		$yp_storage_single = \DB::select("select app_id, id,title,audio_size,img_size_total,audio_compress_size,((audio_size+img_size_total)/1024)*0.9/30 bal_yp_space  from db_ex_business.t_audio where created_at<='$end_date' and audio_state!=2  order by audio_size desc");
		//        $yp_storage = \DB::select("select count(*) num_yp,FORMAT(sum(audio_size*2.6/1024),2) size_yp_space,floor(sum(audio_size*2.6/1024))*0.9 bal_yp_space  from db_ex_business.t_audio where app_id = '$app_id' and  created_at>='$start_date' and created_at<='$end_date'");

		if ($yp_storage_single) {
			foreach ($yp_storage_single as $key => $single_audio) {
				//将资源存储明细放入t_resource_record
				$params                             = [];
				$params['app_id']                   = $single_audio->app_id;
				$params['resource_id']              = $single_audio->id;
				$params['resource_name']            = $single_audio->title;
				$params['resource_type']            = 1;//音频
				$params['detail_type']              = 1;//存储量明细
				$params['day_storage']              = $single_audio->audio_size + $single_audio->img_size_total;//单位:M
				$params['size_total']               = $single_audio->audio_size + $single_audio->img_size_total;//单位:M
				$params['img_size_total']           = $single_audio->img_size_total ? $single_audio->img_size_total : 0;//单位:M
				$params['resource_size_compressed'] = $single_audio->audio_compress_size;//单位:M
				$params['resource_size']            = $single_audio->audio_size;//单位:M
				$params['fee']                      = $single_audio->bal_yp_space * 100;//单位:分
				$params['created_at']               = Utils::getTime();
				$params['charge_at']                = $charge_date;

				$result_insert = \DB::table('db_ex_finance.t_resource_record')->insert($params);
			}
		}

		//图文明细
		$yp_storage_single = \DB::select("select app_id, id,title,img_size_total,((img_size_total)/1024)*0.9/30 bal_yp_space  from db_ex_business.t_image_text where created_at<='$end_date'   order by img_size_total desc");
		//        $yp_storage = \DB::select("select count(*) num_yp,FORMAT(sum(audio_size*2.6/1024),2) size_yp_space,floor(sum(audio_size*2.6/1024))*0.9 bal_yp_space  from db_ex_business.t_audio where app_id = '$app_id' and  created_at>='$start_date' and created_at<='$end_date'");

		if ($yp_storage_single) {
			foreach ($yp_storage_single as $key => $single_audio) {
				//将资源存储明细放入t_resource_record
				$params                             = [];
				$params['app_id']                   = $single_audio->app_id;
				$params['resource_id']              = $single_audio->id;
				$params['resource_name']            = $single_audio->title;
				$params['resource_type']            = 4;//图文
				$params['detail_type']              = 1;//存储量明细
				$params['day_storage']              = $single_audio->img_size_total;//单位:M
				$params['size_total']               = $single_audio->img_size_total;//单位:M
				$params['img_size_total']           = $single_audio->img_size_total ? $single_audio->img_size_total : 0;//单位:M
				$params['resource_size_compressed'] = $single_audio->img_size_total;//单位:M
				$params['resource_size']            = $single_audio->img_size_total;//单位:M
				$params['fee']                      = $single_audio->bal_yp_space * 100;//单位:分
				$params['created_at']               = Utils::getTime();
				$params['charge_at']                = $charge_date;

				$result_insert = \DB::table('db_ex_finance.t_resource_record')->insert($params);
			}
		}

		//视频
		$sp_storage_single = \DB::select("select app_id, id,title,video_size,video_mp4_high_size,img_size_total,((video_size+img_size_total)/1024)*0.9/30 bal_sp_space  from db_ex_business.t_video where created_at<='$end_date' and video_state!=2  order by video_size desc");
		//        $yp_storage = \DB::select("select count(*) num_yp,FORMAT(sum(audio_size*2.6/1024),2) size_yp_space,floor(sum(audio_size*2.6/1024))*0.9 bal_yp_space  from db_ex_business.t_audio where app_id = '$app_id' and  created_at>='$start_date' and created_at<='$end_date'");

		if ($sp_storage_single) {
			foreach ($sp_storage_single as $key => $single_vidio) {
				//将资源存储明细放入t_resource_record
				$params                             = [];
				$params['app_id']                   = $single_vidio->app_id;
				$params['resource_id']              = $single_vidio->id;
				$params['resource_name']            = $single_vidio->title;
				$params['resource_type']            = 2;//视频
				$params['detail_type']              = 1;//存储量明细
				$params['day_storage']              = $single_vidio->video_size + $single_vidio->img_size_total;//单位:M
				$params['size_total']               = $single_vidio->video_size + $single_vidio->img_size_total;//单位:M
				$params['img_size_total']           = $single_vidio->img_size_total ? $single_vidio->img_size_total : 0;//单位:M
				$params['resource_size_compressed'] = $single_vidio->video_mp4_high_size;//单位:M
				$params['resource_size']            = $single_vidio->video_size;//单位:M
				$params['fee']                      = $single_vidio->bal_sp_space * 100; //单位:分
				$params['created_at']               = Utils::getTime();
				$params['charge_at']                = $charge_date;

				$result_insert = \DB::table('db_ex_finance.t_resource_record')->insert($params);
			}
		}

		return $result_insert;
	}

	//活动期间-小鹅通补贴

	public function createChargeRecords ()
	{

		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '-1');

		$end_date = Input::get('end_date', '');
		//        echo $end_date;
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));
		//        $statr_date = $this->get_yestoday($end_date);
		//        echo $end_date.'start_date:'.$statr_date;
		//        return 1;
		$result0 = $this->gen_resource_uv_appid($end_date);
		if ($result0) {

			$result3 = $this->get_yestoday_flow_data_new($end_date);

			if ($result3) {
			} else {
			}
		} else {
		}
		$result1 = $this->get_yestoday_storage($end_date);
		if ($result1) {
		}

		//        $result2 = $this->get_yestoday_sms($end_date);
		//
		//        if($result2){
		//        }
		//
		//
		//
		//        $result4 = $this->get_yestoday_income($end_date);
		//
		//        if($result4){
		//        }

	}

	//任务五:更新流量记录表中的img_size_total、size_compressed、size_total字段

	public function createYesterdaySmsRecords ()
	{
		$end_date = Input::get('end_date', '');
		//        echo $end_date;
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));

		$result2 = $this->get_yestoday_sms($end_date);

		if ($result2) {
		}

		$result4 = $this->get_yestoday_income($end_date);

		if ($result4) {
		}
	}

	//脚本六:生成业务流量中间表记录

	private function get_yestoday_sms ($end_date)
	{

		$start_date = $this->get_yestoday($end_date);
		//        $end_date = Utils::getTime();
		//        $app_id = AppUtils::getAppID();
		//        $openid = AppUtils::getOpenIdByAppId($app_id);
		//        $charge_date = date("Y-m-d",strtotime("-1 day"));
		//        $d = new Datetime($end_date);

		$charge_date   = date("Y-m-d", strtotime($end_date . "-1 day"));
		$result_insert = 1;

		//短信发送量:条
		//前台验证码
		$sms_total     = [];
		$count         = 0;
		$web_sms_total = \DB::select("select app_id, count(*) num_sms,(count(*)*0.05) bal_sms from db_ex_business.t_verify_codes where created_at>='$start_date' and created_at<='$end_date' group by app_id ");
		if (count($web_sms_total) > 0) {
			foreach ($web_sms_total as $key => $web_sms) {
				$sms_total[ $count ]['app_id']  = $web_sms->app_id;
				$sms_total[ $count ]['num_sms'] = $web_sms->num_sms;
				$sms_total[ $count ]['bal_sms'] = $web_sms->bal_sms;
				$count++;
			}
		}

		//        //管理台验证码
		//
		//
		//        $admin_sms_total = \DB::connection('mysql_config')->select("select openid, count(*) num_sms,(count(*)*0.05) bal_sms from db_ex_config.t_mgr_verify_codes where created_at>='$start_date' and created_at<='$end_date' group by openid ");
		//        $count_sms_total = count($sms_total);
		//        if(count($admin_sms_total) > 0){
		//            foreach ($admin_sms_total as $key2=>$app){
		//
		//                $is_added = false;
		//
		//                $app_id = AppUtils::getAppIdByOpenId($app->openid);
		//
		//                if($count_sms_total > 0 && !Utils::isEmptyString($app_id)){
		//                    foreach ($sms_total as $key=>$app2){
		//                        if($app2['app_id'] == $app_id){
		//                            $sms_total[$key2]['num_sms'] += $app->num_sms;
		//                            $sms_total[$key2]['bal_sms'] += $app->bal_sms;
		//                            $is_added = true;
		//
		//                        }
		//                    }
		//                }
		//
		//                if($is_added == false && !Utils::isEmptyString($app_id)){
		//                    $sms_total[$count]['app_id'] = $app_id;
		//                    $sms_total[$count]['num_sms'] = $app->num_sms;
		//                    $sms_total[$count]['bal_sms'] = $app->bal_sms;
		//                    $count++;
		//                }
		//
		//            }
		//        }

		//将昨天的短信记流水
		if (count($sms_total) > 0) {
			foreach ($sms_total as $key => $sms) {
				if (!Utils::isEmptyString($sms['app_id'])) {

					//                    \DB::beginTransaction();

					$app_id = $sms['app_id'];
					$fee    = $sms['bal_sms'] * 100;//单位:分
					//更新账户余额
					//                    $result = \DB::update("update db_ex_config.t_app_conf set balance = balance - '$fee' where app_id = '$app_id' and wx_app_type = 1");

					//                    if($result){
					//插入流水
					$params                    = [];
					$params['app_id']          = $sms['app_id'];
					$params['extra']           = $sms['num_sms'];
					$params['fee']             = $sms['bal_sms'] * 100;//单位:分
					$params['account_balance'] = 0;
					//                        $params['account_balance'] = AccountSystem::query_account_money($app_id);
					$params['created_at']  = Utils::getTime();
					$params['charge_at']   = $charge_date;
					$params['charge_type'] = 204;//短信扣费
					$params['state']       = 1;//未扣费
					$params['id']          = Utils::getOrderId();//流水号

					$result_insert = \DB::table('db_ex_finance.t_balance_charge')->insert($params);
					//                        if($result_insert){
					//                            \DB::commit();
					////                            return 1;
					//                        }else{
					//                            \DB::rollBack();
					//                            return 0;
					//                        }
					//                    }else{
					//                        \DB::rollBack();
					//                        return 0;
					//                    }

				}
			}
		}

		return $result_insert;
	}

	//脚本7:更新业务流量中间表img_size_total

	private function get_yestoday_income ($end_date)
	{

		//        return 1;

		$start_date = $this->get_yestoday($end_date);
		//        $end_date = Utils::getTime();
		//        $charge_date = date("Y-m-d",strtotime("-1 day"));
		//        $d = new Datetime($end_date);
		$charge_date = date("Y-m-d", strtotime($end_date . "-1 day"));

		$result_insert = 1;
		//        \DB::beginTransaction();

		$result = \DB::select("select app_id, sum(price) income from db_ex_business.t_orders where created_at>='$start_date' and created_at<'$end_date' and order_state=1 and (payment_type!=7 or (payment_type=7 and que_check_state=1))  group by app_id order by income desc");
		if ($result) {
			foreach ($result as $key => $app_income) {
				$is_growup = $this->is_growup_version($app_income->app_id);

				if ($is_growup == 1) {//是成长版
					//                    $flag = $this->get_charge_income($app_income->app_id);
					if ($app_income->income != 0) {//未达到4500元    昨天收入不为0元

						$ticheng = env('TICHENG');

						$fee = $app_income->income * $ticheng;//单位:分

						$flag = $this->get_charge_income($app_income->app_id, $fee, $end_date);
						if ($flag > 0) {
							$fee = $flag;
						}
						//                            $app_id = $app_income->app_id;
						//                            $fee = $app_income->income * 100 * $ticheng;//单位:分

						//更新账户余额
						//                            $result = \DB::update("update db_ex_config.t_app_conf set balance = balance - '$fee' where app_id = '$app_id' and wx_app_type = 1");

						//                            if($result){
						if ($flag != -1) {//提成扣费未达到上限(4500元)

							//记录一笔扣费:昨天收入的1%
							$params                = [];
							$params['fee']         = $fee;//单位:分
							$params['app_id']      = $app_income->app_id;//
							$params['charge_type'] = 205;//提成扣费
							//                                $params['account_balance'] = AccountSystem::query_account_money($app_id);
							$params['account_balance'] = 0;
							$params['extra']           = $app_income->income;//单位:分 昨天总的收入
							$params['charge_at']       = $charge_date;//扣费日期
							$params['state']           = 1;//未扣费
							$params['created_at']      = Utils::getTime();//
							$params['id']              = Utils::getOrderId();

							$result_insert = \DB::table('db_ex_finance.t_balance_charge')->insert($params);
						}
						//                                if($result_insert){
						//                                    \DB::commit();
						//                                    return 1;
						//                                }else{
						//                                    \DB::rollBack();
						//                                    return 0;
						//                                }
						//                            }else{
						//                                \DB::rollBack();
						//                                return 0;
						//                            }
						//                        }
					}
				}
			}
		}

		return $result_insert;
	}

	//获取昨天的时间

	private function is_growup_version ($app_id)
	{

		$result = \DB::table('db_ex_config.t_app_conf')->where('app_id', '=', $app_id)->where('wx_app_type', '=', 1)->first();
		if ($result) {
			if ($result->version_type == 2) {//成长版
				return 1;
			} else {
				return 0;
			}
		}

		return 0;
	}

	//获取客户昨天的存储使用量

	private function get_charge_income ($app_id, $fee, $end_date)
	{

		//获取当前时间
		$start_date = date('Y-m-d H:i:s', strtotime($end_date . '-1 year'));
		//        $end_date = Utils::getTime();

		$result = \DB::connection('db_ex_finance')->select("select sum(fee) as charge_income from t_balance_charge where state = 2 and charge_type=205 and app_id='$app_id' and charge_time<='$end_date'")[0];
		//            ->table('t_balance_charge')
		//            ->select('sum(fee) as charge_income')
		//            ->where('charge_type','=','205')
		//            ->where('app_id','=',$app_id)
		//            ->first();
		if ($result) {

			if ($result->charge_income == 450000) {
				return -1;
			}

			if (($result->charge_income + $fee) >= 450000 && $result->charge_income < 450000) {
				$shengyu = 450000 - $result->charge_income;

				return $shengyu;
			} else {
				return 0;
			}

		} else {
			return 0;
		}
	}

	//获取客户昨天的短信发送量

	public function doCharge ()
	{
		$arr_type = ['202', '203', '204', '205'];
		$end_date = Input::get("end_date", '');
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));

		$charge_date = date("Y-m-d", strtotime($end_date . "-1 day"));
		\DB::beginTransaction();

		//备份客户配置表t_app_conf

		//查询t_balance_charge表(昨天,且状态state为0的所有扣费记录(charge_type in(202,203,204,205)))
		$result_records = \DB::connection('db_ex_finance')->table('t_balance_charge')
			->where('charge_at', '=', $charge_date)
			->where('state', '=', 1)
			->whereIn('charge_type', $arr_type)
			->get();

		if ($result_records) {
			foreach ($result_records as $key => $record) {
				//1.扣费,即更新账户余额;2.修改流水记录状态为扣费成功即state=2,扣费时间charge_time=now,可用余额为当前账户余额
				//更新账户余额
				$fee    = $record->fee;
				$app_id = $record->app_id;
				if ($fee != 0) {
					$result_update = \DB::update("update db_ex_config.t_app_conf set balance = balance - '$fee' where app_id = '$app_id' and wx_app_type = 1");
					if ($result_update) {
						$account_balance = AccountSystem::query_account_money($app_id);
						$now             = Utils::getTime();
						//更新扣费流水状态
						$result_update_record = \DB::update("update db_ex_finance.t_balance_charge 
                    set state=2,charge_time='$now',account_balance='$account_balance' where serice_number='$record->serice_number'");
						if ($result_update_record) {
							\DB::commit();
						} else {
							\DB::rollBack();
						}

					} else {
						\DB::rollBack();
					}
				} else if ($fee == 0) {
					$account_balance      = AccountSystem::query_account_money($app_id);
					$now                  = Utils::getTime();
					$result_update_record = \DB::update("update db_ex_finance.t_balance_charge 
                    set state=2,charge_time='$now',account_balance='$account_balance' where serice_number='$record->serice_number'");

					if ($result_update_record) {
						\DB::commit();
					} else {
						\DB::rollBack();
					}
				}
			}
		}

		//        \DB::beginTransaction();

		//        $result1 = $this->get_yestoday_storage();
		//        $result2 = $this->get_yestoday_sms();
		//        $result3 = $this->get_yestoday_flow_data();
		//        $result4 = $this->get_yestoday_income();
		//        if($result1 && $result2 && $result3 && $result4){

		//            //扣除账户费用--当天  在表t_balance_charge中统计类型为:202:流量扣费、203:存储扣费、204:短信扣费、205:提成扣费
		//            $result_charge_fee= \DB::connection('db_ex_finance')
		//                ->select("select app_id, sum(fee) total_fee from db_ex_finance.t_balance_charge where charge_type in(202,203,204,205) and charge_at ='$charge_date' group by app_id");
		//            if($result_charge_fee){
		//                foreach ($result_charge_fee as $key=>$charge_fee_app){
		//                    $total_fee = $charge_fee_app->total_fee;
		//                    $result = \DB::update("update db_ex_config.t_app_conf set balance = balance - '$total_fee' where app_id = '$charge_fee_app->app_id' and wx_app_type = 1");
		//                    if($result){
		////                        \DB::commit();
		//                    }else{
		////                        \DB::rollBack();
		//                    }
		//                }
		//            }else{
		////                \DB::commit();
		//            }

		//        }else{
		//
		//        }
	}

	//获取客户该月的累积播放流量

	public function doChargeSingle ()
	{
		$result_app_id = \DB::connection('mysql_config')->select('select app_id from db_ex_config.t_app_conf where wx_app_type=1');
		if ($result_app_id) {
			foreach ($result_app_id as $key => $app) {
				$params['app_id']          = $app->app_id;
				$params['id']              = Utils::getOrderId();
				$params['charge_type']     = 102;//开通基础版赠送50元
				$params['fee']             = 5000;//单位:分
				$params['account_balance'] = 5000;
				$params['charge_time']     = Utils::getTime();
				$params['created_at']      = Utils::getTime();
				$result_insert             = \DB::connection('db_ex_finance')->table('t_balance_charge')->insert($params);

			}
		}
	}

	//获取客户昨天的营收:扣费1%的收入-version_type =1即成长版的客户,封顶为4500元

	public function doSubsidy ()
	{
		$end_date = Input::get("end_date", '');
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));

		$charge_date = date("Y-m-d", strtotime($end_date . "-1 day"));
		\DB::beginTransaction();
		$result_app = \DB::connection('db_ex_finance')->select("select app_id,sum(fee) money from db_ex_finance.t_balance_charge where charge_type in(202,203,204) and state=2 and charge_at='$charge_date' group by app_id");
		if ($result_app) {
			foreach ($result_app as $key => $app) {
				$app_id        = $app->app_id;
				$subsidy_money = $app->money;
				//更新账户余额
				$result_update = \DB::update("update db_ex_config.t_app_conf set balance = balance + '$subsidy_money' where app_id = '$app_id' and wx_app_type = 1");
				if ($result_update) {
					//新增补贴流水记录,charge_type=301,表t_balance_charge
					$params                    = [];
					$params['app_id']          = $app_id;
					$params['extra']           = $subsidy_money; //单位:分
					$params['fee']             = $subsidy_money;//单位:分
					$params['account_balance'] = AccountSystem::query_account_money($app_id);
					$params['created_at']      = Utils::getTime();
					$params['charge_at']       = date('Y-m-d', time());
					$params['charge_time']     = Utils::getTime();
					$params['state']           = 0;//小鹅通补贴
					$params['charge_type']     = 301;//累积流量扣费
					$params['id']              = Utils::getOrderId();

					$result_insert = \DB::table('db_ex_finance.t_balance_charge')->insert($params);

					if ($result_insert) {
						\DB::commit();
					} else {
						\DB::rollBack();
					}
				} else {
					\DB::rollBack();
				}

			}
		}
	}

	//获取app_id已扣除的收入1%

	public function doUpdateImageSize ()
	{
		$end_date = Input::get("end_date", '');
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));

		$table_date = date("Y_m_d", strtotime($end_date . "-1 day"));

		$start_date = $this->get_yestoday($end_date);

		//查询end_date的所有流量统计记录
		//        for($i=0;$i<=2;++$i){
		//
		//        }
		$data_usage = \DB::select("select * from db_ex_flows.t_data_usage_$table_date where created_at>='$start_date' and created_at<'$end_date'");
		//        $result = \DB::table($table_name)->where('app_id','=',$value->app_id)->where('id','=',$value->resource_id)->first();

		if ($data_usage) {
			foreach ($data_usage as $key => $value) {
				//根据app_id、resource_id、resource_type

				if ($value->resource_type == 1) {//音频

					$table_name = "db_ex_business.t_audio";

				} else if ($value->resource_type == 2) {//视频

					$table_name = "db_ex_business.t_video";
				} else if ($value->resource_type == 4) {//图文

					$table_name = "db_ex_business.t_image_text";
				} else {//直播
					$table_name = "";
				}

				if ($table_name != "") {
					//查询资源
					$result = \DB::table($table_name)->where('app_id', '=', $value->app_id)->where('id', '=', $value->resource_id)->first();

					if ($result) {
						//更新t_data_usage_$table_date中img_size_total字段
						$size_compressed = 0;
						if ($value->resource_type == 1) {
							$size_compressed = $result->audio_compress_size;
						} else if ($value->resource_type == 2) {
							$size_compressed = $result->video_mp4_high_size;
						}
						$img_size_total = $result->img_size_total;

						$update = \DB::update("update db_ex_flows.t_data_usage_$table_date set img_size_total=$img_size_total,size_compressed=$size_compressed,size_total=$img_size_total+$size_compressed where id='$value->id' and size_total=0 and img_size_total=0 limit 1");
					}
				}
			}
		}

	}

	//判断app_id是否为成长版

	public function doGenDataUsageApp ()
	{
		$time   = Input::get("end_date", '');
		$result = $this->gen_resource_uv_appid($time);
		if ($result) {
		}

	}

	//获取客户该月的累积播放流量--新

	public function doUpdateDataUsageApp ()
	{
		for ($i = 0; $i <= 2; $i++) {
			if ($i == 0) {//音频

				$table_name = "db_ex_business.t_audio";

			} else if ($i == 1) {//视频

				$table_name = "db_ex_business.t_video";
			} else if ($i == 2) {//图文

				$table_name = "db_ex_business.t_image_text";
			}

			$result = \DB::table($table_name)->get();
			if ($result) {
				foreach ($result as $key => $value) {
					$app_id = $value->app_id;
					$num    = ord(substr($app_id, -1)) % 10;
					$type   = $i;

					$size_compressed = 0;
					if ($type == 0) {
						$size_compressed = $value->audio_compress_size;
					} else if ($type == 1) {
						$size_compressed = $value->video_mp4_high_size;
					}
					if ($type == 2) {
						$type = 3;
					}
					$img_size_total = $value->img_size_total;

					$update = \DB::update("update db_ex_finance.t_resource_uv_$num set img_size_total=$img_size_total,size_compressed=$size_compressed,size_total=$img_size_total+$size_compressed where app_id='$app_id' and resource_id='$value->id' and resource_type=$type+1  and img_size_total=0");

				}
			}

		}
	}

	private function get_yestoday_flow_data ($end_date)
	{
		//        return 1;
		$start_date = $this->get_yestoday($end_date);
		//        $end_date = Utils::getTime();
		//        $app_id = AppUtils::getAppID();
		//        $charge_date = date("Y-m-d",strtotime("-1 day"));
		//        $d = new Datetime($end_date);

		$charge_date   = date("Y-m-d", strtotime($end_date . "-1 day"));
		$table_date    = date("Y_m_d", strtotime($end_date . "-1 day"));
		$result_insert = 1;

		//昨天音频流量
		$yestoday_flow_app = [];
		$count             = 0;
		$audio_rate        = env('AUDIO_RATE');
		$yp_flow_result    = \DB::select("select app_id,  count(*) num_yp_flow, sum(size)*$audio_rate size_yp_flow, (sum(size)*$audio_rate/1024)*0.6 bal_yp_flow from db_ex_flows.t_data_usage_$table_date where way in(1,2) and created_at>='$start_date' and created_at<'$end_date' and  resource_type=1  group by  app_id ");
		if ($yp_flow_result) {
			foreach ($yp_flow_result as $key => $yp_flow_app) {
				$yestoday_flow_app[ $count ]['app_id']       = $yp_flow_app->app_id;
				$yestoday_flow_app[ $count ]['size_yp_flow'] = $yp_flow_app->size_yp_flow;
				$yestoday_flow_app[ $count ]['bal_yp_flow']  = $yp_flow_app->bal_yp_flow;
				$count++;
			}
		}

		$count_flow_app = count($yestoday_flow_app);
		//昨天视频流量
		$video_rate = env('VIDEO_RATE');

		$sp_flow_result = \DB::select("select app_id,  count(*) num_sp_flow, sum(size) size_sp_flow, (sum(size)*$video_rate/1024)*0.6 bal_sp_flow_xishu from db_ex_flows.t_data_usage_$table_date where created_at>='$start_date' and created_at<'$end_date' and  resource_type in(2,3)  group by  app_id");
		if ($sp_flow_result) {
			foreach ($sp_flow_result as $key => $sp_flow_app) {

				$is_added = false;

				if ($count_flow_app > 0 && !Utils::isEmptyString($sp_flow_app->app_id)) {
					foreach ($yestoday_flow_app as $key2 => $yestoday_flow) {
						if ($yestoday_flow['app_id'] == $sp_flow_app->app_id) {
							$yestoday_flow_app[ $key2 ]['size_yp_flow'] += $sp_flow_app->size_sp_flow;
							$yestoday_flow_app[ $key2 ]['bal_yp_flow']  += $sp_flow_app->bal_sp_flow_xishu;
							$is_added                                   = true;
							//                        break;
						}
					}
				}

				if ($is_added == false) {
					$yestoday_flow_app[ $count ]['app_id']       = $sp_flow_app->app_id;
					$yestoday_flow_app[ $count ]['size_yp_flow'] = $sp_flow_app->size_sp_flow;
					$yestoday_flow_app[ $count ]['bal_yp_flow']  = $sp_flow_app->bal_sp_flow_xishu;
					$count++;
				}
			}
		}

		//将该组记录写入流水表t_balance_charge
		if (count($yestoday_flow_app) > 0) {
			foreach ($yestoday_flow_app as $key => $yestoday_flow) {
				if (!Utils::isEmptyString($yestoday_flow['app_id'])) {

					//                    \DB::beginTransaction();

					$app_id = $yestoday_flow['app_id'];
					$fee    = $yestoday_flow['bal_yp_flow'] * 100;//单位:分
					//更新账户余额
					//                    $result = \DB::update("update db_ex_config.t_app_conf set balance = balance - '$fee' where app_id = '$app_id' and wx_app_type = 1");

					//                    if($result){
					//插入流水
					$params           = [];
					$params['app_id'] = $yestoday_flow['app_id'];
					$params['extra']  = $yestoday_flow['size_yp_flow']; //单位:M
					$params['fee']    = $yestoday_flow['bal_yp_flow'] * 100;//单位:分
					//                        $params['account_balance'] = AccountSystem::query_account_money($app_id);
					$params['account_balance'] = 0;
					$params['created_at']      = Utils::getTime();
					$params['charge_at']       = $charge_date;
					$params['state']           = 1;//未扣费
					$params['charge_type']     = 202;//累积流量扣费
					$params['id']              = Utils::getOrderId();

					$result_insert = \DB::table('db_ex_finance.t_balance_charge')->insert($params);
					//                        if($result_insert){
					////                            \DB::commit();
					////                            return 1;
					//                        }else{
					//                            \DB::rollBack();
					//                            return 0;
					//                        }
					//                    }else{
					////                        \DB::rollBack();
					////                        return 0;
					//                    }

				}
			}
		}

		//统计每个资源的昨天流量大小、播放次数、资源类型
		$yp_flow_result = \DB::select("select resource_id,resource_name,app_id, size,  count(*) num_play, sum(size*$audio_rate) size_yp_flow, (sum(size)*$audio_rate/1024)*0.6 bal_yp_flow from db_ex_flows.t_data_usage_$table_date where way in(1,2) and created_at>='$start_date' and created_at<'$end_date' and  resource_type =1  group by  resource_id");
		if ($yp_flow_result) {
			foreach ($yp_flow_result as $key => $single_audio) {
				//将资源存储明细放入t_resource_record
				$params                = [];
				$params['app_id']      = $single_audio->app_id;
				$params['resource_id'] = $single_audio->resource_id;
				//根据resource_id查出资源的压缩后大小,在t_audio
				//                $result_audio = \DB::table('t_audio')
				//                    ->where('id','=',$single_audio->resource_id)
				//                    ->where('app_id','=',$single_audio->app_id)
				//                    ->first();
				//                if($result_audio){
				//                    $params['resource_size'] = $result_audio->audio_compress_size;
				//                }
				$params['resource_name'] = $single_audio->resource_name;
				$params['resource_size'] = $single_audio->size;
				$params['resource_type'] = 1;//音频
				$params['detail_type']   = 2;//流量明细
				$params['day_datause']   = $single_audio->size_yp_flow;//单位:兆
				$params['day_viewcount'] = $single_audio->num_play;
				$params['fee']           = $single_audio->bal_yp_flow * 100;//单位:分
				$params['created_at']    = Utils::getTime();
				$params['charge_at']     = $charge_date;
				$params['charge_rate']   = $audio_rate;

				$result_insert = \DB::table('db_ex_finance.t_resource_record')->insert($params);
			}
		}
		$sp_flow_result = \DB::select("select resource_id,resource_type,resource_name,size as size,app_id,  count(*) num_play, sum(size) size_sp_flow, sum(size)*$video_rate/1024*0.6 bal_sp_flow_xishu from db_ex_flows.t_data_usage_$table_date where created_at>='$start_date' and created_at<'$end_date' and  resource_type in(2,3)  group by  resource_id");
		if ($sp_flow_result) {
			foreach ($sp_flow_result as $key => $single_audio) {
				//将资源存储明细放入t_resource_record
				$params                  = [];
				$params['app_id']        = $single_audio->app_id;
				$params['resource_id']   = $single_audio->resource_id;
				$params['resource_name'] = $single_audio->resource_name;
				$params['resource_type'] = $single_audio->resource_type;//视频、直播
				//                if($single_audio->resource_type == 2){//视频
				//                    //根据resource_id查出资源的压缩后大小,在t_video
				//                    $result_video = \DB::table('t_video')
				//                        ->where('id','=',$single_audio->resource_id)
				//                        ->where('app_id','=',$single_audio->app_id)
				//                        ->first();
				//                    if($result_video){
				//                        $params['resource_size'] = $result_video->video_size;
				//                    }
				//                }elseif($single_audio->resource_type == 3){//直播
				//                    //根据resource_id查出资源的压缩后大小,在t_alive
				//                    $result_alive = \DB::table('t_alive')
				//                        ->where('id','=',$single_audio->resource_id)
				//                        ->where('app_id','=',$single_audio->app_id)
				//                        ->first();
				//                    if($result_alive){
				//                        $params['resource_size'] = $result_alive->video_size;
				//                    }
				//                }

				$params['detail_type']   = 2;//流量明细
				$params['day_datause']   = $single_audio->size_sp_flow; //单位:兆
				$params['resource_size'] = $single_audio->size; //单位:兆
				$params['day_viewcount'] = $single_audio->num_play;
				$params['fee']           = $single_audio->bal_sp_flow_xishu * 100;//单位:分
				$params['created_at']    = Utils::getTime();
				$params['charge_at']     = $charge_date;
				$params['charge_rate']   = $video_rate;

				$result_insert = \DB::table('db_ex_finance.t_resource_record')->insert($params);
			}
		}

		return $result_insert;
	}

}