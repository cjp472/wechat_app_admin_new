<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\Input;

class SaleController extends Controller
{
	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	//获取所有的分销数据
	public function sale ()
	{
		$ruler  = Input::get("ruler", -1);
		$search = Input::get("search", "");
		if ($ruler == -1)//所有数据
		{
			if (empty($search)) {
				$all_info = \DB::table('t_sales')->leftJoin('t_channels', 't_sales.channel_id', '=', 't_channels.id')
					->select("t_sales.id", "t_sales.sale_name", "t_sales.applier", "t_sales.phone", "t_sales.xiaoe_img_url",
						"t_sales.xiaoe_nick_name", "t_sales.sale_content", "t_sales.apply_at", "t_sales.remark", "t_sales.state", "t_sales.sale_url",
						"t_channels.view_count", "t_channels.open_count")
					->where("t_sales.app_id", "=", $this->app_id)->orderBy("apply_at", "desc")->paginate(10);
			} else {
				$all_info = \DB::table('t_sales')->leftJoin('t_channels', 't_sales.channel_id', '=', 't_channels.id')
					->select("t_sales.id", "t_sales.sale_name", "t_sales.applier", "t_sales.phone", "t_sales.xiaoe_img_url",
						"t_sales.xiaoe_nick_name", "t_sales.sale_content", "t_sales.apply_at", "t_sales.remark", "t_sales.state", "t_sales.sale_url",
						"t_channels.view_count", "t_channels.open_count")
					->where("t_sales.app_id", "=", $this->app_id)->where("applier", "like", "%" . $search . "%")
					->orderBy("apply_at", "desc")->paginate(10);
			}
		} else//有申请中、同意和拒绝三种状态
		{
			if (empty($search)) {
				$all_info = \DB::table('t_sales')->leftJoin('t_channels', 't_sales.channel_id', '=', 't_channels.id')
					->select("t_sales.id", "t_sales.sale_name", "t_sales.applier", "t_sales.phone", "t_sales.xiaoe_img_url",
						"t_sales.xiaoe_nick_name", "t_sales.sale_content", "t_sales.apply_at", "t_sales.remark", "t_sales.state", "t_sales.sale_url",
						"t_channels.view_count", "t_channels.open_count")
					->where("t_sales.app_id", "=", $this->app_id)->where("state", "=", $ruler)
					->orderBy("apply_at", "desc")->paginate(10);
			} else {
				$all_info = \DB::table('t_sales')->leftJoin('t_channels', 't_sales.channel_id', '=', 't_channels.id')
					->select("t_sales.id", "t_sales.sale_name", "t_sales.applier", "t_sales.phone", "t_sales.xiaoe_img_url",
						"t_sales.xiaoe_nick_name", "t_sales.sale_content", "t_sales.apply_at", "t_sales.remark", "t_sales.state", "t_sales.sale_url",
						"t_channels.view_count", "t_channels.open_count")
					->where("t_sales.app_id", "=", $this->app_id)->where("state", "=", $ruler)
					->where("applier", "like", "%" . $search . "%")->orderBy("apply_at", "desc")->paginate(10);
			}
		}

		//        //获取已存在的渠道名称
		//        $channels=\DB::table("t_channels")->select("id","name","resource_title","acc_url")->where("app_id","=",$this->app_id)
		//        ->where("generate_type","=","0")->orderBy("created_at","desc")->get();

		//        return View("admin.sale",compact('ruler','search','all_info','channels'));
		return View("admin.sale", compact('ruler', 'search', 'all_info'));
	}

	/**
	 * 根据渠道名称查找历史渠道列表
	 */
	public function get_channel ()
	{

		$apply_id  = Input::get('id');
		$innerhtml = '';

		//在表t_sales中查找该id对应的申请记录
		$apply_result = \DB::table('t_sales')
			->where('id', '=', $apply_id)
			->first();
		if ($apply_result) {

			//根据sale_content在表t_channels中查询
			$channels = \DB::table('t_channels')
				->where('resource_title', '=', $apply_result->sale_content)
				->where('generate_type', '=', 0)
				->orderBy("created_at", "desc")
				->get();

			if ($channels) {

				foreach ($channels as $key => $channel) {
					//                    <tr>
					//                        <td>
					//                           <input type="radio" name="channel_select" value="{{$value->acc_url}}"channel_id="{{$value->id}}"  @if($key == 0) checked="checked" @endif />
					//                        </td>
					//                        <td>{{$value->name}}</td>
					//                        <td>{{$value->resource_title}}</td>
					//                    </tr>
					$innerhtml .= '<tr>';
					$innerhtml .= '<td>';
					$innerhtml .= '<input type="radio" id="' . $channel->id . '" name="channel_select" class="with-gap" value="' . $channel->acc_url . '" channel_id="' . $channel->id . '"';
					if ($key == 0) {
						$innerhtml .= 'checked="checked" /><label for="' . $channel->id . '"></label></td>';
					} else {
						$innerhtml .= '/><label for="' . $channel->id . '"></label></td>';
					}
					$innerhtml .= '<td>' . $channel->name . '</td>';
					$innerhtml .= '<td>' . $channel->resource_title . '</td>';
				}

				return response()->json(['code' => 0, 'data' => $innerhtml]);

			} else {
				$innerhtml .= '<div>没有相关数据</div>';

				return response()->json(['code' => -2, 'msg' => $innerhtml]);
			}
		} else {
			$innerhtml .= '<div>没有相关数据</div>';

			return response()->json(['code' => -2, 'msg' => $innerhtml]);
		}
	}

	//同意分销
	public function agreeSale ()
	{
		$id         = Input::get("id");
		$channel_id = Input::get("channel_id");
		$sale_url   = Input::get("sale_url");
		$sale_info  = \DB::select("select * from t_sales where app_id=? and id=?", [$this->app_id, $id]);//获取分销信息

		if (empty($sale_url))//然后生成渠道生成channel_id和自己新建链接绑定,最后更新
		{
			//生成渠道
			$channels                  = [];
			$channels['app_id']        = $this->app_id;
			$channels['name']          = $sale_info[0]->sale_name;
			$channels['generate_type'] = 1;
			$channels['channel_type']  = $sale_info[0]->sale_type;
			if ($channels['channel_type'] == 0 || $channels['channel_type'] == 2)//专栏、直播
			{
				if ($channels['channel_type'] == 2) {
					$channels['payment_type']  = 2;
					$channels['resource_type'] = 4;
				} else {
					$channels['payment_type']  = 3;
					$channels['resource_type'] = 0;
				}
				$channels['resource_id']    = $sale_info[0]->sale_id;
				$channels['resource_title'] = $sale_info[0]->sale_content;
				$channels['product_id']     = $sale_info[0]->sale_id;
				$channels['channel_type']   = 0;
			} else if ($channels['channel_type'] == 1)//主页
			{
				$channels['payment_type']   = 0;
				$channels['resource_type']  = 0;
				$channels['resource_id']    = null;
				$channels['resource_title'] = $sale_info[0]->sale_content;
				$channels['product_id']     = null;
			}
			$channels['created_at'] = Utils::getTime();
			$channel_id             = \DB::table("t_channels")->insertGetId($channels);

			if (session("is_collection") == 0) //自有
			{
				$wx_app_id = \DB::connection("mysql_config")->select("select wx_app_id from t_app_conf where app_id=?
                and wx_app_type='1' ", [$this->app_id]);
				if ($sale_info[0]->sale_type == 0)//专栏
				{
					$sale_url = AppUtils::getUrlHeader($this->app_id) . $wx_app_id[0]->wx_app_id . "." . env("DOMAIN_NAME") .
						Utils::contentUrl($channel_id, 3, 0, "", $sale_info[0]->sale_id, $this->app_id);
				} else if ($sale_info[0]->sale_type == 2)//直播
				{
					//查询该直播所属的专栏在表t_pro_res_relation
					//查找表t_pro_res_relation中relation_state为0对应的product_id
					$res_product_id = \DB::table('db_ex_business.t_pro_res_relation')
						->where(['app_id' => $this->app_id, 'resource_type' => '4', 'resource_id' => $sale_info[0]->sale_id])
						->where('relation_state', '=', '0')
						->pluck('product_id');
					//如果$res_product_id的值不存在，则删除$image_text_list中对应的记录
					if (!$res_product_id) {
						$product_id = '';
					} else {
						$product_id = $res_product_id[0];
					}
					$sale_url = AppUtils::getUrlHeader($this->app_id) . $wx_app_id[0]->wx_app_id . "." . env("DOMAIN_NAME") .
						Utils::contentUrl($channel_id, 2, 4, $sale_info[0]->sale_id, $product_id, $this->app_id);
				} else //主页
				{
					$sale_url = AppUtils::getUrlHeader($this->app_id) . $wx_app_id[0]->wx_app_id . "." . env("DOMAIN_NAME") . '/homepage/' .
						Utils::contentUrlHome($channel_id);
				}
			} else //代收
			{
				if ($sale_info[0]->sale_type == 0)//专栏
				{
					$sale_url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") .
						Utils::contentUrl($channel_id, 3, 0, "", $sale_info[0]->sale_id, $this->app_id);
				} else if ($sale_info[0]->sale_type == 2)//直播
				{
					//查询该直播所属的专栏在表t_pro_res_relation
					//查找表t_pro_res_relation中relation_state为0对应的product_id
					$res_product_id = \DB::table('db_ex_business.t_pro_res_relation')
						->where(['app_id' => $this->app_id, 'resource_type' => '4', 'resource_id' => $sale_info[0]->sale_id])
						->where('relation_state', '=', '0')
						->pluck('product_id');
					//如果$res_product_id的值不存在，则删除$image_text_list中对应的记录
					if (!$res_product_id) {
						$product_id = '';
					} else {
						$product_id = $res_product_id[0];
					}
					$sale_url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") .
						Utils::contentUrl($channel_id, 2, 4, $sale_info[0]->sale_id, $product_id, $this->app_id);
				} else //主页
				{
					$sale_url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") . "/" . $this->app_id . '/homepage/' .
						Utils::contentUrlHome($channel_id);
				}
			}

			//更新渠道表的acc_url
			$update_acc_url = \DB::update("update t_channels set acc_url=? where app_id=? and id=? limit 1",
				[$sale_url, $this->app_id, $channel_id]);
		} else //使用原来的链接
		{
			//先把原来的渠道类型更新为1
			$update_channels = \DB::update("update t_channels set generate_type='1' where app_id=? and id=? limit 1",
				[$this->app_id, $channel_id]);
		}

		//再更新流水表
		$update_sale = \DB::update("update t_sales set sale_url=?,channel_id=?,state='1' where app_id=?
        and id=? limit 1", [$sale_url, $channel_id, $this->app_id, $id]);
		if ($update_sale >= 0) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//拒绝分销
	public function disagreeSale ()
	{
		$id            = Input::get("id");
		$refuse_reason = Input::get("refuse_reason");
		$update        = \DB::update("update t_sales set state='2',refuse_reason=? where app_id=? and id=? limit 1",
			[$refuse_reason, $this->app_id, $id]);
		if ($update >= 0) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}
}