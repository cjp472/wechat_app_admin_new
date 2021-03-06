<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31
 * Time: 11:35
 */

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\Input;

class ExerciseController extends Controller
{

	private $exerciseBookId;

	public function exerciseBookList ()
	{

		$search_content     = Input::get("search_content", "");
		$exercise_book_list = $this->getExerciseBookList($search_content);

		$exercise_book_role_list = [];
		//查询每个作业本的角色列表
		foreach ($exercise_book_list as $key => $exercise_book) {
			//在t_exercise_books_roles表中查询该作业本的角色
			$role_list = \DB::table('db_ex_business.t_exercise_books_roles')
				->where('app_id', '=', AppUtils::getAppID())
				->where('exercise_book_id', '=', $exercise_book->exercise_book_id)
				->where('state', '=', 0)
				->get();
			if ($role_list) {
				//在t_users表中查询用户的昵称
				foreach ($role_list as $key2 => $role) {
					$user_id   = $role->user_id;
					$user_info = $this->getUserInfo($user_id);
					if ($user_info) {
						$role->wx_nickname = $user_info->wx_nickname;
						$role->wx_avatar   = $user_info->wx_avatar;
					} else {
						$role->wx_nickanme = '';
						$role->wx_avatar   = '';
					}
				}
			}
			$exercise_book_role_list[ $key ] = $role_list;
			//查询社群名称
			$community_name                = $this->getCommunityInfo($exercise_book->community_id);
			$exercise_book->community_name = $community_name;
			//查询作业数
			$exercise_book->exercise_num = $this->getExerciseNum($exercise_book->exercise_book_id);
		}

		return View("admin.exercise.exerciseBookList",
			compact("exercise_book_list", "exercise_book_role_list", "search_content"));

	}

	private function getExerciseBookList ($search_content)
	{
		$whereRaw = ' 1=1 ';

		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and title like '" . "%" . $search_content . "%'";
		}

		$exercise_books_list = \DB::table('db_ex_business.t_exercise_books')
			->where('app_id', '=', AppUtils::getAppID())
			->whereRaw($whereRaw)
			->orderby('created_at', 'desc')
			->paginate(10);

		return $exercise_books_list;
	}

	private function getUserInfo ($user_id)
	{
		//在t_users中查询用户详细信息
		$user_info = \DB::table("db_ex_business.t_users")
			->select('wx_nickname', 'wx_avatar')
			->where('app_id', '=', AppUtils::getAppID())
			->where('user_id', '=', $user_id)
			->first();
		//        if($user_info){
		//            return $user_info->wx_nickname;
		//        }else{
		//            return '';
		//        }
		return $user_info;
	}

	private function getCommunityInfo ($community_id)
	{
		$info = \DB::table("db_ex_business.t_community")
			->where('app_id', '=', AppUtils::getAppID())
			->where("id", '=', $community_id)
			->first();
		if ($info) {
			return $info->title;
		} else {
			return '';
		}
	}

	private function getExerciseNum ($exercise_book_id)
	{
		$exercise_num = \DB::table("db_ex_business.t_exercises")
			->where("app_id", '=', AppUtils::getAppID())
			->where('exercise_book_id', '=', $exercise_book_id)
			->where("state", '!=', 2)
			->count();

		return $exercise_num;
	}

	public function createExerciseBook ()
	{

		$page_index     = Input::get('page_index', 1);
		$page_type      = 0;
		$community_list = $this->getCommunityList();

		return View("admin.exercise.manageExerciseBook", compact("page_type", "community_list", "page_index"));

	}

	private function getCommunityList ()
	{
		$community_list = \DB::table('db_ex_business.t_community')
			->select('id', 'title')
			->where('app_id', '=', AppUtils::getAppID())
			->where('community_state', '=', 0)
			->get();

		return $community_list;
	}

	public function editExerciseBook ()
	{

		$exercise_book_id = Input::get('exercise_book_id');
		$page_index       = Input::get('page_index', 1);

		$page_type          = 1;
		$app_id             = AppUtils::getAppID();
		$exercise_book_info = $this->getExerciseBookInfo($app_id, $exercise_book_id);
		if ($exercise_book_info) {

			$resource_list = $this->getResourceList($exercise_book_info->resource_type);
		} else {
		}

		$community_list = $this->getCommunityList();
		$exercise_count = $this->isHasExercise(AppUtils::getAppID(), $exercise_book_id);

		return View("admin.exercise.manageExerciseBook",
			compact("exercise_book_info", "page_type", "resource_list", "community_list", "exercise_count", 'page_index'));

	}

	private function getExerciseBookInfo ($app_id, $exercise_book_id)
	{
		//在表db_ex_business.t_exercise_books中查询该作业本信息
		$exercise_info = \DB::table('db_ex_business.t_exercise_books')
			->where('app_id', '=', $app_id)
			->where('exercise_book_id', '=', $exercise_book_id)
			->first();

		if ($exercise_info) {
			if ($exercise_info->resource_type != 5) {//资源课程
				$resource_info = Utils::getResourceInfo($exercise_info->resource_id, $exercise_info->resource_type);
				if ($resource_info) {
					$exercise_info->piece_price = $resource_info->piece_price;
				} else {
					$exercise_info->piece_price = 0;
				}
			} else {//专栏
				$resource_info = $this->getPackageInfo($exercise_info->resource_id);
				if ($resource_info) {
					$exercise_info->price = $resource_info->price;
				} else {
					$exercise_info->price = 0;
				}
			}
		}

		return $exercise_info;
	}

	private function getResourceList ($resource_type)
	{
		if ($resource_type < 1 && $resource_type > 5) {
			return '类型错误';
		}
		$table_name_array = [
			'1' => 'db_ex_business.t_image_text',
			'2' => 'db_ex_business.t_audio',
			'3' => 'db_ex_business.t_video',
			'4' => 'db_ex_business.t_alive',
			'5' => 'db_ex_business.t_pay_products',
		];
		$field_name_array = [
			'1' => 'display_state',
			'2' => 'audio_state',
			'3' => 'video_state',
			'4' => 'state',
			'5' => 'state',
		];
		$price_name_array = [
			'1' => 'piece_price',
			'2' => 'piece_price',
			'3' => 'piece_price',
			'4' => 'piece_price',
			'5' => 'price',
		];
		//获取已关联作业本的资源id集合
		$file_name = 'title';
		$whereRaw  = " 1=1 ";
		if ($resource_type == StringConstants::PACKAGE_SINGLE_LIST) {
			//专栏
			$package_id_list = $this->getExerciseResourceIdList(StringConstants::PACKAGE_SINGLE_LIST);
			$whereRaw        .= ' and is_member = 0';
			$whereRaw        .= " and id not in ( " . implode(',', $package_id_list) . ")";
			$file_name       = 'name';
		} else {
			$resource_id_list = $this->getExerciseResourceIdList(StringConstants::SINGLE_GOODS_ALL);//课程
			$whereRaw         .= " and id not in ( " . implode(',', $resource_id_list) . ")";
		}

		$resource_list = \DB::table($table_name_array[ $resource_type ])
			->select('id', $file_name, $price_name_array[ $resource_type ])
			->where('app_id', '=', AppUtils::getAppID())
			->where($field_name_array[ $resource_type ], '=', 0)
			->whereRaw($whereRaw)
			->orderBy('created_at', 'desc')
			->get();
		if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) {
			$id_list = [];
			//将那些没有没有设置人员的直播剔除掉
			foreach ($resource_list as $key => $resource) {
				//在t_alive_role中查询该直播的人员
				$roles = \DB::table('db_ex_business.t_alive_role')
					->where('app_id', '=', AppUtils::getAppID())
					->where('alive_id', '=', $resource->id)
					->where('state', '=', 0)
					->get();
				if (count($roles) > 0) {
					$id_list[] = $resource;
				}
			}
			$resource_list = $id_list;
		}

		return $resource_list;
	}

	//获取作业信息

	private function getExerciseResourceIdList ($resource_type)
	{
		if ($resource_type < 0 && $resource_type > 5) {
			return '类型错误';
		}
		$whereRaw = " 1=1 ";
		if ($resource_type == StringConstants::SINGLE_GOODS_ALL) {
			$whereRaw .= " and resource_type!=5";
		} else {
			$whereRaw .= " and resource_type=5";
		}
		$id_list = ['1'];

		$resource_id_list = \DB::table('db_ex_business.t_exercise_books')
			->select('resource_id')
			->where('app_id', '=', AppUtils::getAppID())
			->whereRaw($whereRaw)
			->get();
		if ($resource_id_list) {
			foreach ($resource_id_list as $key => $resource_id) {
				$id_list[] = "'" . $resource_id->resource_id . "'";
			}
		}

		return $id_list;
	}

	//获取作业本的角色昵称

	private function isHasExercise ($app_id, $exercise_book_id)
	{
		//在表db_ex_business.t_exercises中查询该作业本下是否有有效的作业记录
		$exercises_count = \DB::table('db_ex_business.t_exercises')
			->where('app_id', '=', $app_id)
			->where('exercise_book_id', '=', $exercise_book_id)
			->where('state', '=', 0)
			->count();

		return $exercises_count;
	}

	//查询作业数

	public function exerciseList ()
	{
		$exercise_book_id = Input::get('exercise_book_id');
		$search_content   = Input::get('search_content');
		$page_index       = Input::get('page_index', 1);

		$exercise_book_info = $this->getExerciseBookInfo(AppUtils::getAppID(), $exercise_book_id);
		if ($exercise_book_info) {
			$exercise_book_title = $exercise_book_info->title;
		} else {
			$exercise_book_title = '';
		}

		//回去该作业本的老师
		$user_wx_nickname = $this->getExerciseBookRoleInfo($exercise_book_id, StringConstants::EXERCISE_BOOK_ROLE_TYPE_TEACHER);

		$exercise_list = $this->getExerciseList($exercise_book_id, $search_content);

		return View("admin.exercise.exerciseList",
			compact('exercise_list', "search_content", 'user_wx_nickname', 'exercise_book_title', 'exercise_book_id', 'page_index'));

	}

	//查询社群名称

	private function getExerciseBookRoleInfo ($exercise_book_id, $role_type)
	{
		$role_info = \DB::table("db_ex_business.t_exercise_books_roles")
			->where('app_id', '=', AppUtils::getAppID())
			->where('exercise_book_id', '=', $exercise_book_id)
			->where('role_type', '=', $role_type)
			->where('state', '=', 0)
			->first();
		if ($role_info) {
			$user_id = $role_info->user_id;
			//在t_users中查询用户详细信息
			$user_info = $this->getUserInfo($user_id);
			if ($user_info) {
				return $user_info->wx_nickname;
			} else {
				return '';
			}
			//            return $wx_nickname;
		} else {
			return '';
		}
	}

	//查询用户信息

	private function getExerciseList ($exercise_book_id, $search_content)
	{
		$whereRaw = ' 1=1 ';

		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and title like '" . "%" . $search_content . "%'";
		}

		$exercise_list = \DB::table('db_ex_business.t_exercises')
			->where('app_id', '=', AppUtils::getAppID())
			->where('exercise_book_id', '=', $exercise_book_id)
			->where('state', '!=', 2)
			->whereRaw($whereRaw)
			->orderby('created_at', 'desc')
			->paginate(10);
		if ($exercise_list) {
			foreach ($exercise_list as $key => $exercise) {
				//查询关联课程的详情
				if ($exercise->resource_type == 5) {//专栏
					$resource_info = $this->getPackageInfo($exercise->resource_id);
				} else {//课程
					$resource_info = Utils::getResourceInfo($exercise->resource_id, $exercise->resource_type);
				}
				if ($resource_info) {
					$exercise->img_url_compressed = $resource_info->img_url_compressed;
				} else {
					$exercise->img_url_compressed = '';
				}
			}
		}

		return $exercise_list;
	}

	//获取作业列表

	private function getPackageInfo ($package_id)
	{
		$package = \DB::table('db_ex_business.t_pay_products')
			->where('app_id', AppUtils::getAppID())
			->where('id', $package_id)
			->first();

		//查询专栏期数
		$resource_list = \DB::table("t_pro_res_relation")
			->where('app_id', AppUtils::getAppID())
			->where('product_id', $package_id)
			->where('resource_type', '<=', 4)
			->where('relation_state', StringConstants::RELATION_NORMAL)
			->get();
		if ($package) {
			$package->resource_count = count($resource_list);
		}

		return $package;
	}

	//查询专栏信息
	//查询专栏信息

	/**
	 * 函数名:uploadExerciseBook
	 * 参数:
	 *      1-title（作业本名称）
	 *      2-resource_id（关联的课程id）
	 *      3-resource_type(资源类型；1-图文；2-音频；3-视频；4-直播；5-专栏)
	 *      4-resource_name（关联的课程名称）
	 *      5-community_id（关联的社群id）
	 *      6-is_enable_notify（是否允许老师推送作业提醒开关，0-不允许；1-允许）
	 * 作用:创建作业本
	 * 作者:keven
	 * 时间:2017-07-31 19:45:27
	 * 返回值:jason格式（【code   data   msg】）
	 */
	public function uploadExerciseBook ()
	{
		$params        = Input::get('params');
		$operator_type = StringConstants::EXERCISE_BOOK_ADD;
		$ret           = $this->saveExerciseBook($params, $operator_type);

		if ($ret == '0') {

			return response()->json(Utils::pack(['exerciseBookId' => $this->exerciseBookId], StringConstants::Code_Succeed, "新增作业本成功"));
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}

	}

	//查询社群列表

	private function saveExerciseBook ($params, $operator_type)
	{
		//参数校验
		if (!array_key_exists('title', $params) || Utils::isEmptyString($params['title'])) {
			return '作业本名称不能为空';
		}
		if (!array_key_exists('resource_id', $params) || Utils::isEmptyString($params['resource_id'])) {
			return '关联的课程不能为空';
		}
		if (!array_key_exists('resource_type', $params) || Utils::isEmptyString($params['resource_type'])) {
			return '关联的课程类型不能为空';
		}
		if (!array_key_exists('resource_name', $params) || Utils::isEmptyString($params['resource_name'])) {
			return '关联的课程不能为空';
		}

		if ($operator_type == StringConstants::EXERCISE_BOOK_ADD) {//新增作业本
			$params['created_at']       = Utils::getTime();
			$params['app_id']           = AppUtils::getAppID();
			$params['exercise_book_id'] = Utils::getUniId('e_');

			$this->exerciseBookId = $params['exercise_book_id'];

			\DB::beginTransaction();
			$ret = \DB::table('db_ex_business.t_exercise_books')->insert($params);
			$msg = '新增作业本成功';
		} else {//更新作业本
			$params['updated_at'] = Utils::getTime();
			\DB::beginTransaction();
			$ret = \DB::table('db_ex_business.t_exercise_books')
				->where('app_id', '=', AppUtils::getAppID())
				->where('exercise_book_id', '=', $params['exercise_book_id'])
				->update($params);
			$msg = "更新作业本失败";
		}

		if ($params['resource_type'] == StringConstants::SINGLE_GOODS_ALIVE) {
			//将直播的人员添加为作业本的角色信息
			$alive_roles = \DB::table("db_ex_business.t_alive_role")
				->select('role_name', 'user_id')
				->where('app_id', '=', AppUtils::getAppID())
				->where('alive_id', '=', $params['resource_id'])
				->where('state', '=', 0)
				->get();
			if ($alive_roles) {
				$exercise_book_role = [];
				foreach ($alive_roles as $key => $role) {
					$exercise_book_role[ $key ]['user_id']          = $role->user_id;
					$exercise_book_role[ $key ]['app_id']           = AppUtils::getAppID();
					$exercise_book_role[ $key ]['exercise_book_id'] = $params['exercise_book_id'];
					$exercise_book_role[ $key ]['created_at']       = Utils::getTime();
					if (strpos($role->role_name, "讲师") === false) {
						//该人员为助教
						$exercise_book_role[ $key ]['role_type'] = StringConstants::EXERCISE_BOOK_ROLE_TYPE_ASSISTANT;
					} else {
						//该人员为讲师
						$exercise_book_role[ $key ]['role_type'] = StringConstants::EXERCISE_BOOK_ROLE_TYPE_TEACHER;
					}
				}
				//将人员插入到作业本角色表t_exercise_books_roles中
				$result = $this->setExerciseBookRoleInside($params['exercise_book_id'], $exercise_book_role);

				if ($result == 0) {
					\DB::commit();
				} else {
					\DB::rollBack();

					return "生成作业本角色信息失败";
				}
			} else {
				\DB::rollBack();

				return "该直播暂无人员设置";
			}
		} else {
			\DB::commit();
		}

		if ($ret) {
			return StringConstants::Code_Succeed;
		} else {
			return $msg;
		}

	}

	//查询资源列表

	private function setExerciseBookRoleInside ($exercise_book_id, $exercise_book_role)
	{

		//将该作业本已有的角色状态置位为删除,然后再插入/更新
		//        \DB::beginTransaction();

		\DB::table('db_ex_business.t_exercise_books_roles')
			->where('app_id', '=', AppUtils::getAppID())
			->where('exercise_book_id', '=', $exercise_book_id)
			->where('state', '=', 0)
			->update(['state' => 1]);
		if (!count($exercise_book_role) > 0) {

			//            \DB::commit();
			return StringConstants::Code_Succeed;
		}

		foreach ($exercise_book_role as $key => $role) {
			try {

				//新增
				$result = \DB::table('db_ex_business.t_exercise_books_roles')->insert($role);
			} catch (\Exception $e) {
				//更新
				$result = \DB::table('db_ex_business.t_exercise_books_roles')
					->where('app_id', '=', AppUtils::getAppID())
					->where('exercise_book_id', '=', $exercise_book_id)
					->where('user_id', '=', $role['user_id'])
					->update(['state' => 0, 'role_type' => $role['role_type']]);
			}
			if (!$result) {
				//                \DB::rollBack();
				return StringConstants::Code_DB_Error;
			}
		}

		//        \DB::commit();
		return StringConstants::Code_Succeed;
	}

	//获取已关联作业本的资源id集合

	/**
	 * 函数名:updateExerciseBook
	 * 参数:
	 *      1-title（作业本名称）
	 *      2-resource_id（关联的课程id）
	 *      3-resource_type(资源类型；1-图文；2-音频；3-视频；4-直播；5-专栏)
	 *      4-resource_name（关联的课程名称）
	 *      5-community_id（关联的社群id）
	 *      6-is_enable_notify（是否允许老师推送作业提醒开关，0-不允许；1-允许）
	 *      7-exercise_book_id
	 * 作用:更新作业本
	 * 作者:keven
	 * 时间:2017-07-31 20:10:55
	 * 返回值:jason格式（【code   data   msg】）
	 */
	public function updateExerciseBook ()
	{
		$params = Input::get('params');
		if (!array_key_exists('exercise_book_id', $params)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, '缺少参数:作业本id'));
		}
		$exercise_book_id = $params['exercise_book_id'];
		$app_id           = AppUtils::getAppID();
		//查询该作业本的信息
		$exercise_book_info = $this->getExerciseBookInfo($app_id, $exercise_book_id);
		if ($exercise_book_info) {
			if (!array_key_exists('resource_id', $params)) {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, '缺少参数:关联的课程'));
			}
			if ($exercise_book_info->resource_id != $params['resource_id']) {
				//校验该作业本是否已经有人创建了作业,若有则不能变更关联的课程
				$is_has_exercise = $this->isHasExercise($app_id, $exercise_book_id);
				if ($is_has_exercise != 0) {//有人创建了作业
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, '作业本已存在作业,不能更改关联的课程'));
				}

			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, '该作业本不存在'));
		}
		$operator_type = StringConstants::EXERCISE_BOOK_EDIT;
		$ret           = $this->saveExerciseBook($params, $operator_type);

		if ($ret == '0') {

			return Utils::result('更新作业本成功!');
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}
	}

	//查询该业务的所有作业本

	/**
	 *函数名:getResourceListByType
	 * 参数:
	 *      resource_type(资源类型；1-图文；2-音频；3-视频；4-直播；5-专栏)
	 * 作用:获取资源列表
	 * 作者:keven
	 * 时间:2017-07-31 21:56:28
	 * 返回值:jason格式（code data msg）
	 *       data['resource_list']
	 */
	public function getResourceListByType ()
	{
		$resource_type         = Input::get('resource_type');
		$resource_list         = $this->getResourceList($resource_type);
		$data['resource_list'] = $resource_list;

		return Utils::result($data);
	}

	//保存作业本信息

	/**
	 * 函数名:setExerciseBookRole
	 * 参数:
	 *      1-exercise_book_id(作业本id)
	 *      2-exercise_book_roles(数组--结构:user_id、role_type)
	 * 作用:设置作业本角色
	 * 作者:keven
	 * 时间:2017-08-01 11:23:17
	 *返回值:json格式（code data msg）
	 */
	public function setExerciseBookRole ()
	{
		$exercise_book_id    = Input::get('exercise_book_id');
		$exercise_book_roles = Input::get('exercise_book_roles');

		if ($exercise_book_roles) {
			foreach ($exercise_book_roles as $key => $role) {
				$role['app_id']              = AppUtils::getAppID();
				$role['exercise_book_id']    = $exercise_book_id;
				$role['created_at']          = Utils::getTime();
				$exercise_book_roles[ $key ] = $role;
			}
		}
		$result = $this->setExerciseBookRoleInside($exercise_book_id, $exercise_book_roles);
		if ($result == StringConstants::Code_Succeed) {
			return response()->json(Utils::pack("0", StringConstants::Code_Succeed, '作业本角色设置成功'));
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, '作业本角色设置失败'));
		}
	}

	//将人员插入到作业本角色表t_exercise_books_roles中

	/**
	 * 函数名:changeExerciseState
	 * 参数:
	 *      1-exercise_book_id(作业本id)
	 *      2-exercise_id(作业id)
	 *      3-state（要修改的状态）
	 * 作用:修改作业的状态
	 * 作者:keven
	 * 时间:2017-08-03 10:16:28
	 * 返回值:json格式（code data msg）
	 */
	public function changeExerciseState ()
	{
		$state            = Input::get('state');
		$exercise_id      = Input::get('exercise_id');
		$exercise_book_id = Input::get('exercise_book_id');
		$app_id           = AppUtils::getAppID();
		if ($state != 0 && $state != 1 && $state != 2) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, '该状态值有误,请核实后再试'));
		}
		//参数验证
		$exercise_info = $this->getExerciseInfo($app_id, $exercise_book_id, $exercise_id);
		if ($exercise_info) {
			$result = \DB::table("db_ex_business.t_exercises")
				->where("app_id", '=', $app_id)
				->where("exercise_book_id", '=', $exercise_book_id)
				->where("exercise_id", '=', $exercise_id)
				->update(['state' => $state]);
			if ($result) {
				return Utils::result($result);
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, '修改状态失败'));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, '该作业信息不存在'));
		}

	}

	//校验该作业本是否已经有人创建了作业,若有则不能变更关联的课程

	private function getExerciseInfo ($app_id, $exercise_book_id, $exercise_id)
	{
		$exercise_info = \DB::table("db_ex_business.t_exercises")
			->where("app_id", '=', $app_id)
			->where("exercise_book_id", '=', $exercise_book_id)
			->where("exercise_id", '=', $exercise_id)
			->first();

		return $exercise_info;
	}

	//查询作业本的信息

	/**
	 * 函数名:setExerciseBookSystemState
	 * 参数:
	 *      1-is_show_exercise_system(0-不开启;1-开启)
	 * 作用:设置作业系统开关状态
	 * 作者:keven
	 * 时间:2017-08-03 23:02:37
	 * 返回值:json格式（code data msg）
	 */
	public function setExerciseBookSystemState ()
	{
		$app_id                  = AppUtils::getAppID();
		$is_show_exercise_system = Input::get('is_show_exercise_system');
		$result                  = \DB::table("db_ex_config.t_app_module")
			->where('app_id', '=', $app_id)
			->update(['is_show_exercise_system' => $is_show_exercise_system]);
		if ($result) {
			return Utils::result($result);
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, '该状态值有误,请核实后再试'));
		}

	}
}



















