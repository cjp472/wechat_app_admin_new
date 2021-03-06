<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ExcelUtils;
use App\Http\Controllers\Tools\ImageUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ActivityController extends Controller
{
	//活动管理首页-进行中
	public function activityManage ()
	{
		//从活动表t_activity中筛选出还未结束的活动

		$search_content = Input::get('searchContent', '');

		$activityList = $this->getActivityList($search_content, StringConstants::ACTIVITY_GOING);

		$package_list = [];

		$actor_successed_num_list  = [];//获取活动的报名成功的人数
		$actor_confirming_num_list = [];//获取活动报名的待审核人数
		$actor_all_num_list        = [];////获取报名的全部人数
		if ($activityList) {
			foreach ($activityList as $key => $activity) {
				//查询所属专栏
				$packageInfo = $this->getActivityPackage($activity->id);
				if ($packageInfo) {
					$package_list[ $key ] = $packageInfo;
					$product_id           = $packageInfo->product_id;
				} else {
					$package_list[ $key ] = '';
					$product_id           = '';
				}
				$activity->activity_url = $this->getActivityUrl($activity->id, $product_id);
				$activityList[ $key ]   = $activity;
			}
			//获取活动的报名成功的人数
			$actor_successed_num_list = $this->getActivityActorNumsList($activityList, StringConstants::ACTIVITY_CONFIRM_PASS);
			//获取活动报名的待审核人数
			$actor_confirming_num_list = $this->getActivityActorNumsList($activityList, StringConstants::ACTIVITY_CONFIRMING);
			//获取报名的全部人数
			$actor_all_num_list = $this->getActivityActorNumsList($activityList, StringConstants::ACTOR_ALL);//全部的用户数

		}
		$type = 0;//活动进行中页面

		return view('admin.activity.activityManage', compact('activityList', 'package_list', 'type', 'search_content', 'actor_successed_num_list', 'actor_confirming_num_list', 'actor_all_num_list'));
	}

	//活动管理首页-已结束

	private function getActivityList ($searchContent, $type)
	{

		$now      = date('Y-m-d H:i:s', time());
		$app_id   = AppUtils::getAppID();
		$whereRaw = '';

		if ($type == StringConstants::ACTIVITY_GOING) {//进行中
			$whereRaw .= "activity_end_at >='" . "$now'";
			$whereRaw .= " and activity_state !=2";
		} else {//已结束
			$whereRaw .= " (activity_end_at <='" . "$now' ";
			$whereRaw .= " or activity_state = 2 )";

		}
		if (!Utils::isEmptyString($searchContent)) {
			$whereRaw .= " and title like '" . "%" . $searchContent . "%'";
		}
		$activityList = \DB::table("db_ex_business.t_activity")
			->where('app_id', '=', $app_id)
			->whereRaw($whereRaw)
			->orderBy('created_at', 'desc')
			->paginate(10);

		return $activityList;
	}

	//创建活动页面

	private function getActivityPackage ($activity_id)
	{
		$app_id                = AppUtils::getAppID();
		$activity_package_info = \DB::table('db_ex_business.t_pro_res_relation')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $activity_id)
			->where('resource_type', '=', 5)
			->where('relation_state', '=', 0)
			->first();

		return $activity_package_info;
	}

	//编辑活动页

	private function getActivityUrl ($activity_id, $package_id)
	{
		$app_id       = AppUtils::getAppID();
		$app_info     = $this->getAppInfo();
		$activity_url = '';
		if ($app_info) {
			$use_collection = $app_info[0]->use_collection;
			$wx_id          = $app_info[0]->wx_app_id;

			if ($use_collection == 1)//个人版
			{
				$activity_url = AppUtils::getUrlHeader($app_id) . env("DOMAIN_DUAN_NAME") . Utils::contentUrl("", 2, 5, $activity_id, $package_id, $app_id);
			} else {
				$activity_url = AppUtils::getUrlHeader($app_id) . $wx_id . "." . env("DOMAIN_NAME") . Utils::contentUrl("", 2, 5, $activity_id, $package_id, $app_id);
			}
		}

		return $activity_url;
	}

	//活动报名管理主界面

	private function getAppInfo ()
	{

		$app_id   = AppUtils::getAppID();
		$app_info = \DB::connection("mysql_config")->select("select wx_app_id,use_collection from t_app_conf where app_id = '$app_id' and wx_app_type = 1");

		if ($app_info) {
			return $app_info;
		} else {
			return -1;
		}
	}

	//活动报名人员列表界面

	private function getActivityActorNumsList ($activityList, $state)
	{

		$actor_successed_num_list = [];
		//在表t_activity_actor中查询activity_id的state=$state的人数总和
		foreach ($activityList as $key => $activity) {
			$activity_id                = $activity->id;
			$userList                   = $this->getActivityActorByState($activity_id, $state);
			$actor_successed_num_list[] = count($userList);
		}

		return $actor_successed_num_list;
	}

	//获取签到管理数据

	private function getActivityActorByState ($activity_id, $activity_state)
	{

		$app_id   = AppUtils::getAppID();
		$whereRaw = '1=1 and pay_state in(-1,1) ';
		if ($activity_state == 1) {//已报名成功
			$whereRaw .= ' and (state=' . StringConstants::ACTIVITY_CONFIRM_PASS . ' or state=' . StringConstants::ACTIVITY_CONFIRM_SINGED . ') ';

		} else if ($activity_state == 0) {
			$whereRaw .= ' and state=' . StringConstants::ACTIVITY_CONFIRMING . ' and pay_state=-1';
		}
		$user_id_list = [];
		//查找用户
		$actor_list = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->whereRaw($whereRaw)
			->orderBy('created_at', 'desc')
			->get();
		if ($actor_list) {
			foreach ($actor_list as $key => $actor) {
				$user_id_list[] = $actor->user_id;
			}
		}

		return $user_id_list;
	}

	// 签到管理数据导出

	public function activityListEnd ()
	{
		$search_content = Input::get('searchContent', '');

		$activityList = $this->getActivityList($search_content, StringConstants::ACTIVITY_END);

		if ($activityList) {
			foreach ($activityList as $key => $activity) {
				//查询所属专栏
				$packageInfo = $this->getActivityPackage($activity->id);
				if ($packageInfo) {
					$package_list[ $key ] = $packageInfo;
					$product_id           = $packageInfo->product_id;

				} else {
					$package_list[ $key ] = '';
					$product_id           = '';
				}

				$activity->activity_url = $this->getActivityUrl($activity->id, $product_id);
				$activityList[ $key ]   = $activity;
			}
		}

		$type = 1;//活动已结束页面

		return view('admin.activity.activityManage', compact('activityList', 'package_list', 'type', 'search_content'));
	}

	public function createActivity ()
	{
		//获取所有的产品包
		$package_list = $this->getAllPackages();

		$type = 0;//新增活动页面

		return view('admin.activity.createActivity', compact('package_list', 'type'));
	}

	//新增活动

	private function getAllPackages ()
	{
		//查询所有的包
		$package_list = \DB::table('t_pay_products')
			->where('app_id', '=', AppUtils::getAppID())
			->where('state', '<', '2')
			->orderby('created_at', 'desc')
			->get();

		return $package_list;
	}

	//更新活动

	public function editActivity ()
	{
		$id     = Input::get("id");
		$app_id = AppUtils::getAppID();
		//查询活动信息
		$activityInfo = \DB::table('db_ex_business.t_activity')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		//获取所有的产品包
		$package_list = $this->getAllPackages();

		//页面类型
		$type = 1;//编辑活动页面

		//活动所属专栏
		$activity_package_info = $this->getActivityPackage($id);

		//获取活动票种列表
		$pay_ticket_list  = \DB::table("t_activity_ticket")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $id)
			->where('state', '=', '0')
			->where('ticket_price', '!=', '0')
			->get();
		$free_ticket_list = \DB::table("t_activity_ticket")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $id)
			->where('state', '=', '0')
			->where('ticket_price', '=', '0')
			->get();

		return view('admin.activity.createActivity', compact('free_ticket_list', 'pay_ticket_list', 'package_list', 'activityInfo', 'type', 'activity_package_info'));
	}

	//活动状态更新:活动上架、下架、关闭;参数--1.type,2.activity_id

	public function activityEnrollment ()
	{

		$activity_id = Input::get("activity_id", '');//活动id
		//获取活动信息
		$activity_title = '';
		$activity_info  = $this->getActivityInfo($activity_id);
		if ($activity_info) {
			$activity_title = $activity_info->title;
			$state          = $activity_info->activity_state;//活动状态;0-进行中,1-下架,2-取消的活动
		} else {
			return response()->json(['code' => -521, 'msg' => '未查询到该活动!']);
		}
		$activity_link = $this->getActivitySignUrl($activity_id);

		return view('admin.activity.activityEnrollment', compact('activity_id', 'activity_title', 'state', 'activity_link'));
	}

	//活动报名-通过;参数--1.activity_id,2.user_id_list 数组

	private function getActivityInfo ($activity_id)
	{
		$app_id       = AppUtils::getAppID();
		$activityInfo = \DB::table("db_ex_business.t_activity")
			->where('app_id', '=', $app_id)
			->where('id', '=', $activity_id)
			->first();

		return $activityInfo;
	}

	//活动报名-拒绝;参数--1.activity_id,2.user_id_list 数组

	private function getActivitySignUrl ($activity_id)
	{
		$app_id            = AppUtils::getAppID();
		$app_info          = $this->getAppInfo();
		$activity_sign_url = '';
		if ($app_info) {
			$use_collection = $app_info[0]->use_collection;
			$wx_id          = $app_info[0]->wx_app_id;

			if ($use_collection == 1)//个人版
			{
				$activity_sign_url = AppUtils::getUrlHeader($app_id) . env("DOMAIN_DUAN_NAME") . "/" . $app_id . '/activity_sign_c/' . $activity_id;
			} else {
				$activity_sign_url = AppUtils::getUrlHeader($app_id) . $wx_id . "." . env("DOMAIN_NAME") . '/activity_sign_c/' . $activity_id;
			}
		}

		return $activity_sign_url;
	}

	public function getEnrollmentPage ()
	{
		$app_id      = AppUtils::getAppID();
		$activity_id = Input::get("activity_id", '');//活动id
		//判断该活动是否已经结束,即activity_end_at时候小于当前系统时间,若已结束,则将报名用户的记录改为已作废即state=4;
		$is_expire = $this->IsExpireActivity($activity_id);//1-已过期,0-未过期
		//获取活动信息
		$activity_info = $this->getActivityInfo($activity_id);
		if ($activity_info) {
			$state = $activity_info->activity_state;//活动状态;0-进行中,1-下架,2-取消的活动
		} else {
			return response()->json(['code' => -521, 'msg' => '未查询到该活动!']);
		}

		//        if($activity_state == StringConstants::ACTIVITY_ACTOR_CONFIRMING && $state == StringConstants::ACTIVITY_CLOSED){
		//            return response()->json(['code' => -521, 'msg' => '该活动没有待审核的记录!']);
		//        }

		if (Utils::isEmptyString($activity_id)) {
			return response()->json(['code' => -1, 'msg' => '请传入活动id!']);
		}
		$activity_state = Input::get("activity_state", 0);//报名状态:0-全部;1-待审核;2-已报名成功;3-已关闭
		$search_content = Input::get('searchContent', '');//搜索内容
		$whereRaw       = '';
		if ($state != StringConstants::ACTIVITY_CLOSED) {//进行中的任务
			if ($activity_state == 0) {//全部报名记录
				$whereRaw .= '1=1  and pay_state in(-1,1)';
			} else if ($activity_state == 1) {//待审核
				$whereRaw = ' state=0 and pay_state=-1';
			} else if ($activity_state == 2) {//已报名成功
				$whereRaw = 'state in (1,5)';
			} else if ($activity_state == 3) {//已关闭
				$whereRaw = 'state not in(0,1,5)';
			}
		} else {//已取消的活动,需要剔除掉待审核的报名记录
			if ($activity_state == 0) {//全部报名记录
				$whereRaw .= 'state!=0 and pay_state in(-1,1)';
			} else if ($activity_state == 1) {//待审核
				$whereRaw = ' state=0 ';
			} else if ($activity_state == 2) {//已报名成功
				$whereRaw = ' state=1 ';
			} else if ($activity_state == 3) {//已关闭
				$whereRaw = 'state not in(0,1,5)';
			}
		}

		if (!empty($search_content)) {
			if (Utils::isValidNumber($search_content)) {
				$whereRaw .= ' and phone like' . "'%" . $search_content . "%'";
			} else {
				$whereRaw .= ' and real_name like ' . "'%" . $search_content . "%'";
			}
		}

		$activity_actor_list = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->whereRaw($whereRaw)
			->orderBy('created_at', 'desc')
			->paginate(10);
		if ($activity_actor_list) {
			foreach ($activity_actor_list as $key => $activity_actor) {
				//获取报名用户的个人信息(t_users)
				$user_info = \DB::table("db_ex_business.t_users")
					->where('app_id', '=', $app_id)
					->where('user_id', '=', $activity_actor->user_id)
					->first();
				if ($user_info) {
					$activity_actor_list[ $key ]->wx_avatar_wx = $user_info->wx_avatar_wx;//用户微信头像
					$activity_actor_list[ $key ]->wx_nickname  = $user_info->wx_nickname;//用户微信头像
				} else {
					$activity_actor_list[ $key ]->wx_avatar_wx = "";//用户微信头像
					$activity_actor_list[ $key ]->wx_nickname  = "";//用户微信头像
				}
			}
		}

		$all_num        = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_ALL);//全部的用户数
		$confirming_num = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_CONFIRMING);//待审核的用户数
		$pass_num       = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_PASS);//已成功的用户数
		$unpass_num     = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_UNPASS);//已关闭的用户数

		return view('admin.activity.enrollmentPage', compact('activity_actor_list', 'all_num', 'confirming_num', 'pass_num', 'unpass_num', 'activity_id', 'activity_state', 'search_content', 'is_expire', 'state'));
	}

	private function IsExpireActivity ($activity_id)
	{

		$now    = Utils::getTime();
		$app_id = AppUtils::getAppID();

		$activity_info = $this->getActivityInfo($activity_id);
		if ($activity_info) {
			if ($activity_info->activity_end_at < $now) {//已过期
				//活动已过期,将t_activity_actor中该活动报名的所有记录标记为4
				$update_result = $this->setActivityActorState($activity_id, StringConstants::ACTIVITY_EXPIRE);

				return 1;
			} else {//未过期
				return 0;
			}
		} else {
			return -1;//
		}
	}

	//给活动特定条件下的用户发送小纸条

	private function setActivityActorState ($activity_id, $state)
	{
		$app_id = AppUtils::getAppID();
		//        $whereRaw = ' 1=1 or (ticket_price!=0 && pay_state!=0)';
		//将t_activity_actor中该活动报名的所有记录标记为$state
		$update_result = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->where('state', '=', 0)
			->where('ticket_price', '=', 0)
			->update(['state' => $state, 'updated_at' => Utils::getTime()]);

		return $update_result;
	}

	//给活动特定条件下的用户发送短信

	private function getActivityActorNum ($activity_id, $type)
	{
		$app_id = AppUtils::getAppID();

		$whereRaw = '';
		if ($type == 0) {//全部报名记录

			$whereRaw .= '1=1 and pay_state in(-1,1)';
		} else if ($type == 1) {//待审核
			$whereRaw = 'state=0 and pay_state=-1';
		} else if ($type == 2) {//已报名成功
			$whereRaw = 'state in (1,5)';
		} else if ($type == 3) {//已关闭
			$whereRaw = 'state not in(0,1,5)';
		} else if ($type == 4) {//已签到
			$whereRaw = 'state = 5';
		} else if ($type == 5) {//未签到
			$whereRaw = 'state = 1';
		}
		$activity_actor_list = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->whereRaw($whereRaw)
			->get();
		$num                 = count($activity_actor_list);

		return $num;
	}

	//根据活动id即$activity_id和$activity_state即报名记录状态,在表t_activity_actor中筛选、查找用户

	public function getAttendancePage ()
	{
		$app_id         = AppUtils::getAppID();
		$activity_id    = Input::get("activity_id", '');//活动id
		$search_content = Input::get('searchContent', '');//搜索内容
		$whereRaw       = '1=1';

		if (!empty($search_content)) {
			if (Utils::isValidNumber($search_content)) {
				$whereRaw .= ' and phone like' . "'%" . $search_content . "%'";
			} else {
				$whereRaw .= ' and real_name like ' . "'%" . $search_content . "%'";
			}
		}

		$activity_actor_list = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->whereIn('state', [1, 5])
			->whereRaw($whereRaw)
			->orderBy('created_at', 'desc')
			->paginate(10);

		if ($activity_actor_list) {
			foreach ($activity_actor_list as $key => $activity_actor) {
				//获取报名用户的个人信息(t_users)
				$user_info = \DB::table("db_ex_business.t_users")
					->where('app_id', '=', $app_id)
					->where('user_id', '=', $activity_actor->user_id)
					->first();
				if ($user_info) {
					$activity_actor_list[ $key ]->wx_avatar_wx = $user_info->wx_avatar_wx;//用户微信头像
					$activity_actor_list[ $key ]->wx_nickname  = $user_info->wx_nickname;//用户微信头像
				} else {
					$activity_actor_list[ $key ]->wx_avatar_wx = "";//用户微信头像
					$activity_actor_list[ $key ]->wx_nickname  = "";//用户微信头像
				}
			}
		}
		$pass_num   = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_PASS);//已成功的用户数
		$sign_num   = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_SIGN);//已签到的用户数
		$unsign_num = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_UNSIGN);//未签到的用户数

		return view('admin.activity.attendancePage', compact('activity_actor_list', 'pass_num', 'sign_num', 'unsign_num', 'activity_id', 'search_content'));
	}

	//审核用户报名信息

	public function excelAttendance (Request $request)
	{
		// 月份和版本
		$activity_id = Input::get("activity_id", '');//活动id
		$version     = $request->input('version', 2003);

		$app_id = AppUtils::getAppID();
		$sql    = "
            select v2.wx_nickname,v2.wx_avatar,v1.real_name,v1.phone,v1.ticket_name,v1.ticket_price,v1.ticket_num,v1.state from (
                select * from t_activity_actor where app_id = '{$app_id}' and activity_id = '{$activity_id}' and state in (1,5) 
            ) v1 LEFT JOIN t_users v2 ON v1.app_id = v2.app_id and v1.user_id = v2.user_id ORDER BY v1.created_at
        ";

		$data = DB::select($sql);

		$excelData[] = ['序号', '昵称', '头像', '真实姓名', '手机号', '票种', '价格/元', '票号', '状态'];

		$i = 0;
		foreach ($data as $v) {
			if ($v->state == 5) {
				$v->state = '已签到';
			} else {
				$v->state = '未签到';
			}
			$i++;
			$rowData     = [
				$i,
				$v->wx_nickname,
				$v->wx_avatar,
				$v->real_name,
				$v->phone,
				$v->ticket_name,
				$v->ticket_price * 0.01,
				$v->ticket_num,
				$v->state,
			];
			$excelData[] = $rowData;
		}

		$title = "签到记录";
		// 处理数据格式
		$excelData = ExcelUtils::getCorrectData($excelData);

		// 下载
		if ($excelData) {
			if ($version == 2003) {
				ExcelUtils::downExcel($title, $excelData);
			} else {
				ExcelUtils::downloadGbkCsv($title, $excelData);
			}
		}
	}

	//更改活动人员状态

	/**
	 * 改变活动人员签到状态
	 * @return \Illuminate\Http\JsonResponse
	 * state - 1-未签到， 5-已签到,4-作废(取消报名)
	 */
	public function changeActivityActorState ()
	{
		$activity_id = Input::get('activity_id', '');
		$user_id     = Input::get('user_id', '');
		$state       = Input::get('state', '');
		if (Utils::isEmptyString($state)) {
			return response()->json(['code' => -1, 'msg' => '请传入人员状态!']);
		} else if ($state != 1 && $state != 5 && $state != 4) {
			return response()->json(['code' => -1, 'msg' => '请传入正确的人员状态!']);
		}
		$update_state = $this->changeActiveActorState($activity_id, $user_id, $state);
		if ($update_state) {
			return response()->json(['code' => 0, 'msg' => '更改活动人员状态成功!']);
		} else {
			return response()->json(['code' => -1, 'msg' => '更改活动人员状态失败!']);
		}
	}

	//查询活动所属专栏

	private function changeActiveActorState ($activity_id, $user_id, $state)
	{
		$app_id = AppUtils::getAppID();
		//更改状态
		$update_state = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->where('user_id', '=', $user_id)
			->where('state', '!=', $state)
			->update(['state' => $state]);

		return $update_state;
	}

	//查询活动专栏的所有记录

	public function uploadActivity (Request $request)
	{
		//获取post请求的参数
		$params       = Input::get('params', '');
		$package_id   = Input::get('package_id', '');//关联专栏id
		$package_name = Input::get('package_name', '');//关联专栏id
		$ticketParams = Input::get('ticketParams', '');//票种列表(包含:票种名称、价格、票总数、票种说明、是否需要审核)

		$ret = $this->saveActivityInfo($request, $ticketParams, $params, $package_id, $package_name, StringConstants::ACTIVITY_ADD);

		if ($ret == '0') {
			return $this->result('新增活动成功!');
		} else {
			$msg = $ret;

			return response()->json(['code' => -1, 'msg' => $msg]);
		}
	}

	//查询活动用户报名记录,t_activity_actor

	private function saveActivityInfo ($request, $ticketParams, $params, $package_id, $package_name, $type)
	{
		$has_package = false;//是否有专栏

		//活动名称/活动地点/活动时间/报名时间/上架时间(必填)
		//title/place/activity_start_at\activity_end_at/enroll_start_at\enroll_end_at/start_at
		if (Utils::isEmptyString($params['title'])) {
			return '保存失败，填填写活动名称!';
		}
		if (Utils::isEmptyString($params['place'])) {
			return '保存失败，请填写活动地点!';
		}
		if (Utils::isEmptyString($params['activity_start_at'])) {
			return '保存失败，请填写活动开始时间!';
		}
		if (Utils::isEmptyString($params['activity_end_at'])) {
			return '保存失败，请填写活动结束时间!';
		}
		if ($params['is_default_enroll_time'] == 1) {
			if (Utils::isEmptyString($params['enroll_start_at'])) {
				return '保存失败，请填写报名开始时间!';
			}
			if (Utils::isEmptyString($params['enroll_end_at'])) {
				return '保存失败，请填写报名结束时间!';
			}
		}

		if (!$ticketParams) {
			return '保存失败，请添加票种!';
		}

		if (array_key_exists('actor_num', $params)) {
			Utils::array_remove($params, 'actor_num');
		}
		if (array_key_exists('is_confirm', $params)) {
			Utils::array_remove($params, 'is_confirm');
		}

		if ($params['activity_start_at'] > $params['activity_end_at']) {
			return '保存失败，活动开始时间不能大于结束时间!';
		}
		if ($params['enroll_start_at'] > $params['enroll_end_at']) {
			return '保存失败，报名开始时间不能大于结束时间!';
		}
		if ($params['activity_end_at'] < Utils::getTime() && $type == StringConstants::ACTIVITY_ADD) {
			return '保存失败，活动结束时间不能小于当前时间!';
		}

		if (!Utils::isEmptyString($package_id)) {
			$has_package = true;
			if (Utils::isEmptyString($package_name)) {
				return '保存失败，请选择对应的专栏!';
			}
			if ($ticketParams) {
				foreach ($ticketParams as $key => $ticket) {
					if ($ticket['ticket_price'] > 0) {
						return '保存失败，选择所属专栏后暂不支持添加收费票种!';
					}
				}
			}
			//            return response()->json(['code' => -521, 'msg' => '上传失败，请选择对应的专栏!']);
		}

		if ($has_package) {
			if (Utils::isEmptyString($params['start_at'])) {
				return '保存失败，请填写上架时间!';
			}
			if ($params['start_at'] > Utils::getTime()) {//未上架,即activity_state=1;
				$params['activity_state'] = StringConstants::ACTIVITY_LIST_OUT;
			} else {//上架,即activity_state=0;
				$params['activity_state'] = StringConstants::ACTIVITY_LISTING;
			}
		} else {//上架,即activity_state=0;
			$params['activity_state'] = StringConstants::ACTIVITY_LISTING;
		}

		//默认活动结束前都可以报名
		if ($params['is_default_enroll_time'] == 0) {
			if ($type == StringConstants::ACTIVITY_ADD) {
				$params['enroll_start_at'] = Utils::getTime();
			} else if ($type == StringConstants::ACTIVITY_UPDATE) {
				//查询活动信息

				$activity_info = $this->getActivityInfo($params['id']);
				if ($activity_info) {
					$params['enroll_start_at'] = $activity_info->created_at;
				} else {
					return '保存失败，未查询到该活动信息!';
				}
			}
			$params['enroll_end_at'] = $params['activity_end_at'];
		}
		if ($params['enroll_end_at'] != $params['activity_end_at']) {
			$params['is_default_enroll_time'] = 1;
		}

		$params['actor_num'] = 0;   //  活动人数
		if ($type == StringConstants::ACTIVITY_ADD) {//新增活动
			$activity_id          = 'activity_' . Utils::getOrderId(8) . random_int(1000, 9999);
			$params['id']         = $activity_id;
			$params['created_at'] = Utils::getTime();
		} else {//编辑活动
			$activity_id          = $params['id'];
			$params['updated_at'] = Utils::getTime();
		}

		$app_id                  = AppUtils::getAppID();
		$params['app_id']        = $app_id;
		$is_need_insert_relation = true;

		if ($type == StringConstants::ACTIVITY_UPDATE) {//编辑活动
			//在表t_pro_res_relation中查询活动id为$id的记录
			$activity_relation_list = $this->getActivityPackageUnstate($activity_id);
			if ($activity_relation_list) {
				foreach ($activity_relation_list as $key => $activity_relation) {
					if ($activity_relation->product_id != $package_id) {//专栏不同
						//更新该活动-专栏关系状态为删除,即relation_state=1
						//查询该活动所属专栏,即relation_state=0的
						$app_id   = AppUtils::getAppID();
						$relation = \DB::table('db_ex_business.t_pro_res_relation')
							->where('app_id', '=', $app_id)
							->where('resource_id', '=', $activity_id)
							->where('resource_type', '=', 5)
							->where('relation_state', '=', 0)
							->get();
						if ($relation) {
							foreach ($relation as $key2 => $value) {
								if ($value->product_id != $package_id) {
									$update_relation = \DB::update("update db_ex_business.t_pro_res_relation set relation_state=1 where app_id = '$app_id' and resource_id='$activity_id' and relation_state=0 and product_id='$value->product_id' and resource_type=5 limit 1");
									if ($update_relation) {
									} else {
										return "删除活动-专栏关系失败!";
									}
								}
							}
						}
					} else {//专栏没变,无需修改
						$is_need_insert_relation = false;

						if ($activity_relation->relation_state == 1) {
							$update_relation = \DB::update("update db_ex_business.t_pro_res_relation set relation_state=0 where app_id = '$app_id' and resource_id='$activity_id' and relation_state=1 and product_id='$activity_relation->product_id' and resource_type=5 limit 1");
							if ($update_relation) {
							} else {
								return "更新活动-专栏关系失败!";
							}
						}
					}
				}
			}

			//活动的票种旧的更新，新的插入
			$updateRole = \DB::update("update t_activity_ticket set state='1' where app_id=? and activity_id=?", [$app_id,
				$activity_id]);
		}

		if ($has_package && $is_need_insert_relation) {
			//将活动与专栏的关系新增至表t_pro_res_relation中
			$relation_params['app_id']        = $app_id;
			$relation_params['product_id']    = $package_id;
			$relation_params['product_name']  = $package_name;
			$relation_params['resource_type'] = 5;//活动与专栏的关系
			$relation_params['resource_id']   = $activity_id;
			$relation_params['created_at']    = Utils::getTime();

			//新增活动与专栏的关系记录
			$result_insert_relation = \DB::table("db_ex_business.t_pro_res_relation")->insert($relation_params);

			if ($result_insert_relation) {
			} else {
				return "insert relation record failed!";
			}
		}

		//插活动票种表
		if ($ticketParams) {
			$is_biggest = 0;
			foreach ($ticketParams as $key => $value) {
				$is_exist = 0;
				if (!Utils::isEmptyString($value["id"])) {
					$is_exist = 1;
				} else {
					//                    $is_exist=0;
					$value["id"] = Utils::getOrderId(8) . random_int(1000, 9999);
				}
				$value["state"] = 0;
				if ($value['ticket_count'] == 0) {
					$is_biggest = 1;
				} else {
					$params['actor_num'] += $value['ticket_count'];
				}
				if ($is_exist == 0) {
					$value["ticket_id"]   = 't_' . Utils::getOrderId(8) . random_int(1000, 9999);
					$value["app_id"]      = $app_id;
					$value["activity_id"] = $activity_id;
					$value["created_at"]  = Utils::getTime();
					$resultTicket         = \DB::table("t_activity_ticket")->insert($value);
				} else {
					$value["updated_at"] = Utils::getTime();
					$resultTicket        = \DB::table("t_activity_ticket")
						->where('id', '=', $value["id"])
						->where('app_id', '=', $app_id)
						->update($value);
				}
				if ($resultTicket) {
				} else {
				}
			}

			if ($is_biggest == 1) {
				$params['actor_num'] = 0;
			}
		}

		$params['activity_url'] = $this->getActivityUrl($activity_id, $package_id);
		//压缩图片
		$table_name = "db_ex_business.t_activity";

		//        $params['img_url_compressed'] = $params['img_url'];

		if ($type == 0) {//新增活动
			//生成活动
			$result_insert_activity = \DB::table("db_ex_business.t_activity")->insert($params);
			if ($result_insert_activity) {
				//压缩图片

				//if (array_key_exists('img_url', $params)) $this->imageDeal($params['img_url'], $table_name, $activity_id);//,160,120,60);
				if (array_key_exists('img_url', $params)) ImageUtils::resImgCompress($request, $table_name, $app_id, $activity_id, $params['img_url']);

				return StringConstants::ACTIVITY_ADD_SUCCESSED;
			} else {
				return "insert activity record failed!";
			}
		} else {//编辑活动
			//更新活动
			$result_update_activity = \DB::table("db_ex_business.t_activity")
				->where('id', '=', $activity_id)
				->where('app_id', '=', $app_id)
				->update($params);
			if ($result_update_activity) {
				//压缩图片

				//if (array_key_exists('img_url', $params)) $this->imageDeal($params['img_url'], $table_name, $activity_id);//,160,120,60);
				//
				if (array_key_exists('img_url', $params)) ImageUtils::resImgCompress($request, $table_name, $app_id, $activity_id, $params['img_url']);

				return StringConstants::ACTIVITY_UPDATE_SUCCESSED;
			} else {
				return "update activity record failed!";
			}
		}

	}

	//查询活动信息

	private function getActivityPackageUnstate ($activity_id)
	{
		$app_id                = AppUtils::getAppID();
		$activity_package_info = \DB::table('db_ex_business.t_pro_res_relation')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $activity_id)
			->where('resource_type', '=', 5)
			->get();

		return $activity_package_info;
	}

	//查询所有的专栏

	private function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	//查询活动列表

	public function saveActivity (Request $request)
	{
		//获取post请求的参数
		$params       = Input::get('params', '');
		$package_id   = Input::get('package_id', '');//关联专栏id
		$package_name = Input::get('package_name', '');//关联专栏名
		$ticketParams = Input::get('ticketParams', '');//票种列表(包含:活动票种id、票种名称、价格、票总数、是否需要审核)

		$ret = $this->saveActivityInfo($request, $ticketParams, $params, $package_id, $package_name, StringConstants::ACTIVITY_UPDATE);

		if ($ret == '0') {
			return $this->result('更新活动成功!');
		} else {
			$msg = $ret;

			return response()->json(['code' => -1, 'msg' => $msg]);
		}
	}

	//查询活动不同$condition的记录数

	public function updateActivityState ()
	{
		$id   = Input::get('activity_id', '');
		$type = Input::get('type', '');

		if (Utils::isEmptyString($id)) {
			return response()->json(['code' => -521, 'msg' => '活动id为空!']);
		}
		if (Utils::isEmptyString($type)) {
			return response()->json(['code' => -521, 'msg' => '请选择操作类型!']);
		}

		$app_id = AppUtils::getAppID();

		$msg = '更新失败!';

		//在活动表t_activity更新该活动状态
		$filed_name  = 'start_at';
		$filed_value = Utils::getTime();

		if ($type == StringConstants::ACTIVITY_LISTING) {//上架
			$msg         = "上架成功!";
			$filed_name  = 'start_at';
			$filed_value = Utils::getTime();
		} else if ($type == StringConstants::ACTIVITY_LIST_OUT) {//下架
			$msg         = "下架成功!";
			$filed_name  = 'stop_at';
			$filed_value = Utils::getTime();
		} else if ($type == StringConstants::ACTIVITY_CLOSED) {//关闭活动
			$msg         = "关闭活动成功!";
			$filed_name  = 'updated_at';
			$filed_value = Utils::getTime();
			//需要将t_activity_actor中该活动的待审核报名记录更改为已拒绝
			$ret = $this->setActivityActorState($id, StringConstants::ACTIVITY_EXPIRE);
			//            if($ret == 0){
			//                return response()->json(['code' => -1, 'msg' => "更新活动报名状态为已拒绝失败!"]);
			//            }

			//给报名成功的人发送短信通知:活动已取消。
			$user_id_list = $this->getActivityActorByState($id, StringConstants::ACTIVITY_ACTOR_PASS);
			$result       = $this->checkActivityActorRecords($user_id_list, $id, '', StringConstants::ACTIVITY_EXPIRE);
		}

		$params['activity_state'] = $type;
		$params["$filed_name"]    = $filed_value;

		$update_activity_state = \DB::table('db_ex_business.t_activity')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->where('activity_state', '!=', $type)
			->update($params);

		if (!$update_activity_state) {
			return response()->json(['code' => -1, 'msg' => "更新活动状态失败!"]);
		} else {
			return $this->result($msg);
		}
	}

	//保存活动信息

	private function checkActivityActorRecords ($user_id_list, $activity_id, $refuse_reason, $type)
	{
		$app_id = AppUtils::getAppID();
		//1.先查询活动的用户报名记录
		//2.更改记录状态(审核通过、拒绝)state为1、2,前提为该条记录为待审核。
		//3.发送短信息通知审核通过。

		foreach ($user_id_list as $key => $user_id) {
			//查询活动用户报名记录,t_activity_actor
			$activity_actor = $this->getActivityActor($user_id, $activity_id);

			if ($activity_actor) {
				$phone = $activity_actor->phone;
				//查询活动信息
				$activity_info = $this->getActivityInfo($activity_id);
				if ($activity_info) {
					$activity_name = $activity_info->title;
				} else {
					$activity_name = '';
				}
				//更改记录状态(审核通过)state为1,前提为该条记录为待审核。
				$update_state = \DB::table("db_ex_business.t_activity_actor")
					->where('app_id', '=', $app_id)
					->where('activity_id', '=', $activity_id)
					->where('user_id', '=', $user_id)
					->where('state', '!=', $type)
					->update(['state' => $type]);
				if ($update_state) {

					if ($type == StringConstants::ACTIVITY_CONFIRM_PASS) {
						//发送短信息通知审核通过。
						$content = "【小鹅通】" . "您已成功报名" . $activity_name . "，请留意活动时间，提前安排好您的行程。";
					} else if ($type == StringConstants::ACTIVITY_CONFIRM_UPPASS) {
						//发送短信息通知审核不通过。非常抱歉，您报名的{1}审核未通过，感谢您对本活动的关注。
						$new_refuse_reason = '[' . $refuse_reason . "]，您报名的[" . $activity_name . "]审核未通过，感谢您对本活动的关注。";
						$content           = "【小鹅通】" . "非常抱歉，由于" . $new_refuse_reason;
					} else {
						//发送短信息通知审核不通过。非常抱歉，您报名的{1}审核未通过，感谢您对本活动的关注。
						$new_refuse_reason = "您报名的[" . $activity_name . "]已取消，给您带来的不便主办人员深表歉意。";
						$content           = "【小鹅通】" . "非常抱歉，由于" . $new_refuse_reason;
					}

					$ret = Utils::sendsms($phone, $content);
					if ($ret == false) {
						return -3;
						//                        return response()->json(Utils::pack("1", StringConstants::Code_Failed, "发送报名通知短信失败!"));
					}
				} else {
					return -2;
					//                    return response()->json(['code' => -2, 'msg' => '更新状态失败!']);
				}
			} else {
				return -1;
				//                return response()->json(['code' => -1, 'msg' => '无该用户的报名记录!']);
			}
		}

		return 1;
	}

	//拼接活动签到验证链接

	private function getActivityActor ($user_id, $activity_id)
	{
		$app_id = AppUtils::getAppID();

		$activity_actor = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->where('user_id', '=', $user_id)
			->first();

		return $activity_actor;
	}

	//拼接活动链接信息

	public function passActivity ()
	{

		$activity_id  = Input::get("activity_id", '');
		$user_id_list = Input::get("user_id_list", '');

		if (Utils::isEmptyString($activity_id)) {
			return response()->json(['code' => -521, 'msg' => '活动id为空!']);
		}
		if (Utils::isEmptyString($user_id_list)) {
			return response()->json(['code' => -521, 'msg' => '请选择操作的记录!']);
		}
		//活动报名的允许人数上限
		$activity_info = $this->getActivityInfo($activity_id);
		$actor_num     = 0;
		if ($activity_info) {
			$actor_num = $activity_info->actor_num;
		} else {
			return response()->json(['code' => -521, 'msg' => '未查询到该活动!']);
		}

		//该活动报名成功的人数
		$pass_num = $this->getActivityActorNum($activity_id, StringConstants::ACTIVITY_ACTOR_PASS);//已成功的用户数
		//前端传过来的需要通过的人数
		$web_commit_num = count($user_id_list);
		//剩余报名人数
		$remainder_num = $actor_num - $pass_num;
		if ($actor_num && $web_commit_num > $remainder_num) {
			return response()->json(['code' => -521, 'msg' => '超出活动限制人数，还剩' . $remainder_num . '个名额！!']);
		}

		//审核活动报名记录--通过
		$refuse_reason = '';
		$result        = $this->checkActivityActorRecords($user_id_list, $activity_id, $refuse_reason, StringConstants::ACTIVITY_CONFIRM_PASS);

		if ($result) {
			return $this->result("活动报名成功通知下发!");
		} else if ($result == -1) {
			return response()->json(['code' => -1, 'msg' => '无该活动信息!']);
		} else if ($result == -2) {
			return response()->json(['code' => -2, 'msg' => '审核操作失败!']);
		} else if ($result == -3) {
			return response()->json(['code' => -3, 'msg' => '发送短信失败!']);
		}

		return $this->result("活动报名成功通知下发!");

	}

	//查询客户信息

	public function denyActivity ()
	{

		$activity_id   = Input::get("activity_id", '');
		$user_id_list  = Input::get("user_id_list", '');
		$refuse_reason = Input::get("refuse_reason", '');

		if (Utils::isEmptyString($activity_id)) {
			return response()->json(['code' => -521, 'msg' => '活动id为空!']);
		}
		if (Utils::isEmptyString($user_id_list)) {
			return response()->json(['code' => -521, 'msg' => '请选择操作的记录!']);
		}
		if (Utils::isEmptyString($refuse_reason)) {
			return response()->json(['code' => -521, 'msg' => '请填写拒绝理由!']);
		}

		//审核活动报名记录--拒绝
		$result = $this->checkActivityActorRecords($user_id_list, $activity_id, $refuse_reason, StringConstants::ACTIVITY_CONFIRM_UPPASS);

		if ($result) {
		} else {
		}

		return $this->result("活动报名成功通知下发!");

	}

	//查询活动报名成功的人数

	public function activityExcel ()
	{

		ini_set('memory_limit', '1024M');
		set_time_limit(600);

		$app_id      = AppUtils::getAppID();
		$activity_id = Input::get("activity_id", '');    //活动id
		$version     = Input::get('version', 2003);

		//  获取活动相关信息 t_activity
		$activity_message = \DB::table("db_ex_business.t_activity")
			->where('app_id', '=', $app_id)
			->where('id', '=', $activity_id)
			->first();
		if ($activity_message) {
			$activity_title = $activity_message->title;

			$form_field       = $activity_message->form_field;        //  报名表单字段
			$form_field_array = json_decode($form_field, true);     //  解析Json字符串
		} else {
			return '该活动不存在';
		}

		//  表格标题顺序 昵称 + 个人提交信息（json解析得到） + 报名时间 + 状态
		$firstRawData[0] = '昵称';
		$exist_field_num = count($firstRawData);
		$new_field_num   = count($form_field_array);

		foreach ($form_field_array as $key_1 => $value_1) {
			$new_filed_name = $value_1['field_name'];
			if (empty($new_filed_name)) {
				$new_filed_name = "-";
			}

			$firstRawData[ $exist_field_num + $key_1 ] = $new_filed_name;
		}

		$firstRawData[ $exist_field_num + $new_field_num ]     = "报名时间";
		$firstRawData[ $exist_field_num + $new_field_num + 1 ] = "状态";

		$excelData[0] = $firstRawData;

		$pay_state_arr = [-1, 1];
		//  获取应用id + 活动id 对应的用户列表信息 t_activity_actor
		$activity_actor_list = \DB::table("db_ex_business.t_activity_actor")
			->where('app_id', '=', $app_id)
			->where('activity_id', '=', $activity_id)
			->whereIn("pay_state", $pay_state_arr)
			->orderBy('created_at', 'desc')
			->get();

		if ($activity_actor_list) {
			foreach ($activity_actor_list as $key_2 => $activity_actor) {
				//获取报名用户的个人信息(t_users)
				$user_info = \DB::table("db_ex_business.t_users")
					->where('app_id', '=', $app_id)
					->where('user_id', '=', $activity_actor->user_id)
					->first();

				$wx_nickname = "";
				if ($user_info) {
					$wx_nickname = $user_info->wx_nickname;//用户微信昵称
				}

				$state = "";
				switch ($activity_actor->state) {
					case 0:
						$state = "待审核";
						break;
					case 1:
						$state = "已报名成功";
						break;
					case 2:
						$state = "已拒绝";
						break;
					case 3:
						$state = "已取消";
						break;
					case 4:
						$state = "已拒绝";
						break;

					default:
						$state = "待审核";
						break;

				}

				//  提交的内容
				$submit_content = $activity_actor->field_content;

				//  解析Json字符串
				$content_array = json_decode($submit_content, true);

				//  将$content_array 的对象 转化为新数组$full_content_array 的键值对
				$full_content_array = [];
				foreach ($content_array as $key_4 => $value_4) {
					foreach ($value_4 as $key_5 => $value_5) {
						$full_content_array[ $key_5 ] = $value_5 ? $value_5 : "";
					}
				}

				$rowData[0] = $wx_nickname;

				for ($j = 0; $j < $new_field_num; $j++) {
					$new_field = $firstRawData[ $exist_field_num + $j ];
					if (key_exists($new_field, $full_content_array)) {
						$new_value = $full_content_array[ $new_field ];
						if (empty($new_value)) {    //  没有填写新字段
							$new_value = "";
						}
					} else {                        //  没有填写新字段
						$new_value = "";
					}
					$rowData[ $exist_field_num + $j ] = $new_value;
				}

				$rowData[ $exist_field_num + $new_field_num ]     = $activity_actor->created_at;
				$rowData[ $exist_field_num + $new_field_num + 1 ] = $state;

				$excelData[ $key_2 + 1 ] = $rowData;

			}
		}

		$title = $activity_title . "-活动报名名单";
		// 处理数据格式
		$excelData = ExcelUtils::getCorrectData($excelData);

		// 下载
		if ($excelData) {
			if ($version == 2003) {
				ExcelUtils::downExcel($title, $excelData);
			} else {
				ExcelUtils::downloadGbkCsv($title, $excelData);
			}
		}
	}

	//判断该活动是否已经结束,即activity_end_at时候小于当前系统时间,若已结束,则将报名用户的记录改为已作废即state=4;

	/**活动消息通知;
	 *参数--
	 ***1.activity_id,
	 **2.activity_state(0-全部,1-报名成功,2-已勾选(需传user_id_list)),
	 **3.sms_type(0-地址变更,1-时间变更,2-活动取消,3-通用),
	 **4.notify_type(0-小纸条,1-短信)
	 **5.notify_content(通知内容)
	 **/
	public function activityNotify ()
	{

		$activity_id    = Input::get("activity_id", '');
		$activity_state = Input::get("activity_state", '');
		//        $sms_type = Input::get("sms_type",'');
		$notify_type    = Input::get("notify_type", '');
		$notify_content = Input::get("notify_content");

		$user_id_list = [];
		if ($activity_state == 2) {//给指定用户发送通知
			$user_id_list = Input::get("user_id_list");
		}

		//1.根据$activity_state判定发送的用户群体,
		//若为0-全部、1-已报名成功 的则:
		//    根据活动id即$activity_id和$activity_state即报名记录状态,在表t_activity_actor中筛选、查找用户
		//若为2-勾选的用户,即user_id_list,给这些勾选的用户发送

		if ($activity_state == 0 || $activity_state == 1) {
			if ($activity_state == 0) {
				$activity_state = -1;
			}
			$user_id_list = $this->getActivityActorByState($activity_id, $activity_state);
		}

		//1.根据$notify_type判断是发送小纸条还是短信
		//2.进入不同的发送方式进行处理
		if ($notify_type == 0) {//发送小纸条
			$result_message = $this->notifyByMessage($notify_content, $user_id_list);
			if ($result_message) {
				return $this->result("发送系统消息成功");
			} else {
				return response()->json(['code' => -2, 'msg' => '发送系统消息失败!']);
			}
		} else if ($notify_type == 1) {//发送短信
			$result_sms = $this->notifyBySms($notify_content, $user_id_list, $activity_id);
			if ($result_sms) {
				return $this->result("发送短信成功");
			} else {
				return response()->json(['code' => -2, 'msg' => '发送短信失败!']);
			}
		}

	}

	//将活动的报名记录全部标记为4

	private function notifyByMessage ($notify_content, $user_id_list)
	{

		if (!empty($notify_content)) {
			//  1、获取app_id，user_id， send_nick_name， content_clickable， skip_type， skip_target， source， src_id， type， content， state， send_at， created_at
			$data = [];

			$data['app_id']         = AppUtils::getAppID();
			$data['source']         = 0;
			$data['type']           = 0;
			$data['send_nick_name'] = "活动管理员";
			$data['content']        = $notify_content;
			$data['state']          = 0;

			$data['send_at']    = date('Y-m-d H:i:s', time());
			$data['created_at'] = date('Y-m-d H:i:s', time());

			$result_message = [];
			$insert         = 0;

			\DB::beginTransaction();
			//  2、  根据用户id 一个一个  插入表 db_ex_business.t_messages
			foreach ($user_id_list as $key => $value) {
				$data['user_id'] = $value;
				$insert          = \DB::table("t_messages")->insertGetId($data);

				//  3   .record notify information
				$result_message[ $key ] = ($insert ? 1 : 0);
			}
			if ($insert) {
				\DB::commit();
			} else {
				\DB::rollBack();
			}

			return $insert;

		} else {
			return -1;
		}

	}

	private function notifyBySms ($notify_content, $user_id_list, $activity_id)
	{

		//获取活动信息
		$activity_info = $this->getActivityInfo($activity_id);
		if ($activity_info) {
			foreach ($user_id_list as $key => $user_id) {
				$actor_info = $this->getActivityActor($user_id, $activity_id);
				if ($actor_info) {
					$phone = $actor_info->phone;
					//                    $content = '';
					//                    if($sms_type == 0){//地址变更
					//                        //短信模板:您报名的{1}，活动地址发生变更：{2}
					//                        $content = "【小鹅通】"."您报名的".$activity_info->title."，活动地址发生变更：".$notify_content;
					//                    }elseif($sms_type == 1){//时间变更
					//                        //短信模板:您报名的{1}，活动时间发生变更：{2}
					//                        $content = "【小鹅通】"."您报名的".$activity_info->title."，活动时间发生变更：".$notify_content;
					//                    }elseif($sms_type == 2){//活动取消
					//                        //短信模板:您报名的{1}，由于{2}原因，活动取消，给您带来的不便，主办人员深表歉意！
					//                        $content = "【小鹅通】"."您报名的".$activity_info->title."，由于".$notify_content."原因，活动取消，给您带来的不便，主办人员深表歉意！";
					//                    }elseif($sms_type == 3){//通用
					//                        //短信模板:感谢您报名{1}，主办方通知您：{2}
					//                        $content = "【小鹅通】"."感谢您报名".$activity_info->title."，主办方通知您：".$notify_content;
					//                    }
					//                    $content = "【小鹅通】"."活动通知：".$notify_content;
					$content = "【小鹅通】" . "感谢您报名" . $activity_info->title . "，主办方通知您：" . $notify_content;

					$ret = Utils::sendsms($phone, $content);
					if ($ret == false) {
					}
				} else {
				}
			}
		} else {
			return 0;
		}

		return 1;
	}

	/**
	 *图片处理
	 *
	 * @param $image_url
	 * @param $table_name
	 * @param $image_id
	 */
	private function imageDeal ($image_url, $table_name, $image_id)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		Utils::asyncThread($host_url . '/downloadImage?image_id=' . $image_id . '&app_id='
			. $app_id . '&table_name=' . $table_name . '&file_url=' . $image_url);
	}


	/**
	 * 导出excle -  - 活动报名名单-暂时不做选择，导出全部用户
	 */
	//    public function activityExportExcle() {
	//
	//        ini_set('memory_limit', '1024M');
	//        set_time_limit(600);
	//
	//        $app_id = AppUtils::getAppID();
	//        $activity_id = Input::get("activity_id", '');    //活动id
	//
	//        //  获取活动相关信息 t_activity
	//        $activity_message = \DB::table("db_ex_business.t_activity")
	//                ->where('app_id', '=', $app_id)
	//                ->where('id', '=', $activity_id)
	//                ->first();
	//        $activity_title = $activity_message->title;
	//
	//        $form_field = $activity_message->form_field;        //  报名表单字段
	//        $form_field_array = json_decode($form_field, true);     //  解析Json字符串
	//
	//
	//        //  表格标题顺序 昵称 + 个人提交信息（json解析得到） + 报名时间 + 状态
	//        $firstRawData[0] = '昵称';
	//        $exist_field_num = count($firstRawData);
	//        $new_field_num = count($form_field_array);
	//
	//        foreach ($form_field_array as $key_1 => $value_1) {
	//            $new_filed_name = $value_1['field_name'];
	//            if (empty($new_filed_name)) {
	//                $new_filed_name = "-";
	//            }
	//
	//            $firstRawData[$exist_field_num + $key_1] = $new_filed_name;
	//        }
	//
	//        $firstRawData[$exist_field_num + $new_field_num] = "报名时间";
	//        $firstRawData[$exist_field_num + $new_field_num + 1] = "状态";
	//
	//        $excelData[0] = $firstRawData;
	//
	//
	//        $pay_state_arr = [-1,1];
	//        //  获取应用id + 活动id 对应的用户列表信息 t_activity_actor
	//        $activity_actor_list = \DB::table("db_ex_business.t_activity_actor")
	//                ->where('app_id', '=', $app_id)
	//                ->where('activity_id', '=', $activity_id)
	//                ->whereIn("pay_state",$pay_state_arr)
	//                ->orderBy('created_at', 'desc')
	//                ->get();
	//
	//        if ($activity_actor_list) {
	//            foreach ($activity_actor_list as $key_2 => $activity_actor) {
	//                //获取报名用户的个人信息(t_users)
	//                $user_info = \DB::table("db_ex_business.t_users")
	//                        ->where('app_id', '=', $app_id)
	//                        ->where('user_id', '=', $activity_actor->user_id)
	//                        ->first();
	//
	//                $wx_nickname = "";
	//                if ($user_info) {
	//                    $wx_nickname = $user_info->wx_nickname;//用户微信昵称
	//                }
	//
	//                $state = "";
	//                switch ($activity_actor->state) {
	//                    case 0:
	//                        $state = "待审核";
	//                        break;
	//                    case 1:
	//                        $state = "已报名成功";
	//                        break;
	//                    case 2:
	//                        $state = "已拒绝";
	//                        break;
	//                    case 3:
	//                        $state = "已取消";
	//                        break;
	//                    case 4:
	//                        $state = "已拒绝";
	//                        break;
	//
	//                    default:
	//                        $state = "待审核";
	//                        break;
	//
	//                }
	//
	//                //  提交的内容
	//                $submit_content = $activity_actor->field_content;
	//
	//                //  解析Json字符串
	//                $content_array = json_decode($submit_content, true);
	//
	//                //  将$content_array 的对象 转化为新数组$full_content_array 的键值对
	//                $full_content_array = [];
	//                foreach ($content_array as $key_4 => $value_4) {
	//                    foreach ($value_4 as $key_5 => $value_5) {
	//                        $full_content_array[$key_5] = $value_5 ? $value_5 : "";
	//                    }
	//                }
	//
	//                $rowData[0] = $wx_nickname;
	//
	//                for ($j = 0; $j < $new_field_num; $j++) {
	//                    $new_field = $firstRawData[$exist_field_num + $j];
	//                    if (key_exists($new_field, $full_content_array)) {
	//                        $new_value = $full_content_array[$new_field];
	//                        if (empty($new_value)) {    //  没有填写新字段
	//                            $new_value = "";
	//                        }
	//                    } else {                        //  没有填写新字段
	//                        $new_value = "";
	//                    }
	//                    $rowData[$exist_field_num + $j] = $new_value;
	//                }
	//
	//                $rowData[$exist_field_num + $new_field_num] = $activity_actor->created_at;
	//                $rowData[$exist_field_num + $new_field_num + 1] = $state;
	//
	//                $excelData[$key_2 + 1] = $rowData;
	//
	//            }
	//        }
	//
	//        Excel::create($activity_title . "-活动报名名单", function ($excel) use ($excelData) {
	//            $excel->sheet("订单数据", function ($sheet) use ($excelData) {
	//                //标题
	//                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
	//                $widths = [20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20];
	//                for ($i = 0; $i < count($rows); $i++) {
	//                    //宽度
	//                    $sheet->setWidth([$rows[$i] => $widths[$i]]);
	//                }
	//                $sheet->fromArray($excelData);
	//
	//            });
	//        })->download("csv");
	//
	//
	//    }
}