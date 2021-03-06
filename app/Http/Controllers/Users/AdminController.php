<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use DB;
use Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{
	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	/*********************** 子账户 *************************/
	public function childPage ()
	{
		$data = DB::connection("mysql_config")->table("t_admin_user")
			->select('id', 'role_name', 'username')
			->where("app_id", $this->app_id)
			->where("is_deleted", 0)
			->orderBy("created_at", "desc")
			->paginate(10);
		//        dump($data);
		//        exit;
		return view('admin.accountSetting.childAccount', [
			'data' => $data,
		]);
	}

	/**
	 * 添加/编辑子账户页面
	 *
	 * @param $action  add/edit
	 * @param $id
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function adminChildPage ($action, $id = null)
	{
		$info = AppUtils::getPrivilege();

		// 如果是修改页面，查询用户信息，并设定已有的权限信息
		if ($action === 'edit') {
			if (!$id) return response()->json(['code' => -1, 'msg' => '无效用户', 'data' => []]);
			// 查询用户信息
			$user_info = DB::connection('mysql_config')->table('t_admin_user')
				->where('is_deleted', 0)->where('app_id', $this->app_id)->where('id', $id)->first();
			$privilege = json_decode($user_info->privilege, true);

			foreach ($info as $v) {
				if ($privilege[ $v->id ]) {
					$v->is_chose = true;
				} else {
					$v->is_chose = false;
				}
			}
		}

		// 获得该业务的版本和运营模式
		$app_info       = DB::connection('mysql_config')->table('t_app_conf')->where('app_id', $this->app_id)->where('wx_app_type', 1)->first();
		$use_collection = $app_info->version_type;
		$data           = [];
		foreach ($info as $v) {
			// 个人模式 关掉企业模式订单列表的权限
			$v->is_permission = true;
			if ($use_collection) {
				if ($v->id === 126) $v->is_permission = false;
			}

			// 整理权限数组信息
			$child = [];
			if ($v->parent_id === 0 && $v->pri_level === 0) {
				foreach ($info as $v1) {
					if ($v1->parent_id === $v->id && $v1->pri_level === 1) {
						$child[] = $v1;
					}
				}
				$v->child = $child;
				$data[]   = $v;
			}
		}

		// 添加和修改 返回不同的view
		if ($action === 'edit') {
			return view('admin.addAdminUser', ['data' => $data, 'info' => $user_info]);
		}

		return view('admin.addAdminUser', ['data' => $data]);
	}

	/**
	 * 添加/更新子账号操作
	 *
	 * @param Request $request
	 * @param         $action  add/edit
	 * @param         $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function adminChild (Request $request, $action, $id = null)
	{
		if ($action === 'edit' && !$id) return response()->json(['code' => -1, 'msg' => '无效用户', 'data' => []]);

		$this->validate($request, [
			'params.role_name'  => 'present',
			'params.phone'      => 'present',
			'params.password'   => 'required|min:6|max:18|alpha_num',
			'params.repassword' => 'required|string|same:params.password',
			'params.privilege'  => 'required',
		]);

		// 修改操作不用检测用户名
		if ($action === 'add') {
			$this->validate($request, [
				'params.username' => 'required|min:6|max:18|alpha_dash',
			]);

			// 校验用户名是否已存在
			$name = $request->input('params.username');
			$bool = AppUtils::checkUsernameRepeat((string)$name);
			if (!$bool) return response()->json(['code' => -1, 'msg' => '用户名已存在', 'data' => []]);
		}

		// 取出权限
		$params    = $request->input('params');
		$privilege = array_pop($params);
		// 权限验证
		ksort($privilege);  // 根据key排序
		$privilege_info = DB::connection('mysql_config')->table('t_admin_privilege')
			->whereNull('deleted_at')->orderBy('id')->pluck('parent_id', 'id');
		if (count($privilege) !== count($privilege_info)) return response()->json(['code' => -2, 'msg' => '数据错误', 'data' => []]);

		foreach ($privilege as $k => $v) {
			if (!array_key_exists($k, $privilege_info)) {
				return response()->json(['code' => -2, 'msg' => '数据错误', 'data' => []]);
			}

			// 如果是顶级权限，且顶级权限为0，子权限不能为1
			if ($k < 108 && $v < 1) {
				$privilege_list = [];
				foreach ($privilege_info as $k1 => $v1) {
					if ($v1 == $k) {
						$privilege_list[] = $k1;

					}
				}
				foreach ($privilege as $k2 => $v2) {
					if (in_array($k2, $privilege_list)) {

						if ($v2 > 0) return response()->json(['code' => -2, 'msg' => '数据错误', 'data' => []]);
					}
				}
			}
		}
		$privilege = json_encode($privilege);

		// 拼装数据
		unset($params['repassword']);
		$params['privilege']        = $privilege;
		$params['app_id']           = $this->app_id;
		$params['password_encrypt'] = Hash::make($params['password']);
		$params['created_at']       = date('Y-m-d H:i:s');

		$act = false;
		if ($action === 'add') {
			$act = DB::connection('mysql_config')->table('t_admin_user')->insert($params);
		} else if ($action === 'edit') {
			unset($params['username']);
			unset($params['created_at']);
			$params['updated_at'] = date('Y-m-d H:i:s');
			$act                  = DB::connection('mysql_config')->table('t_admin_user')->where('id', $id)->update($params);
		}

		if (!$act) return response()->json(['code' => -1, 'msg' => 'db error', 'data' => []]);

		return response()->json(['code' => 0, 'msg' => '请求成功', 'data' => []]);
	}

	//删除子账户操作
	public function delAdminChild ($id)
	{
		if (!$id) return response()->json(['code' => -1, 'msg' => '无效用户', 'data' => []]);

		$update = DB::connection('mysql_config')->table('t_admin_user')->where('id', $id)->update(['is_deleted' => 1]);

		if ($update) {
			return response()->json(['code' => 0, 'msg' => '请求成功', 'data' => []]);
		} else {
			return response()->json(['code' => -1, 'msg' => '请求失败', 'data' => []]);
		}
	}

	// 校验用户名是否已存在
	public function checkUsername (Request $request, $name)
	{
		if (!$name) response()->json(['code' => -2, 'msg' => '请输入用户名', 'data' => []]);

		$bool = AppUtils::checkUsernameRepeat((string)$name);
		if (!$bool) return response()->json(['code' => -1, 'msg' => '用户名已存在', 'data' => []]);

		return response()->json(['code' => 0, 'msg' => 'success', 'data' => []]);
	}

	public function test ()
	{
		$data = [
			//            ['pri_name'=>'知识店铺','created_at'=>date('Y-m-d H:i:s')],
			//            ['pri_name'=>'知识商品','created_at'=>date('Y-m-d H:i:s')],
			//            ['pri_name'=>'营销中心','created_at'=>date('Y-m-d H:i:s')],
			//            ['pri_name'=>'社群运营','created_at'=>date('Y-m-d H:i:s')],
			//            ['pri_name'=>'用户管理','created_at'=>date('Y-m-d H:i:s')],
			//            ['pri_name'=>'数据分析','created_at'=>date('Y-m-d H:i:s')],
			//            ['pri_name'=>'财务管理','created_at'=>date('Y-m-d H:i:s')],
			//            ['pri_name'=>'账户管理','created_at'=>date('Y-m-d H:i:s')],

			['pri_name' => '手机预览', 'parent_id' => 100, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '店铺装修', 'parent_id' => 100, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '分享设置', 'parent_id' => 100, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '公众号设置', 'parent_id' => 100, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '功能管理', 'parent_id' => 100, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],

			['pri_name' => '统计分发', 'parent_id' => 102, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '推广员', 'parent_id' => 102, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '邀请码', 'parent_id' => 102, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '邀请卡', 'parent_id' => 102, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '优惠券', 'parent_id' => 102, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],

			['pri_name' => '评论互动', 'parent_id' => 103, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '活动管理', 'parent_id' => 103, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '小社群', 'parent_id' => 103, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '问答', 'parent_id' => 103, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],

			['pri_name' => '用户列表', 'parent_id' => 104, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '开通记录', 'parent_id' => 104, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '消息列表', 'parent_id' => 104, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '反馈列表', 'parent_id' => 104, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],

			['pri_name' => '企业模式收入', 'parent_id' => 106, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '个人模式收入', 'parent_id' => 106, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '订单列表', 'parent_id' => 106, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '提现记录', 'parent_id' => 106, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],

			['pri_name' => '账户一览', 'parent_id' => 107, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '账号管理', 'parent_id' => 107, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '运营模式', 'parent_id' => 107, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],
			['pri_name' => '小程序配置', 'parent_id' => 107, 'pri_level' => 1, 'created_at' => date('Y-m-d H:i:s')],

		];
		//        DB::connection('mysql_config')->table('t_admin_privilege')->insert($data);

	}

	public function changePrivilege ()
	{
		// 获得旧数据
		$data = DB::connection('mysql_config')->select("
            SELECT
                v1.id,
                v2.*
            FROM
                (
                    SELECT
                        id,
                        app_id,
                        group_id
                    FROM
                        t_admin_user
                    WHERE
                        group_id > 0
                    AND is_deleted = 0
                ) v1
            LEFT JOIN (SELECT * FROM t_admin_group) v2 ON v1.app_id = v2.app_id
            AND v1.group_id = v2.group_id
            WHERE
                v2.app_id IS NOT NULL
            ORDER BY
                v1.app_id,
                v1.group_id
        ");

		$access = DB::connection('mysql_config')->table('t_admin_privilege')
			->select(DB::raw("1 as value,id"))->whereNull('deleted_at')->orderBy('id')->pluck('value', 'id');

		foreach ($data as $v) {
			$privilege = $access;
			if ($v->dashboard_admin === 0) {
				$privilege[105] = 0;
			}

			if ($v->content_list === 0) {
				$privilege[101] = 0;
			}

			if ($v->content_comment === 0) {
				$privilege[118] = 0;
			}

			if ($v->user_list === 0) {
				$privilege[122] = 0;
			}

			if ($v->message_admin === 0) {
				$privilege[124] = 0;
			}

			if ($v->feedback_admin === 0) {
				$privilege[125] = 0;
			}

			if ($v->channel_admin === 0) {
				$privilege[113] = 0;
			}

			if ($v->invitecode_admin === 0) {
				$privilege[115] = 0;
			}

			if ($v->money_admin === 0) {
				$privilege[106] = 0;
				$privilege[126] = 0;
				$privilege[127] = 0;
				$privilege[128] = 0;
				$privilege[129] = 0;
			}

			if ($v->account_admin === 0) {
				$privilege[107] = 0;
				$privilege[130] = 0;
				$privilege[131] = 0;
				$privilege[132] = 0;
				$privilege[133] = 0;
			}

			$privilege = json_encode($privilege);

			$update = DB::connection('mysql_config')->table('t_admin_user')->where('id', $v->id)->where('group_id', $v->group_id)->where('app_id', $v->app_id)->update(['privilege' => $privilege]);
			//            if (!$update){
			//                echo '数据错误！！！';
			//                break;
			//            }

		}
	}

}
