<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class RedPacketAdminController extends Controller
{
	private $request;
	private $app_id;//系统的app_id

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	public function redPacket ()
	{

		$packet_attr    = Input::get('packet_attr', '');
		$search_content = Input::get('search_content', '');
		$state          = Input::get('state', '-1');

		if (empty($search_content)) {
			//没有搜索内容
			if ($state == -1) {
				//全部状态
				$redPacket = \DB::table('t_lucky_money_orders')
					->select('*')
					->where('app_id', '=', $this->app_id)
					->orderby('created_at', 'desc')
					->groupby('id')
					->paginate(10);
			} else {
				//其他状态
				$redPacket = \DB::table('t_lucky_money_orders')
					->select('*')
					->where('app_id', '=', $this->app_id)
					->where('state', '=', $state)
					->orderby('created_at', 'desc')
					->groupby('id')
					->paginate(10);
			}
		} else {
			//有搜索内容
			if ($state == -1) {
				//全部状态
				if ($packet_attr == 'wx_nickname') {
					//查昵称
					//先查出符合搜索条件的用户id
					$user_result = \DB::table('t_users')->select('user_id')
						->where('app_id', '=', $this->app_id)
						->where($packet_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($packet_attr)-length('$search_content')")//按匹配度查询
						->get();

					//将id存入数组
					$user_id = [];
					foreach ($user_result as $value) {
						$user_id[] = $value->user_id;
					}

					//查询符合条件的红包订单
					$redPacket = \DB::table('t_lucky_money_orders')->select('*')
						->where('app_id', '=', $this->app_id)
						->whereIn('share_user_id', $user_id)
						->orderby('created_at', 'desc')
						->groupby('id')
						->paginate(10);

				} else {
					//所有状态
					//查 发送时间 或者 外部订单号
					$redPacket = \DB::table('t_lucky_money_orders')
						->select('*')
						->where('app_id', '=', $this->app_id)
						->where($packet_attr, 'like', '%' . $search_content . '%')
						->orderby('created_at', 'desc')
						->groupby('id')
						->paginate(10);
				}
			} else {
				//其他状态
				if ($packet_attr == 'wx_nickname') {
					//查昵称
					//先查出符合搜索条件的用户id
					$user_result = \DB::table('t_users')->select('user_id')
						->where('app_id', '=', $this->app_id)
						->where($packet_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($packet_attr)-length('$search_content')")//按匹配度查询
						->get();

					//将id存入数组
					$user_id = [];
					foreach ($user_result as $value) {
						$user_id[] = $value->user_id;
					}

					$redPacket = \DB::table('t_lucky_money_orders')->select('*')
						->where('app_id', '=', $this->app_id)
						->where('state', '=', $state)
						->whereIn('share_user_id', $user_id)
						->orderby('created_at', 'desc')
						->groupby('id')
						->paginate(10);

				} else {
					//查 发送时间 或者 外部订单号
					$redPacket = \DB::table('t_lucky_money_orders')->select('*')
						->where('app_id', '=', $this->app_id)
						->where('state', '=', $state)
						->where($packet_attr, 'like', '%' . $search_content . '%')
						->orderby('created_at', 'desc')
						->groupby('id')
						->paginate(10);
				}
			}
		}

		$user_info = [];
		foreach ($redPacket as $key => $value) {
			$temp = \DB::table('t_users')
				->select('user_id', 'wx_nickname', 'wx_avatar')
				->where('app_id', '=', $this->app_id)
				->where('user_id', '=', $value->share_user_id)
				->first();
			if (empty($temp)) {
				$temp              = new \stdClass();
				$temp->user_id     = '';
				$temp->wx_nickname = '无';
				$temp->wx_avatar   = '';
			}
			$user_info[ count($user_info) ] = $temp;
		}

		//查询通过分享开通会员的用户头像和昵称
		$openUsers = [];
		foreach ($redPacket as $order) {
			//查询用户昵称和头像
			$openUser = \DB::table('t_users')
				->select('user_id', 'wx_nickname', 'wx_avatar')
				->where('app_id', '=', $this->app_id)
				->where('user_id', '=', $order->buy_user_id)
				->first();
			if (empty($openUser)) {
				$openUser              = new \stdClass();
				$openUser->user_id     = '';
				$openUser->wx_nickname = '无';
				$openUser->wx_avatar   = '';
			}
			$openUsers[ count($openUsers) ] = $openUser;
		}

		return View('admin.redPacketAdmin', compact('redPacket', 'user_info', 'openUsers', 'packet_attr', 'search_content', 'state'));
	}

	//导出购买订单数据
	public function exportExcel ()
	{
		$start_time = Input::get('start_time');
		$end_time   = Input::get('end_time');

		Excel::create($start_time . "至" . $end_time . "红包记录", function($excel) use ($start_time, $end_time) {
			$excel->sheet("红包记录", function($sheet) use ($start_time, $end_time) {
				//标题
				$rows   = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
				$widths = [30, 20, 30, 30, 20, 30, 20, 80, 30, 20];
				$th     = ['领取人id', '领取人昵称', '领取人头像', '开通人id', '开通人昵称', '开通人头像', '红包金额(分)', '红包状态(0-待发送；1-已发送；2-领取成功；3-用户多次不领取红包，不再尝试发送；4-多次发送失败，不再尝试发送；5、发送失败，待重试)', '外部订单号', '红包创建时间'];
				for ($i = 0; $i < count($rows); $i++) {
					//宽度
					$sheet->setWidth([$rows[ $i ] => $widths[ $i ]]);
					$sheet->cell($rows[ $i ] . '1', function($cell) use ($th, $i) {
						$cell->setValue($th[ $i ]);
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
					});
				}

				//内容

				$result = \DB::table('t_lucky_money_orders')->select('*')->where('app_id', '=', $this->app_id)
					->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time)
					->orderby('created_at', 'asc')->get();

				//获取所有的 接受红包用户id  开通用户id
				$user_id = \DB::select('select distinct share_user_id,buy_user_id from t_lucky_money_orders where app_id = ? and created_at >= ? and created_at <= ?', [$this->app_id, $start_time, $end_time]);

				$share_user_info = [];
				$buy_user_info   = [];
				//查找对应id的user_id 头像 昵称
				foreach ($user_id as $value) {
					$share_user_result = \DB::select('select user_id,wx_avatar,wx_nickname from t_users where app_id = ? and user_id = ?', [$this->app_id, $value->share_user_id]);
					if (empty($share_user_result)) {
						$share_user_result                        = new \stdClass();
						$share_user_result->user_id               = $value->share_user_id;
						$share_user_result->wx_avatar             = '未找到头像';
						$share_user_result->wx_nickname           = '未找到昵称';
						$share_user_info[ $value->share_user_id ] = $share_user_result;
					} else {
						$share_user_info[ $value->share_user_id ] = $share_user_result[0];
					}

					$buy_user_result = \DB::select('select user_id,wx_avatar,wx_nickname from t_users where app_id = ? and user_id = ?', [$this->app_id, $value->buy_user_id]);
					if (empty($buy_user_result)) {
						$buy_user_result                      = new \stdClass();
						$buy_user_result->user_id             = $value->buy_user_id;
						$buy_user_result->wx_avatar           = '未找到头像';
						$buy_user_result->wx_nickname         = '未找到昵称';
						$buy_user_info[ $value->buy_user_id ] = $buy_user_result;
					} else {
						$buy_user_info[ $value->buy_user_id ] = $buy_user_result[0];
					}
				}

				foreach ($result as $key => $value) //每行
				{
					//分享人id
					$sheet->cell('A' . ($key + 2), function($cell) use ($value) {
						$cell->setValue($value->share_user_id);
						$cell->setAlignment('center');
					});
					//分享人昵称
					$sheet->cell('B' . ($key + 2), function($cell) use ($value, $share_user_info) {
						$cell->setValue($share_user_info[ $value->share_user_id ]->wx_nickname);
						$cell->setAlignment('center');
					});
					//分享人头像
					$sheet->cell('C' . ($key + 2), function($cell) use ($value, $share_user_info) {
						$cell->setValue($share_user_info[ $value->share_user_id ]->wx_avatar);
						$cell->setAlignment('center');
					});
					//开通人id
					$sheet->cell('D' . ($key + 2), function($cell) use ($value) {
						$cell->setValue($value->buy_user_id);
						$cell->setAlignment('center');
					});
					//开通人昵称
					$sheet->cell('E' . ($key + 2), function($cell) use ($value, $buy_user_info) {
						$cell->setValue($buy_user_info[ $value->buy_user_id ]->wx_nickname);
						$cell->setAlignment('center');
					});
					//开通人头像
					$sheet->cell('F' . ($key + 2), function($cell) use ($value, $buy_user_info) {
						$cell->setValue($buy_user_info[ $value->buy_user_id ]->wx_avatar);
						$cell->setAlignment('center');
					});
					//红包金额
					$sheet->cell('G' . ($key + 2), function($cell) use ($value) {
						$cell->setValue($value->money);
						$cell->setAlignment('center');
					});
					//红包状态
					$sheet->cell('H' . ($key + 2), function($cell) use ($value) {
						$cell->setValue($value->state);
						$cell->setAlignment('center');
					});
					//外部订单号
					$sheet->cell('I' . ($key + 2), function($cell) use ($value) {
						$cell->setValue($value->lucky_money_send_id);
						$cell->setAlignment('center');
					});
					//红包创建时间
					$sheet->cell('J' . ($key + 2), function($cell) use ($value) {
						$cell->setValue($value->created_at);
						$cell->setAlignment('center');
					});
				}
			});
		})->export("csv");

	}

}





