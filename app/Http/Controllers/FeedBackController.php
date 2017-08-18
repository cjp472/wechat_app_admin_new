<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class FeedBackController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	//用户页面
	public function feedback ()
	{
		$ruler     = trim(Input::get("ruler"));//维度
		$search    = trim(Input::get("search"));//搜索内容
		$apptype   = trim(Input::get("apptype", ''));//应用
		$wxapptype = $apptype ? $apptype - 1 : -1; //echo $wxapptype;

		//隐藏参数 快捷白名单
		$forbid = Input::get('forbid', 0);
		//获取搜索集,总数作为页脚参考
		if (empty($search)) {
			if ($wxapptype == -1) {
				$allInfo = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)
					->orderby("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)
					->orderby("created_at", "desc")->count();
			} else {
				$allInfo = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)->where('wx_app_type', '=', $wxapptype)
					->orderby("created_at", "desc")->paginate(10);
				$count   = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)->where('wx_app_type', '=', $wxapptype)
					->orderby("created_at", "desc")->count();
			}

		} else {
			if ($ruler == 0) //内容
			{
				if ($wxapptype == -1) {
					$allInfo = \DB::table("t_user_feedback")->select()->where("content", "like", "%" . $search . "%")
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()->where("content", "like", "%" . $search . "%")
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
				} else {
					$allInfo = \DB::table("t_user_feedback")->select()->where("content", "like", "%" . $search . "%")->where('wx_app_type', '=', $wxapptype)
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()->where("content", "like", "%" . $search . "%")->where('wx_app_type', '=', $wxapptype)
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
				}

			} else if ($ruler == 1) //时间
			{
				if ($wxapptype == -1) {
					$allInfo = \DB::table("t_user_feedback")->select()->where("created_at", "like", "%" . $search . "%")
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()->where("created_at", "like", "%" . $search . "%")
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
				} else {
					$allInfo = \DB::table("t_user_feedback")->select()->where("created_at", "like", "%" . $search . "%")->where('wx_app_type', '=', $wxapptype)
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()->where("created_at", "like", "%" . $search . "%")->where('wx_app_type', '=', $wxapptype)
						->where("app_id", "=", $this->app_id)->orderBy("created_at", "desc")->count();
				}

			} else if ($ruler == 2) //昵称
			{
				if ($wxapptype == -1) {
					$allInfo = \DB::table("t_user_feedback")->select('t_user_feedback.*', 't_users.wx_nickname')
						->leftjoin('t_users', 't_user_feedback.user_id', '=', 't_users.user_id')
						->where("t_users.wx_nickname", "like", "%" . $search . "%")
						->where("t_user_feedback.app_id", "=", $this->app_id)->orderBy("t_user_feedback.created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()
						->leftjoin('t_users', 't_user_feedback.user_id', '=', 't_users.user_id')
						->where("t_users.wx_nickname", "like", "%" . $search . "%")
						->where("t_user_feedback.app_id", "=", $this->app_id)->orderBy("t_user_feedback.created_at", "desc")->count();
				} else {
					$allInfo = \DB::table("t_user_feedback")->select('t_user_feedback.*', 't_users.wx_nickname')
						->leftjoin('t_users', 't_user_feedback.user_id', '=', 't_users.user_id')
						->where("t_users.wx_nickname", "like", "%" . $search . "%")->where('t_user_feedback.wx_app_type', '=', $wxapptype)
						->where("t_user_feedback.app_id", "=", $this->app_id)->orderBy("t_user_feedback.created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()
						->leftjoin('t_users', 't_user_feedback.user_id', '=', 't_users.user_id')
						->where("t_users.wx_nickname", "like", "%" . $search . "%")->where('t_user_feedback.wx_app_type', '=', $wxapptype)
						->where("t_user_feedback.app_id", "=", $this->app_id)->orderBy("t_user_feedback.created_at", "desc")->count();
				}

			} else //所有信息
			{
				if ($wxapptype == -1) {
					$allInfo = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)
						->orderBy("created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)
						->orderBy("created_at", "desc")->count();
				} else {
					$allInfo = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)->where('wx_app_type', '=', $wxapptype)
						->orderBy("created_at", "desc")->paginate(10);
					$count   = \DB::table("t_user_feedback")->select()->where("app_id", "=", $this->app_id)->where('wx_app_type', '=', $wxapptype)
						->orderBy("created_at", "desc")->count();
				}

			}
		}//print_r($allInfo);
		//有搜索集下一步，没有就为空数组
		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				//先取用户信息
				$data[ $key ]['id']       = $value->id;
				$data[ $key ]['app_id']   = $this->app_id;
				$temp                     = \DB::select("select wx_avatar,wx_nickname from t_users where app_id = ? and user_id = ?",
					[$this->app_id, $value->user_id]);
				$data[ $key ]['avatar']   = empty($temp[0]->wx_avatar) ? '../images/default.png' : $temp[0]->wx_avatar;
				$data[ $key ]['nickname'] = empty($temp[0]->wx_nickname) ? '无' : $temp[0]->wx_nickname;
				//最新回复时间 //content,
				$messageinfo = \DB::select("select send_at,content,send_nick_name from t_messages where app_id = '$this->app_id' and user_id = '$value->user_id' and type = '0'
               and src_id = '$value->id' and source = '3' and state = '0' order by send_at desc limit 0,1 ");
				if (!$messageinfo) {
					$messageinfo = \DB::select("select send_at,content,send_nick_name from t_messages where app_id = '$this->app_id' and user_id = '$value->user_id' and type = '0'
                and source = '0' and state in ('0','2','3') and send_at>'$value->created_at' order by send_at desc limit 0,1 ");
				}
				$data[ $key ]['replied_at']     = empty($messageinfo[0]->send_at) ? '--' : $messageinfo[0]->send_at;
				$data[ $key ]['adminmsg']       = $messageinfo ? $messageinfo[0]->content : '';
				$data[ $key ]['send_nick_name'] = $messageinfo ? $messageinfo[0]->send_nick_name : '';
				if ($forbid == 1) {
					$inforbid                 = \DB::select("select app_id from t_forbid_users where app_id='$this->app_id' and user_id='$value->user_id'");
					$data[ $key ]['inforbid'] = $inforbid ? 1 : 0;
				}
				$data[ $key ]['user_id']     = $value->user_id;
				$data[ $key ]['wx_app_type'] = $value->wx_app_type ? '公众号' : '小程序';
				$data[ $key ]['content']     = $value->content;
				$data[ $key ]['created_at']  = empty($value->created_at) ? '无' : $value->created_at;
			}
		}//print_r($data);
		//小程序接入判断
		$micro_func = \DB::connection('mysql_config')->select("select wx_app_id from t_app_conf where app_id = '$this->app_id' and wx_app_type=0 ");
		$micro_func = empty($micro_func[0]->wx_app_id) ? 0 : $micro_func[0]->wx_app_id;
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

		return View('admin.feedback', compact('data', 'search', 'ruler', 'allInfo', 'count', 'model', 'micro_func', 'apptype', 'forbid'));
	}

	//白名单操作
	public function forbiduser ()
	{
		$user_id = Input::get('userId');
		$stat    = Input::get('stat');

		if ($user_id) {
			$data['app_id']  = $this->app_id;
			$data['user_id'] = $user_id;
			if ($stat == 1) {
				$data['created_at'] = Utils::getTime();
				$date['updated_at'] = Utils::getTime();
				//加白名单
				$result = \DB::table('t_forbid_users')->insert($data);

				if ($result) {
					return response()->json(['code' => 0, 'msg' => '操作成功']);
				} else {
					return response()->json(['code' => 1, 'msg' => '操作失败']);
				}
			} else {
				//除白名单
				//$result = \DB::table('t_forbid_users')->where($data)->delete();
				return response()->json(['code' => 1, 'msg' => '操作失败']);
			}

		}

	}
}





