<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AccountSystem;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\AudioUtils;
use App\Http\Controllers\Tools\CdnUtils;
use App\Http\Controllers\Tools\GlobalString;
use App\Http\Controllers\Tools\ResContentComm;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\UserUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

include "../vendor/getid3/getid3.php";

class TestController extends Controller
{
	public function testRefreshUrl ()
	{
		$arrayUrl = ['urls.0' => 'http://wechatappdev-10011692.file.myqcloud.com/app0GqwtBxT5084/audio/1ea94eae8eaf562e5097530e6ba3199b/1ea94eae8eaf562e5097530e6ba3199bpress.mp3'];

		return CdnUtils::RefreshCdnUrl($arrayUrl);
	}

	//每日任务-短信通知答主有人向他提问
	public function sendNotifySmsToAnswer ()
	{
		$today_start = date('Y-m-d', time()) . ' 00:00:00';
		$today_end   = date('Y-m-d', time()) . ' 23:59:59';

		//在问题表t_que_question查询在今天的所有问题,group by answerer_id
		$answerer_list = \DB::table('db_ex_business.t_que_question')
			->whereBetween('created_at', [$today_start, $today_end])
			->where('expire_at', '>', Utils::getTime())
			->where('phase', '=', 1)
			->where('state', '=', 0)
			->groupBy('answerer_id')
			->get();
		if ($answerer_list) {
			foreach ($answerer_list as $key => $answerer) {

				//在表t_que_answerer中查询答主的phone
				$answerInfo = \DB::table('db_ex_business.t_que_answerer')
					->where('answerer_id', '=', $answerer->answerer_id)
					->where('app_id', '=', $answerer->app_id)
					->where('product_id', '=', $answerer->product_id)
					->first();
				if ($answerInfo) {
					$message['app_id']        = $answerInfo->app_id;
					$message['resource_id']   = $answerer->question_id;
					$message['template_type'] = 3;
					$message['user_ids']      = [$answerInfo->answerer_id];
					$message['target_url']    = Utils::contentUrl('', 5, "", $answerer->question_id, $answerer->product_id, $answerer->app_id);
					$message['data']          = [
						'stu_name' => $answerer->questioner_name,
						'question' => '【小鹅通】' . "亲爱的答主：有用户向您提出问题，赶紧去问答专区回答问题吧！",
						'tea_name' => $answerInfo->answerer_id,
					];

					$message = http_build_query($message);
					$url     = env("TEMPLATE_URL") . "open/template.create/1.0";
					//发包
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					$result = curl_exec($ch);
					$result = json_decode($result);
					if ($result && property_exists($result, 'code') && $result->code === 0) {
					} else {
					}
					/* $phone = $answerInfo->phone;
                    $content = '【小鹅通】'."亲爱的答主：有用户向您提出问题，赶紧去问答专区回答问题吧！";
                    $ret = Utils::sendsms($phone, $content);
                    if ($result == false) {
                    }else{
                        //短信记录入库
                        $params['app_id'] = $answerInfo->app_id;
                        $params['user_id'] = $answerInfo->answerer_id;
                        $params['type'] = 4;//问答短信通知
                        $params['phone'] = $phone;
                        $params['code'] = $content;
                        $params['created_at'] = Utils::getTime();
                        $result = \DB::table('db_ex_business.t_verify_codes')->insert($params);
                    }*/
				} else {
				}
			}
		} else {
		}
	}

	//问答-自动退款
	public function autoRefund ()
	{
		//1.在表t_que_question中查找出phase=1且expire_at小于当前时间的所有问题记录
		$refundList = $this->getRefundList();
		//2.根据问题id查询问题详情,然后查询订单详情、app_id、下单人、以及订单号
		if ($refundList) {
			$que_id_list = [];
			foreach ($refundList as $key => $refund) {
				$que_id_list[] = $refund->id;
			}
			$result = $this->commitRefund($que_id_list);
			if ($result) {
			}
		} else {
		}
	}

	//退款操作

	private function getRefundList ()
	{
		//在问题表t_que_question中查询expire_at小于当前时间的所有记录
		$refund_record_list = \DB::table("db_ex_business.t_que_question")
			->where("state", '!=', StringConstants::QUESTION_STATE_DELETE)
			->where("phase", '=', StringConstants::QUESTION_PAY_STATE_PAY)//待回答
			->where("expire_at", '<', Utils::getTime())
			->get();

		return $refund_record_list;
	}

	//修改问题状态(未回答已退款)

	private function commitRefund ($que_id_list)
	{
		$result_order_update   = 0;
		$result_question_state = 0;

		foreach ($que_id_list as $key => $que_id) {
			//查询问题详情
			$que_info = $this->getQuestionInfo($que_id);

			if ($que_info) {
				//根据问题id和所属的问答专区在订单表t_orders中查询对应的订单
				$question_id   = $que_info->question_id;//问题id
				$product_id    = $que_info->product_id;//问答专区id
				$questioner_id = $que_info->questioner_id;//提问人的id
				$app_id        = $que_info->app_id;//业务id
				$order_info    = $this->getQueOrderInfo($question_id, $product_id, $questioner_id, $app_id);//获取订单信息
				if ($order_info) {

					//获取订单的单价
					$order_price  = $order_info->price;//单位:分
					$order_charge = $order_price * 0;//微信手续费:0
					//判断该商户可提现余额(库db_ex_finance中的表t_usable_balance)是否比订单的手续费多
					$account_money = 1;
					if ($account_money >= $order_charge) {//商户可提现余额大于手续费
						//向账户系统发送退款申请(参数:app_id/order_id/user_id)
						$wholeUrl = env('ADMIN_ACCOUNT_HTTP');

						//."/?app_id=".$this->app_id."&user_id=".$order_info->user_id."&order_id=".$order_info->orader_id
						$data = [
							'app_id'   => $app_id,
							'user_id'  => $order_info->user_id,
							'order_id' => $order_info->order_id,
						];

						$data = http_build_query($data);
						//                        $data = json_encode($data);
						$jsonString = file_get_contents($wholeUrl, 0, stream_context_create([
							'http' => [
								'timeout' => 30,
								'method'  => 'POST',
								'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
									"Content-length:" . strlen($data) . "\r\n" .
									"Cookie: foo=bar\r\n" .
									"\r\n",
								'content' => $data,
							],
						]));
						//                        $jsonString = file_get_contents($wholeUrl);

						$ret = json_decode($jsonString, true);

						if (array_key_exists('ret', $ret)) {
							if ($ret['ret'] == 0) {//退款成功
								//修改该笔订单的核算状态和支付状态
								//添加事务
								\DB::beginTransaction();
								$result_order_update = $this->changeOrderState($order_info->order_id, $order_info->user_id, $app_id);
								//修改该问题的状态(phase)
								$result_question_state = $this->modifyQueState($que_id, $app_id);

								if ($result_order_update && $result_question_state) {
									\DB::commit();
									//                                    return $this->result("退款成功");
								} else {
									\DB::rollBack();
								}
							} else {//退款操作返回失败
							}
						} else {//账户系统返回错误
						}
					} else {//商户可提现余额小于手续费
						//商户余额不足于支付订单手续费0.6%
					}
				} else {//订单不存在
				}

			} else {//该问题不存在
			}
		}

		if ($result_order_update && $result_question_state) {
		}

		return true;
	}

	//修改订单状态

	private function getQuestionInfo ($que_id)
	{
		//在问题表t_que_question中查询该id对应的问题详情
		$queInfo = \DB::table("db_ex_business.t_que_question")
			->where("id", '=', $que_id)
			->first();

		return $queInfo;
	}

	//获取订单信息

	private function getQueOrderInfo ($question_id, $product_id, $questioner_id, $app_id)
	{
		//在表t_orders中根据问题id和问答专区id以及提问人id查询该问题的支付订单信息
		$order_info = \DB::table("db_ex_business.t_orders")
			->where("app_id", '=', $app_id)
			->where("user_id", '=', $questioner_id)
			->where("payment_type", '=', StringConstants::PAYMENT_TYPE_QUESTION)
			->where("resource_id", '=', $question_id)
			->where("product_id", '=', $product_id)
			->where("order_state", '=', StringConstants::ORDER_STATE_PAY)
			->where("use_collection", '=', StringConstants::ORDER_STATE_PERSON)
			->where("que_check_state", '=', StringConstants::ORDER_QUE_CHECK_STATE_UNCHECK)
			->first();

		return $order_info;
	}

	//获取问题详情

	private function changeOrderState ($order_id, $user_id, $app_id)
	{

		$params['que_check_state'] = StringConstants::ORDER_QUE_CHECK_STATE_CHECK_FAILED;
		$params['updated_at']      = Utils::getTime();

		$result = \DB::table("db_ex_business.t_orders")
			->where("app_id", '=', $app_id)
			->where("order_id", '=', $order_id)
			->where("user_id", '=', $user_id)
			->where("que_check_state", '=', StringConstants::ORDER_QUE_CHECK_STATE_UNCHECK)
			->update($params);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	//退款列表

	private function modifyQueState ($que_id, $app_id)
	{

		$params['phase']      = StringConstants::QUESTION_PAY_STATE_REFUND;
		$params['updated_at'] = Utils::getTime();
		$result               = \DB::table("db_ex_business.t_que_question")
			->where('app_id', '=', $app_id)
			->where('id', '=', $que_id)
			->where('phase', '=', StringConstants::QUESTION_PAY_STATE_PAY)
			->update($params);
		if ($result) {
			//            \DB::commit();
			return true;
		} else {
			//            \DB::rollBack();
			return false;
		}
	}

	//问答相关数据导出

	public function exportQuestionDataExcel ()
	{

		//处理步骤
		//1.在db_ex_business.t_que_products中查询各个商户的问答专区信息（商户app_id、问答专区名）
		//2.在db_ex_business.t_que_answer中查询问答专区的答主数（state:0-上线;1-下线）
		//3.在db_ex_business.t_que_question中查询问答专区的问题数（phase:1-已支付待回答;2-已回答;3-已退款）

		Excel::create("exportQuestionDataExcel", function($excel) {
			$excel->sheet('sheet1', function($sheet) {

				//1.在db_ex_business.t_que_products中查询各个商户的问答专区信息（商户app_id、问答专区名）
				$prefectureList = $this->getQuePrefectureList();
				if ($prefectureList) {
					foreach ($prefectureList as $key => $prefecture) {
						//获取商户app_name
						$app_name       = $this->getAppName($prefecture->app_id);
						$prefectureName = $prefecture->title;

						//2.在db_ex_business.t_que_answer中查询问答专区的答主数（state:0-上线;1-下线）
						$onlineAnswerNum  = $this->getAnswerNum($prefecture->app_id, $prefecture->id, 0);//上线的答主数
						$outlineAnswerNum = $this->getAnswerNum($prefecture->app_id, $prefecture->id, 1);//下线的答主数

						//3.在db_ex_business.t_que_question中查询问答专区的问题数（phase:1-已支付待回答;2-已回答;3-已退款）
						$unAnsweQueNum  = $this->getQuestionNum($prefecture->app_id, $prefecture->id, 1);//已支付待回答的问题数
						$AnswedQueNum   = $this->getQuestionNum($prefecture->app_id, $prefecture->id, 2);//已回答的问题数
						$refundedQueNum = $this->getQuestionNum($prefecture->app_id, $prefecture->id, 3);//已退款的问题数

						//字段:商户名、问答专区名、上线答主数、下线答主数、待回答问题数、已回答问题数、已退款问题数
						//商户名
						$sheet->cell('A' . ($key + 2), function($cell) use ($app_name) {
							$cell->setValue($app_name);
						});
						//问答专区名
						$sheet->cell('B' . ($key + 2), function($cell) use ($prefectureName) {
							$cell->setValue($prefectureName);
						});
						//上线答主数
						$sheet->cell('C' . ($key + 2), function($cell) use ($onlineAnswerNum) {
							$cell->setValue($onlineAnswerNum);
						});
						//下线答主数
						$sheet->cell('D' . ($key + 2), function($cell) use ($outlineAnswerNum) {
							$cell->setValue($outlineAnswerNum);
						});
						//待回答问题数
						$sheet->cell('E' . ($key + 2), function($cell) use ($unAnsweQueNum) {
							$cell->setValue($unAnsweQueNum);
						});
						//已回答问题数
						$sheet->cell('F' . ($key + 2), function($cell) use ($AnswedQueNum) {
							$cell->setValue($AnswedQueNum);
						});
						//已退款问题数
						$sheet->cell('G' . ($key + 2), function($cell) use ($refundedQueNum) {
							$cell->setValue($refundedQueNum);
						});

					}
				} else {
				}
			});
		})->export('csv');
	}

	//导出 - 小社群 - 小鹅通反馈需求与建议 - 的 Excel 文件

	private function getQuePrefectureList ()
	{
		$prefectureList = \DB::table('db_ex_business.t_que_products')
			->orderBy('created_at', 'desc')
			->get();

		return $prefectureList;
	}

	//在db_ex_business.t_que_products中查询各个商户的问答专区列表

	private function getAppName ($app_id)
	{
		$app_info = AppUtils::getAppConfInfo($app_id);
		$app_name = '';
		if ($app_info) {
			$app_name = $app_info->wx_app_name;
		}

		return $app_name;
	}

	//获取对应状态的答主数

	private function getAnswerNum ($app_id, $product_id, $state)
	{

		$answerList = \DB::table('db_ex_business.t_que_answerer')
			->where('app_id', '=', $app_id)
			->where('product_id', '=', $product_id)
			->where('state', '=', $state)
			->get();
		$answerNum  = count($answerList);

		return $answerNum;
	}

	//获取回答数
	private function getQuestionNum ($app_id, $product_id, $phase)
	{
		$queList = \DB::table("db_ex_business.t_que_question")
			->where('app_id', '=', $app_id)
			->where('product_id', '=', $product_id)
			->where('state', '=', 0)
			->where('phase', '=', $phase)
			->get();
		$queNum  = count($queList);

		return $queNum;
	}

	//成长版、专业版退款

	public function exportAllQuestionListExcel ()
	{

		$startDate = Input::get("start_date", "2017-06-12 00:00:00");

		$currentTime = Utils::getTime();
		$endDate     = Input::get("end_date", $currentTime);

		$title = "小鹅通-全部问题反馈列表";

		$excelData = \DB::table('db_ex_business.t_community_feeds')
			->select('content')
			->where('app_id', '=', env('XIAOE_APP_ID'))
			->where("community_id", '=', env('XIAOE_COMMUNITY_ID'))
			->where('feeds_state', '!=', 2)
			->where('created_at', '>', $startDate)
			->where('created_at', '<', $endDate)
			->orderBy("created_at", "desc")
			->get();

		foreach ($excelData as $key => $value) {

			$dynamicContentArray = json_decode($value->content, true);

			if (key_exists('text', $dynamicContentArray)) {
				$text = $dynamicContentArray['text'] == "" ? "这个动态没有文字" : $dynamicContentArray['text'];
			} else {
				$text = "";
			}

			$excelData[ $key ] = [$text];
		}

		// 处理数据格式
		//        $excelData = ExcelUtils::getCorrectData($excelData);

		// 下载
		if ($excelData) {
			Excel::create($title, function($excel) use ($excelData) {
				$excel->sheet("订单数据", function($sheet) use ($excelData) {
					//标题
					$rows   = ['A'];
					$widths = [200];
					for ($i = 0; $i < count($rows); $i++) {
						//宽度
						$sheet->setWidth([$rows[ $i ] => $widths[ $i ]]);
					}
					$sheet->fromArray($excelData);

				});
			})->download("csv");

		} else {
			return "excel 数据为空，请仔细检查";
		}

	}

	//修改app的版本

	public function refund ()
	{
		//操作流程:
		//1.修改t_app_conf中的版本(字段:version_type\expire_time)
		//2.在db_ex_finance.t_balance_charge表中添加退款流水(404-成长版退款(150元)、405-专业版退款(450元))
		//3.在db_ex_config.t_app_module中将对应字段置位
		//    --成长版:$params['gift_buy'] = 0;//赠送好友;$params['try_audio'] = 0;//试听分享
		//    --专业版:
		//            -$params['gift_buy'] = 0;//赠送好友
		//            -$params['try_audio'] = 0;//试听分享
		//            -$params['if_caption_define'] = 0;//首页名称自定义
		//            -$params['daily_sign'] = 0;//日签分享
		//            -$params['alive_by_video'] = 0;//视频直播

		$app_id       = Input::get("app_id");
		$version_type = Input::get("version_type");

		if ($version_type < 2 && $version_type > 3) {
			return "你呀的输错版本类型了,赶紧确认去......";
		}

		//添加事务保护
		\DB::beginTransaction();

		$app_info = AppUtils::getAppConfInfo($app_id);
		if ($app_info) {
			if ($app_info->version_type == $version_type) {
				//1.修改t_app_conf中的版本(字段:version_type\expire_time)
				$result_change_app_info = $this->changeAppVersionInfo($app_id, $version_type);
				if ($result_change_app_info) {
					//2.在db_ex_finance.t_balance_charge表中添加退款流水(404-成长版退款(50元)、405-专业版退款(450元))
					if ($version_type == 2) {//成长版
						$fee         = -5000;
						$charge_type = 404;
					} else {//专业版
						$fee         = -45000;
						$charge_type = 405;
					}
					$order_id          = Utils::getUniId();
					$add_refund_record = AccountSystem::account_money_record($app_id, $charge_type, $fee, $order_id);
					if ($add_refund_record) {
						//3.在db_ex_config.t_app_module中将对应字段置位
						//    --成长版:$params['gift_buy'] = 0;//赠送好友;$params['try_audio'] = 0;//试听分享
						//    --专业版:
						//            -$params['gift_buy'] = 0;//赠送好友
						//            -$params['try_audio'] = 0;//试听分享
						//            -$params['if_caption_define'] = 0;//首页名称自定义
						//            -$params['daily_sign'] = 0;//日签分享
						//            -$params['alive_by_video'] = 0;//视频直播
						if ($version_type == 2) {//成长版
							$params['gift_buy']  = 0;//赠送好友;
							$params['try_audio'] = 0;//试听分享
						} else {//专业版
							$params['gift_buy']          = 0;//赠送好友
							$params['try_audio']         = 0;//试听分享
							$params['if_caption_define'] = 0;//首页名称自定义
							$params['daily_sign']        = 0;//日签分享
							$params['alive_by_video']    = 0;//视频直播
						}
						$update_app_module = \DB::table("db_ex_config.t_app_module")
							->where("app_id", '=', $app_id)
							->update($params);
						if ($update_app_module) {
							\DB::commit();

							return "成功了";
						} else {
							\DB::rollBack();

							return "更新app_module配置失败啦。。。。";
						}

					} else {
						\DB::rollBack();

						return "生成退款流水失败啦。。。。";
					}
				} else {
					\DB::rollBack();

					return "更新app_info失败啦。。。。";
				}
			} else {
				return "版本不一致......";
			}
		} else {
			return "咱的仓库里面没有你要找的那货,赶紧确认去......";
		}

	}

	private function changeAppVersionInfo ($app_id, $version_type)
	{
		$params['version_type'] = 1;
		$params['expire_time']  = '';
		$params['updated_at']   = Utils::getTime();
		$update_result          = \DB::table("db_ex_config.t_app_conf")
			->where("app_id", '=', $app_id)
			->where("version_type", '=', $version_type)
			->update($params);

		return $update_result;
	}

	//获取所有上架状态的商品id

	public function choiceChosenResourceExcel ()
	{
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '-1');
		//处理步骤:
		//1.将所有资源的信息录入到中间表db_ex_business.t_resource_chosen_middle中（信息包含:app_id、app_name、resource_id、resource_type、resource_name、is_chosen、price）
		//2.将推广员开关打开的app_id的全部资源is_enable_chosen=1,未打开的置为0;
		//3.在订单表中查询order_state=1的所有订单,并按product_id分组,得到sum_price和total以及product_id,更新至中间表

		$product_list = $this->getProductList();
		//        dd($product_list);
		if ($product_list) {
			foreach ($product_list as $key => $product) {

				//将记录插入中间表
				$params['app_id']        = $product->app_id;
				$params['app_name']      = $this->getAppName($product->app_id);
				$params['resource_id']   = $product->id;
				$params['resource_type'] = $product->resource_type;
				$params['resource_name'] = $product->title;
				$params['is_chosen']     = $product->is_chosen;
				$params['price']         = $product->piece_price;
				try {
					$params['created_at'] = Utils::getTime();
					$result               = \DB::table('db_ex_business.t_resource_chosen_middle')->insert($params);
				} catch (\Exception $e) {
					$params['updated_at'] = Utils::getTime();
					$result               = \DB::table('db_ex_business.t_resource_chosen_middle')
						->where('resource_id', '=', $product->id)
						->where('resource_type', '=', $product->resource_type)
						->update($params);
				}
			}

			$app_id_list   = $this->getAppIdList(1);//查询所有已开通推广员开关的客户app_id
			$update_enable = $this->setEnableChosenState($app_id_list, 1);

			$app_id_list   = $this->getAppIdList(0);//查询所有未开通推广员开关的客户app_id
			$update_unable = $this->setEnableChosenState($app_id_list, 0);

			//            //3.在订单表中查询order_state=1的所有订单,并按product_id分组,得到sum_price和total以及product_id,更新至中间表
			//            $order_product_list = $this->getOrderProducts();//获取信息（sum_price、total、product_id、resource_type）
			//
			//            foreach ($order_product_list as $key=>$product){
			//                $paramsOrder['purchase_count'] = $product->total;
			//                $paramsOrder['purchase_money'] = $product->sum_price;
			//                $update = \DB::table('db_ex_business.t_resource_chosen_middle')
			//                    ->where('resource_id','=',$product->product_id)
			//                    ->where('resource_type','=',$product->resource_type)
			//                    ->update($paramsOrder);
			//            }

		} else {
		}
	}

	private function getProductList ()
	{
		$product_list = [];

		$this->getResourceList($product_list, StringConstants::SINGLE_GOODS_PACKAGE);

		$this->getResourceList($product_list, StringConstants::SINGLE_GOODS_ARTICLE);

		$this->getResourceList($product_list, StringConstants::SINGLE_GOODS_AUDIO);

		$this->getResourceList($product_list, StringConstants::SINGLE_GOODS_VIDEO);

		$this->getResourceList($product_list, StringConstants::SINGLE_GOODS_ALIVE);

		return $product_list;
	}

	private function getResourceList (&$product_list, $resource_type)
	{
		$table_name_array = [
			'1' => 'db_ex_business.t_image_text',
			'2' => 'db_ex_business.t_audio',
			'3' => 'db_ex_business.t_video',
			'4' => 'db_ex_business.t_alive',
			'6' => 'db_ex_business.t_pay_products',
		];
		$field_name_array = [
			'1' => 'display_state',
			'2' => 'audio_state',
			'3' => 'video_state',
			'4' => 'state',
			'6' => 'state',
		];

		if ($resource_type == 6) {
			$filed_name  = 'name';
			$piece_price = 'price';
		} else {
			$filed_name  = 'title';
			$piece_price = 'piece_price';
		}
		$result_list = \DB::table($table_name_array[ $resource_type ])
			->select('app_id', 'id', $filed_name, 'is_chosen', $piece_price)
			->where($field_name_array[ $resource_type ], '=', 0)
			->where('has_distribute', '=', 1)
			->get();
		if ($result_list) {
			foreach ($result_list as $key => $result) {
				//信息包含:app_id、resource_id、resource_type、title、is_chosen、piece_price
				if ($resource_type == 6) {
					$result->title       = $result->name;
					$result->piece_price = $result->price;
				}
				$result->resource_type = $resource_type;
				//在t_pay_products中查询该product_id的信息
				$productInfo = $this->getProductInfo($result->id, $result->app_id);
				if ($productInfo) {
					if ($productInfo->is_member == 0) {
						$result->resource_type = 5;//专栏
					} else {
						$result->resource_type = 6;//会员
					}
				}
				$product_list[] = $result;
			}
		}
	}

	private function getProductInfo ($id, $app_id)
	{
		$productInfo = \DB::table('db_ex_business.t_pay_products')
			->where('id', '=', $id)
			->where('app_id', '=', $app_id)
			->first();

		return $productInfo;
	}

	private function getAppIdList ($has_distribute)
	{
		$whereRaw = ' 1=1 ';
		if ($has_distribute == 1) {//开通了推广员功能开关
			$whereRaw .= ' and has_distribute = ' . $has_distribute;
		}
		$app_List = \DB::table('db_ex_config.t_app_module')
			->whereRaw($whereRaw)
			->get();

		$id_list = [];
		foreach ($app_List as $key => $app) {
			//            $id_list[]="'".$app->app_id."'";
			if ($has_distribute == 1) {//开通了推广员功能开关
				//进一步判断是否开启了“允许上架小鹅通精选”的开关,即is_enable_chosen=1
				$resule_config = \DB::table("db_ex_business.t_distribute_config")
					->where('app_id', '=', $app->app_id)
					->where('is_enable_chosen', '=', $has_distribute)
					->first();
				if ($resule_config) {
					$id_list[] = $app->app_id;
				}
			} else {//查询未允许商品放进小鹅通精选的所有app_id
				if ($app->has_distribute == 1) {
					$resule_config = \DB::table("db_ex_business.t_distribute_config")
						->where('app_id', '=', $app->app_id)
						->where('is_enable_chosen', '=', 0)
						->first();
					if ($resule_config) {
						$id_list[] = $app->app_id;
					}
				} else {
					$id_list[] = $app->app_id;
				}
			}
		}

		return $id_list;
	}

	//将所有资源的信息录入到中间表db_ex_business.t_resource_chosen_middle中（信息包含:app_id、app_name、resource_id、resource_type、resource_name、is_chosen、price）

	private function setEnableChosenState ($app_id_list, $has_distribute)
	{
		//        $app_id_list = $this->getAppIdList(1);//查询所有已开通推广员开关的客户app_id
		$update_enable = 0;
		foreach ($app_id_list as $key => $app_id) {
			$paramsApp['is_enable_chosen'] = $has_distribute;
			$update_enable                 = \DB::table('db_ex_business.t_resource_chosen_middle')
				->where('app_id', '=', $app_id)
				->update($paramsApp);
		}

		return $update_enable;
	}

	public function updateOrderProducts ()
	{
		//3.在订单表中查询order_state=1的所有订单,并按product_id分组,得到sum_price和total以及product_id,更新至中间表
		$order_product_list = $this->getOrderProducts();//获取信息（sum_price、total、product_id、resource_type）

		foreach ($order_product_list as $key => $product) {
			$paramsOrder['purchase_count'] = $product->total;
			$paramsOrder['purchase_money'] = $product->sum_price;
			//从资源表中获取信息,更新中间表中的price和img信息
			if ($product->resource_type == StringConstants::SINGLE_GOODS_PACKAGE || $product->resource_type == StringConstants::SINGLE_GOODS_MEMBER) {//专栏、会员
				//获取专栏或会员的信息
				$product_info = $this->getPackageInfo($product->app_id, $product->product_id);
				try {
					$paramsOrder['resource_name']      = $product_info->name;
					$paramsOrder['price']              = $product_info->price;
					$paramsOrder['img_url']            = $product_info->img_url;
					$paramsOrder['img_url_compressed'] = $product_info->img_url_compressed;
				} catch (\Exception $e) {
				}
			} else {
				//获取资源的信息
				$product_info = $this->getResourceInfo($product->product_id, $product->resource_type, $product->app_id);
				try {
					$paramsOrder['resource_name']      = $product_info->title;
					$paramsOrder['price']              = $product_info->piece_price;
					$paramsOrder['img_url']            = $product_info->img_url;
					$paramsOrder['img_url_compressed'] = $product_info->img_url_compressed;
				} catch (\Exception $e) {
				}
			}

			$update = \DB::table('db_ex_business.t_resource_chosen_middle')
				->where('resource_id', '=', $product->product_id)
				->where('resource_type', '=', $product->resource_type)
				->update($paramsOrder);
		}
	}

	//在订单表中查询order_state=1的所有订单,并按product_id分组,得到sum_price和total以及product_id,更新至中间表

	private function getOrderProducts ()
	{
		$resourceList = \DB::connection('onlyReadMysql')->select("select app_id,sum(price) as sum_price,price,payment_type,product_id,resource_type,count(1) as total from db_ex_business.t_orders where order_state=1 GROUP BY product_id ");
		if ($resourceList) {
			foreach ($resourceList as $key => $resource) {

				if ($resource->payment_type == 3) {//专栏
					//在t_pay_products中查询该product_id的信息
					$productInfo = $this->getProductInfo($resource->product_id, $resource->app_id);
					if ($productInfo) {
						if ($productInfo->is_member == 0) {
							$resource->resource_type = 5;//专栏
						} else {
							$resource->resource_type = 6;//会员
						}
					}
				}

				$resourceList[ $key ] = $resource;
			}
		} else {
		}

		return $resourceList;
	}

	//查询专栏信息

	private function getPackageInfo ($app_id, $id)
	{
		$package_info = \DB::table("db_ex_business.t_pay_products")
			->where("app_id", '=', $app_id)
			->where("id", '=', $id)
			->first();

		return $package_info;
	}

	private function getResourceInfo ($resource_id, $resource_type, $app_id)
	{
		if ($resource_type < 0 || $resource_type > 4) {

			return -1;
		}

		switch ($resource_type) {
			case 1:
				$articleInfo = $this->getArticleInfo($resource_id, $app_id);

				return $articleInfo;
			case 2:
				$audioInfo = $this->getAudioInfo($resource_id, $app_id);

				return $audioInfo;
			case 3:
				$videoInfo = $this->getVideoInfo($resource_id, $app_id);

				return $videoInfo;
			case 4:
				$aliveInfo = $this->getAliveInfo($resource_id, $app_id);

				return $aliveInfo;
		}
	}

	//为内容分销提供商品数据

	private function getArticleInfo ($id, $app_id)
	{

		$articleInfo = \DB::table("db_ex_business.t_image_text")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $articleInfo;
	}

	//TOP60的 专栏/会员  销售额排序（订阅数乘以单价），以及每个专栏/会员对应的总评论数；

	private function getAudioInfo ($id, $app_id)
	{

		$audioInfo = \DB::table("db_ex_business.t_audio")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $audioInfo;
	}

	//获取专栏价格

	private function getVideoInfo ($id, $app_id)
	{

		$videoInfo = \DB::table("db_ex_business.t_video")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $videoInfo;
	}

	//在配置表t_app_module中查出隐藏了订阅数的app_id集合

	private function getAliveInfo ($id, $app_id)
	{

		$aliveInfo = \DB::table("db_ex_business.t_alive")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $aliveInfo;
	}

	//查询专栏的所有单品(在关系表t_pro_res_relation)

	public function choiceChosenResourceExcel1111 ()
	{

		//字段:业务名、业务id、资源名、资源id、资源类型、销售量、销售金额
		//处理步骤:
		//1.将所有资源的信息录入到中间表db_ex_business.t_resource_chosen_middle中（信息包含:app_id、app_name、resource_id、resource_type、resource_name、is_chosen、price）
		//2.将推广员开关打开的app_id的全部资源is_enable_chosen=1,未打开的置为0;
		//3.在订单表中查询order_state=1的所有订单,并按product_id分组,得到sum_price和total以及product_id,更新至中间表

		$whereRaw     = '';
		$app_id_list  = $this->getAppIdList();//查询所有已开通推广员开关的客户app_id
		$app_id_array = implode(',', $app_id_list);
		//查询所有上架状态的商品id
		$product_id_list  = $this->getProductIdList();
		$product_id_array = implode(',', $product_id_list);

		//                $whereRaw .= " and app_id  in (".implode(',',$app_id_list).")";

		$resourceList = \DB::select("select app_id,sum(price) as sum_price,price,payment_type,product_id,purchase_name,resource_type,count(1) as total from db_ex_business.t_purchase where is_deleted=0 and app_id in($app_id_array) and product_id in($product_id_array) GROUP BY product_id order by sum_price desc");
		if ($resourceList) {
			foreach ($resourceList as $key => $resource) {
				$resource->app_name = $this->getAppName($resource->app_id);
				if ($resource->payment_type == 3) {//专栏
					$resource->resource_type = 5;//专栏
				} else if ($resource->payment_type == 9) {//会员
					$resource->resource_type = 6;//会员
				}

				$resource_type = $resource->resource_type;
				$app_name      = $resource->app_name;
				$app_id        = $resource->app_id;
				$sum_price     = $this->fen2Yuan($resource->sum_price);
				$price         = $this->fen2Yuan($resource->price);
				$product_id    = $resource->product_id;
				$purchase_name = $resource->purchase_name;
				$total         = $resource->total;

				//字段:业务名、业务id、资源名、资源id、资源类型、销售量、销售金额、单价

			}
		} else {
		}
	}

	//获取单品资源的评论数

	private function getProductIdList ()
	{

		$product_id_list = [];

		$this->getResourceIdList($product_id_list, StringConstants::SINGLE_GOODS_PACKAGE);
		$this->getResourceIdList($product_id_list, StringConstants::SINGLE_GOODS_ARTICLE);
		$this->getResourceIdList($product_id_list, StringConstants::SINGLE_GOODS_AUDIO);
		$this->getResourceIdList($product_id_list, StringConstants::SINGLE_GOODS_VIDEO);
		$this->getResourceIdList($product_id_list, StringConstants::SINGLE_GOODS_ALIVE);

		return $product_id_list;
	}

	private function getResourceIdList (&$product_id_list, $resource_type)
	{
		$table_name_array = [
			'1' => 'db_ex_business.t_image_text',
			'2' => 'db_ex_business.t_audio',
			'3' => 'db_ex_business.t_video',
			'4' => 'db_ex_business.t_alive',
			'6' => 'db_ex_business.t_pay_products',
		];
		$field_name_array = [
			'1' => 'display_state',
			'2' => 'audio_state',
			'3' => 'video_state',
			'4' => 'state',
			'6' => 'state',
		];

		$result_list = \DB::table($table_name_array[ $resource_type ])
			->where($field_name_array[ $resource_type ], '=', 0)
			->where('is_chosen', '=', 0)
			->get();
		if ($result_list) {
			foreach ($result_list as $key => $result) {
				$product_id_list[] = "'" . $result->id . "'";
			}
		}
	}

	//获取图文资源的信息

	private function fen2Yuan ($orgValue)
	{
		$number = number_format($orgValue / 100, 2);
		$number = str_replace(",", "", $number);

		return $number;
	}

	//获取音频资源的信息

	public function Top60 ()
	{

		//TOP60的 专栏/会员  销售额排序（订阅数乘以单价）    ，以及每个专栏/会员对应的总评论数；
		//业务名，专栏名，排名，专栏价格，订阅数，销售额， 评论数

		//处理步骤:
		//1.先在配置表t_app_module中查出隐藏了订阅数的app_id集合,
		//2.剔除掉该app_id集合
		//3.在专栏表t_pay_products中查询按销售额(订阅数乘以单价)排序的前60名的专栏信息(业务名，专栏名，排名，专栏价格，订阅数，销售额， 评论数)
		//4.评论数通过统计专栏内所有的单品来算
		Excel::create("Top60", function($excel) {
			$excel->sheet('sheet1', function($sheet) {

				$hide_sub_app_list = $this->getHideSubAppList();
				$whereRaw          = " 1=1 ";
				if ($hide_sub_app_list) {
					$whereRaw .= " and app_id not in (" . implode(',', $hide_sub_app_list) . ")";
				}

				$package_list = \DB::table("db_ex_business.t_purchase")
					->select('app_id', 'product_id', 'purchase_name', 'price', \DB::raw('count(*) as purchase_count'), \DB::raw('price*count(*) as total_fee'))
					->where('is_deleted', '=', 0)
					->where('created_at', '<=', '2017-03-31 23:59:59')
					->whereIn('generate_type', [0, 1, 3])
					->whereIn('payment_type', [3, 9])
					->whereRaw($whereRaw)
					->groupBy('product_id')
					->orderBy('total_fee', 'desc')
					->take(60)
					->get();

				if ($package_list) {
					foreach ($package_list as $key => $package) {
						//查询该专栏的总评论数
						$package_comment_count = 0;
						//查询专栏的所有单品(在关系表t_pro_res_relation)
						$resource_list = $this->getPackageResourceList($package);
						if ($resource_list) {
							//获取评论数
							foreach ($resource_list as $key2 => $resource) {
								$resource_comment_count = $this->getResourceCommentCount($resource);
								$package_comment_count  += $resource_comment_count;
							}
						}
						//                        $package->comment_count = $package_comment_count;
						//查询该专栏所属的业务app_id名称
						$app_name = $this->getAppName($package->app_id);
						//                        $package->app_name = $app_name;
						$level = $key + 1;
						//                        $package_list[$key] = $package;
						$package_name = $package->purchase_name;
						//获取专栏信息
						$product_info = $this->getPackageInfo($package->app_id, $package->product_id);
						if ($product_info) {
							$package_price = $product_info->price / 100;
						} else {
							$package_price = 0.00;
						}
						$package_purchase_count = $package->purchase_count;
						$package_total_fee      = $package->total_fee / 100;

						//业务名，专栏名，排名，专栏价格，订阅数，销售额， 评论数

						//业务名
						$sheet->cell('A' . ($key + 2), function($cell) use ($app_name) {
							$cell->setValue($app_name);
						});
						//专栏名
						$sheet->cell('B' . ($key + 2), function($cell) use ($package_name) {
							$cell->setValue($package_name);
						});
						//排名
						$sheet->cell('C' . ($key + 2), function($cell) use ($level) {
							$cell->setValue($level);
						});
						//专栏价格
						$sheet->cell('D' . ($key + 2), function($cell) use ($package_price) {
							$cell->setValue($package_price);
						});
						//订阅数
						$sheet->cell('E' . ($key + 2), function($cell) use ($package_purchase_count) {
							$cell->setValue($package_purchase_count);
						});
						//销售额
						$sheet->cell('F' . ($key + 2), function($cell) use ($package_total_fee) {
							$cell->setValue($package_total_fee);
						});
						//评论数
						$sheet->cell('G' . ($key + 2), function($cell) use ($package_comment_count) {
							$cell->setValue($package_comment_count);
						});

					}
				}
			});
		})->export('csv');
	}

	//获取视频资源的信息

	private function getHideSubAppList ()
	{
		$id_list = ['1'];

		$app_id_list = \DB::table("db_ex_config.t_app_module")
			->select('app_id')
			->where('hide_sub_count', '=', 1)
			->get();
		if ($app_id_list) {
			foreach ($app_id_list as $key => $app_id) {
				$id_list[] = "'" . $app_id->app_id . "'";
			}
		}

		return $id_list;
	}

	//获取直播资源的信息

	private function getPackageResourceList ($package)
	{

		$singlesList = \DB::table("db_ex_business.t_pro_res_relation")
			->where('app_id', '=', $package->app_id)
			->where('product_id', '=', $package->product_id)
			->where('resource_type', '<=', 4)
			->where('relation_state', '=', StringConstants::RELATION_NORMAL)
			->get();

		return $singlesList;
	}

	//查询该专栏所属的业务app_id名称

	private function getResourceCommentCount ($resource)
	{
		$resource_info          = $this->getResourceInfo($resource->resource_id, $resource->resource_type, $resource->app_id);
		$resource_comment_count = 0;
		if ($resource_info) {
			$resource_comment_count = $resource_info->comment_count;
		}

		return $resource_comment_count;
	}

	//科学队长所有节目的完播量和播放量；

	public function scienceLeaderAudioStatistics ()
	{

		Excel::create("scienceLeaderAudioStatistics", function($excel) {
			$excel->sheet('sheet1', function($sheet) {

				//在表t_pay_products中查询出该业务的所有的专栏
				$app_id = 'app6uMOq3u41326';
				//                $package_list = $this->getPackageList($app_id);
				//                if ($package_list) {
				//                    foreach ($package_list as $key => $package) {
				$count = 2;
				//查询科学队长的所有音频节目
				//                        $package->product_id = $package->id;
				$resource_list = $this->getAudioList($app_id);
				if ($resource_list) {
					foreach ($resource_list as $key2 => $resource) {
						//                                if ($resource->resource_type == StringConstants::SINGLE_GOODS_AUDIO) {
						//查询音频评论播放信息
						$audio = $this->audioPlayInfo($resource->id, $app_id);
						if ($audio) {
							$playcount   = $audio->playcount;
							$finishcount = $audio->finishcount;
							//                                        $audio_name = $audio->title;
						} else {
							$playcount   = 0;
							$finishcount = 0;
							//                                        $audio_name = '无';
						}
						$audio_name = $resource->title;

						//节目名称， 完播量， 播放量

						//节目名称
						$sheet->cell('A' . ($count), function($cell) use ($audio_name) {
							$cell->setValue($audio_name);
						});
						//完播量
						$sheet->cell('B' . ($count), function($cell) use ($finishcount) {
							$cell->setValue($finishcount);
						});
						//播放量
						$sheet->cell('C' . ($count), function($cell) use ($playcount) {
							$cell->setValue($playcount);
						});
						$count++;
						//                                }
					}
				}

				//                    }
				//                }
			});
		})->export('csv');

	}

	//查询音频列表
	private function getAudioList ($app_id)
	{
		$audio_list = \DB::table("db_ex_business.t_audio")
			->where('audio_state', '!=', 2)
			->where('app_id', '=', $app_id)
			->get();

		return $audio_list;
	}

	//查询音频评论播放信息
	private function audioPlayInfo ($resource_id, $app_id)
	{

		//查询音频列表中制定app_id的且没有删除的所有记录
		$audio = \DB::table('t_audio')
			->where('app_id', '=', $app_id)
			->where('id', '=', $resource_id)
			->where('audio_state', '!=', 2)
			->first();

		//查询音频播放统计数据
		$audio_count = \DB::select("select sum(play_count) as playSum, sum(finish_count) as finishSum from db_ex_business.t_audio_analyse
        where app_id='$app_id' and audio_id='$resource_id' ");

		//评论数 $audio->comment_count
		if ($audio_count) {

			//播放量 audio->playcount
			$audio->playcount = $audio_count[0]->playSum ? $audio_count[0]->playSum : 0;

			//完成量 $audio->finishcount
			$audio->finishcount = $audio_count[0]->finishSum ? $audio_count[0]->finishSum : 0;

			//完播率 $audio->finishpercent
			$audio->finishpercent = $audio_count[0]->finishSum && $audio_count[0]->playSum ? round(($audio_count[0]->finishSum / $audio_count[0]->playSum), 4) * 100 : '0.00';
		}

		return $audio;
	}

	//十点读书,资源流量信息
	public function shidian ()
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(3600);
		Excel::create("shidian", function($excel) {
			$excel->sheet('sheet1', function($sheet) {
				$app_id  = 'appRY9yTVR18157';
				$records = \DB::select("select resource_name,resource_type,sum(fee) as fee from db_ex_finance.t_resource_record 
                where detail_type=2 and app_id='appuAhZGRFx3075' and charge_at >= '2017-04-01' and charge_at <='2017-04-30' group by resource_id order by fee desc");
				foreach ($records as $key => $value) {

					$resource_name = $value->resource_name;
					$resource_type = $value->resource_type;
					$fee           = number_format($value->fee * 0.01, 2);

					//资源名
					$sheet->cell('A' . ($key + 1), function($cell) use ($resource_name) {
						$cell->setValue($resource_name);
					});
					//资源类型
					if ($resource_type == 1) {
						$resource_type = '音频';
					} else if ($resource_type == 2) {
						$resource_type = '视频';
					} else if ($resource_type == 3) {
						$resource_type = '直播';
					} else if ($resource_type == 4) {
						$resource_type = '图文';
					}
					$sheet->cell('B' . ($key + 1), function($cell) use ($resource_type) {
						$cell->setValue($resource_type);
					});
					//金额
					$sheet->cell('C' . ($key + 1), function($cell) use ($fee) {
						$cell->setValue($fee);
					});
				}
			});
		})->export('csv');
	}

	//时寒冰导出,约40000条数据,分4次导出excel
	public function shihanbing ()
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(3600);
		Excel::create("data", function($excel) {
			$excel->sheet('sheet1', function($sheet) {
				$app_id = 'appRY9yTVR18157';
				$users  = \DB::select("select distinct user_id from t_purchase 
                where app_id = ? and created_at <= '2017-02-27 14:42:00' order by created_at", [$app_id]);
				foreach ($users as $key => $value) {
					if ($key >= 40000 && $key <= 44999) {
						$user_id = $value->user_id;
						//用户信息
						$user = \DB::select("select * from t_users where app_id = ? and user_id = ?",
							[$app_id, $user_id])[0];

						//订购栏目+订购时间
						$result1    = \DB::select("select purchase_name,created_at from t_purchase where app_id = ? 
                        and user_id = ? order by created_at", [$app_id, $user_id]);
						$purchase   = '';
						$created_at = '';
						foreach ($result1 as $pkey => $pvalue) {
							if (empty($purchase)) {
								$purchase   = $pvalue->purchase_name;
								$created_at = $pvalue->created_at;
							} else {
								$purchase   = $purchase . '|' . $pvalue->purchase_name;
								$created_at = $created_at . '|' . $pvalue->created_at;
							}
						}

						//消费金额
						$result2 = \DB::select("select sum(price)/100 as sum from t_purchase where app_id = ? 
                        and user_id = ?", [$app_id, $user_id])[0];

						//评论
						$result3 = \DB::select("select content from t_comments where app_id = ? and user_id = ?",
							[$app_id, $user_id]);
						$comment = '';
						if ($result3) {
							foreach ($result3 as $ckey => $cvalue) {
								if (empty($comment)) {
									$comment = $cvalue->content;
								} else {
									$comment = $comment . '|' . $cvalue->content;
								}
							}
						}

						//意见反馈
						$result4  = \DB::select("select content from t_user_feedback where app_id = ? and user_id = ?",
							[$app_id, $user_id]);
						$feedback = '';
						if ($result4) {
							foreach ($result4 as $fkey => $fvalue) {
								if (empty($feedback)) {
									$feedback = $fvalue->content;
								} else {
									$feedback = $feedback . '|' . $fvalue->content;
								}
							}
						}

						//昵称
						$sheet->cell('A' . ($key + 1), function($cell) use ($user) {
							$cell->setValue(str_replace('=', '', $user->wx_nickname));
						});
						//订购栏目
						$sheet->cell('B' . ($key + 1), function($cell) use ($purchase) {
							$cell->setValue($purchase);
						});
						//消费金额
						$sheet->cell('C' . ($key + 1), function($cell) use ($result2) {
							$cell->setValue($result2->sum);
						});
						//订购时间
						$sheet->cell('D' . ($key + 1), function($cell) use ($created_at) {
							$cell->setValue($created_at . "\t");
						});
						//姓名
						$sheet->cell('E' . ($key + 1), function($cell) use ($user) {
							$cell->setValue(str_replace('=', '', $user->wx_name));
						});
						//电话
						$sheet->cell('F' . ($key + 1), function($cell) use ($user) {
							$cell->setValue($user->phone);
						});
						//地址
						$sheet->cell('G' . ($key + 1), function($cell) use ($user) {
							$cell->setValue($user->address);
						});
						//生日
						$sheet->cell('H' . ($key + 1), function($cell) use ($user) {
							$cell->setValue($user->birth);
						});
						//性别
						$sheet->cell('I' . ($key + 1), function($cell) use ($user) {
							$cell->setValue($user->wx_gender);
						});
						//公司
						$sheet->cell('J' . ($key + 1), function($cell) use ($user) {
							$cell->setValue($user->company);
						});
						//职位
						$sheet->cell('K' . ($key + 1), function($cell) use ($user) {
							$cell->setValue($user->job);
						});
						//评论
						$sheet->cell('L' . ($key + 1), function($cell) use ($comment) {
							$cell->setValue($comment);
						});
						//反馈
						$sheet->cell('M' . ($key + 1), function($cell) use ($feedback) {
							$cell->setValue($feedback);
						});
					}
				}
			});
		})->export('csv');
	}

	/***
	 * 暂用作更新音频列表大小为0的数据
	 */
	public function updateAudio0Size ()
	{
		set_time_limit(3600);
		$audioResult = DB::connection('mysql')->table('t_audio')
			->select('app_id', 'id', 'audio_url', 'audio_compress_url', 'audio_state')
			->where('audio_state', '!=', 2)
			->whereNotNull('audio_url')
			->where('audio_url', 'like', '%wxresource%')
			->whereNotIn('app_id', ['appabcdefgh1234', 'apprnDA0ZDw4581', 'app1', 'appTNtMT4978776'])
			->get();

		foreach ($audioResult as $item) {
			$update             = [];
			$audio_compress_url = $item->audio_compress_url;
			if (empty($audio_compress_url)) {
				$update['audio_compress_url'] = $item->audio_url;
				$audio_compress_url           = $item->audio_url;
			}

			$filePath = substr($audio_compress_url, 45);
			$fileInfo = \App\Http\Controllers\Tools\UploadUtils::statFile($filePath);
			if ($fileInfo['code'] == 0) {
				$fileSize                      = number_format($fileInfo['data']['filesize'] / 1024 / 1024, 2);
				$update['audio_compress_size'] = $fileSize;
				DB::table('t_audio')
					->where('app_id', '=', $item->app_id)
					->where('id', '=', $item->id)
					->where('audio_url', 'like', '%wxresource%')
					->update($update);

				//                dump($update);
			} else {
			}
		}

		return "SUCCESS";
	}

	public function passwordTest ()
	{
		$result = \DB::connection("mysql_config")->select("select id,password from t_admin_user
        where password_encrypt is null or password_encrypt='' and is_deleted =0");
		foreach ($result as $key => $value) {
			$update = \DB::connection("mysql_config")->update("update t_admin_user set password_encrypt = ? 
            where id = ? and is_deleted = 0 limit 1", [Hash::make($value->password), $value->id]);
		}
	}

	/**
	 * 重置账户密码
	 */
	public function resetAppPassword ()
	{
		$phone           = Input::get("phone", '');
		$result_app_list = \DB::table("db_ex_config.t_merchant_conf")->where('phone', '=', $phone)->get();
		foreach ($result_app_list as $key => $result_app) {
			//重置密码为123456
			//            $params['password']= '123456';
			$params['password']   = Hash::make("123456");
			$params['updated_at'] = Utils::getTime();
			$update_password      = \DB::table("db_ex_config.t_mgr_login")
				->where("merchant_id", '=', $result_app->merchant_id)
				->update($params);
			if ($update_password) {
			} else {
			}
		}
	}

	/**
	 * 临时 用作查询校正音频资源大小和流量
	 */
	public function getAudioSize ()
	{
		$audioResult = DB::connection('mysql')->table('t_audio')
			->select('app_id', 'id', 'title', 'audio_url', 'audio_compress_url', 'm3u8_url',
				'audio_length', 'audio_size', 'view_count', 'finish_count')
			->whereNotNull('audio_url')
			->whereNotIn('app_id', ['appabcdefgh1234', 'apprnDA0ZDw4581', 'app1', 'appTNtMT4978776'])
			->get();

		//        $resultAudio = "";

		$count = 0;
		foreach ($audioResult as $item) {
			$count++;
			$query = DB::connection('mysql_stat')->table('t_cul_audio')
				->where('app_id', '=', $item->app_id)
				->where('id', '=', $item->id)
				->get();
			if ($query && count($query) > 0) {
				continue;
			}
			//            dump($count ." go ". $item->app_id. " " .$item->id);

			$tempAudio['app_id']             = $item->app_id;
			$tempAudio['id']                 = $item->id;
			$tempAudio['title']              = $item->title;
			$tempAudio['audio_url']          = $item->audio_url;
			$tempAudio['audio_compress_url'] = $item->audio_compress_url;
			$tempAudio['m3u8_url']           = $item->m3u8_url;
			$tempAudio['view_count']         = $item->view_count;
			$tempAudio['finish_count']       = $item->finish_count;
			$tempAudio['audio_size']         = $item->audio_size;
			$tempAudio['audio_length']       = $item->audio_length;

			$filePath_url = substr($item->audio_url, 47);
			$fileInfo_url = UploadUtils::statFile($filePath_url);
			if (!empty($item->audio_url) && $fileInfo_url['code'] == 0) {
				$fileSize                    = number_format($fileInfo_url['data']['filesize'] / 1024 / 1024, 2);
				$tempAudio['cul_audio_size'] = $fileSize;
			} else {
				$tempAudio['cul_audio_size'] = 0;
			}

			$filePath_compress = substr($item->audio_compress_url, 47);
			$fileInfo_compress = \App\Http\Controllers\Tools\UploadUtils::statFile($filePath_compress);
			if (!empty($item->audio_compress_url) && $fileInfo_compress['code'] == 0) {
				$fileSize                       = number_format($fileInfo_compress['data']['filesize'] / 1024 / 1024, 2);
				$tempAudio['cul_compress_size'] = $fileSize;
			} else {
				$tempAudio['cul_compress_size'] = 0;
			}

			if (!empty($item->m3u8_url)) {
				$fileM3u8                   = Utils::downloadFileFromNet($item->m3u8_url);
				$content                    = file($fileM3u8);
				$tempAudio['cul_m3u8_size'] = 0;
				$temp_size                  = 0;
				foreach ($content as $contentItem) {
					if (str_contains($contentItem, "http://wechatappdev-10011692.file.myqcloud.com")
						&& str_contains($contentItem, ".ts")
					) {
						$contentItem   = str_replace("\n", "", $contentItem);
						$filePath_m3u8 = substr($contentItem, 47);
						$fileInfo_m3u8 = \App\Http\Controllers\Tools\UploadUtils::statFile($filePath_m3u8);
						if ($fileInfo_m3u8['code'] == 0) {
							$fileSize  = number_format($fileInfo_m3u8['data']['filesize'] / 1024 / 1024, 2);
							$temp_size = (float)$temp_size + (float)$fileSize;
						}
					}
				}
				$tempAudio['cul_m3u8_size'] = $temp_size;
			} else {
				$tempAudio['cul_m3u8_size'] = 0;
			}

			$resultUsage = DB::select("
select count(*) as count, sum(t2.size) as sum from (
select * from t_audio where app_id = '$item->app_id' and id = '$item->id'
) t1 left join (select * from t_data_usage
where app_id = '$item->app_id' and resource_type = 1 and resource_id = '$item->id' and way = 1
) t2 on t1.app_id = t2.app_id and t1.id = t2.resource_id
");
			if ($resultUsage && count($resultUsage) > 0) {
				$tempAudio['cul_count']      = $resultUsage[0]->count;
				$tempAudio['total_src_size'] = $resultUsage[0]->sum;
			} else {
				$tempAudio['cul_count']      = 0;
				$tempAudio['total_src_size'] = 0;
			}

			$tempAudio['total_cul_audio_size']         = $tempAudio['cul_audio_size'] * $tempAudio['cul_count'];
			$tempAudio['total_cul_audio_compress_url'] = $tempAudio['cul_compress_size'] * $tempAudio['cul_count'];
			$tempAudio['total_cul_m3u8_url']           = $tempAudio['cul_m3u8_size'] * $tempAudio['cul_count'];

			$query = DB::connection('mysql_stat')->table('t_cul_audio')
				->where('app_id', '=', $item->app_id)
				->where('id', '=', $item->id)
				->get();
			if ($query && count($query) > 0) {
				$updateReuslt = DB::connection('mysql_stat')->table('t_cul_audio')
					->where('app_id', '=', $item->app_id)
					->where('id', '=', $item->id)
					->update([
						'app_id'                       => $tempAudio['app_id'],
						'id'                           => $tempAudio['id'],
						'title'                        => $tempAudio['title'],
						'audio_url'                    => $tempAudio['audio_url'],
						'audio_compress_url'           => $tempAudio['audio_compress_url'],
						'm3u8_url'                     => $tempAudio['m3u8_url'],
						'audio_length'                 => $tempAudio['audio_length'],
						'view_count'                   => $tempAudio['view_count'],
						'finish_count'                 => $tempAudio['finish_count'],
						'audio_size'                   => $tempAudio['audio_size'],
						'cul_audio_size'               => $tempAudio['cul_audio_size'],
						'cul_audio_compress_url'       => $tempAudio['cul_compress_size'],
						'cul_m3u8_url'                 => $tempAudio['cul_m3u8_size'],
						'cul_count'                    => $tempAudio['cul_count'],
						'total_src_size'               => $tempAudio['total_src_size'],
						'total_cul_audio_size'         => $tempAudio['total_cul_audio_size'],
						'total_cul_audio_compress_url' => $tempAudio['total_cul_audio_compress_url'],
						'total_cul_m3u8_url'           => $tempAudio['total_cul_m3u8_url'],
						'created_at'                   => Utils::getTime(),
					]);
				//                dump("update app_id = ". $tempAudio['app_id']. " id = ". $tempAudio['id']);
				//                echo "update app_id = ". $tempAudio['app_id']. " id = ". $tempAudio['id'];
			} else {
				$insertResult = DB::connection('mysql_stat')->table('t_cul_audio')
					->insert([
						'app_id'                       => $tempAudio['app_id'],
						'id'                           => $tempAudio['id'],
						'title'                        => $tempAudio['title'],
						'audio_url'                    => $tempAudio['audio_url'],
						'audio_compress_url'           => $tempAudio['audio_compress_url'],
						'm3u8_url'                     => $tempAudio['m3u8_url'],
						'audio_length'                 => $tempAudio['audio_length'],
						'view_count'                   => $tempAudio['view_count'],
						'finish_count'                 => $tempAudio['finish_count'],
						'audio_size'                   => $tempAudio['audio_size'],
						'cul_audio_size'               => $tempAudio['cul_audio_size'],
						'cul_audio_compress_url'       => $tempAudio['cul_compress_size'],
						'cul_m3u8_url'                 => $tempAudio['cul_m3u8_size'],
						'cul_count'                    => $tempAudio['cul_count'],
						'total_src_size'               => $tempAudio['total_src_size'],
						'total_cul_audio_size'         => $tempAudio['total_cul_audio_size'],
						'total_cul_audio_compress_url' => $tempAudio['total_cul_audio_compress_url'],
						'total_cul_m3u8_url'           => $tempAudio['total_cul_m3u8_url'],
						'created_at'                   => Utils::getTime(),
					]);
				//                dump("insert app_id = ". $tempAudio['app_id']. " id = ". $tempAudio['id']);
				//                echo "insert app_id = ". $tempAudio['app_id']. " id = ". $tempAudio['id'];
			}
			//            $resultAudio[] = $tempAudio;
		}

		return " END";
	}

	/***
	 * 临时用于计算音视频流量
	 */
	public function culAudioAndVideoFlow ()
	{

		set_time_limit(3600);

		//        $appConf = DB::connection('mysql')->select("
		//select app_id,if(wx_app_name<>'', wx_app_name,wx_share_title) name,
		//date(created_at) as date from db_ex_config.t_app_conf
		//where wx_app_type=1 and bind_at != '0000-00-00 00:00:00'
		//");
		//        foreach($appConf as $item) {
		//            $temp['app_id'] = $item->app_id;
		//            $temp['name'] = $item->name;
		//            for ($i = 0; $i < 125; $i++) {
		//                $temp['descinfo'] = date('Y-m-d', strtotime("-$i day"));
		//                if ($temp['descinfo'] < $item->date) {
		//                    break;
		//                }
		//                try {
		//                    DB::connection('mysql_tmp_test')->table('t_tmp_stat_fee')
		//                        ->insert($temp);
		//                } catch (\Exception $ex) {
		//                    continue;
		//                }
		//            }
		//        }
		//
		//        $ypSize = DB::connection('mysql')->select("
		//select app_id,count(*) num_yp,floor(sum(audio_size/1024)) size_yp_space,
		//floor(sum(audio_size/1024))*0.9 bal_yp_space  from db_ex_business.t_audio
		//group by app_id order by size_yp_space desc, num_yp desc
		//");
		//        foreach ($ypSize as $item) {
		//            DB::connection('mysql_tmp_test')->table('t_tmp_stat_fee')
		//                ->where('app_id', '=', $item->app_id)
		//                ->update([
		//                    'audio_num' => $item->num_yp,
		//                    'audio_space' => $item->size_yp_space,
		//                    'audio_space_bal' => $item->bal_yp_space
		//                ]);
		//        }

		//        $spSize = DB::connection('mysql')->select("
		//select app_id,count(*) num_sp,floor(sum(video_size/1024)) size_sp_space,
		//floor(sum(video_size/1024))*0.9 bal_sp_space from db_ex_business.t_video
		//group by app_id order by size_sp_space desc, num_sp desc
		//");
		//        foreach ($spSize as $item) {
		//            DB::connection('mysql_tmp_test')->table('t_tmp_stat_fee')
		//                ->where('app_id', '=', $item->app_id)
		//                ->update([
		//                    'video_num' => $item->num_sp,
		//                    'video_space' => $item->size_sp_space,
		//                    'video_space_bal' => $item->bal_sp_space
		//                ]);
		//        }

		$ypFlow = DB::connection('mysql')->select("
SELECT
    app_id,
    sum(count)                        AS num_yp_flow,
    floor(sum(flow_zie) / 1024)       AS size_yp_flow,
    floor(sum(flow_zie) / 1024 * 0.6) AS bal_yp_flow,
    date
FROM (
       SELECT
         t1.app_id,
         t1.id,
         t1.audio_compress_size,
         t2.date,
         ifnull(t2.count, 0)                                 AS count,
         floor(t1.audio_compress_size * ifnull(t2.count, 0)) AS flow_zie
       FROM db_ex_business.t_audio t1
         LEFT JOIN (
                     SELECT
                       app_id,
                       resource_id,
                       date(created_at) AS date,
                       count(*)         AS count
                     FROM db_ex_business.t_data_usage
                     WHERE resource_type = 1 AND way = 1
                     GROUP BY app_id, resource_id, date
                   ) t2 ON t1.app_id = t2.app_id AND t1.id = t2.resource_id
     ) tm
GROUP BY tm.app_id, tm.date
ORDER BY bal_yp_flow DESC
");
		foreach ($ypFlow as $item) {
			DB::connection('mysql_tmp_test')->table('t_tmp_stat_fee_2')
				->where('app_id', '=', $item->app_id)
				->where('descinfo', '=', $item->date)
				->update([
					'audio_see_num'      => $item->num_yp_flow,
					'audio_see_flow'     => $item->size_yp_flow,
					'audio_see_flow_bal' => $item->bal_yp_flow,
				]);
		}

		$spFlow = DB::connection('mysql')->select("
SELECT
    app_id,
    sum(count)                        AS num_sp_flow,
    floor(sum(flow_zie) / 1024)       AS size_sp_flow,
    floor(sum(flow_zie) / 1024 * 0.6) AS bal_sp_flow,
    date
FROM (
       SELECT
         t1.app_id,
         t1.id,
         t1.video_mp4_high_size,
         t2.date,
         ifnull(t2.count, 0)                                 AS count,
         floor(t1.video_mp4_high_size * ifnull(t2.count, 0)) AS flow_zie
       FROM db_ex_business.t_video t1 LEFT JOIN
         (
           SELECT
             app_id,
             resource_id,
             date(created_at) AS date,
             count(*)         AS count
           FROM db_ex_business.t_data_usage
           WHERE resource_type = 2 AND way = 1
           GROUP BY app_id, resource_id, date
         ) t2 ON t1.app_id = t2.app_id AND t1.id = t2.resource_id
     ) tm
GROUP BY tm.app_id, tm.date
ORDER BY bal_sp_flow DESC
");
		foreach ($spFlow as $item) {
			DB::connection('mysql_tmp_test')->table('t_tmp_stat_fee_2')
				->where('app_id', '=', $item->app_id)
				->where('descinfo', '=', $item->date)
				->update([
					'video_see_num'      => $item->num_sp_flow,
					'video_see_flow'     => $item->size_sp_flow,
					'video_see_flow_bal' => $item->bal_sp_flow,
				]);
		}

		return "SUCCESS";
	}


	//    public function import_flow_data(){
	//        //
	//        $i = 1;
	//
	//    }

	/**
	 * 临时更新音频图片大小
	 */
	public function updateAudioImageSize ()
	{

		$audioInfo = DB::connection("mysql")->select("
select app_id, id, descrb, img_url, img_url_compressed, sign_url, sign_url_compressed from t_audio
where img_size_total = 0
");
		foreach ($audioInfo as $item) {
			Utils::updateAudioImgTotalSize($item);
		}
	}

	/**
	 * 临时更新视频图片大小
	 */
	public function updateVideoImageSize ()
	{

		$videoInfo = DB::connection("mysql")->select("
select app_id, id, descrb, img_url, img_url_compressed, patch_img_url, patch_img_url_compressed from t_video
where img_size_total = 0
");
		foreach ($videoInfo as $item) {
			Utils::updateVideoImgTotalSize($item);
		}
	}

	/**
	 * 临时更新图文图片大小
	 */
	public function updateTextImageSize ()
	{

		$videoInfo = DB::connection("mysql")->select("
select app_id, id, content, img_url, img_url_compressed from t_image_text
where img_size_total = 0
");
		foreach ($videoInfo as $item) {
			Utils::updateImageTextTotalSize($item);

		}
	}

	/**
	 * 更新视频转码中状态
	 */
	public function updateVideoState ()
	{
		$videoInfo = DB::connection("mysql")->select("
SELECT * from t_video
WHERE is_transcode = 0 and created_at > '2017-04-04 20:00:00'
 and file_id !=''
");

		foreach ($videoInfo as $item) {
			$app_id  = $item->app_id;
			$id      = $item->id;
			$file_id = $item->file_id;

			$this->updateVideoParams($app_id, $id, $file_id);
		}
	}

	//保存视频资源的时候主动查询转码状态
	private function updateVideoParams ($app_id, $id, $file_id)
	{

		$video_length = ResContentComm::getVideoLength($file_id);
		//        $params['video_length'] = $video_length;


		$video_url      = '';
		$video_mp4      = '';
		$video_mp4_high = '';
		$video_hls      = '';

		$video_mp4_size         = 0;
		$video_mp4_bitrate      = 0;
		$video_mp4_high_size    = 0;
		$video_mp4_high_bitrate = 0;

		//根据file_id 查询视频链接
		$private_params = ['fileId' => $file_id];

		$resultArray = ResContentComm::videoApi('DescribeVodPlayUrls', $private_params);


		if ($resultArray['code'] == 0 && array_key_exists('playSet', $resultArray)) {
			$playSet = $resultArray['playSet'];
			for ($i = 0; $i < count($playSet); $i++) {
				if ($playSet[ $i ]['definition'] == 0)//原视频
				{
					$video_url = $playSet[ $i ]['url'];
					//                    $params['video_url'] = $video_url;
				} else if ($playSet[ $i ]['definition'] == 20)//标清mp4
				{
					$video_mp4         = $playSet[ $i ]['url'];
					$video_mp4_bitrate = $playSet[ $i ]['vbitrate'];
					$video_mp4_size    = $video_mp4_bitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					//                    $params['video_mp4_vbitrate'] = $video_mp4_bitrate;
					//                    $params['video_mp4_size'] = $video_mp4_size;
					//                    $params['video_mp4'] = $video_mp4;
				} else if ($playSet[ $i ]['definition'] == 230)//高清m3u8
				{
					$video_hls = $playSet[ $i ]['url'];
					//                    $params['video_hls'] = $video_hls;

				} else if ($playSet[ $i ]['definition'] == 30)//高清mp4
				{
					$video_mp4_high = $playSet[ $i ]['url'];
					//添加码率
					$video_mp4_high_bitrate = $playSet[ $i ]['vbitrate'];
					$video_mp4_high_size    = $video_mp4_high_bitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					//                    $params['video_mp4_high_size'] = $video_mp4_high_size;
					//                    $params['video_mp4_high_vbitrate'] = $video_mp4_high_bitrate;
					//                    $params['video_mp4_high'] = $video_mp4_high;
				} else//其他格式 暂时没有提供支持
				{
				}
			}


			if (!empty($video_url) && !empty($video_mp4)) {
				$result = DB::connection("mysql")->table('t_video')
					->where('app_id', '=', $app_id)
					->where('id', '=', $id)
					->where('file_id', '=', $file_id)
					->update([
						'video_length'            => $video_length,
						'video_url'               => $video_url,
						'video_mp4'               => $video_mp4,
						'video_mp4_size'          => $video_mp4_size,
						'video_mp4_vbitrate'      => $video_mp4_bitrate,
						'video_hls'               => $video_hls,
						'video_mp4_high'          => $video_mp4_high,
						'video_mp4_high_size'     => $video_mp4_high_size,
						'video_mp4_high_vbitrate' => $video_mp4_high_bitrate,
						'is_transcode'            => 1,
					]);
				if ($result) {
				} else {
				}
			}
		}
	}

	/**
	 * 更新视频转码中状态
	 */
	public function updateAliveState ()
	{
		$videoInfo = DB::connection("mysql")->select("
SELECT * from db_ex_business.t_alive
WHERE is_transcode = 0 and created_at > '2017-04-05 20:00:00'
 and file_id !=''
");

		foreach ($videoInfo as $item) {
			$app_id  = $item->app_id;
			$id      = $item->id;
			$file_id = $item->file_id;

			$this->updateAliveParams($app_id, $id, $file_id);
		}
	}

	//保存视频直播时主动查询转码状态
	private function updateAliveParams ($app_id, $id, $fileId)
	{

		$length = ResContentComm::getVideoLength($fileId);
		//        $params['video_length'] = $length;
		//根据fileId查询hls地址
		$getParams   = ['fileId' => $fileId];
		$resultArray = ResContentComm::videoApi('DescribeVodPlayUrls', $getParams);
		if ($resultArray['code'] == 0 && array_key_exists('playSet', $resultArray)) {
			$alive_m3u8_bitrate = 0;
			$alive_m3u8_size    = 0;

			$m3u8url = '';//获取m3u8链接
			for ($i = 0; $i < count($resultArray['playSet']); $i++) {
				if ($resultArray['playSet'][ $i ]['definition'] == 230) {
					$m3u8url            = $resultArray['playSet'][ $i ]['url'];
					$alive_m3u8_bitrate = $resultArray['playSet'][ $i ]['vbitrate'];
					$alive_m3u8_size    = $alive_m3u8_bitrate / 8 * $length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					//                    $params['alive_m3u8_high_vbitrate'] = $alive_m3u8_bitrate;
					//                    $params['alive_m3u8_high_size'] = $alive_m3u8_size;

				}
			}

			if (empty($m3u8url))//转码失败
			{
				//                $params['is_transcode'] = 2;
				return;
			} else {
				//下载m3u8文件到服务器
				$filename = basename($m3u8url);
				$myurl    = storage_path('app/public/temp') . '/' . $filename;
				set_time_limit(24 * 60 * 60);
				$m3u8File = fopen($m3u8url, "rb");
				if ($m3u8File) {
					$myFile = fopen($myurl, "wb");
					if ($myFile) {
						while (!feof($m3u8File)) {
							fwrite($myFile, fread($m3u8File, 1024 * 8), 1024 * 8);
						}
					}
					fclose($m3u8File);
					if ($myFile) {
						fclose($myFile);
					}
				}
				//下载完后获取文件内容并更新
				$listOld = file_get_contents($myurl);
				$listNew = "";
				//转为数组批量替换,最后一个为空不要
				$arrs = explode("\n", $listOld);
				for ($i = 0; $i < count($arrs) - 1; $i++) {
					if (!strstr($arrs[ $i ], "#")) {
						$arrs[ $i ] = "http://" . explode('_', $filename)[0] . ".vod.myqcloud.com/" . $arrs[ $i ];
					}
					$listNew = $listNew . $arrs[ $i ] . "\n";
				}

				$video_length = 0;
				for ($i = 0; $i < count($arrs) - 1; $i++) {
					if (strstr($arrs[ $i ], "#EXTINF:")) {
						$temp         = str_replace(',', '', str_replace('#EXTINF:', '', $arrs[ $i ]));
						$video_length += (float)$temp;
					}
				}

				//                $params['state'] = 0;
				//                $params['is_transcode'] = 1;
				//                $params['list_file_content'] = $listNew;
				//                $params['video_length'] = $video_length;

				$result = DB::connection('mysql')->table('t_alive')
					->where('app_id', '=', $app_id)
					->where('id', '=', $id)
					->where('file_id', '=', $fileId)
					->update([
						'video_length'             => $video_length,
						'alive_m3u8_high_size'     => $alive_m3u8_size,
						'alive_m3u8_high_vbitrate' => $alive_m3u8_bitrate,
						'is_transcode'             => 1,
						'state'                    => 0,
						'list_file_content'        => $listNew,
					]);
				if ($result) {
				} else {
				}
			}
		}
	}

	/**
	 * 临时批量压缩banner图
	 */
	public function tempCompressBanner ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');

		$bannerList = DB::select("
select * from t_banner where image_url is not null and image_url != ''
");
		$count      = 0;
		foreach ($bannerList as $item) {
			$count++;

			$app_id             = $item->app_id;
			$id                 = $item->id;
			$image_url          = $item->image_url;
			$img_url_compressed = $item->img_url_compressed;

			if (!empty($image_url)
				&& (empty($img_url_compressed) || basename($image_url) != basename($img_url_compressed)
					|| $image_url == $img_url_compressed)) {
				try {
					//下载原图
					$src_image = Utils::downloadFileFromNet($image_url);
					//压缩处理
					$tar_image = Utils::imageCompress($src_image, 750, 280, 80);
					//上传压缩图
					$img_url_compressed = Utils::uploadCompressImage($tar_image, $app_id);
					//更新数据库
					$updateResult = DB::table('t_banner')
						->where('app_id', '=', $app_id)
						->where('id', '=', $id)
						->update(['img_url_compressed' => $img_url_compressed]);
					//删除本地文件
					@unlink($src_image);
					@unlink($tar_image);
				} catch (\Exception $ex) {
				}
			} else {
			}
		}
	}

	/**
	 * 临时批量压缩专栏图
	 */
	public function tempCompressPro ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');

		$bannerList = DB::select("
select * from t_pay_products where img_url is not null and img_url != ''
");
		$count      = 0;
		foreach ($bannerList as $item) {
			$count++;

			$app_id             = $item->app_id;
			$id                 = $item->id;
			$image_url          = $item->img_url;
			$img_url_compressed = $item->img_url_compressed;

			if (!empty($image_url)
				&& (empty($img_url_compressed) || basename($image_url) != basename($img_url_compressed)
					|| $image_url == $img_url_compressed)) {
				try {
					//下载原图
					//                    $src_image = Utils::downloadFileFromNet($image_url);
					//压缩处理
					$tar_image = Utils::imageCompress($image_url, 160, 120, 80);
					//上传压缩图
					$img_url_compressed = Utils::uploadCompressImage($tar_image, $app_id);
					//更新数据库
					$updateResult = DB::table('t_banner')
						->where('app_id', '=', $app_id)
						->where('id', '=', $id)
						->update(['img_url_compressed' => $img_url_compressed]);
					//删除本地文件
					//                    @unlink($src_image);
					@unlink($tar_image);
				} catch (\Exception $ex) {
				}
			} else {
			}
		}
	}

	/**
	 * 临时批量压缩音频
	 */
	public function tempCompressAudio ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');

		$end_time = Utils::getTime(-600);

		$audio_list = DB::select("
SELECT * from t_audio
where audio_url is not NULL and audio_url != '' and
      (audio_url = audio_compress_url or audio_compress_url is NULL or audio_compress_url = ''
       or audio_compress_url = m3u8_url or m3u8_url not like '%.m3u8')
and created_at < '$end_time'
");
		$count      = 0;
		foreach ($audio_list as $item) {
			$count++;

			$app_id       = $item->app_id;
			$id           = $item->id;
			$audio_url    = $item->audio_url;
			$audio_length = $item->audio_length;

			try {
				AudioUtils::SingleAudioCompress('t_audio', $app_id, $id, $audio_url, $audio_length);
			} catch (\Exception $ex) {
			}
		}
	}

	/**
	 * 上传M3u8文件
	 * 1.压缩M3u8生成 ts文件列表 和 M3u8文件清单
	 * 2.上传ts文件列表
	 * 3.替换M3u8清单文件中ts
	 * 4.上传M3u8清单文件
	 *
	 * @param     $src_audio
	 * @param     $app_id
	 * @param     $prefix
	 * @param     $audio_length
	 * @param int $new_length
	 *
	 * @return string
	 */
	function uploadM3u8Audio ($src_audio, $app_id, $prefix, $audio_length, &$new_length = 0)
	{
		//压缩M3u8生成 ts文件列表 和 M3u8文件清单
		$m3u8_path = Utils::audioM3u8Compressing($src_audio, $prefix);
		//根据M3u8文件获取ts所属目录
		$ts_path = substr($m3u8_path, 0, strpos($m3u8_path, '/' . basename($m3u8_path)));
		//遍历上传ts文件
		$ts_list = glob($ts_path . '/*.ts');
		foreach ($ts_list as $item) {
			Utils::uploadMp3Audio($item, $app_id, $prefix);
		}
		//替换m3u8文件中ts链接
		$dst_path  = '/' . $app_id . env('AUDIO_COMPRESS_PATH') . "" . $prefix;
		$fileRoot  = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
		$urlHeader = $fileRoot . $dst_path;
		$listOld   = file_get_contents($m3u8_path);
		$listNew   = "";
		$arrs      = explode("\n", $listOld);        //转为数组批量替换,最后一个为空不要
		for ($i = 0; $i < count($arrs) - 1; $i++) {
			if (!strstr($arrs[ $i ], "#")) {
				$arrs[ $i ] = $urlHeader . "/" . $arrs[ $i ];
			}
			$listNew = $listNew . $arrs[ $i ] . "\n";
		}
		$fp = fopen($m3u8_path, "w");
		fwrite($fp, $listNew);
		fclose($fp);

		//验证M3u8长度是否正确
		$audioLength = 0;
		foreach ($arrs as $arr) {
			if (strstr($arr, "#EXTINF:")) {
				$temp        = str_replace(',', '', str_replace('#EXTINF:', '', $arr));
				$audioLength += (float)$temp;
			}
		}
		if ($audio_length == 0 && $audioLength > 0) {
			$new_length = $audioLength;
		} else if (empty($audio_length) || empty($audioLength)
			|| $audioLength - $audio_length > 10 || $audioLength - $audio_length < -10) {
			return '';
		}

		//上传M3u8清单文件
		$result = Utils::uploadMp3Audio($m3u8_path, $app_id, $prefix);

		//删除m3u8文件夹
		Utils::delDirAndFile($ts_path, true);

		return $result;
	}

	public function test123 ()
	{
		$src_image1 = storage_path('app/public/temp/11311.png');
		$src_image2 = storage_path('app/public/temp/3333.png');
		$src_image1 = "http://wx.qlogo.cn/mmopen/TMWcHgLYzaicpJjSNCCKZEfDspQAA7S4jSiblRPJ3HIjnSnic41v2a1WWlCU9cUoPibgJqMM4qKW7IHM8NQHgr78cWvt0yOcxQ8d/0";
		$src_image2 = "http://wx.qlogo.cn/mmopen/zQP5pxKTQQrGia2EE6x7ViaBVJSysGwfkwCm2obX5bCWiavkKuHe3LCib5G2J7Up0Y0gsF4EqDRjLqddfwRLiaE89Dq14vibDicOehS/0";
		//        dump(md5_file($src_image));
		UserUtils::curlDownloadImage($src_image1, "1234567", $md51);
		dump($md51);
		UserUtils::curlDownloadImage($src_image2, "12345678", $md52);
		dump($md52);
	}

	public function testtime ()
	{
		sleep(5);

		return view("admin.testtime", [
		]);
	}
}
