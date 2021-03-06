<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ExcelUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Mockery\CountValidator\Exception;

class InviteCodeController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	//邀请码页面
	public function inviteCode ()
	{
		$ruler  = trim(Input::get("ruler"));//维度
		$search = trim(Input::get("search"));//搜索内容
		//获取搜索集,总数作为页脚参考
		if (empty($search)) {
			$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
				->orderby("created_at", "desc")->paginate(10);
			$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
				->orderby("created_at", "desc")->count();
		} else {
			if ($ruler == 0) //名称
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
					->where("name", "like", "%" . $search . "%")->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where("name", "like", "%" . $search . "%")
					->where('generate_type', '=', 0)->orderBy("created_at", "desc")->count();
			} else if ($ruler == 1) //批次
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
					->where("id", "like", "%" . $search . "%")->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
					->where("id", "like", "%" . $search . "%")->orderBy("created_at", "desc")->count();
			} else if ($ruler == 2) //创建时间
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
					->where("created_at", "like", "%" . $search . "%")->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
					->where("created_at", "like", "%" . $search . "%")->orderBy("created_at", "desc")->count();
			} else //所有信息
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
					->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 0)
					->orderBy("created_at", "desc")->count();
			}
		}//print_r($allInfo);
		//有搜索集下一步，没有就为空数组
		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				$data[ $key ]['app_id']     = $this->app_id;
				$data[ $key ]['id']         = $value->id;
				$data[ $key ]['name']       = empty($value->name) ? '无' : $value->name;
				$data[ $key ]['card_title'] = empty($value->card_title) ? '无' : $value->card_title;
				$data[ $key ]['allCount']   = $value->count;
				//使用邀请码数量
				$temp                     = \DB::select("
select count(*) as count from t_gift_code where app_id = ? and batch_id = ?
and state = '1' ", [$this->app_id, $value->id]);
				$data[ $key ]['useCount'] = $temp[0]->count;

				//系统生成
				$data[ $key ]['buy_user_id']   = $value->buy_user_id;
				$data[ $key ]['buy_user_name'] = '';

				$data[ $key ]['target_name'] = $value->target_name;
				$data[ $key ]['applier']     = empty($value->applier) ? '' : $value->applier;
				$data[ $key ]['start_at']    = $value->start_at;
				$data[ $key ]['stop_at']     = $value->stop_at;
				if (empty($value->stop_at)) {
					$data[ $key ]['state'] = '正常';
				} else {
					$data[ $key ]['state'] = (Utils::getTime() > $value->stop_at) ? '失效' : '正常';
				}
			}
		}

		//功能启用查询
		$appmodules = AppUtils::getModuleInfo($this->app_id); //dump($appmodule);
		if (!$appmodules) {
			$appmodule['group_buy'] = 0;
			$appmodule['gift_buy']  = 0;
		} else {
			$appmodule['group_buy'] = $appmodules[0]->group_buy ? $appmodules[0]->group_buy : 0;
			$appmodule['gift_buy']  = $appmodules[0]->gift_buy ? $appmodules[0]->gift_buy : 0;
		}

		return View('admin.inviteCode', compact('search', 'ruler', 'allInfo', 'count', 'data', 'appmodule'));
	}

	//团购列表页
	public function groupCode ()
	{
		$ruler  = trim(Input::get("ruler"));//维度
		$search = trim(Input::get("search"));//搜索内容
		//获取搜索集,总数作为页脚参考
		if (empty($search)) {
			$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 1)
				->orderby("created_at", "desc")->paginate(10);
			$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 1)
				->orderby("created_at", "desc")->count();
		} else {
			if ($ruler == 0) //名称
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("name", "like", "%" . $search . "%")->where('generate_type', '=', 1)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("name", "like", "%" . $search . "%")->where('generate_type', '=', 1)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
			} else if ($ruler == 1) //批次
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("id", "like", "%" . $search . "%")->where('generate_type', '=', 1)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("id", "like", "%" . $search . "%")->where('generate_type', '=', 1)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
			} else if ($ruler == 2) //创建时间
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("created_at", "like", "%" . $search . "%")->where('generate_type', '=', 1)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("created_at", "like", "%" . $search . "%")->where('generate_type', '=', 1)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
			} else //所有信息
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 1)
					->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 1)
					->orderBy("created_at", "desc")->count();
			}
		}//print_r($allInfo);
		//有搜索集下一步，没有就为空数组
		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				$data[ $key ]['app_id']     = $this->app_id;
				$data[ $key ]['id']         = $value->id;
				$data[ $key ]['name']       = empty($value->name) ? '无' : $value->name;
				$data[ $key ]['card_title'] = empty($value->card_title) ? '无' : $value->card_title;
				$data[ $key ]['allCount']   = $value->count;
				//使用邀请码数量
				$temp                     = \DB::select("select count(*) as count from t_gift_code where batch_id = ?
                and state = '1' ", [$value->id]);
				$data[ $key ]['useCount'] = $temp[0]->count;

				//用户购买
				$user_Info                     = \DB::select("select user_id , wx_nickname from t_users where app_id = ? and user_id = ?", [AppUtils::getAppID(), $value->buy_user_id]);
				$data[ $key ]['buy_user_id']   = empty($value->buy_user_id) ? '' : $value->buy_user_id;
				$data[ $key ]['buy_user_name'] = empty($user_Info[0]->wx_nickname) ? '无' : $user_Info[0]->wx_nickname;

				$data[ $key ]['target_name'] = $value->target_name;
				$data[ $key ]['applier']     = empty($value->applier) ? '' : $value->applier;
				$data[ $key ]['start_at']    = $value->start_at;
				$data[ $key ]['stop_at']     = $value->stop_at;
				$data[ $key ]['state']       = (Utils::getTime() > $value->stop_at) ? '失效' : '正常';
			}
		}

		//功能启用查询
		$appmodules = AppUtils::getModuleInfo($this->app_id);
		if (!$appmodules) {
			$appmodule['group_buy'] = 0;
			$appmodule['gift_buy']  = 0;
		} else {
			$appmodule['group_buy'] = $appmodules[0]->group_buy ? $appmodules[0]->group_buy : 0;
			$appmodule['gift_buy']  = $appmodules[0]->gift_buy ? $appmodules[0]->gift_buy : 0;
		}

		return View('admin.groupCode', compact('search', 'ruler', 'allInfo', 'count', 'data', 'appmodule'));
	}

	//买赠列表页
	public function giftCode ()
	{
		$ruler  = trim(Input::get("ruler"));//维度
		$search = trim(Input::get("search"));//搜索内容
		//获取搜索集,总数作为页脚参考
		if (empty($search)) {
			$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 2)
				->orderby("created_at", "desc")->paginate(10);
			$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 2)
				->orderby("created_at", "desc")->count();
		} else {
			if ($ruler == 0) //名称
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("name", "like", "%" . $search . "%")->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("name", "like", "%" . $search . "%")->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
			} else if ($ruler == 1) //批次
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("id", "like", "%" . $search . "%")->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("id", "like", "%" . $search . "%")->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
			} else if ($ruler == 2) //创建时间
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("created_at", "like", "%" . $search . "%")->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("created_at", "like", "%" . $search . "%")->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
			} // kevin
			else if ($ruler == 3) //购买人
			{
				$userIdArray = [];
				//获取用户表中微信昵称对应的user_id(可能有多个)
				$userId = \DB::table("t_users")->select('user_id')->where("app_id", "=", $this->app_id)->where("wx_nickname", "like", "%" . $search . "%")->get();
				// 将userid对象数组存到userid数组中
				foreach ($userId as $key => $value) {
					$userIdArray[ $key ] = $value->user_id;
				}
				$allInfo = \DB::table("t_gift_batch")->select()->whereIn("buy_user_id", $userIdArray)->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->whereIn("buy_user_id", $userIdArray)->where('generate_type', '=', 2)
					->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
			} // kevin

			else //所有信息
			{
				$allInfo = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 2)
					->orderBy("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_gift_batch")->select()->where("app_id", "=", $this->app_id)->where('generate_type', '=', 2)
					->orderBy("created_at", "desc")->count();
			}
		}//print_r($allInfo);
		//有搜索集下一步，没有就为空数组
		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				$data[ $key ]['app_id']     = $this->app_id;
				$data[ $key ]['id']         = $value->id;
				$data[ $key ]['name']       = empty($value->name) ? '无' : $value->name;
				$data[ $key ]['card_title'] = empty($value->card_title) ? '无' : $value->card_title;
				$data[ $key ]['allCount']   = $value->count;
				//使用邀请码数量
				$temp                     = \DB::select("select count(*) as count from t_gift_code where batch_id = ?
                and state = '1' ", [$value->id]);
				$data[ $key ]['useCount'] = $temp[0]->count;

				//用户购买
				$user_Info                     = \DB::select("select user_id , wx_nickname from t_users where app_id = ? and user_id = ?", [AppUtils::getAppID(), $value->buy_user_id]);
				$data[ $key ]['buy_user_id']   = empty($value->buy_user_id) ? '' : $value->buy_user_id;
				$data[ $key ]['buy_user_name'] = empty($user_Info[0]->wx_nickname) ? '无' : $user_Info[0]->wx_nickname;

				$data[ $key ]['target_name'] = $value->target_name;
				$data[ $key ]['applier']     = empty($value->applier) ? '' : $value->applier;
				$data[ $key ]['start_at']    = $value->start_at;
				$data[ $key ]['stop_at']     = $value->stop_at;
				$data[ $key ]['state']       = (Utils::getTime() > $value->stop_at) ? '失效' : '正常';
			}
		}

		//功能启用查询
		$appmodules = AppUtils::getModuleInfo($this->app_id);
		if (!$appmodules) {
			$appmodule['group_buy'] = 0;
			$appmodule['gift_buy']  = 0;
		} else {
			$appmodule['group_buy'] = $appmodules[0]->group_buy ? $appmodules[0]->group_buy : 0;
			$appmodule['gift_buy']  = $appmodules[0]->gift_buy ? $appmodules[0]->gift_buy : 0;
		}

		return View('admin.giftCode', compact('search', 'ruler', 'allInfo', 'count', 'data', 'appmodule'));
	}

	//邀请码使用详情列表页
	public function inviteList (Request $request)
	{
		$bid    = $request->input('bid', '');
		$state  = $request->input('state', 0);
		$ruler  = $request->input('ruler');
		$search = $request->input('search', '');
		// 直接查询出对应的数据
		$listInfo = DB::table("t_gift_code as v1")
			->select('v1.*', 'v2.wx_nickname', 'v2.wx_avatar')
			->leftjoin('t_users as v2', function($join) use ($ruler, $search) {
				$join->on('v1.app_id', '=', 'v2.app_id')
					->on('v1.user_id', '=', 'v2.user_id')
					->where(function($query) use ($ruler, $search) {
						if ($ruler < 1 && $search) return $query->where('v2.wx_nickname', 'like', "%{$search}%");
					});
			})
			->where('batch_id', $bid)
			->where('v1.app_id', $this->app_id)
			->where(function($query) use ($ruler, $search) {
				if ($ruler > 0 && $search) return $query->where('v1.code', 'like', "%{$search}%");
			})
			->where(function($query) use ($state) {
				if ($state == 1) $query->where('v1.state', 0);
				if ($state == 2) $query->where('v1.state', 1);

				return $query;
			})
			->paginate(10);

		foreach ($listInfo as $v) {
			if ($v->state === 0) {
				$v->state   = '未使用';
				$v->used_at = '--';
			} else if ($v->state === 1) {
				$v->state = '已使用';
			}
		}
		// 总数量
		$count = $listInfo->total();

		return view("admin.inviteList", [
			'listInfo' => $listInfo,
			'search'   => $search,
			'ruler'    => $ruler,
			'state'    => $state,
			'bid'      => $bid,
			'app_id'   => $this->app_id,
			'count'    => $count,
		]);
	}

	//团购使用详情列表页
	public function groupList ()
	{
		$bid    = Input::get('bid');
		$ruler  = trim(Input::get('ruler'));
		$state  = trim(Input::get('state', 0));
		$search = trim(Input::get('search'));

		$app_id    = $this->app_id;
		$batchInfo = '';
		$allInfo   = '';
		$count     = 0;

		$whereRaw = "app_id = '$app_id' and batch_id = '$bid'";
		if ($state > 0) {
			$state_at = $state - 1;
			$whereRaw .= " and state = '$state_at'";
		}

		if (empty($search)) {
			$allInfo = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->paginate(10);
			$count   = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->count();
		} else {
			//用户名称
			if ($ruler == 0) {
				if ($state == 1) {//未使用
					$allInfo = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->paginate(10);
					$count   = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->count();
				} else {
					$allInfo = \DB::table('t_gift_code')->select()
						->where(\DB::Raw("user_id in (select user_id from t_users where app_id='$this->app_id' and wx_nickname','like','%'.$search.'%')"))
						->whereRaw("$whereRaw")
						->paginate(10);
					$count   = \DB::table('t_gift_code')->select()
						->where(\DB::Raw("user_id in (select user_id from t_users where app_id='$this->app_id' and wx_nickname','like','%'.$search.'%')"))
						->whereRaw("$whereRaw")
						->count();
				}
			} //邀请码 ruler==1
			else {
				$allInfo = \DB::table('t_gift_code')->select()
					->where('code', 'like', '%' . $search . '%')->whereRaw("$whereRaw")->paginate(10);
				$count   = \DB::table('t_gift_code')->select()
					->where('code', 'like', '%' . $search . '%')->whereRaw("$whereRaw")->count();

			}
		}

		//  `qr_code_url` varchar(256) DEFAULT NULL COMMENT '生成二维码的链接（或直接使用的链接）',
		//  `error_counts` int(11) NOT NULL DEFAULT '0' COMMENT '当天匹配出现错误的次数（配合last_tried_at使用）',
		//  `last_tried_at` timestamp NULL DEFAULT NULL COMMENT '上一次尝试的时间',
		//  `lock_time` timestamp NULL DEFAULT NULL COMMENT '锁定时间（如果是当天，表示已锁定）',
		//  `period` int(11) DEFAULT NULL COMMENT '有效期(秒) null则无限制',

		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				//用户信息
				$data[ $key ]['id']         = $value->id;
				$data[ $key ]['code']       = $value->code;
				$data[ $key ]['state']      = $value->state == 1 ? '已使用' : '未使用';
				$data[ $key ]['card_name']  = $value->card_name ? $value->card_name : ' -- ';
				$data[ $key ]['card_wish']  = $value->card_wish ? $value->card_wish : ' -- ';
				$data[ $key ]['updated_at'] = $value->state == 1 ? $value->updated_at : ' -- ';
				$data[ $key ]['period']     = $value->period;
				if ($value->user_id) {
					$userInfo = \DB::select("select wx_nickname,wx_avatar,wx_avatar_wx from t_users where user_id = '$value->user_id'");
					//dump($userInfo);
					$data[ $key ]['user_id']     = $value->user_id;
					$data[ $key ]['wx_nickname'] = !empty($userInfo[0]->wx_nickname) ? $userInfo[0]->wx_nickname : '';//$value->wx_nickname;//
					$data[ $key ]['wx_avatar']   = !empty($userInfo[0]->wx_avatar) ? $userInfo[0]->wx_avatar : $userInfo[0]->wx_avatar_wx;
				} else {
					$data[ $key ]['user_id']     = '';
					$data[ $key ]['wx_nickname'] = '';
					$data[ $key ]['wx_avatar']   = '../images/default.png';
				}

			}
		}

		return view("admin.groupList", compact('search', 'ruler', 'state', 'allInfo', 'bid', 'app_id', 'data', 'count'));

	}

	//买赠使用详情列表页
	public function giftList ()
	{
		$bid    = Input::get('bid');
		$ruler  = trim(Input::get('ruler'));
		$state  = trim(Input::get('state', 0));
		$search = trim(Input::get('search'));

		$app_id = $this->app_id;

		$allInfo = '';
		$count   = 0;

		$whereRaw = "app_id = '$this->app_id' and batch_id = '$bid'";
		if ($state > 0) {
			$state_at = $state - 1;
			$whereRaw .= " and state = '$state_at'";
		}

		if (empty($search)) {
			$allInfo = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->paginate(10);
			$count   = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->count();
		} else {
			//用户名称
			if ($ruler == 0) {
				if ($state == 1) {//未使用
					$allInfo = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->paginate(10);
					$count   = \DB::table('t_gift_code')->select()->whereRaw("$whereRaw")->count();
				} else {
					$allInfo = \DB::table('t_gift_code')->select()
						->where(\DB::Raw("user_id in (select user_id from t_users where app_id='$this->app_id' and wx_nickname','like','%'.$search.'%')"))
						->whereRaw("$whereRaw")
						->paginate(10);
					$count   = \DB::table('t_gift_code')->select()
						->where(\DB::Raw("user_id in (select user_id from t_users where app_id='$this->app_id' and wx_nickname','like','%'.$search.'%')"))
						->whereRaw("$whereRaw")
						->count();
				}
			} //邀请码 ruler==1
			else {
				$allInfo = \DB::table('t_gift_code')->select()
					->where('code', 'like', '%' . $search . '%')->whereRaw("$whereRaw")->paginate(10);
				$count   = \DB::table('t_gift_code')->select()
					->where('code', 'like', '%' . $search . '%')->whereRaw("$whereRaw")->count();

			}
		}

		//  `qr_code_url` varchar(256) DEFAULT NULL COMMENT '生成二维码的链接（或直接使用的链接）',
		//  `error_counts` int(11) NOT NULL DEFAULT '0' COMMENT '当天匹配出现错误的次数（配合last_tried_at使用）',
		//  `last_tried_at` timestamp NULL DEFAULT NULL COMMENT '上一次尝试的时间',
		//  `lock_time` timestamp NULL DEFAULT NULL COMMENT '锁定时间（如果是当天，表示已锁定）',
		//  `period` int(11) DEFAULT NULL COMMENT '有效期(秒) null则无限制',

		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				//用户信息
				$data[ $key ]['id']   = $value->id;
				$data[ $key ]['code'] = $value->code;
				if ($value->state == 0) {
					$data[ $key ]['state'] = '未使用';
				} else if ($value->state == 1) {
					$data[ $key ]['state'] = '已使用';
				} else if ($value->state == 2) {
					$data[ $key ]['state'] = '已作废';
				}
				//                $data[$key]['state'] = $value->state == 1? '已使用' : '未使用';
				$data[ $key ]['card_name'] = $value->card_name ? $value->card_name : ' -- ';
				$data[ $key ]['card_wish'] = $value->card_wish ? $value->card_wish : ' -- ';
				$data[ $key ]['used_at']   = $value->state == 1 ? $value->used_at : ' -- ';
				$data[ $key ]['period']    = $value->period;
				if ($value->user_id) {
					$userInfo = \DB::select("select wx_nickname,wx_avatar,wx_avatar_wx from t_users where user_id = '$value->user_id'");
					//dump($userInfo);
					$data[ $key ]['user_id']     = $value->user_id;
					$data[ $key ]['wx_nickname'] = !empty($userInfo[0]->wx_nickname) ? $userInfo[0]->wx_nickname : '';//$value->wx_nickname;//
					$data[ $key ]['wx_avatar']   = !empty($userInfo[0]->wx_avatar) ? $userInfo[0]->wx_avatar : $userInfo[0]->wx_avatar_wx;
				} else {
					$data[ $key ]['user_id']     = '';
					$data[ $key ]['wx_nickname'] = '';
					$data[ $key ]['wx_avatar']   = '../images/default.png';
				}

			}
		}

		return view("admin.giftList", compact('search', 'ruler', 'state', 'allInfo', 'bid', 'app_id', 'data', 'count'));

	}

	//作废邀请码
	public function giftInvalid ()
	{

		$code = Input::get('code');

		$res = \DB::table('t_gift_code')
			->where('app_id', $this->app_id)
			->where('code', $code)
			->update(['state' => 2]);
		if ($res == 1)
			return response()->json(['code' => 0, 'msg' => 'ok']);
		else
			return response()->json(['code' => 1, 'msg' => 'db update error']);
	}

	//新增邀请码页面
	public function addInviteCode ()
	{
		$packages = \DB::select("select * from t_pay_products where app_id = ? and price <> '0' and state < 2 and is_member=0  
        order by created_at DESC ", [$this->app_id]);

		$group_data = \DB::select('select * from t_group_config where app_id = ?
        order by created_at', [$this->app_id]);

		$result = AppUtils::getModuleInfo($this->app_id);

		return View("admin.addInviteCode", compact('packages', 'group_data', 'result'));
	}

	//获取资源,不要免费
	public function getRes ()
	{
		$type = Input::get("type");
		if ($type == 0)//专栏
		{
			$res = \DB::select("select id,name,price,img_url from t_pay_products where app_id = ? and state < 2 
            and price <> '0' and is_member=0 order by created_at", [$this->app_id]);
		} else if ($type == 1)//音频
		{
			$res = \DB::select("select id,title,piece_price,img_url from t_audio where app_id = ? and audio_state<2 
            and payment_type = '2' and piece_price <> '0' order by created_at", [$this->app_id]);
		} else if ($type == 2)//视频
		{
			$res = \DB::select("select id,title,piece_price,img_url from t_video where app_id = ? and video_state<2 
            and payment_type = '2' and piece_price <> '0' order by created_at", [$this->app_id]);
		} else if ($type == 3)//图文
		{
			$res = \DB::select("select id,title,piece_price,img_url from t_image_text where app_id = ? and display_state<2 
            and payment_type = '2' and piece_price <> '0' order by created_at", [$this->app_id]);
		} else if ($type == 4)//直播
		{
			$res = \DB::select("select id,title,piece_price,img_url from t_alive where app_id = ? and state<2 
            and payment_type = '2' and piece_price <> '0' order by created_at", [$this->app_id]);
		} else if ($type == 5)//会员
		{
			$res = \DB::select("select id,name,price,img_url from t_pay_products where app_id = ? and state < 2 
            and price <> '0' and is_member=1 order by created_at", [$this->app_id]);
		} else if ($type == 7)//社群
		{
			$res = \DB::select("select id,title,piece_price,img_url from t_community where app_id = ? and community_state < 2 
             order by created_at", [$this->app_id]);
		}

		return response()->json(['res' => $res]);
	}

	//新增邀请码操作
	public function doAddInvite ()
	{
		set_time_limit(300);// timeout 300s

		//批次表
		$batch = Input::get("params");
		//限定每次最多生成1000个邀请码
		if ($batch['count'] > 1000) return response()->json(['ret' => 2]);

		$batch['app_id'] = $this->app_id;
		if (!empty($batch['group_config_id'])) {
			$batch['generate_type'] = 1;
		} else {
			$batch['generate_type'] = 0;
		}
		//获取有效时长
		if ($batch['product_id']) {
			$product_period  = \DB::select("select period from t_pay_products where app_id = ? and id = ?", [$this->app_id, $batch['product_id']])[0];
			$batch['period'] = $product_period ? $product_period->period : null;
		}
		$batch['created_at'] = Utils::getTime();
		$batch['updated_at'] = Utils::getTime();
		$insertBatch         = \DB::table("t_gift_batch")->insertGetId($batch);

		$wxAppIdinfo    = \DB::connection("mysql_config")->select("select wx_app_id,use_collection from t_app_conf 
            where app_id = ? and wx_app_type = 1", [$this->app_id])[0];
		$wxAppId        = $wxAppIdinfo->wx_app_id;
		$use_collection = $wxAppIdinfo->use_collection;
		//邀请码表
		for ($i = 0; $i < $batch['count']; $i++) {
			$code                     = [];
			$code['app_id']           = $this->app_id;
			$code['batch_id']         = $insertBatch;
			$code['first_half_code']  = Utils::getRandom(12, 'NUMBER');
			$code['second_half_code'] = Utils::getRandom(4, 'NUMBER');
			$code['code']             = $code['first_half_code'] . $code['second_half_code'];
			//获取分享链接
			if ($use_collection == 1) {
				$code['qr_code_url'] = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") . "/" . $this->app_id . "/giftcode/" . $code['code'];

			} else {
				$code['qr_code_url'] = AppUtils::getUrlHeader($this->app_id) . $wxAppId . "." . env("DOMAIN_NAME") . "/giftcode/" . $code['code'];
			}

			if ($batch['product_id']) $code['period'] = $batch['period'];

			$code['state']      = 0;
			$code['created_at'] = $code['updated_at'] = Utils::getTime();

			//防止重复
			try {
				$insertCode = \DB::table("t_gift_code")->insert($code);
			} catch (Exception $e) {
				$i--;
			}
		}

		if ($insertCode) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//导出邀请码 excel
	public function exportData (Request $request)
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(300); //超时时间300s
		$id      = $request->input("id");
		$version = $request->input("version", 2003);
		//内容
		$gift_batch_info = DB::select("
            SELECT
                id,name,card_title,card_desc,applier,reason,start_at,stop_at,created_at
            FROM
                t_gift_batch
            WHERE
                app_id = ?
            AND id = ?
        ", [$this->app_id, $id])[0];

		$result = DB::select("
            SELECT g.app_id, g.batch_id,g.code,g.qr_code_url,g.state,g.user_id,u.wx_nickname FROM
            (select * from t_gift_code where app_id = ? and batch_id = ?) g
            LEFT JOIN (
                SELECT * FROM t_users WHERE app_id = ? and user_id in (
                  select user_id from t_gift_code where app_id = ? and batch_id = ?
                )
            ) u ON g.app_id = u.app_id and g.user_id = u.user_id
        ", [$this->app_id, $id, $this->app_id, $this->app_id, $id]);

		$cellData[] = ['批次', '批次名称', '邀请码', '邀请码链接', '是否使用', '使用人id', '使用人昵称', '邀请码标题', '使用须知', '申请人',
			'申请原因', '生效时间', '失效时间', '生成时间'];
		foreach ($result as $key => $value) //每行
		{
			//批次//批次名称//邀请码//链接//是否使用
			$value->state = $value->state == 0 ? '未使用' : '已使用';

			//使用人id//使用人昵称//标题//内容//申请人//申请原因//生效时间//失效时间//生成时间
			$cellData[] = [
				$value->batch_id,
				$gift_batch_info->name,
				"'" . $value->code,
				$value->qr_code_url,
				$value->state,
				$value->user_id,
				$value->wx_nickname,
				$gift_batch_info->card_title,
				$gift_batch_info->card_desc,
				$gift_batch_info->applier,
				$gift_batch_info->reason,
				$gift_batch_info->start_at,
				$gift_batch_info->stop_at,
				$gift_batch_info->created_at,
			];
		}
		$title = "邀请码_批次" . $id;
		// 处理数据格式
		$excelData = ExcelUtils::getCorrectData($cellData);

		// 下载
		if ($excelData) {
			if ($version == 2003) {
				ExcelUtils::downExcel($title, $excelData);
			} else {
				ExcelUtils::downloadGbkCsv($title, $excelData);
			}
		}
	}

}





