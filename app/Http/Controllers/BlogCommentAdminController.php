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

class BlogCommentAdminController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	public function BlogCommentAdmin ()
	{

		$comment_attr   = Input::get('comment_attr', '');
		$search_content = Input::get('search_content', '');
		$comment_state  = Input::get('comment_state', '-1');

		if (empty($search_content)) {
			if ($comment_state == -1) {
				$commentList = \DB::table('t_comments')
					->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
					->where('t_comments.app_id', '=', $this->app_id)
					->where('t_comments.type', '=', 3)
					->orderby('t_comments.created_at', 'desc')
					->groupby('t_comments.id')
					->paginate(10);

				$user_info = [];
				foreach ($commentList as $key => $value) {
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
			} else if ($comment_state < 2) {
				$commentList = \DB::table('t_comments')
					->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
					->where('t_comments.app_id', '=', $this->app_id)
					->where('t_comments.type', '=', 3)
					->where('t_comments.comment_state', '=', $comment_state)
					->orderby('t_comments.created_at', 'desc')
					->groupby('t_comments.id')
					->paginate(10);

				$user_info = [];
				foreach ($commentList as $key => $value) {
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
			} else if ($comment_state == 2) {
				$commentList = \DB::table('t_comments')
					->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
					->where('t_comments.app_id', '=', $this->app_id)
					->where('t_comments.type', '=', 3)
					->where('t_comments.is_top', '=', 0)
					->orderby('t_comments.created_at', 'desc')
					->groupby('t_comments.id')
					->paginate(10);

				$user_info = [];
				foreach ($commentList as $key => $value) {
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
			} else if ($comment_state == 3) {
				$commentList = \DB::table('t_comments')
					->select(\DB::raw("t_comments.* , t_comments.id as comment_id , 
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
					->where('t_comments.app_id', '=', $this->app_id)
					->where('t_comments.type', '=', 3)
					->where('t_comments.is_top', '=', 1)
					->orderby('t_comments.created_at', 'desc')
					->groupby('t_comments.id')
					->paginate(10);

				$user_info = [];
				foreach ($commentList as $key => $value) {
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
			}
		} //输入框有查询文本
		else {
			//全部状态
			if ($comment_state == -1) {
				if ($comment_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , 
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->whereIn('t_comments.user_id', $user_id)
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , 
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);

					$user_info = [];
					foreach ($commentList as $key => $value) {
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
				}
			} //显示和隐藏 两种状态
			else if ($comment_state < 2) {
				if ($comment_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                    t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                    t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->whereIn('t_comments.user_id', $user_id)
						->where('t_comments.comment_state', '=', $comment_state)
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                    t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                    t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->where('t_comments.comment_state', '=', $comment_state)
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);

					$user_info = [];
					foreach ($commentList as $key => $value) {
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
			} //未精选状态
			else if ($comment_state == 2) {
				if ($comment_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , 
                    t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                    t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->whereIn('t_comments.user_id', $user_id)
						->where('t_comments.is_top', '=', 0)
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , 
                    t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , 
                    t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->where('t_comments.is_top', '=', 0)
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);

					$user_info = [];
					foreach ($commentList as $key => $value) {
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

				// 精选状态
			} else if ($comment_state == 3) {
				if ($comment_attr == "t_users.wx_nickname") {
					$user_info = [];
					$user_id   = [];
					$temp      = DB::table('t_users')->select('wx_nickname', 'wx_avatar', 'user_id')
						->where('app_id', '=', $this->app_id)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->get();
					foreach ($temp as $value) {
						$user_info[ $value->user_id ] = $value;
						$user_id[ count($user_id) ]   = $value->user_id;
					}

					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , 
                    t_comments.type , t_comments.record_id ,t_comments.record_title , t_comments.content , 
                    t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->whereIn('t_comments.user_id', $user_id)
						->where('t_comments.is_top', '=', 1)
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);

				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , 
                    t_comments.type , t_comments.record_id ,t_comments.record_title , t_comments.content , 
                    t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', 3)
						->where($comment_attr, 'like', '%' . $search_content . '%')
						->where('t_comments.is_top', '=', 1)
						->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
						->orderby('t_comments.created_at', 'desc')
						->groupby('t_comments.id')
						->paginate(10);

					$user_info = [];
					foreach ($commentList as $key => $value) {
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

		return view('admin.blogCommentAdmin', compact('commentList', 'user_info', 'comment_attr', 'search_content', 'comment_state'));

	}

	//更新评论状态
	public function updateBlogCommentState ()
	{
		//对应评论在评论表的id
		$id           = $_GET["id"];
		$state        = $_GET["state"];
		$type         = $_GET["type"];
		$recordId     = $_GET["recordId"];
		$current_time = Utils::getTime();

		//原评论数
		$comment_count = \DB::table('t_community_record')
			->select('comment_count')
			->where('t_community_record.app_id', '=', $this->app_id)
			->where('id', '=', $recordId)->get();
		$count         = [];
		foreach ($comment_count as $value) {
			$count[] = $value->comment_count;
		}

		if ($type == "show") {
			//更新评论状态
			if ($state == 1) {
				$count[0]--;
				$blog_update = \DB::table('t_community_record')
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('id', '=', $recordId)
					->update(['comment_count' => $count[0], 'updated_at' => $current_time]);
			} else {
				$count[0]++;
				$blog_update = \DB::table('t_community_record')
					->where('t_community_record.app_id', '=', $this->app_id)
					->where('id', '=', $recordId)
					->update(['comment_count' => $count[0], 'updated_at' => $current_time]);
			}
			$id = \DB::table('t_comments')
				->where('id', '=', $id)
				->update(['comment_state' => $state, 'updated_at' => $current_time]);
			if ($id) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => 1, 'msg' => '更新失败']);
			}
		} else {
			//更新评论状态
			$id = \DB::table('t_comments')
				->where('id', '=', $id)
				->update(['is_top' => $state, 'updated_at' => $current_time]);
			if ($id) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => 1, 'msg' => '更新失败']);
			}
		}
	}
}