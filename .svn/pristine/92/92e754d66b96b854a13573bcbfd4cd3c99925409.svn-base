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

class CommentAdminController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	public function commentAdmin ()
	{

		$comment_attr   = Input::get('comment_attr', '');
		$search_content = Input::get('search_content', '');
		$comment_state  = Input::get('comment_state', '-1');
		$apptype        = trim(Input::get("apptype"));//应用
		$orderParameter = Input::get('order_parameter', '');  //  用于排序的get参数(可能值：1,10,2,20,'')

		$orderBy   = 't_comments.created_at';    //默认为评论时间
		$orderSort = 'desc';                   //默认为降序排列
		switch ($orderParameter) {
			case 1:
				$orderBy   = 't_comments.created_at';
				$orderSort = 'desc';
				break;
			case 10:
				$orderBy   = 't_comments.created_at';
				$orderSort = 'asc';
				break;
			case 2:
				$orderBy   = 't_comments.zan_num';
				$orderSort = 'desc';
				break;
			case 20:
				$orderBy   = 't_comments.zan_num';
				$orderSort = 'asc';
				break;

			default:
				break;
		}

		$wxapptype = $apptype ? $apptype - 1 : -1; //echo $wxapptype;

		$type      = Input::get('type', "");
		$record_id = Input::get('record_id', "");
		$reurl     = Input::get('reurl');

		if ($type != "" && $record_id != "") {
			//全部状态
			if ($comment_state == -1) {
				if ($wxapptype == -1) {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , t_comments.type ,
                        t_comments.record_id ,t_comments.record_title, t_comments.content ,t_comments.wx_app_type,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top, t_comments.zan_num"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', $type)
						->where('t_comments.record_id', $record_id)
						//                        ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , t_comments.type ,
                        t_comments.record_id ,t_comments.record_title, t_comments.content ,t_comments.wx_app_type,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top, t_comments.zan_num"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '=', $type)
						->where('t_comments.wx_app_type', '=', $wxapptype)
						->where('t_comments.record_id', $record_id)
						//                        ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				}

				$user_info = [];
				foreach ($commentList as $key => $value) {
					$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->user_id     = '';
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}
			}
		} else if (empty($search_content)) {
			//全部状态
			if ($comment_state == -1) {
				if ($wxapptype == -1) {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , t_comments.type ,
                        t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '<', 3)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id , t_comments.type ,
                        t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '<', 3)
						->where('t_comments.wx_app_type', '=', $wxapptype)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				}

				$commentsSum = DB::table('t_comments')
					->where('t_comments.app_id', '=', $this->app_id)->where('t_comments.type', '<', 3)->count();

				$user_info = [];
				foreach ($commentList as $key => $value) {
					$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->user_id     = '';
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}
			} //显示/隐藏状态
			else if ($comment_state < 2) {
				if ($wxapptype == -1) {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '<', 3)
						->where('t_comments.comment_state', '=', $comment_state)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.wx_app_type', '=', $wxapptype)
						->where('t_comments.type', '<', 3)
						->where('t_comments.comment_state', '=', $comment_state)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				}

				$commentsSum = DB::table('t_comments')
					->where('t_comments.app_id', '=', $this->app_id)->where('t_comments.type', '<', 3)
					->where('t_comments.comment_state', '=', $comment_state)->count();

				$user_info = [];
				foreach ($commentList as $key => $value) {
					$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->user_id     = '';
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}
			} //未精选状态
			else if ($comment_state == 2) {
				if ($wxapptype == -1) {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '<', 3)
						->where('t_comments.is_top', '=', 0)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.wx_app_type', '=', $wxapptype)
						->where('t_comments.type', '<', 3)
						->where('t_comments.is_top', '=', 0)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				}

				$commentsSum = DB::table('t_comments')
					->where('t_comments.app_id', '=', $this->app_id)->where('t_comments.type', '<', 3)
					->where('t_comments.is_top', '=', 0)->count();

				$user_info = [];
				foreach ($commentList as $key => $value) {
					$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->user_id     = '';
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}
			} //精选状态
			else if ($comment_state == 3) {
				if ($wxapptype == -1) {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.type', '<', 3)
						->where('t_comments.is_top', '=', 1)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				} else {
					$commentList = \DB::table('t_comments')
						->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
						->where('t_comments.app_id', '=', $this->app_id)
						->where('t_comments.wx_app_type', '=', $wxapptype)
						->where('t_comments.type', '<', 3)
						->where('t_comments.is_top', '=', 1)
						//                            ->orderby('t_comments.created_at','desc')
						->orderby($orderBy, $orderSort)
						->groupby('t_comments.id')
						->paginate(10);
				}

				$commentsSum = DB::table('t_comments')
					->where('t_comments.app_id', '=', $this->app_id)->where('t_comments.type', '<', 3)
					->where('t_comments.is_top', '=', 1)->count();

				$user_info = [];
				foreach ($commentList as $key => $value) {
					$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->user_id     = '';
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[ count($user_info) ] = $temp;
				}
			}
		} //有搜索内容
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

					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

				} else {
					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

					$user_info = [];
					foreach ($commentList as $key => $value) {
						$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->user_id     = '';
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

					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                            t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                            t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							->where('t_comments.comment_state', '=', $comment_state)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                            t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                            t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							->where('t_comments.comment_state', '=', $comment_state)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

				} else {
					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                            t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                            t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->where('t_comments.comment_state', '=', $comment_state)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                            t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                            t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->where('t_comments.comment_state', '=', $comment_state)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

					$user_info = [];
					foreach ($commentList as $key => $value) {
						$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)
							->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->user_id     = '';
							$temp->wx_nickname = '无';
							$temp->wx_avatar   = '';
						}
						$user_info[ count($user_info) ] = $temp;
					}
				}
			} //未精选 状态
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

					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							->where('t_comments.is_top', '=', 0)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							->where('t_comments.is_top', '=', 0)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

				} else {
					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->where('t_comments.is_top', '=', 0)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->where('t_comments.is_top', '=', 0)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

					$user_info = [];
					foreach ($commentList as $key => $value) {
						$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)
							->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->user_id     = '';
							$temp->wx_nickname = '无';
							$temp->wx_avatar   = '';
						}
						$user_info[ count($user_info) ] = $temp;
					}
				}
			} // 精选状态
			else if ($comment_state == 3) {
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

					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							->where('t_comments.is_top', '=', 1)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content , t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->whereIn('t_comments.user_id', $user_id)
							->where('t_comments.is_top', '=', 1)
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

				} else {
					if ($wxapptype == -1) {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.type', '<', 3)
							->where('t_comments.is_top', '=', 1)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					} else {
						$commentList = \DB::table('t_comments')
							->select(\DB::raw("t_comments.* , t_comments.id as comment_id ,
                        t_comments.type , t_comments.record_id ,t_comments.record_title, t_comments.content ,  t_comments.wx_app_type ,
                        t_comments.created_at as comment_time , t_comments.comment_state , t_comments.is_top"))
							->where('t_comments.app_id', '=', $this->app_id)
							->where('t_comments.wx_app_type', '=', $wxapptype)
							->where('t_comments.type', '<', 3)
							->where('t_comments.is_top', '=', 1)
							->where($comment_attr, 'like', '%' . $search_content . '%')
							->orderByRaw("length($comment_attr)-length('$search_content')")//按匹配度查询
							//                                ->orderby('t_comments.created_at','desc')
							->orderby($orderBy, $orderSort)
							->groupby('t_comments.id')
							->paginate(10);
					}

					$user_info = [];
					foreach ($commentList as $key => $value) {
						$temp = DB::table('t_users')->select('user_id', 'wx_nickname', 'wx_avatar')
							->where('app_id', '=', $this->app_id)
							->where('user_id', '=', $value->user_id)
							->first();
						if (empty($temp)) {
							$temp              = new \stdClass();
							$temp->user_id     = '';
							$temp->wx_nickname = '无';
							$temp->wx_avatar   = '';
						}
						$user_info[ count($user_info) ] = $temp;
					}
				}
			}
		}

		//小程序接入判断
		$micro_func = \DB::connection('mysql_config')->select("select wx_app_id from t_app_conf where app_id = '$this->app_id' and wx_app_type=0 ");
		$micro_func = empty($micro_func[0]->wx_app_id) ? 0 : $micro_func[0]->wx_app_id;
		//来源类型
		$apptypeName = [
			'0' => '小程序', '1' => '公众号',
		];

		//回复模板信息
		$model     = [];
		$modelInfo = \DB::select("select id , send_nick_name as name, content from t_messages_model where app_id = ?", [$this->app_id]);
		if ($modelInfo) {
			foreach ($modelInfo as $key => $value) {
				$model[ $key ][] = $value->id;
				$model[ $key ][] = $value->name;
				$model[ $key ][] = $value->content;
			}
		}

		return view('admin.commentAdmin', compact('commentList', 'user_info', 'comment_attr', 'search_content', 'comment_state', 'type', 'record_id', 'reurl', 'model', 'micro_func', 'apptype', 'apptypeName', 'orderParameter'));
	}

	//更新评论状态
	public function updateCommentState ()
	{
		$id            = $_GET["id"];
		$state         = $_GET["state"];
		$type          = $_GET["type"];
		$resource_type = $_GET["resource_type"];
		$current_time  = Utils::getTime();

		//更新隐显状态
		if ($type == "show") {
			$record_id = $_GET['record_id'];
			$id        = \DB::table('t_comments')->where('app_id', '=', $this->app_id)->where('id', '=', $id)
				->update(['comment_state' => $state, 'updated_at' => $current_time]);
			switch ($resource_type) {
				case 0:
					if ($state == 1) //隐藏
					{
						$dec = \DB::update("update t_image_text set comment_count = comment_count-1 where app_id = ? and id= ?", [$this->app_id, $record_id]);
					} else  //显示
					{
						$add = \DB::update("update t_image_text set comment_count = comment_count+1 where app_id = ? and id = ?", [$this->app_id, $record_id]);
					}
					break;
				case 1:
					if ($state == 1) //隐藏
					{
						$dec = \DB::update("update t_audio set comment_count = comment_count-1 where app_id = ? and id= ?", [$this->app_id, $record_id]);
					} else  //显示
					{
						$add = \DB::update("update t_audio set comment_count = comment_count+1 where app_id = ? and id = ?", [$this->app_id, $record_id]);
					}
					break;
				case 2:
					if ($state == 1) //隐藏
					{
						$dec = \DB::update("update t_video set comment_count = comment_count-1 where app_id = ? and id= ?", [$this->app_id, $record_id]);
					} else  //显示
					{
						$add = \DB::update("update t_video set comment_count = comment_count+1 where app_id = ? and id = ?", [$this->app_id, $record_id]);
					}
					break;
			}
		} else //更新置顶状态
		{
			$id = \DB::table('t_comments')->where('app_id', '=', $this->app_id)->where('id', '=', $id)
				->update(['is_top' => $state, 'updated_at' => $current_time]);
		}

		return response()->json(['code' => 0, 'msg' => '修改成功']);

	}

	//提交管理员回复
	public function submitAdminComment ()
	{

		$replay_content     = $_POST["replay_content"];
		$comment_admin_name = $_POST["comment_admin_name"];
		$user_id            = $_POST["user_id"];
		$comment_id         = $_POST["comment_id"];

		//获取当前时间
		$current_time = Utils::getTime();

		//更新管理员回复的评论
		$updateComment = DB::update('update t_comments set admin_name = ? , admin_content = ? , admin_created_at = ?
          where app_id = ? and id = ?', [$comment_admin_name, $replay_content, $current_time, $this->app_id, $comment_id]);
		if ($updateComment) {
			//如果更新成功 则给用户推送一条私人消息
			//先获取管理员回复的评论的所有信息
			$commentToReplayResult = DB::table('t_comments')->where('id', '=', $comment_id)->get();
			$comment               = [];
			foreach ($commentToReplayResult as $key => $value) {
				$comment[] = $value;
			}

			$message_array = ['app_id'    => $this->app_id, 'user_id' => $user_id, 'type' => 0, 'source' => 0, 'content' => $replay_content,
							  'skip_type' => $comment[0]->type + 1, 'skip_target' => $comment[0]->record_id, 'send_nick_name' => $comment_admin_name, 'created_at' => $current_time, 'updated_at' => $current_time, 'send_at' => $current_time];
			$result        = \DB::table('t_messages')->insert(
				$message_array
			);

			return response()->json(['code' => 0, 'msg' => '回复成功']);
		} else {
			return response()->json(['code' => 1, 'msg' => '回复失败，请检查网络']);
		}
		//
		//
		//        //先获取管理员回复的评论的所有信息
		//        $commentToReplayResult = DB::table('t_comments')->where('id','=',$comment_id)->get();
		//        $comment = array();
		//        foreach ($commentToReplayResult as $key => $value){
		//            $comment[] = $value;
		//        }
		//
		//        $admin_comment_array = array('app_id'=>$comment[0]->app_id , 'user_id'=>$comment[0]->user_id ,
		//            'type'=>$comment[0]->type , 'record_id'=>$comment[0]->record_id , 'record_title'=>$comment[0]->record_title, 'content'=>$replay_content ,
		//            'src_comment_id'=>$comment_id , 'src_user_id'=>$user_id , 'src_content'=>$comment[0]->content ,
		//            'is_admin'=>1 ,'admin_name'=>$comment_admin_name ,'wx_app_type'=>$comment[0]->wx_app_type, 'created_at'=>$current_time);
		//
		//        $insertAdminCommentResult = DB::table('t_comments')->insert($admin_comment_array);
		//        if($insertAdminCommentResult){
		//
		//            //插入到消息表中 系统管理员 私人消息
		//            $message_array = array('app_id' => $this->app_id ,'user_id' => $user_id,'type' => 0,'source' => 0, 'content' => $replay_content,
		//                'send_nick_name' => $comment_admin_name,'created_at' => $current_time, 'updated_at' => $current_time, 'send_at' => $current_time);
		//            $result = \DB::table('t_messages')->insert(
		//                $message_array
		//            );
		//
		//            return response()->json(['code' => 0, 'msg' => '回复成功','array' => $comment ,'arrayinsert' => $admin_comment_array]);
		//        }else{
		//            return response()->json(['code' => 1, 'msg' => '回复失败，请检查网络','array' => $comment ,'arrayinsert' => $admin_comment_array]);
		//        }

	}
}