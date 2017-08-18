<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26 0026
 * Time: 下午 2:47
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use App\Http\Controllers\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class BlogAdminController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	public function BlogAdmin ()
	{

		$blog_attr      = Input::get('blog_attr', '');
		$search_content = Input::get('search_content', '');
		$blog_state     = Input::get('blog_state', '-1');

		//无搜索内容
		if (empty($search_content)) {
			//全部状态
			if ($blog_state == -1) {
				$blogResult = \DB::table('t_community_record')
					->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
					->where('t_community_record.app_id', '=', $this->app_id)
					->orderby('t_community_record.created_at', 'desc')
					->groupby('t_community_record.id')
					->paginate(10);

				$user_info = [];
				foreach ($blogResult as $key => $value) {
					$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}
				//帖子数量
				$blogSum = DB::table('t_community_record')
					->where('t_community_record.app_id', '=', $this->app_id)->count();

			} //显示或隐藏状态
			else if ($blog_state < 2) {
				$blogResult = \DB::table('t_community_record')
					->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id ,  
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('t_community_record.display_state', '=', $blog_state)
					->orderby('t_community_record.created_at', 'desc')
					->groupby('t_community_record.id')
					->paginate(10);

				$user_info = [];
				foreach ($blogResult as $key => $value) {
					$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}
				//帖子数量
				$blogSum = DB::table('t_community_record')
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('t_community_record.display_state', '=', $blog_state)
					->count();
			} //未精选状态
			else if ($blog_state == 2) {
				$blogResult = \DB::table('t_community_record')
					->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('t_community_record.is_top', '=', 0)
					->orderby('t_community_record.created_at', 'desc')
					->groupby('t_community_record.id')
					->paginate(10);

				$user_info = [];
				foreach ($blogResult as $key => $value) {
					$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}

				$blogSum = DB::table('t_community_record')
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('t_community_record.is_top', '=', 0)
					->count();
			} //精选状态
			else if ($blog_state == 3) {
				$blogResult = \DB::table('t_community_record')
					->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('t_community_record.is_top', '=', 1)
					->orderby('t_community_record.created_at', 'desc')
					->groupby('t_community_record.id')
					->paginate(10);

				$user_info = [];
				foreach ($blogResult as $key => $value) {
					$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}

				$blogSum = DB::table('t_community_record')
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('t_community_record.is_top', '=', 1)
					->count();
			}
		} else {
			//输入框有查询文本
			//全部状态
			if ($blog_state == -1) {
				if ($blog_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                        t_community_record.content , t_community_record.img_url,
                        t_community_record.created_at as create_time , t_community_record.display_state,
                        t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->whereIn('t_community_record.user_id', $user_id)
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

				} else {
					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                        t_community_record.content , t_community_record.img_url,
                        t_community_record.created_at as create_time , t_community_record.display_state,
                        t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

					$user_info = [];
					foreach ($blogResult as $key => $value) {
						$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)
							->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->wx_nickname = '无';
							$temp->wx_avatar   = '';
						}
						$user_info[ count($user_info) ] = $temp;
					}
				}

			} else if ($blog_state < 2) {
				if ($blog_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->where('t_community_record.display_state', '=', $blog_state)
						->whereIn('t_community_record.user_id', $user_id)
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

				} else {
					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->where('t_community_record.display_state', '=', $blog_state)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

					$user_info = [];
					foreach ($blogResult as $key => $value) {
						$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)
							->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->wx_nickname = '无';
							$temp->wx_avatar   = '';
						}
						$user_info[ count($user_info) ] = $temp;
					}
				}
			} else if ($blog_state == 2) {
				if ($blog_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->where('t_community_record.is_top', '=', 0)
						->whereIn('t_community_record.user_id', $user_id)
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

				} else {
					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id , 
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->where('t_community_record.is_top', '=', 0)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

					$user_info = [];
					foreach ($blogResult as $key => $value) {
						$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)
							->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->wx_nickname = '无';
							$temp->wx_avatar   = '';
						}
						$user_info[ count($user_info) ] = $temp;
					}
				}

			} else if ($blog_state == 3) {
				if ($blog_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id ,  
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->where('t_community_record.is_top', '=', 1)
						->whereIn('t_community_record.user_id', $user_id)
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

				} else {
					$blogResult = \DB::table('t_community_record')
						->select(\DB::raw("t_community_record.* , t_community_record.id as comment_id ,  
                t_community_record.content , t_community_record.img_url,
                t_community_record.created_at as create_time , t_community_record.display_state,
                t_community_record.is_top"))
						->where('t_community_record.app_id', '=', $this->app_id)
						->where('t_community_record.is_top', '=', 1)
						->where($blog_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($blog_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_community_record.created_at', 'desc')
						->groupby('t_community_record.id')
						->paginate(10);

					$user_info = [];
					foreach ($blogResult as $key => $value) {
						$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)
							->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->wx_nickname = '无';
							$temp->wx_avatar   = '';
						}
						$user_info[ count($user_info) ] = $temp;
					}
				}
			}
		}

		return view('admin.blogAdmin', compact('blogResult', 'user_info', 'blog_attr', 'search_content', 'blog_state'));
	}

	//更新帖子状态update_blog_state
	public function updateBlogState ()
	{
		//对应评论在评论表的id

		$id           = $_GET["id"];
		$state        = $_GET["state"];
		$type         = $_GET["type"];
		$current_time = Utils::getTime();

		//        echo $id+">>"+$state+">>"+$type+">>"+$current_time;
		if ($type == "show") {
			//更新博客状态
			$blog_update = \DB::table('t_community_record')
				->where('t_community_record.app_id', '=', $this->app_id)
				->where('id', '=', $id)
				->update(['display_state' => $state, 'updated_at' => $current_time]);
			if ($blog_update) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => 1, 'msg' => '更新失败']);
			}
		} else {
			//更新帖子状态
			$blog_update = \DB::table('t_community_record')
				->where('t_community_record.app_id', '=', $this->app_id)
				->where('id', '=', $id)
				->update(['is_top' => $state, 'updated_at' => $current_time]);
			if ($blog_update) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => 1, 'msg' => '更新失败']);
			}
		}
	}
}