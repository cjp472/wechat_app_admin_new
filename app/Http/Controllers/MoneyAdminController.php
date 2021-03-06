<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26 0026
 * Time: 下午 2:47
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ExcelUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Input;

class MoneyAdminController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	// 企业模式收入
	public function companyIncomeList (Request $request)
	{
		$start_time = $request->input('start_time', '');
		$end_time   = $request->input('end_time', '');

		// 设定分页和回显搜索参数
		$search_array = [];
		if ($start_time) $search_array['start_time'] = $start_time;
		if ($end_time) $search_array['end_time'] = $end_time;

		// 时间处理，并开始拼接where 条件
		$where = " app_id = '{$this->app_id}' and order_state = 1 and use_collection = 0 and payment_type!=7 and payment_type!=8 ";

		// 时间条件
		if ($start_time) $where .= " and created_at >= '{$start_time}'";
		if ($end_time) {
			$end_time = date('Y-m-d 23:59:59', strtotime($end_time));
			$where    .= " and created_at <= '{$end_time}'";
		}

		$ListInfo = DB::connection('mysql')->table('t_orders')
			->select('created_at', 'purchase_name', 'price', 'payment_type', 'resource_type')
			->whereRaw($where)
			->orderBy('created_at', 'desc')
			//            ->tosql();
			->paginate(10);

		// 直接处理需要返回的数据
		foreach ($ListInfo as $v) {
			// 商品类型
			if ($v->payment_type == 3 || $v->payment_type == 6) {
				$v->attr = '专栏';
			} else if ($v->payment_type == 4) {
				$v->attr = '团购';
			} else if ($v->payment_type == 11) {
				$v->attr = '活动';
			} else if ($v->payment_type == 9 || $v->payment_type == 10) {
				$v->attr = '会员';
			} else if ($v->payment_type == 2 || $v->payment_type == 5) {
				if ($v->resource_type == 1) {
					$v->attr = '图文';
				} else if ($v->resource_type == 2) {
					$v->attr = '音频';
				} else if ($v->resource_type == 3) {
					$v->attr = '视频';
				} else if ($v->resource_type == 4) {
					$v->attr = '直播';
				} else if ($v->resource_type == 7) {
					$v->attr = '社群';
				} else {
					$v->attr = '--';
				}
			} else {
				$v->attr = '--';
			}
		}

		// 总收入
		$count_sum = DB::connection('mysql')->table('t_orders')->whereRaw("app_id = '{$this->app_id}' and order_state = 1 and use_collection = 0")->sum('price');
		$count_sum = $count_sum ? $count_sum : 0;
		// 今日总收入
		$today           = date('Y-m-d', time());
		$count_sum_today = DB::connection('mysql')->table("t_orders")->whereRaw("app_id = '{$this->app_id}' and order_state = 1 and use_collection = 0")
			->where('created_at', 'like', "%$today%")->sum('price');
		$count_sum_today = $count_sum_today ? $count_sum_today : 0;

		return view('admin.businessModel', [
			'ListInfo'        => $ListInfo,
			'search_array'    => $search_array,
			'count_sum'       => $count_sum,
			'count_sum_today' => $count_sum_today,
		]);

	}

	// 个人模式收入
	public function personalIncomeList (Request $request)
	{
		$start_time = $request->input('start_time', '');
		$end_time   = $request->input('end_time', '');

		// 设定分页和回显搜索参数
		$search_array = [];
		if ($start_time) $search_array['start_time'] = $start_time;
		if ($end_time) $search_array['end_time'] = $end_time;

		// 时间处理，并开始拼接where 条件
		$where = " app_id = '{$this->app_id}' and order_state = 1 and use_collection = 1 and (payment_type!=7 or (payment_type=7 and que_check_state=1))";

		// 时间条件
		if ($start_time) $where .= " and created_at >= '{$start_time}'";
		if ($end_time) {
			$end_time = date('Y-m-d 23:59:59', strtotime($end_time));
			$where    .= " and created_at <= '{$end_time}'";
		}

		$ListInfo = DB::connection('mysql')->table('t_orders')
			->select(DB::raw('created_at,purchase_name,price,payment_type,resource_type,ifnull(distribute_price,0) as distribute_price,ifnull(superior_distribute_price,0) as superior_distribute_price'))
			->whereRaw($where)
			->orderBy('created_at', 'desc')
			->paginate(10);

		//        dump($ListInfo);
		//        exit;

		// 直接处理需要返回的数据
		foreach ($ListInfo as $v) {
			$v->pay = ($v->distribute_price + $v->superior_distribute_price);
			// 商品类型
			if ($v->payment_type == 3 || $v->payment_type == 6) {
				$v->attr = '专栏';
			} else if ($v->payment_type == 4) {
				$v->attr = '团购';
			} else if ($v->payment_type == 11) {
				$v->attr = '活动';
			} else if ($v->payment_type == 9 || $v->payment_type == 10) {
				$v->attr = '会员';
			} else if ($v->payment_type == 7 || $v->payment_type == 8) {
				$v->attr  = '问答';
				$v->pay   = 0;//支出
				$v->price = $v->price - $v->distribute_price - $v->superior_distribute_price;
				if ($v->payment_type == 7) {
					$v->purchase_name = "付费提问";
				} else if ($v->payment_type == 8) {
					$v->purchase_name = "付费偷听";
				}
			} else if ($v->payment_type == 2 || $v->payment_type == 5) {
				if ($v->resource_type == 1) {
					$v->attr = '图文';
				} else if ($v->resource_type == 2) {
					$v->attr = '音频';
				} else if ($v->resource_type == 3) {
					$v->attr = '视频';
				} else if ($v->resource_type == 4) {
					$v->attr = '直播';
				} else if ($v->resource_type == 7) {
					$v->attr = '社群';
				} else {
					$v->attr = '--';
				}
			} else {
				$v->attr = '--';
			}
		}

		//            dump($ListInfo);
		//            exit;
		// 总收入DB::raw('(price-ifnull(distribute_price,0)-ifnull(superior_distribute_price,0))')
		$count_sum = DB::connection('mysql')->table('t_orders')->whereRaw("app_id = '{$this->app_id}' and order_state = 1 and use_collection = 1 and (payment_type!=7 or (payment_type=7 and que_check_state=1)) ")->sum('price');
		$count_sum = $count_sum ? $count_sum : 0;

		// 今日总收入
		$today           = date('Y-m-d', time());
		$count_sum_today = DB::connection('mysql')->table("t_orders")->whereRaw("app_id = '{$this->app_id}' and order_state = 1 and use_collection = 1 and (payment_type!=7 or (payment_type=7 and que_check_state=1)) ")
			->where('created_at', 'like', "%$today%")->sum('price');
		$count_sum_today = $count_sum_today ? $count_sum_today : 0;

		//待结算
		$whereRaw           = " order_state=1 and use_collection=1 and que_check_state=0 and payment_type = 7";
		$count_sum_checking = \DB::table("db_ex_business.t_orders")
			->where('app_id', '=', $this->app_id)
			->whereRaw($whereRaw)
			->sum('price');
		$count_sum_checking = $count_sum_checking ? $count_sum_checking : 0;

		// 总支出
		$pay_info = DB::connection('mysql')->table('t_orders')
			->select(DB::raw('sum(distribute_price) as distribute_price,sum(superior_distribute_price) as superior_distribute_price'))
			->whereRaw("app_id = '{$this->app_id}' and order_state = 1 and use_collection = 1 and payment_type not in (7,8)")
			->first();
		//        dump($pay_info);
		//        exit;
		if ($pay_info) {
			$pay_count = ($pay_info->distribute_price + $pay_info->superior_distribute_price);
		} else {
			$pay_count = 0.00;
		}

		//        dump($pay_info);

		// 可提现余额
		$count_balance = \DB::connection("db_ex_finance")
			->table('t_usable_balance')
			->where('app_id', $this->app_id)
			->first();
		if ($count_balance) {
			$count_balance = $count_balance->account_balance;
		} else {
			$count_balance = 0.00;
		}

		$is_exist = $this->checkQuestionSetting();

		return view('admin.individualModel', [
			'ListInfo'             => $ListInfo,
			'search_array'         => $search_array,
			'count_sum'            => $count_sum,
			'count_sum_today'      => $count_sum_today,
			'pay_count'            => $pay_count,
			'count_balance'        => $count_balance,
			'count_sum_checking'   => $count_sum_checking,
			'has_question_setting' => $is_exist,
		]);
	}

	//查询该客户是否设置了问答
	private function checkQuestionSetting ()
	{
		$result = \DB::table("db_ex_business.t_que_products")
			->where("app_id", '=', $this->app_id)
			->where('state', '!=', 2)
			->get();
		if ($result) {//有问答设置
			return 1;
		} else {//没有问答设置
			return 0;
		}
	}

	// 订单记录
	public function orderList (Request $request)
	{
		$select_type    = $request->input('select_type', '');   // 搜索框内容
		$select_content = trim($request->input('select_content', ''));
		$order_id       = $request->input('order_id', '');    // 订单id
		$order_type     = (int)$request->input('order_type', 0);   // 订单类型
		$distribute     = (int)$request->input('distribute', 0); //分销订单
		// 时间搜索
		$start_time = $request->input('start_time', '');
		$end_time   = $request->input('end_time', '');

		// 设定回显及分页参数
		$search_array = [
			'distribute'     => $distribute,
			'order_type'     => $order_type,
			'select_type'    => $select_type,
			'select_content' => $select_content,
			'order_id'       => $order_id,
			'start_time'     => $start_time,
			'end_time'       => $end_time,
		];
		// 重置时间条件
		if ($end_time) $end_time = date('Y-m-d 23:59:59', strtotime($end_time));

		$order_state = [1, 3];

		/**
		 * 购买同一款产品的所有订单查询
		 * $payment         array|[];
		 * $resource        int|'';
		 * $resource_id     string|'';
		 * $product_id      string|'';
		 */
		$payment_type  = [];
		$resource_type = '';
		$resource_id   = '';
		$product_id    = '';
		$order_info    = null;
		if ($order_id) {
			$order_info = DB::connection('mysql')->table('t_orders')->select('payment_type', 'resource_type', 'product_id', 'resource_id')
				->where('app_id', $this->app_id)->where('order_id', $order_id)->whereIn('order_state', [1, 3])->first();
		}
		if ($order_info) {
			if ($order_info->payment_type == 2 || $order_info->payment_type == 5) {
				//单品 及单品买赠
				if ($order_info->resource_type < 5) {
					$payment_type  = [2, 4];
					$resource_type = $order_info->resource_type;
					$resource_id   = $order_info->resource_id;
					$product_id    = $order_info->resource_id;
				} //社群
				else if ($order_info->resource_type == 7) {
					$payment_type  = [2];
					$resource_type = 7;
					$resource_id   = $order_info->resource_id;
					$product_id    = $order_info->resource_id;
				} // 以防特殊情况
				else {
					$payment_type  = [];
					$resource_type = '';
					$resource_id   = '';
					$product_id    = '';
				}
			} // 产品包(专栏和会员)
			else if ($order_info->payment_type == 3 || $order_info->payment_type == 6) {
				$payment_type  = [3, 6];
				$resource_type = '';
				$resource_id   = '';
				$product_id    = $order_info->product_id;
			} // 问答和偷听
			else if ($order_info->payment_type == 7 || $order_info->payment_type == 8) {
				$payment_type  = [7, 8];
				$resource_type = 0;
				$resource_id   = $order_info->resource_id;
				$product_id    = $order_info->product_id;

			} // 活动报名
			else if ($order_info->resource_type == 5) {
				$payment_type  = [3, 6, 11];
				$resource_type = 5;
				$resource_id   = $order_info->resource_id;
				$product_id    = '';
			} // 直播打赏
			else if ($order_info->payment_type == 12 && $order_info->resource_type == 4) {
				$payment_type  = [12];
				$resource_type = 4;
				$resource_id   = $order_info->resource_id;
				$product_id    = '';
			} // 其他情况 全部默认为空，如果有新订单逻辑  请自行加入
			else {
				$payment_type  = [];
				$resource_type = '';
				$resource_id   = '';
				$product_id    = '';
			}
		} else {
			$payment_type  = [];
			$resource_type = '';
			$resource_id   = '';
			$product_id    = '';
		}

		// 直接查询出对应的数据
		$ListInfo = DB::table("t_orders as v1")
			->select('v1.*', 'v2.wx_nickname', 'v2.wx_avatar')
			->leftjoin('t_users as v2', function($join) use ($select_type, $select_content) {
				$join->on('v1.app_id', '=', 'v2.app_id')
					->on('v1.user_id', '=', 'v2.user_id')
					->where(function($query) use ($select_type, $select_content) {
						if ($select_type == 'name' && $select_content) return $query->where('v2.wx_nickname', 'like', "%{$select_content}%");
					});
			})
			// app_id 及时间条件
			->where("v2.app_id", $this->app_id)
			->where("v1.app_id", $this->app_id)
			->where(function($query) use ($start_time) {
				if ($start_time) return $query->where('v1.created_at', '>', $start_time);
			})
			->where(function($query) use ($end_time) {
				if ($end_time) return $query->where('v1.created_at', '<=', $end_time);
			})
			// 订单状态
			->whereIn("v1.order_state", $order_state)
			// 商品名 及 商品搜索
			->where(function($query) use ($select_type, $select_content) {
				if ($select_type == 'content' && $select_content) return $query->where('purchase_name', 'like', "%{$select_content}%");
			})
			->where(function($query) use ($payment_type) {
				if ($payment_type) return $query->whereIn('payment_type', $payment_type);
			})
			->where(function($query) use ($resource_type) {
				if (is_int($resource_type)) return $query->where('resource_type', $resource_type);
			})
			->where(function($query) use ($resource_id) {
				if ($resource_id) return $query->where('resource_id', $resource_id);

			})
			->where(function($query) use ($product_id) {
				if ($product_id) return $query->where('product_id', $product_id);
			})
			// 是否参与分销
			->where(function($query) use ($distribute) {
				if ($distribute === 1) return $query->whereIn('share_type', [4, 5, 6]);
				if ($distribute === 2) return $query->whereRaw("share_type not in (4,5,6) or share_type is null");
			})
			// 订单类型
			->where(function($query) use ($order_type) {
				if ($order_type === 2) return $query->whereIn('payment_type', [4, 5, 6]);
				if ($order_type === 1) return $query->where('is_renew', 0)->whereNotIn('payment_type', [4, 5, 6]);
				if ($order_type === 3) return $query->where('is_renew', 1);
			})
			->orderBy('created_at', 'desc')
			->paginate(10);

		//        dump($ListInfo);

		// 订单属性
		$product_id_arr     = [];
		$question_id        = [];
		$coupon_user_id     = [];
		$invite_user_id     = [];
		$distribute_user_id = [];
		$platform_id        = [];
		$source_user_id     = [];
		foreach ($ListInfo as $v) {
			// 产品包id
			if (in_array($v->payment_type, [3, 6]) && ($v->resource_type != 5) && $v->product_id) $product_id_arr[] = $v->product_id;

			// 问答订单(结算状态)
			if ($v->payment_type == 7 && (int)$v->order_state === 1 && $v->que_check_state == StringConstants::ORDER_QUE_CHECK_STATE_UNCHECK) $question_id[] = $v->order_id;

			// 优惠券
			if ($v->cu_id && $v->cou_price > 0) $coupon_user_id[] = $v->cu_id;

			// 邀请卡
			if ($v->share_type == 4 && $v->share_user_id) $invite_user_id[] = $v->share_user_id;

			// 推广员
			if ($v->share_type == 5) {
				if ($v->share_user_id) $distribute_user_id[] = $v->share_user_id;
				if ($v->superior_distribute_user_id) $distribute_user_id[] = $v->superior_distribute_user_id;
			}

			// 精选商城的分享人
			if ($v->source === 1) $source_user_id[] = $v->share_user_id;

			// 平台分销
			if ($v->share_type == 6 && $v->superior_distribute_user_id) $platform_id[] = $v->superior_distribute_user_id;
		}

		// 产品包
		$product_info = [];
		if ($product_id_arr) {
			$product_info = DB::table('t_pay_products')->where('app_id', $this->app_id)->whereIn('id', $product_id_arr)->pluck('is_member', 'id');
		}

		//问答订单
		$question_info = [];
		if ($question_id) {
			//去问题表中查询该订单的过期时间
			$question_info = DB::table("t_que_question")->where("app_id", $this->app_id)->whereIn("order_id", $question_id)->pluck('expire_at', 'order_id');
		}

		// 优惠券信息
		$coupon_info = [];
		if ($coupon_user_id) {
			$coupon_info = DB::connection('mysql')->table('t_coupon_user')
				->select('t_coupon_user.id as cu_id', 't_coupon.title as cou_title')
				->leftjoin('t_coupon', function($join) {
					$join->on('t_coupon_user.app_id', '=', 't_coupon.app_id')
						->on('t_coupon_user.cou_id', '=', 't_coupon.id');
				})
				->where('t_coupon.app_id', $this->app_id)
				->where('t_coupon_user.app_id', $this->app_id)
				->whereIn('t_coupon_user.id', $coupon_user_id)
				->pluck('cou_title', 'cu_id');
		}
		// 分销用户信息
		$user_info   = [];
		$user_id_arr = array_merge($invite_user_id, $distribute_user_id);
		if ($user_id_arr) {
			$user_info = DB::connection('mysql')->table('t_users')->where('app_id', $this->app_id)->whereIn('user_id', $user_id_arr)->pluck('wx_nickname', 'user_id');
		}

		// 精选商城分享人
		$source_user_info = DB::table('t_xiaoe_users')->whereIn('user_id', $source_user_id)->pluck('wx_nickname', 'user_id');
		// 合并所有的分销用户信息
		$user_info = array_merge($user_info, $source_user_info);

		// 分销平台信息
		$platform_info = [];
		if ($platform_id) {
			$platform_info = DB::connection('mysql')->table('t_distribute_platform_user')->whereIn('platform_id', $platform_id)->pluck('platform_name', 'platform_id');
		}

		// 信息处理
		foreach ($ListInfo as $v) {
			// 优惠券
			$extra_info = new \stdClass();
			if ($v->cu_id && $coupon_info) {
				if (array_key_exists($v->cu_id, $coupon_info)) {
					$extra_info->has_coupon   = true;
					$extra_info->coupon_name  = $coupon_info["$v->cu_id"];
					$extra_info->coupon_price = $v->cou_price * 0.01;
				} else {
					$extra_info->has_coupon = false;
				}
			} else {
				$extra_info->has_coupon = false;
			}
			// 邀请卡
			if ($v->share_type == 4 && $v->share_user_id && $user_info) {
				if (array_key_exists($v->share_user_id, $user_info)) {
					$extra_info->has_invite         = true;
					$extra_info->invite_user        = $user_info["$v->share_user_id"];
					$extra_info->distribute_percent = $v->distribute_percent * 0.01;
					$extra_info->distribute_price   = $v->distribute_price * 0.01;
				} else {
					$extra_info->has_invite = false;
				}
			} else {
				$extra_info->has_invite = false;
			}
			// 推广员
			if ($v->share_type == 5 && $v->share_user_id && $user_info) {
				if (array_key_exists($v->share_user_id, $user_info)) {
					$extra_info->has_distribute     = true;
					$extra_info->distribute_user    = $user_info["$v->share_user_id"];
					$extra_info->distribute_percent = $v->distribute_percent * 0.01;
					$extra_info->distribute_price   = $v->distribute_price * 0.01;

					// 二级推广员
					if ($v->superior_distribute_user_id && array_key_exists($v->superior_distribute_user_id, $user_info)) {
						$extra_info->has_superior_distribute     = true;
						$extra_info->superior_distribute_user_id = $user_info["$v->superior_distribute_user_id"];
						$extra_info->superior_distribute_percent = $v->superior_distribute_percent * 0.01;
						$extra_info->superior_distribute_price   = $v->superior_distribute_price * 0.01;
					} else {
						$extra_info->has_superior_distribute = false;
					}
				} else {
					$extra_info->has_distribute = false;
				}
			} else {
				$extra_info->has_distribute = false;
			}

			// 平台分销
			if ($v->share_type == 6 && $v->superior_distribute_user_id && $platform_info) {
				if (array_key_exists($v->superior_distribute_user_id, $user_info)) {
					$extra_info->has_platform     = true;
					$extra_info->platform_user    = $user_info["$v->share_user_id"];
					$extra_info->platfrom_percent = $v->superior_distribute_percent * 0.01;
					$extra_info->platform_price   = $v->superior_distribute_price * 0.01;
				} else {
					$extra_info->has_platform = false;
				}
			} else {
				$extra_info->has_platform = false;
			}

			if ($v->use_collection === 0) {
				$extra_info->has_platform   = false;
				$extra_info->has_distribute = false;
				$extra_info->has_invite     = false;
			}

			$v->extra_info = $extra_info;// 订单额外信息

			// 是否参与分销
			if (in_array($v->share_type, [4, 5, 6])) {
				$v->distribute = true;
			} else {
				$v->distribute = false;
			}

			// 订单类型
			if (in_array($v->payment_type, [2, 3, 7, 8, 11, 12])) {
				if ($v->is_renew) {
					$v->type = "续费订单";
				} else {
					$v->type = "普通订单";
				}

			} else if (in_array($v->payment_type, [4, 5, 6])) {
				$v->type = "买赠订单";
			} else {
				$v->type = "其他";
			}

			// 订单状态
			if ($v->order_state == 1) {
				// 问答订单状态 特殊逻辑
				if ($v->payment_type == 7 && $v->que_check_state == StringConstants::ORDER_QUE_CHECK_STATE_UNCHECK) {//未核算
					if (array_key_exists($v->order_id, $question_info)) {
						if ($question_info[ $v->order_id ] >= Utils::getTime()) {
							$v->status = "进行中";
						} else {
							$v->status = "待退款";
						}
					} else {
						$v->status = "已完成";
					}
				} else {
					$v->status = "已完成";
				}
			} else if ($v->order_state == 3) {
				$v->status = "已退款";
			}

			// 商品类型
			if ($v->payment_type == 2 || $v->payment_type == 5) {
				switch ($v->resource_type) {
					case 1 :
						$v->attr = '图文';
						break;
					case 2 :
						$v->attr = '音频';
						break;
					case 3 :
						$v->attr = '视频';
						break;
					case 4 :
						$v->attr = '直播';
						break;
					case 7 :
						$v->attr = '社群';
						break;
					default :
						$v->attr = "--";
				}
			} else if ($v->payment_type == 3 || $v->payment_type == 6) {
				if (array_key_exists($v->product_id, $product_info)) {
					if ($product_info["$v->product_id"]) {
						$v->attr = "会员";
					} else {
						$v->attr = "专栏";
					}
				} else {
					$v->attr = "--";
				}
			} else if ($v->payment_type == 4) {
				$v->attr = '团购';
			} else if ($v->payment_type == 7) {
				$v->attr = '问答';
			} else if ($v->payment_type == 8) {
				$v->attr = '问答偷听';
			} else if ($v->payment_type == 12) {
				$v->attr = '直播打赏';
			} else {
				$v->attr = '--';
			}
			// 活动 单独标记
			if ($v->resource_type == 5) {
				$v->attr = '活动';
			}

			$v->price = $v->price * 0.01;
		}

		//        dump($ListInfo);
		//        exit;

		/*
				exit;
		//dump($order_info);
			  /*  exit;
				// 拼接 where 条件
				$where = " app_id = '$this->app_id' and order_state in (1,3) ";

				//分销条件
				if($distribute == 1) $where.=" and distribute_price > 0 ";
				if($distribute == 2) $where.=" and distribute_price is NULL ";

				// 订单类型
				if ($order_type){
					switch ($order_type){
						case 1:     // 普通订单
							$where .= " and payment_type in (2,3,7,8,9,11,12) ";
							break;
						case 2:     // 买赠订单
							$where .= " and payment_type in (4,5,6) ";
							break;
						default :
							$where .= "";
					}
				}

				// 产品包名搜索
				if ($order_id){
					$info = DB::connection('mysql')->table('t_orders')
						->select('payment_type','resource_id','product_id','purchase_name')
						->where('app_id',$this->app_id)
						->where('order_id',$order_id)
						->first();
					if($info){
						if ($info->payment_type == 2 && $info->resource_id != ""){
							$where .= " and resource_id = '$info->resource_id'";
						} elseif ($info->payment_type != 2 && $info->product_id != "") {
							$where .= " and product_id = '$info->product_id'";
						} else {
							$where .= "";
						}
					}
				}

				// 条件筛选
				$user_info = [];
				$user_id = [];
				if ($select_content){
					if ($select_type == "time"){    // 时间
						$where .= " and created_at like '%$select_content%'";
					} elseif ($select_type == "content") {      // 内容
						$where .= " and purchase_name like binary '%$select_content%'";
					} elseif ($select_type == "name"){      // 昵称
						// 获得可以匹配到的用户信息
						$user_data = DB::connection('mysql')->table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
							->where('app_id', '=', $this->app_id)
							->whereRaw("wx_nickname like binary '%$select_content%'")
							->get();

						if ($user_data){
							foreach ($user_data as $v) {
								$user_info[$v->user_id] = $v;
								$user_id[] = $v->user_id;
							}
							$user_id = array_unique($user_id);
						}
					} else {
						$where .= "";
					}
				}

				$today = date('Y-m-d',time());
				// 如果搜索条件含有用户名
				if ($select_type == 'name' && $select_content){
					$ListInfo = DB::connection('mysql')->table('t_orders')
						->select('order_state','que_check_state','order_id','user_id','product_id','payment_type','resource_type','purchase_name','price','share_type','share_user_id','cu_id','cou_price','distribute_price','distribute_percent','superior_distribute_user_id','superior_distribute_price','superior_distribute_percent','created_at')
						->whereRaw($where)
						->whereIn('user_id',$user_id)
						->orderBy('created_at','desc')
						->paginate(10);
					if ($ListInfo){
						foreach ($ListInfo as $v){
							if (array_key_exists($v->user_id,$user_info)){
								$v->img = $user_info[$v->user_id]->wx_avatar? $user_info[$v->user_id]->wx_avatar : '../images/default.png';
								$v->name = $user_info[$v->user_id]->wx_nickname? $user_info[$v->user_id]->wx_nickname : '未知';
							}else {
								$v->img = '../images/default.png';
								$v->name = '未知';
							}
						}
					}
				}else {
					// 先根据条件查询获得数据，再查询用户数据
					$ListInfo = DB::connection('mysql')->table('t_orders')
						->select('order_state','que_check_state','order_id','user_id','product_id','payment_type','resource_type','purchase_name','price','share_type','share_user_id','cu_id','cou_price','distribute_price','distribute_percent','superior_distribute_user_id','superior_distribute_price','superior_distribute_percent','created_at')
						->whereRaw($where)
						->orderBy('created_at','desc')
						->paginate(10);
					if ($ListInfo){
						foreach ($ListInfo as $v){
							$user_id[] = $v->user_id;
						}
						$user_id = array_unique($user_id);
						// 查询并插入对应的用户数据
						if ($user_id){
							$user_data = DB::connection('mysql')->table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
								->where('app_id', '=', $this->app_id)
								->whereIn('user_id',$user_id)
								->get();
							if ($user_data){
								foreach ($user_data as $v) {
									$user_info[$v->user_id] = $v;
								}
								foreach ($ListInfo as $v){
									if (array_key_exists($v->user_id,$user_info)){
										$v->img = $user_info[$v->user_id]->wx_avatar? $user_info[$v->user_id]->wx_avatar : '../images/default.png';
										$v->name = $user_info[$v->user_id]->wx_nickname? $user_info[$v->user_id]->wx_nickname : '未知';
									}else {
										$v->img = '../images/default.png';
										$v->name = '未知';
									}
								}
							}else {
								foreach($ListInfo as $v){
									$v->img = '../images/default.png';
									$v->name = '未知';
								}
							}

						}
					}

				}*/
		/* // 直接组装需要返回前台的数据
		 foreach ($ListInfo as $v){
			 //是否为分销订单
			 if ($v->distribute_price >0 ){
				 $v->distribute = "参与";
			 }
			 else{
				 $v->distribute = "不参与";
			 }
			 if($v->payment_type == 2 || $v->payment_type == 3 || $v->payment_type == 7
					 || $v->payment_type == 8 || $v->payment_type == 9 || $v->payment_type == 11 || $v->payment_type == 12) {
				 $v->type = "普通订单";
			 } elseif ($v->payment_type == 4 || $v->payment_type == 5 || $v->payment_type == 6 || $v->payment_type == 10){
				 $v->type = "买赠订单";
			 } else {
				 $v->type = "其他";
			 }

			 if($v->order_state == 1){

				 $v->order_state = "已完成";
			 }elseif($v->order_state == 3){
				 $v->order_state = "已退款";
			 }
			 // 商品类型
			 if ($v->payment_type == 3 || $v->payment_type == 6) {
				 //区分是会员还是专栏
				 $product=DB::connection('mysql')->table('t_pay_products')
					 ->where('app_id',$this->app_id)
					 ->where('id', $v->product_id)
					 ->select('is_member')
					 ->first();
				 if($product)
				 {
					 if($product->is_member == 1)    $v->attr="会员";
					 else    $v->attr= "专栏";
				 }
				 else    $v->attr = '专栏';
			 } elseif ($v->payment_type == 4) {
				 $v->attr = '团购';
			 }
			 elseif($v->payment_type == 11){
				 $v->attr = '活动';
			 } elseif($v->payment_type == 7 || $v->payment_type == 8){
				 $v->attr = '问答';
				 if($v->payment_type == 7){//提问
					 if($v->order_state == "已完成" && $v->que_check_state == StringConstants::ORDER_QUE_CHECK_STATE_UNCHECK){//未核算

						 //去问题表中查询该订单的过期时间
						 $question_info = \DB::table("db_ex_business.t_que_question")
							 ->where("app_id",'=',$this->app_id)
							 ->where("order_id",'=',$v->order_id)
							 ->first();
						 if($question_info){
							 if($question_info->expire_at >= Utils::getTime()){
								 $v->order_state = "进行中";
							 }else{
								 $v->order_state = "待退款";
							 }
						 }else{
							 $v->order_state = "进行中";
						 }

					 }
				 }
			 } elseif ($v->payment_type == 2 || $v->payment_type == 5) {
				 if ($v->resource_type == 1){
					 $v->attr = '图文';
				 } elseif ($v->resource_type == 2){
					 $v->attr = '音频';
				 } elseif ($v->resource_type == 3){
					 $v->attr = '视频';
				 } elseif ($v->resource_type == 4){
					 $v->attr = '直播';
				 }elseif ($v->resource_type == 7){
					 $v->attr = '社群';
				 } else {
					 $v->attr = '--';
				 }
			 } elseif($v->payment_type==12)
			 {
				 $v->attr = '直播打赏';
			 }else{
				 $v->attr = '--';
			 }

			 $v->price = $v->price * 0.01;

			 //添加优惠券信息
			 if($v->cu_id)
			 {
				 $coupon_user_info = DB::connection('mysql')->table('t_coupon_user')
					 ->select('cou_id')
					 ->where('app_id', $this->app_id)
					 ->where('id', $v->cu_id)
					 ->first();
				 if($coupon_user_info)    {
					 $coupon_info = DB::connection('mysql')->table('t_coupon')
						 ->select('title')
						 ->where('app_id', $this->app_id)
						 ->where('id', $coupon_user_info->cou_id)
						 ->first();
					 if($coupon_info) {
						 $v->cou_info = [
							 'title' => $coupon_info->title,
							 'price' => $v->cou_price * 0.01
						 ];
					 }

				 }

			 }

			 // 邀请卡数据
			 if (($v->share_type == 4) && ($v->distribute_price > 0)){
				 if (array_key_exists($v->share_user_id,$invite_user_info)){
					 $v->invite_info = [
						 'name' => $invite_user_info[$v->share_user_id],
						 'distribute_percent' => $v->distribute_percent,
						 'distribute_price' => $v->distribute_price*0.01
					 ];
				 }
 //                var_dump($v->invite_info);
 //                exit;
			 }
			 // 分销数据
			 if (($v->share_type == 5) && ($v->distribute_price > 0)){
				 if (array_key_exists($v->share_user_id,$distribute_user_info)){
					 $v->distribute_info = [
						 'name' => $distribute_user_info[$v->share_user_id],
						 'distribute_percent' => $v->distribute_percent,
						 'distribute_price' => $v->distribute_price*0.01
					 ];

					 if (array_key_exists($v->superior_distribute_user_id,$distribute_user_info)){
						 $distribute_arr = $v->distribute_info;
						 $distribute_arr['super_name'] = $distribute_user_info[$v->superior_distribute_user_id];
						 $distribute_arr['superior_distribute_percent'] = $v->superior_distribute_percent;
						 $distribute_arr['superior_distribute_price'] = $v->superior_distribute_price*0.01;

						 $v->distribute_info = $distribute_arr;
					 }
				 }
			 }
		 }*/
		$export_times = ExcelUtils::getMonths();

		return view('admin.orderList', [
			'ListInfo'     => $ListInfo,    // 订单列表
			'search_array' => $search_array,    // 搜索内容回显
			'export_times' => $export_times,            // 导出excel 需要的时间值
		]);
	}

	// 开通记录
	public function payAdmin (Request $request)
	{
		//        dump($request->all());
		//        exit;
		// 类型搜索
		$generate_type = $request->input('generate_type', '');
		// 筛选条件搜索
		$search_type    = $request->input('search_type', '');
		$search_content = trim($request->input('search_content'));
		// 时间搜索
		$start_time = $request->input('start_time', '2016');
		$end_time   = $request->input('end_time', date('Y-m-d H:i:s', time()));
		if (!$start_time) $start_time = '2016';
		if (!$end_time) $end_time = date('Y-m-d H:i:s', time());
		// 名称搜索

		$payment_type = $request->input('payment_type');
		$resource_id  = $request->input('resource_id');
		$product_id   = $request->input('product_id');

		// 设定回显和分页参数
		$search_array = [];
		if ($generate_type) $search_array['generate_type'] = $generate_type;  // 类型
		if ($search_content) {
			if ($search_type) {
				$search_array['search_type'] = $search_type;    // 筛选方式
			} else {
				$search_array['search_type'] = 'name';
			}
			$search_array['search_content'] = $search_content;      // 搜索内容
		}
		if ($start_time) $search_array['start_time'] = $start_time;     // 开始时间
		if ($end_time) $search_array['end_time'] = $end_time;           // 结束时间
		// 内容跳转
		if ($payment_type) $search_array['payment_type'] = $payment_type;
		if ($resource_id) $search_array['resource_id'] = $resource_id;
		if ($product_id) $search_array['product_id'] = $product_id;
		$real_end_time = date('Y-m-d 23:59:59', strtotime($end_time));
		// 开始拼接where条件
		$where = " app_id = '$this->app_id' and is_deleted = 0 and created_at >= '$start_time' and created_at <= '$real_end_time'";
		// 类型
		if ($generate_type == 1) {
			$where .= " and generate_type = 0";
		} else if ($generate_type == 2) {
			$where .= " and generate_type = 1";
		} else {
			$where .= "";
		}

		// 内容条件搜索
		if ($payment_type && $payment_type == 2) {
			if ($product_id) {
				$where .= " and payment_type =2 and product_id = '$product_id'";
			} else if ($product_id == '' && $resource_id) {
				$where .= "and payment_type = 2 and product_id = '$resource_id'";
			} else {
				$where .= "";
			}
		} else if ($payment_type && $payment_type == 3) {
			if ($product_id) {
				$where .= "and payment_type = 3 and product_id = '$product_id'";
			} else {
				$where .= "";
			}
		}

		//条件搜索
		$user_info = [];
		$user_id   = [];
		if ($search_content) {
			if ($search_type == "content") {
				$where .= " and purchase_name like '%$search_content%'";
			} else if ($search_type == "name") {
				// 获得可以匹配到的用户信息
				$user_data = DB::connection('mysql')->table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
					->where('app_id', '=', $this->app_id)
					->whereRaw("wx_nickname like binary '%$search_content%'")
					->get();

				if ($user_data) {
					foreach ($user_data as $v) {
						if ($v->user_id) {
							$user_info[ $v->user_id ] = $v;
							$user_id[]                = $v->user_id;
						}
					}
					$user_id = array_unique($user_id);
				}
			} else {
				$where .= "";
			}
		}
		//        dump($user_id);
		//         dump($where);
		//        exit;
		$today = date('Y-m-d H:i:s', time());
		// 如果筛选条件中有用户名
		if ($search_type == 'name' && $search_content) {
			$ListInfo = DB::connection('mysql')->table('t_purchase')
				->select('user_id', 'payment_type', 'resource_type', 'resource_id', 'product_id', 'purchase_name', 'share_user_id', 'generate_type', 'price', 'created_at')
				->whereRaw($where)
				->whereIn('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->paginate(10);

			if ($ListInfo) {
				// 填充用户信息 以及分享人信息
				$share_user = [];
				foreach ($ListInfo as $v) {
					if (array_key_exists($v->user_id, $user_info)) {
						$v->img  = $user_info[ $v->user_id ]->wx_avatar ? $user_info[ $v->user_id ]->wx_avatar : '../images/default.png';
						$v->name = $user_info[ $v->user_id ]->wx_nickname ? $user_info[ $v->user_id ]->wx_nickname : '未知';
					} else {
						$v->img  = '../images/default.png';
						$v->name = '未知';
					}

					//                    $share_user[] = $v->share_user_id;
					//                    if ($v->share_user_id){
					//                        $share_user[] = $v->share_user_id;
					//                    }
				}
				//                $share_user = array_unique($share_user);
				//                if ($share_user) {
				//                    $share_user_data = DB::connection('mysql')->table('t_users')->select('wx_nickname', 'user_id')
				//                        ->where('app_id', '=', $this->app_id)
				//                        ->whereIn('user_id', $share_user)
				//                        ->get();
				//                    if ($share_user_data) {
				//                        foreach ($share_user_data as $v) {
				//                            $user_info[$v->user_id] = $v;
				//                        }
				//                        foreach ($ListInfo as $v) {
				//                            // 分享人信息
				//                            if (array_key_exists($v->share_user_id, $user_info)) {
				//                                $v->share_name = $user_info[$v->share_user_id]->wx_nickname ? $user_info[$v->share_user_id]->wx_nickname : '未知';
				//                            } else {
				//                                $v->share_name = '';
				//                            }
				//                        }
				//                    }else {
				//                        foreach ($ListInfo as $v){
				//                            $v->share_name = '';
				//                        }
				//                    }
				//                } else {
				//                    foreach ($ListInfo as $v){
				//                        $v->share_name ='';
				//                    }
				//                }
			}

			// 成功下单人数
			$pay_user_sum = DB::connection('mysql')->table('t_purchase')->whereRaw($where)->whereIn('user_id', $user_id)->distinct('user_id')->count('user_id');
		} else {
			// 先根据条件查询获得数据，再查询用户数据
			$ListInfo = DB::connection('mysql')->table('t_purchase')
				->select('user_id', 'payment_type', 'resource_type', 'resource_id', 'product_id', 'purchase_name', 'share_user_id', 'generate_type', 'price', 'created_at')
				->whereRaw($where)
				->orderBy('created_at', 'desc')
				->paginate(10);
			if ($ListInfo) {
				foreach ($ListInfo as $v) {
					$user_id[] = $v->user_id;
					//                    $user_id[] = $v->share_user_id;
				}
				$user_id = array_unique($user_id);
				// 填充用户信息 以及分享人信息
				if ($user_id) {
					$user_data = DB::connection('mysql')->table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->whereIn('user_id', $user_id)
						->get();
					if ($user_data) {
						foreach ($user_data as $v) {
							$user_info[ $v->user_id ] = $v;
						}
						foreach ($ListInfo as $v) {
							// 用户信息
							if (array_key_exists($v->user_id, $user_info)) {
								$v->img  = $user_info[ $v->user_id ]->wx_avatar ? $user_info[ $v->user_id ]->wx_avatar : '../images/default.png';
								$v->name = $user_info[ $v->user_id ]->wx_nickname ? $user_info[ $v->user_id ]->wx_nickname : '未知';
							} else {
								$v->img  = '../images/default.png';
								$v->name = '未知';
							}
							//                            // 分享人信息
							//                            if (array_key_exists($v->share_user_id,$user_info)){
							//                                $v->share_name = $user_info[$v->share_user_id]->wx_nickname? $user_info[$v->share_user_id]->wx_nickname : '未知';
							//                            }else {
							//                                $v->share_name = '';
							//                            }
						}
					}
				}
			}
			// 成功下单人数
			$pay_user_sum = DB::connection('mysql')->table('t_purchase')->whereRaw($where)->distinct('user_id')->count('user_id');
		}

		// 直接组装需要返回前台的数据
		foreach ($ListInfo as $v) {
			// 购买类型
			if ($v->generate_type == 0) {
				$v->generate = "购买";
			} else if ($v->generate_type == 1) {
				$v->generate = "邀请码";
			} else if ($v->generate_type == 2) {
				$v->generate = "体验开通";
			} else if ($v->generate_type == 3) {
				$v->generate = "福利赠送开通";
			} else {
				$v->generate = "其他";
			}

			// 商品类型
			if ($v->payment_type == 3) {
				$v->attr = '专栏';
			} else if ($v->payment_type == 2) {
				if ($v->resource_type == 1) {
					$v->attr = '图文';
				} else if ($v->resource_type == 2) {
					$v->attr = '音频';
				} else if ($v->resource_type == 3) {
					$v->attr = '视频';
				} else if ($v->resource_type == 4) {
					$v->attr = '直播';
				} else if ($v->resource_type == 5) {
					$v->attr = '活动';
				} else if ($v->resource_type == 7) {
					$v->attr = '社群';
				} else {
					$v->attr = '--';
				}
			} else {
				$v->attr = '--';
			}

			$v->price = $v->price * 0.01;
		}
		//        dump($ListInfo);
		//        exit;
		//成功下单人数
		$pay_user_sum = $pay_user_sum ? $pay_user_sum : 0;

		$export_times = ExcelUtils::getMonths();

		return view('admin.payAdmin', [
			'ListInfo'       => $ListInfo,    // 订单列表
			'search_array'   => $search_array,    // 搜索内容回显
			'pay_user_sum'   => $pay_user_sum,    // 下单人数
			'export_times'   => $export_times,            // 导出excel 需要的时间值
			'generate_type'  => $generate_type,
			'search_type'    => $search_type,
			'search_content' => $search_content,

		]);

	}

	//删除订单 `user_id`,`payment_type`,`resource_type`,`product_id
	public function deletePurchase ()
	{//dump(Input::all());
		$user_id            = Input::get('user_id');
		$payment_type       = Input::get('payment_type');
		$resource_type      = Input::get('resource_type');
		$product_id         = Input::get('product_id');
		$resource_id        = Input::get('resource_id');
		$data['is_deleted'] = '1';
		$data['updated_at'] = Utils::getTime();

		if ($resource_id) {
			$result = \DB::table('t_purchase')
				->where('app_id', '=', $this->app_id)
				->where('user_id', '=', $user_id)
				->where('payment_type', '=', $payment_type)
				->where('resource_type', '=', $resource_type)
				->where('product_id', '=', $product_id)
				->where('resource_id', '=', $resource_id)
				->update($data);
		} else {//产品包时
			$result = \DB::table('t_purchase')
				->where('app_id', '=', $this->app_id)
				->where('user_id', '=', $user_id)
				->where('payment_type', '=', $payment_type)
				->where('resource_type', '=', $resource_type)
				->where('product_id', '=', $product_id)
				->update($data);
		}

		if ($result) {
			return response()->json(['code' => 0, 'msg' => '删除成功']);
		} else {
			return response()->json(['code' => 1, 'msg' => '删除失败']);
		}
	}



	//消费记录  -- 弃用
	/* public function dataUsage()
	 {
		 $start=Input::get("start","");
		 $end=Input::get("end","");
		 $ruler=trim(Input::get("ruler"));
		 $search=trim(Input::get("search"));
		 $orderView = trim(Input::get('order_view','1'));

		 $orderBy = 'sumSize';
		 $orderSort = 'desc';
		 switch ($orderView) {
			 case 1:
				 $orderBy = 'sumSize'; $orderSort = 'desc';
				 break;
			 case 2: //fee
				 $orderBy = 'sumSize'; $orderSort = 'desc';
				 break;
			 case 10:
				 $orderBy = 'sumSize'; $orderSort = 'asc';
				 break;
			 case 20: //fee
				 $orderBy = 'sumSize'; $orderSort = 'asc';
				 break;
		 }

		 //获取筛选内容+筛选总流量+筛选时间
		 if( $start && $end ) //从start到end
		 {
			 $duration=$start.'-'.$end;
			 if($search)
			 {
				 if($ruler == 0)//资源名称
				 {
					 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->select(\DB::raw("resource_id,resource_name,resource_type, SUM(size) as sumSize"))
						 ->where("app_id","=",$this->app_id)
						 ->where("use_at",">=",$start)->where("use_at","<=",$end)
						 ->where("resource_name","like","%".$search."%")
						 ->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

					 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->where("app_id","=",$this->app_id)
						 ->where("use_at",">=",$start)->where("use_at","<=",$end)
						 ->where("resource_name","like","%".$search."%")->sum("size");
				 }
			 }
			 else
			 {
				 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->select(\DB::raw("resource_id,resource_name,resource_type, SUM(size) as sumSize"))
					 ->where("app_id","=",$this->app_id)
					 ->where("use_at",">=",$start)->where("use_at","<=",$end)
					 ->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

				 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->where("app_id","=",$this->app_id)
					 ->where("use_at",">=",$start)->where("use_at","<=",$end)
					 ->sum("size");
			 }
		 }
		 elseif( $start && !$end )//从start到当前时间
		 {
			 $duration=$start.'至今';
			 if($search)
			 {
				 if($ruler == 0)
				 {
					 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->select(\DB::raw("resource_id,resource_name,resource_type, SUM(size) as sumSize"))
						 ->where("app_id","=",$this->app_id)->where("use_at",">=",$start)
						 ->where("resource_name","like","%".$search."%")
						 ->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

					 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->where("app_id","=",$this->app_id)->where("use_at",">=",$start)
						 ->where("resource_name","like","%".$search."%")->sum("size");
				 }
			 }
			 else
			 {
				 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->select(\DB::raw("resource_id,resource_name,resource_type, SUM(size) as sumSize"))
					 ->where("app_id","=",$this->app_id)->where("use_at",">=",$start)
					 ->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

				 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->where("app_id","=",$this->app_id)->where("use_at",">=",$start)->sum("size");
			 }
		 }
		 elseif( !$start && $end ) //从世界之源到end
		 {
			 $duration=$end.'之前';
			 if($search)
			 {
				 if($ruler == 0)
				 {
					 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->select(\DB::raw("resource_id,resource_name,resource_type, SUM(size) as sumSize"))
						 ->where("app_id","=",$this->app_id)->where("use_at","<=",$end)
						 ->where("resource_name","like","%".$search."%")
						 ->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

					 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->where("app_id","=",$this->app_id)->where("use_at","<=",$end)
						 ->where("resource_name","like","%".$search."%")->sum("size");
				 }
			 }
			 else
			 {
				 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->select(\DB::raw("resource_id,resource_name,resource_type, SUM(size) as sumSize"))
					 ->where("app_id","=",$this->app_id)->where("use_at","<=",$end)
					 ->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

				 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->where("app_id","=",$this->app_id)->where("use_at","<=",$end)->sum("size");
			 }
		 }
		 else //从世界之源到目前时间
		 {
			 $duration='所有';
			 if($search)
			 {
				 if($ruler == 0)
				 {
					 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->select(\DB::raw("resource_id,resource_name,resource_type,SUM(size) as sumSize"))
						 ->where("app_id","=",$this->app_id)->where("resource_name","like","%".$search."%")
						 ->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

					 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
						 ->where("app_id","=",$this->app_id)->where("resource_name","like","%".$search."%")->sum("size");
				 }
			 }
			 else
			 {
				 $resultInfo=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->select(\DB::raw("resource_id,resource_name,resource_type, SUM(size) as sumSize"))
					 ->where("app_id","=",$this->app_id)->groupBy("resource_id")->orderBy($orderBy,$orderSort)->paginate(10);

				 $resultSize=\DB::connection("mysql_stat")->table("t_usage_hourcount")
					 ->where("app_id","=",$this->app_id)->sum("size");
			 }
		 }//dump($resultInfo);

		 //获得今日和总计
		 $todayResult=\DB::connection("mysql_stat")->select("select ifnull(sum(size),0) as sum from
		 t_usage_hourcount where app_id = ? and date(use_at) = ?",[$this->app_id,date("Y-m-d")]);
		 $todaySize=$todayResult[0]->sum;

		 //所有总和
		 $allResult=\DB::connection("mysql_stat")->select("select ifnull(sum(size),0) as sum from
		 t_usage_hourcount where app_id = ?",[$this->app_id]);
		 $allSize=$allResult[0]->sum;

		 $data=[];
		 if($resultInfo)
		 {

			 foreach ($resultInfo as $key => $value)
			 {
				 //资源名称+类型
				 $data[$key]['resource_name']=$value->resource_name;
				 //`resource_type` int(11) NOT NULL COMMENT '资源类型：0-无、1-音频、2-视频、3-直播、4-图文、5-直播回放'
				 $resourceType = array(
					 '0' => '其他',
					 '1' => '音频',
					 '2' => '视频',
					 '3' => '直播',
					 '4' => '图文',
					 '5' => '直播回放'
				 );
				 $data[$key]['resource_type'] = $resourceType[$value->resource_type];
				 $data[$key]['duration']=$duration;

				 $data[$key]['size']=round($value->sumSize,2);
				 $data[$key]['fee']=round(($value->sumSize/1024)*0.6,2);

			 }
		 }

		 return View('admin.dataUsage',compact('todaySize','allSize','resultInfo','resultSize','data',
			 'search','ruler','start', 'end'));
	 }*/
}
