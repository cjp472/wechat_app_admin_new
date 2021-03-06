<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\SuperUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SuperAdminController extends Controller
{
	//
	public function toSuperPage (Request $request)
	{
		//        DB::enableQueryLog();
		$page = $request->input('page', 1);

		//        $search_type = $request->input('search_type','name');
		$search_content = $request->input('content', '');
		$search_array   = ['content' => $search_content];

		//        // 拼接搜索条件sql
		//        switch ($search_type){
		//            case 'name':
		//                $search_sql = " where name like '%{$search_content}%'";
		//                break;
		//            case 'nick_name':
		//                $search_sql = " where nick_name like '%{$search_content}%'";
		//                break;
		//            case 'phone':
		//                $search_sql = " where phone like '%{$search_content}%'";
		//                break;
		//            case 'app_id':
		//                $search_sql = " where app_id like '%{$search_content}%'";
		//                break;
		//            case 'wx_app_id':
		//                $search_sql = " where wx_app_id like '%{$search_content}%'";
		//                break;
		//            case 'wx_app_name':
		//                $search_sql = " where wx_app_name like '%{$search_content}%'";
		//                break;
		//            default :
		//                $search_sql = '';
		//        }
		//
		if ($search_content != '') {
			$search_sql = " where name like '%{$search_content}%' or nick_name like '%{$search_content}%' 
            or phone like '%{$search_content}%' or app_id like '%{$search_content}%'
            or wx_app_id like '%{$search_content}%' or wx_app_name like '%{$search_content}%'";
		} else {
			$search_sql = "";
		}

		if (SuperUtils::checkIsAdmin(AppUtils::getSuperOpenId())) {
			//        if (1){
			// 拼接要查询的sql
			$sql = "
                select * from (
                    SELECT
                        t1.openid,t1.login_id,t1.name,t1.nick_name,t1.created_at,
                        t2.app_id,t2.wx_app_id,t2.wx_app_name,t2.use_collection,t2.version_type,t2.isNewer,t2.balance,
                        t3.phone,t3.wx_app_name AS mer_wx_app_name
                    FROM
                        db_ex_config.t_mgr_login t1
                    LEFT JOIN (
                        SELECT
                            *
                        FROM
                            db_ex_config.t_app_conf
                        WHERE
                            wx_app_type = 1
                    ) t2 ON t1.merchant_id = t2.merchant_id
                    LEFT JOIN db_ex_config.t_merchant_conf t3 ON t1.merchant_id = t3.merchant_id
                    where t1.merchant_id is not null and t2.app_id is not NULL 
                )tt1 
            ";
			$sql .= $search_sql;
			// 计算要查询的总数
			$total_sql = "
                select count(v1.login_id) as count from ({$sql})v1
            ";
			$total     = DB::connection('mysql_config')->select($total_sql);
			$total     = $total[0]->count;

			$perPage = 10; // 每页显示的条数
			// 判断当前页数
			if ($page) {
				$current_page = $page;
				$current_page = $current_page <= 0 ? 1 : $current_page;
			} else {
				$current_page = 1;
			}
			$offset = ($current_page - 1) * $perPage; // 计算偏移量

			// 查询条件，继续拼接sql
			$sql .= " order by balance desc,app_id ";
			$sql .= " limit {$perPage} offset {$offset}";

			$data = DB::connection('mysql')->select($sql);

			$paginator = new LengthAwarePaginator($data, $total, $perPage, $current_page, [
				'path'     => Paginator::resolveCurrentPath(), //生成路径
				'pageName' => 'page',
			]);

			$app_id_arr = [];
			foreach ($paginator as $v) {
				$app_id_arr[] = $v->app_id;
			}
			$extra_data = DB::connection('mysql_chain')->table('t_joined_extra')->select('app_id', 'sum_count', 'sum_income')->whereIn('app_id', $app_id_arr)->get();
			foreach ($paginator as $v) {
				$v->sum_count  = 0;
				$v->sum_income = 0;
				foreach ($extra_data as $v1) {
					if ($v->app_id == $v1->app_id) {
						$v->sum_count  = $v1->sum_count;
						$v->sum_income = $v1->sum_income;
					}
				}
			}

			return View('admin.superAdmin', ['paginator' => $paginator, 'search_array' => $search_array]);
		} else {
			return redirect('/login');
		}
	}

	public function superLogin (Request $request)
	{
		$user_openid = $request->openid;

		if (SuperUtils::checkIsAdmin(AppUtils::getSuperOpenId())) {

			$exist = \DB::reconnect("mysql_config")->select("select * from t_mgr_login where openid = ?", [$user_openid]);
			if ($exist) {
				AppUtils::setLoginStatus(
					$exist[0]->openid,
					$exist[0]->nick_name,
					$exist[0]->gender,
					$exist[0]->logo,
					AppUtils::getAccessAuth(AppUtils::getAppIdByOpenId($exist[0]->openid), null, 0)
				);

				return redirect('/accountview');
			}
		} else {
			return redirect('/login');
		}
	}

	/***
	 * 设置客户为https
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function set_app_https (Request $request)
	{
		$app_id = Input::get('app_id', '');
		if (!empty($app_id)) {
			$result = DB::reconnect('mysql_config')->table('t_app_conf')
				->where('app_id', '=', $app_id)
				->where('wx_app_type', '=', 1)
				->where('isNewer', '=', 1)
				->update([
					'isNewer' => 0,
				]);
			if ($result) {
				return response()->json(['code' => 0, 'msg' => '设置成功']);
			} else {
				return response()->json(['code' => -2, 'msg' => '设置失败']);
			}
		} else {
			return response()->json(['code' => -1, 'msg' => '未找到该用户']);
		}
	}

	/**
	 * 封号处理
	 */
	public function set_close ()
	{
		$app_id = Input::get('app_id', '');
		$openid = Input::get('openid', '');
		$type   = Input::get('type', '_unDefine');

		if (!empty($app_id)) {
			$result1 = DB::connection('mysql_config')->table('t_app_conf')
				->where('app_id', '=', $app_id)
				->update([
					'app_id' => $app_id . $type,
				]);
			$result2 = DB::connection('mysql_config')->table('t_mgr_login')
				->where('openid', '=', $openid)
				->update([
					'openid'   => $openid . $type,
					'password' => null,
				]);

			if ($result1 || $result2) {
				return response()->json(['code' => 0, 'msg' => '设置成功']);
			} else {
				return response()->json(['code' => -2, 'msg' => '设置失败']);
			}
		} else {
			return response()->json(['code' => -1, 'msg' => '未找到该用户']);
		}
	}
}
