<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class MessageController extends Controller
{
	private $request;
	private $app_id;//系统的app_id

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	//消息首页
	public function message ()
	{
		//获取数据源
		$search = Input::get("search");
		$ruler  = Input::get("ruler");
		$typer  = Input::get("typer");
		if (empty($search)) {
			if ($typer)//群发//私人
			{
				$typern  = $typer - 1;
				$allInfo = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
					->Where("type", "=", $typern)->orderby("send_at", "desc")->paginate(10);
				$count   = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
					->Where("type", "=", $typern)->count();
			} else//默认所有
			{
				$allInfo = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
					->orderby("send_at", "desc")->paginate(10);
				$count   = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)->count();
			}
		} else {
			if ($ruler == 0)//内容
			{
				if ($typer)//类型
				{
					$typern  = $typer - 1;
					$allInfo = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)->where("type", "=", $typern)
						->Where("content", "like", "%" . $search . "%")->orderby("send_at", "desc")->paginate(10);
					$count   = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)->where("type", "=", $typern)
						->Where("content", "like", "%" . $search . "%")->count();
				} else {
					$allInfo = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
						->Where("content", "like", "%" . $search . "%")->orderby("send_at", "desc")->paginate(10);
					$count   = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
						->Where("content", "like", "%" . $search . "%")->count();
				}
			} else if ($ruler == 1)//发送人
			{
				if ($typer)//类型
				{
					$typern  = $typer - 1;
					$allInfo = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)->where("type", '=', $typern)
						->Where("send_nick_name", "like", "%" . $search . "%")->orderby("send_at", "desc")->paginate(10);
					$count   = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)->where("type", '=', $typern)
						->Where("send_nick_name", "like", "%" . $search . "%")->count();
				} else {
					$allInfo = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
						->Where("send_nick_name", "like", "%" . $search . "%")->orderby("send_at", "desc")->paginate(10);
					$count   = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
						->Where("send_nick_name", "like", "%" . $search . "%")->count();
				}
			} else if ($ruler == 2)//接收人
			{
				if ($typer)//类型
				{
					$typern  = $typer - 1;
					$allInfo = \DB::table("t_messages")
						->leftjoin('t_users', 't_messages.user_id', '=', 't_users.user_id')
						->select('t_messages.*', 't_users.wx_nickname')->where("t_messages.app_id", "=", $this->app_id)
						->where('t_messages.type', '=', $typern)
						->Where("t_users.wx_nickname", "like", "%" . $search . "%")->orderby("t_messages.send_at", "desc")->paginate(10);
					$count   = \DB::table("t_messages")
						->leftjoin('t_users', 't_messages.user_id', '=', 't_users.user_id')
						->select()->where("t_messages.app_id", "=", $this->app_id)->where("t_messages.type", '=', $typern)
						->Where("t_users.wx_nickname", "like", "%" . $search . "%")->count();
				} else {
					$allInfo = \DB::table("t_messages")
						->leftjoin('t_users', 't_messages.user_id', '=', 't_users.user_id')
						->select('t_messages.*', 't_users.wx_nickname')->where("t_messages.app_id", "=", $this->app_id)
						->Where("t_users.wx_nickname", "like", "%" . $search . "%")->orderby("t_messages.send_at", "desc")->paginate(10);
					$count   = \DB::table("t_messages")
						->leftjoin('t_users', 't_messages.user_id', '=', 't_users.user_id')
						->select()->where("t_messages.app_id", "=", $this->app_id)
						->Where("t_users.wx_nickname", "like", "%" . $search . "%")->count();
				}
			} else {
				$allInfo = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)
					->orderby("send_at", "desc")->paginate(10);
				$count   = \DB::table("t_messages")->select()->where("app_id", "=", $this->app_id)->count();
			}
		}
		//返回的数据
		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				$data[ $key ]["id"]             = $value->id;
				$data[ $key ]["type"]           = $value->type == '0' ? '私人' : '群发';
				$temp                           = \DB::select("select wx_nickname from t_users where app_id = ? and user_id = ?",
					[$this->app_id, $value->user_id]);
				$data[ $key ]["receiver"]       = $value->type == '1' ? '所有人' : (empty($temp) ? '无' : $temp[0]->wx_nickname);
				$data[ $key ]["send_nick_name"] = empty($value->send_nick_name) ? '无' : $value->send_nick_name;
				$data[ $key ]["content"]        = empty($value->content) ? '无' : $value->content;
				$data[ $key ]["send_at"]        = empty($value->send_at) ? '无' : $value->send_at;
				//判断state
				if ($value->state == 1)//撤回
				{
					$data[ $key ]["state"] = "已撤回";
				} else {
					$data[ $key ]["state"] = (time() - strtotime($value->send_at) > 0) ? '已发送' : '未发送';
				}
			}
		}

		return View("admin.message", compact('data', 'count', 'allInfo', 'search', 'ruler', 'typer'));
	}

	//推送消息页面(群发)
	public function messageAdd ()
	{
		$audioList = \DB::connection('mysql')->table('t_audio')->where('app_id', $this->app_id)->orderBy('created_at', 'desc')->get();
		$app_id    = AppUtils::getAppID();
		$result    = AppUtils::getModuleInfo($app_id);

		return view("admin.messageAdd", compact('audioList', 'result'));
	}

	//保存消息
	public function messageSave ()
	{
		$params                    = Input::get("params");
		$data                      = [];
		$data['app_id']            = $this->app_id;
		$data['type']              = '1';
		$data['send_nick_name']    = Input::get("sendNickName");
		$data['source']            = '0';
		$message_audio             = $params["message_audio"];
		$data['skip_type']         = $params["skip_type"];
		$data["skip_target"]       = $params["skip_target"];
		$data["content_clickable"] = $params["skip_title"];
		$data['content']           = Input::get("content");
		$data['send_at']           = Input::get("sendAt");

		$data['created_at'] = date('Y-m-d H:i:s', time());
		//        var_dump($params);
		//        exit;
		$insert = \DB::table("t_messages")->insertGetId($data);
		if ($insert) {
			if ($message_audio != "") {
				$message_id = \DB::select('select max(id) as M_id from t_messages where app_id = ?', [AppUtils::getAppID()]);

				$update = \DB::update('update t_audio set message_id = ? where app_id = ? and id = ?', [$message_id[0]->M_id, AppUtils::getAppID(), $message_audio]);

				if (!$update) {
					return response()->json(['ret' => 1]);
				}
			}

			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//编辑消息页面
	public function messageEdit ()
	{
		$result       = \DB::select("select send_at,send_nick_name,content from t_messages where app_id = ? and  id = ?",
			[$this->app_id, Input::get("id")]);
		$sendAt       = $result[0]->send_at;
		$sendNickName = $result[0]->send_nick_name;
		$content      = $result[0]->content;

		return View('admin.messageEdit', compact('sendAt', 'sendNickName', 'content'));
	}

	//更新消息
	public function messageUpdate ()
	{
		$update = \DB::update("update t_messages set send_at = ?,send_nick_name = ?,content = ? where id = ?",
			[Input::get("sendAt"), Input::get("sendNickName"), Input::get("content"), Input::get("id")]);
		if ($update >= 0) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//消息撤回
	public function messageDelete ()
	{
		$update = \DB::update("update t_messages set state = '1' where id = ?", [Input::get("id")]);
		if ($update >= 0) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}
}





