<?php

//问答专区控制器
//问答创建、编辑，答主编辑
// 2017年05月09日
//    完成部分：
// 1、问答创建调通
// 2、问答编辑调通
// 3、问答上下架接口、问题上下架接口调通
// 4、保存答主信息接口
//    未完成部分：
// 1、问题列表和答主列表 筛选（稍繁琐）

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ImageUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use App\Http\Controllers\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class QuestionAndAnswerController extends Controller
{

	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	public function createQuestionAndAnswer ()
	{

		$page_type = 0;

		return view('admin.questionAndAnswer.manageQuestionAndAnswer', compact('page_type'));
	}

	//  创建问答页面

	public function editQuestionAndAnswer ()
	{

		$page_type = 1;
		$app_id    = $this->app_id;
		$id        = Input::get('id', '');
		if (empty($id))
			return response()->json(['code' => 0, 'msg' => 'id is required']);

		$data = \DB::table('t_que_products')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return view('admin.questionAndAnswer.manageQuestionAndAnswer', compact('data', 'page_type'));
	}

	//  编辑问答页面

	public function questionAndAnswerDetail ()
	{
		$page_type = Input::get("page_type", "1");     //  0-问题列表  1 - 答主列表(默认)  2 - 设置页面

		$app_id = $this->app_id;
		$today  = date('Y-m-d', time());

		$product_info = $this->getQueProductInfo();

		if (!$product_info)
			return view('admin.communityOperate.typeSelect');

		if ($page_type == 0) {
			$question_list = \DB::table('t_que_question')
				->where('app_id', '=', $app_id)
				->where('product_id', '=', $product_info->id)
				->where('phase', '!=', 0)
				->orderBy('created_at', 'desc')
				->paginate(10);
			foreach ($question_list as $item) {
				$item->listenCount = \DB::table('t_que_listen')
					->where('app_id', '=', $app_id)
					->where('product_id', '=', $item->product_id)
					->where('question_id', '=', $item->question_id)
					->count();
				$item->listenToday = \DB::table('t_que_listen')
					->where('app_id', '=', $app_id)
					->where('product_id', '=', $item->product_id)
					->where('question_id', '=', $item->question_id)
					->where('created_at', '>=', $today)
					->count();
			}
		} else if ($page_type == 1) {
			$answerer_list = \DB::table('t_que_answerer')
				->where('app_id', '=', $app_id)
				->where('product_id', '=', $product_info->id)
				->orderBy('created_at', 'desc')
				->paginate(10);
			foreach ($answerer_list as $item) {
				$item->answerCount = \DB::table('t_que_question')
					->where('app_id', '=', $app_id)
					->where('product_id', '=', $item->product_id)
					->where('answerer_id', '=', $item->answerer_id)
					->where('phase', '=', 2)
					->count();
				$item->answerToday = \DB::table('t_que_question')
					->where('app_id', '=', $app_id)
					->where('product_id', '=', $item->product_id)
					->where('answerer_id', '=', $item->answerer_id)
					->where('phase', '=', 2)
					->where('created_at', '>=', $today)
					->count();
				$item->url         = $product_info->url;
			}
		} else {
			$settingData = $this->getSeting();
		}

		return view('admin.questionAndAnswer.questionAndAnswerDetail', compact('product_info', 'answerer_list', 'question_list', 'settingData', 'page_type'));
	}

	//  问答详情页面  包括所有的问题列表和答主列表

	private function getQueProductInfo ()
	{
		$app_id      = $this->app_id;
		$que_product = \DB::table('t_que_products')
			->where('app_id', '=', $app_id)
			->first();

		if (!$que_product)
			return '';

		$url_arr          = $this->getURL($app_id, $que_product->id); //答主链接
		$que_product->url = $url_arr[1];

		$arr['type']           = 5;
		$arr['resource_type']  = '';
		$arr['resource_id']    = '';
		$arr['product_id']     = $que_product->id;
		$arr['app_id']         = $app_id;
		$page_url              = \GuzzleHttp\json_encode($arr);
		$que_product->page_url = $url_arr[0] . Utils::urlSafe_b64encode($page_url);

		return $que_product;
	}

	//编辑答主信息页面

	private function getURL ($app_id, $product_id)
	{
		$app_info = AppUtils::getAppConfInfo($app_id);  // app 信息
		$url      = '';
		$page_url = '';
		//生成群主设置访问链接
		if ($app_info) {
			if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {
				if ($app_info->use_collection == 0) {
					$url      = AppUtils::getUrlHeader($app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
					$page_url = $url . '/content_page/';
					$url      .= '/ask_invite_answer/';
				} else {
					$url      = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME');
					$page_url = $url . '/content_page/';
					$url      .= '/' . $app_id . '/ask_invite_answer/';
				}

				$url .= $product_id;
			}
		}

		return [$page_url, $url];

	}

	//  问题列表

	private function getSeting ()
	{
		$res = \DB::table('db_ex_config.t_app_module')
			->select('is_show_eavesdropper_number', 'is_que_sms_remind')
			->where('app_id', '=', $this->app_id)
			->first();

		$settingData = [
			'isShowListen' => $res->is_show_eavesdropper_number,
			'isSmsRemind'  => $res->is_que_sms_remind,
		];

		return $settingData;
	}

	//  答主列表

	public function editAnswerer ()
	{
		$app_id       = $this->app_id;
		$answerer_id  = Input::get('answerer_id');
		$state        = Input::get('state');
		$product_info = $this->getQueProductInfo();

		if (empty($answerer_id))
			return response()->json(['code' => 1, 'msg' => 'answerer_id is required']);

		$answerer_info = \DB::table('t_que_answerer')
			->where('app_id', '=', $app_id)
			->where('product_id', '=', $product_info->id)
			->where('answerer_id', '=', $answerer_id)
			->first();

		return view('admin.questionAndAnswer.editAnswerer', compact('answerer_info', 'state'));
	}

	//设置页面

	public function getQuestionList ()
	{
		$app_id      = $this->app_id;
		$que_product = $this->getQueProductInfo();
		$last7date   = date('Y-m-d', strtotime('-7 days'));
		$last30date  = date('Y-m-d', strtotime('-30 days'));
		$tomorrow    = date('Y-m-d', strtotime('+1 days'));
		$today       = date('Y-m-d', time());

		$search = Input::get('search', '');
		$time   = Input::get('time', '');
		$time1  = Input::get('time1', '');

		//        $start = Input::get('start','');
		//        $end   = Input::get('end','');
		$whereRaw = '';

		if ($search) {

			$whereRaw .= self::whereAdd($whereRaw) . "app_id = '$app_id'";
			$whereRaw .= self::whereAdd($whereRaw) . "product_id = '$que_product->id'";
			$whereRaw .= self::whereAdd($whereRaw) . "phase != 0";

			$whereRaw .= self::whereAdd($whereRaw) . "(questioner_name like '%" . $search . "%'" . " or answerer_name like '%" . $search . "%')";

		}
		if ($time || $time1) {
			if (!$search) {
				$whereRaw .= self::whereAdd($whereRaw) . "app_id = '$app_id'";
				$whereRaw .= self::whereAdd($whereRaw) . "product_id = '$que_product->id'";
				$whereRaw .= self::whereAdd($whereRaw) . "phase != 0";
			}

			if ($time) {
				if ($time == 7)
					$start = $last7date;
				else if ($time == 30)
					$start = $last30date;

				$end = $tomorrow;
			}

			if ($time1) {
				//            ?2017-05-10 ~ 2017-05-11
				$start = substr($time1, 0, 10);
				$end   = substr($time1, -10, 10);
				//
			}
			$whereRaw .= self::whereAdd($whereRaw) . "date(created_at) >= '$start'";
			$whereRaw .= self::whereAdd($whereRaw) . "date(created_at) <= '$end'";
		}

		if ($whereRaw) {
			$question_list = \DB::table("t_que_question")
				->select()
				->whereRaw("$whereRaw")
				->orderBy('created_at', 'desc')
				->paginate(10);
		} else {
			$question_list = \DB::table('t_que_question')
				->where('app_id', '=', $app_id)
				->where('product_id', '=', $que_product->id)
				->where('phase', '!=', 0)
				->orderBy('created_at', 'desc')
				->paginate(10);
		}
		//        var_dump($question_list);
		//          exit;

		//还需要两个数据：偷听者、今日偷听
		foreach ($question_list as $item) {
			$item->listenCount = \DB::table('t_que_listen')
				->where('app_id', '=', $app_id)
				->where('product_id', '=', $item->product_id)
				->where('question_id', '=', $item->question_id)
				->count();
			$item->listenToday = \DB::table('t_que_listen')
				->where('app_id', '=', $app_id)
				->where('product_id', '=', $item->product_id)
				->where('question_id', '=', $item->question_id)
				->where('created_at', '>=', $today)
				->count();
		}

		return view('admin.questionAndAnswer.questionList', compact('question_list'));
	}

	private function whereAdd ($str)
	{
		return $str ? ' and ' : '';
	}



	/*************
	 * ajax
	 *************/
	// 保存问答专区
	public function getResponderList ()
	{
		$app_id = $this->app_id;
		$today  = date('Y-m-d', time());

		$product_info = $this->getQueProductInfo();
		$url          = $product_info->url;
		$state        = Input::get('state', '');
		$search       = Input::get('search', '');
		$whereRaw     = '';

		$whereRaw .= self::whereAdd($whereRaw) . "app_id = '$app_id'";
		$whereRaw .= self::whereAdd($whereRaw) . "product_id = '$product_info->id'";

		if (!Utils::isEmptyString($state))
			$whereRaw .= self::whereAdd($whereRaw) . "state = '$state'";

		if ($search)
			$whereRaw .= self::whereAdd($whereRaw) . "(answerer_name like '%" . $search . "%'" . " or phone like '%" . $search . "%')";

		//        if($whereRaw){
		$answerer_list = \DB::table("t_que_answerer")
			->select()
			->whereRaw("$whereRaw")
			->orderBy('created_at', 'desc')
			->paginate(10);

		//        } else {
		//            $answerer_list = \DB::table('t_que_answerer')
		//                ->where('app_id','=',$app_id)
		//                ->where('product_id','=',$product_info->id)
		//                ->orderBy('created_at','desc')
		//                ->paginate(10);
		//        }

		//还需要两个数据  总回答数 和 今日回答数
		foreach ($answerer_list as $item) {
			$item->answerCount = \DB::table('t_que_question')
				->where('app_id', '=', $app_id)
				->where('product_id', '=', $item->product_id)
				->where('answerer_id', '=', $item->answerer_id)
				->where('phase', '=', 2)
				->count();
			$item->answerToday = \DB::table('t_que_question')
				->where('app_id', '=', $app_id)
				->where('product_id', '=', $item->product_id)
				->where('answerer_id', '=', $item->answerer_id)
				->where('created_at', '>=', $today)
				->where('phase', '=', 2)
				->count();
			$item->url         = $product_info->url;
		}

		//        var_dump($answerer_list);
		//        exit;
		return view('admin.questionAndAnswer.responderList', compact('answerer_list', 'url'));
	}

	//上下架问答专区操作

	public function getSettingPage ()
	{

		$settingData = $this->getSeting();

		return view('admin.questionAndAnswer.settingPage', compact('settingData'));
	}

	//上下架问题操作

	/**
	 * 是否显示偷听人数  isShowListen   1-显示  0-隐藏
	 * 是否短信提醒     isSmsRemind    1-提醒  0-不提醒
	 * 提交问答设置
	 */
	public function commitSetting ()
	{
		$params = Input::get("params");

		$data['is_show_eavesdropper_number'] = $params['isShowListen'];
		$data['is_que_sms_remind']           = $params['isSmsRemind'];
		$data['updated_at']                  = Utils::getTime();

		$res = \DB::table('db_ex_config.t_app_module')
			->where('app_id', '=', $this->app_id)
			->update($data);

		if (!$res) return response()->json(['code' => 1024, 'msg' => 'db error']);

		return response()->json(['code' => 0, 'msg' => 'ok']);
	}

	//上下架 答主 操作

	public function saveQuestionAndAnswer (Request $request)
	{
		$app_id = $this->app_id;
		$id     = Input::get('id', '');

		$data['title']   = Input::get('title');
		$data['desc']    = Input::get('desc');
		$data['img_url'] = Input::get('img_url');
		// 压缩

		if (array_key_exists('img_url', $data)) {
			$data['img_url_compressed'] = $data['img_url'];
			//ResContentComm::imageDeal($data['img_url'], 't_que_products', $id);//,160,120,60);
			ImageUtils::resImgCompress($request, 'db_ex_business.t_que_products', $app_id, $id, $data['img_url']);
		}

		//        if (array_key_exists('img_url', $data)) {
		////            $data['img_url_compressed'] = $data['img_url'];
		//            ResContentComm::imageDealo($data['img_url'], 't_community', 1, 100, 100, 80, 'img_url_compressed');
		//        }
		$data['price'] = Input::get('price');
		$res           = Utils::checkPrice($data['price'], 1000000);
		if ($res !== 0) return response()->json(['code' => 8, 'msg' => $res]);

		$data['listen_for_business']   = Input::get('listen_for_business');
		$data['listen_for_answer']     = Input::get('listen_for_answer');
		$data['listen_for_questioner'] = Input::get('listen_for_questioner');
		$sumOfPercent                  = $data['listen_for_business'] + $data['listen_for_answer'] + $data['listen_for_questioner'];

		if ($sumOfPercent != 100) return response()->json(['code' => 16, 'msg' => ' sum of percent  must = 100']);

		foreach ($data as $k => $v) {
			if (Utils::isEmptyString($v))
				return response()->json(['code' => 2, 'msg' => $k . ' is required']);
		}

		$data['state'] = Input::get('state');
		if (Utils::isEmptyString($data['state'])) return response()->json(['code' => 2, 'msg' => 'state is required']);

		$data['updated_at'] = Utils::getTime();

		if ($id) { //保存
			$res = \DB::table('t_que_products')
				->where('app_id', '=', $app_id)
				->where('id', '=', $id)
				->update($data);

			if (!$res > 0) return response()->json(['code' => 1024, 'msg' => 'db error']);

			return response()->json(['code' => 0, 'msg' => 'ok', 'data' => $id]);

		} else { //新建
			//新建之前先查表 如果已有问答 则返回FALSE  每个应用只能创建一条问答
			$qa = \DB::table('t_que_products')
				->where('app_id', '=', $app_id)
				->first();

			if ($qa)
				return response()->json(['code' => 1, 'msg' => 'the app already had the questionANDanswer']);

			$data['app_id']     = $app_id;
			$data['id']         = Utils::getUniId('q_');
			$data['created_at'] = Utils::getTime();

			$res = \DB::table('t_que_products')->insert($data);
			if (!$res) return response()->json(['code' => 1024, 'msg' => 'db error']);
			if ($res) {
				if (array_key_exists('img_url', $data)) {
					//ResContentComm::imageDeal($data['img_url'], 't_que_products', $data['id']);//,160,120,60);
					ImageUtils::resImgCompress($request, 'db_ex_business.t_que_products', $data['app_id'], $data['id'], $data['img_url']);
				}

				return response()->json(['code' => 0, 'msg' => 'ok', 'data' => $data['id']]);
			}

		}
	}

	//保存答主信息

	public function changeStateQueProducts ()
	{
		$app_id = $this->app_id;
		$id     = Input::get('id');
		if (empty($id)) return response()->json(['code' => 1, 'msg' => 'id is required']);

		$state = Input::get('state');
		if (Utils::isEmptyString($state)) return response()->json(['code' => 2, 'msg' => 'state is required']);

		$res = \DB::table('t_que_products')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->update(['state' => $state, 'updated_at' => Utils::getTime()]);
		if (!$res > 0) return response()->json(['code' => 1024, 'msg' => 'de error']);

		return response()->json(['code' => 0, 'msg' => $state]);
	}

	//    //邀请答主操作  生成链接  每个问答专区只有一个
	//    public function inviteAnswerer(){
	//        $app_id = $this->app_id;
	////        $product_id = Input::get('product_id');t_user_account
	//
	//        $product_id = \DB::table('t_que_products')
	//            ->where('app_id','=',$app_id)
	//            ->value('id');
	//        echo $product_id;
	//        if (empty($product_id))
	//            return response()->json(['code' => 0 , 'msg' => 'no que products']);
	//
	//        $app_info = AppUtils::getAppConfInfo($app_id);  // app 信息
	//        $url = '';
	//        //生成资源访问链接
	//        if($app_info){
	//            if(!empty($app_info->wx_app_id) || $app_info->use_collection == 1)
	//            {
	//                if($app_info->use_collection == 0){
	//                    $url = AppUtils::getUrlHeader($app_id).$app_info->wx_app_id.'.'.env('DOMAIN_NAME');
	//                }else{
	//                    $url = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME') . '/'. $app_id ;
	//                }
	//                $url .= '/ask_invite/';
	//                $url .= $product_id;
	//            }
	//        }
	//
	//        echo $url;
	//
	//    }

	public function changeQuestionState ()
	{
		$app_id      = $this->app_id;
		$question_id = Input::get('question_id');
		$state       = Input::get('state');

		if (empty($question_id) || Utils::isEmptyString($state))
			return response()->json(['code' => 1, 'msg' => 'parameter is required']);

		$res = \DB::table('t_que_question')
			->where('app_id', '=', $app_id)
			->where('question_id', '=', $question_id)
			->update(['state' => $state, 'updated_at' => Utils::getTime()]);
		if (!$res > 0) return response()->json(['code' => 1024, 'msg' => 'db error']);

		return response()->json(['code' => 0, 'msg' => 'ok']);
	}

	public function changeAnswererState ()
	{
		$app_id      = $this->app_id;
		$answerer_id = Input::get('answerer_id');
		$state       = Input::get('state');

		if (empty($answerer_id) || Utils::isEmptyString($state))
			return response()->json(['code' => 1, 'msg' => 'parameter is required']);

		if ($state == 0) {  //需要上线操作
			$price = \DB::table('t_que_answerer')
				->where('app_id', '=', $app_id)
				->where('answerer_id', '=', $answerer_id)
				->value('price');
			if ($price > 0) { //判断价格

			} else {
				return response()->json(['code' => 2, 'msg' => 'price must > 0']);
			}
		}

		$res = \DB::table('t_que_answerer')
			->where('app_id', '=', $app_id)
			->where('answerer_id', '=', $answerer_id)
			->update(['state' => $state, 'updated_at' => Utils::getTime()]);
		if (!$res > 0) return response()->json(['code' => 1024, 'msg' => 'db error']);

		return response()->json(['code' => 0, 'msg' => 'ok']);
	}

	//答主回答了,短信提醒提问者

	public function saveAnswerer (Request $request)
	{
		$app_id     = $this->app_id;
		$product_id = $this->getProductidByAppid();

		$answerer_id = Input::get('answerer_id');

		if (empty($answerer_id)) return response()->json(['code' => 1, 'msg' => 'answererid is required']);

		$answerer_avatar = Input::get('answerer_avatar');
		if (!Utils::isEmptyString($answerer_avatar))
			$data['avatar_large'] = $answerer_avatar;

		$data['answerer_name']   = Input::get('answerer_name');
		$data['phone']           = Input::get('phone');
		$data['position']        = Input::get('position');
		$data['summary']         = Input::get('summary');
		$data['price']           = Input::get('price');
		$data['profit_business'] = Input::get('profit_business');
		$data['profit_answer']   = Input::get('profit_answer');
		$data['updated_at']      = Utils::getTime();

		$res = Utils::checkPrice($data['price'], 1000000);
		if ($res !== 0) return response()->json(['code' => 8, 'msg' => $res]);

		$res = \DB::table('t_que_answerer')
			->where('app_id', '=', $app_id)
			->where('product_id', '=', $product_id)
			->where('answerer_id', '=', $answerer_id)
			->update($data);
		if ($res > 0) {
			if (!Utils::isEmptyString($answerer_avatar))
				ImageUtils::queHeadImgCompress($request, $app_id, $answerer_id, $answerer_avatar);

			return response()->json(['code' => 0, 'msg' => 'ok']);
		} else {
			return response()->json(['code' => 1024, 'msg' => 'db error']);
		}
	}

	private function getProductidByAppid ()
	{

		$app_id      = $this->app_id;
		$que_product = \DB::table('t_que_products')
			->where('app_id', '=', $app_id)
			->first();
		if (empty($que_product->id)) return '';

		return $que_product->id;
	}

	public function isHaveQA ()
	{
		$product = $this->getProductidByAppid();
		if ($product) return response()->json(['code' => 0]);
		else return response()->json(['code' => 1]);

	}

	/**
	 * 提交问题的回答
	 * 参数:1-id(问题id);2-answerer_content(答案:音频);3-answerer_length(语音时长);4-answerer_size（语音文件大小）
	 */
	public function commitAnswer ()
	{
		$app_id              = $this->app_id;
		$question_id         = Input::get('id');
		$answerer_content    = Input::get('answerer_content');
		$answerer_length     = Input::get('answerer_length');
		$answerer_size       = Input::get('answerer_size');
		$is_enable_eavesdrop = Input::get('is_enable_eavesdrop');
		//查询该业务是否开启了后台回答问题的开关
		$moduleInfo = AppUtils::getModuleInfo($app_id);
		if ($moduleInfo) {
			if ($moduleInfo[0]->has_set_answer == 0) {//未开启
				return response()->json(Utils::pack("", StringConstants::Code_Failed, "未开启后台回答开关"));
			}
		} else {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, "未开启后台回答开关"));
		}

		if (Utils::isEmptyString($answerer_content)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, "请上传音频答案"));
		}
		if (Utils::isEmptyString($question_id)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, "请选择问题回答"));
		}
		if (Utils::isEmptyString($answerer_length) || $answerer_length < 0) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, "解码失败,请重新生成音频后再次尝试上传"));
		}
		if (Utils::isEmptyString($answerer_size) || $answerer_size <= 0) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, "问题答案音频大小不能为空"));
		}

		//查询该问题的详情,在表t_que_question中
		$questionInfo = $this->getQuestionInfo($question_id);
		if ($questionInfo->is_enable_eavesdrop == 0) $is_enable_eavesdrop = 0; // 如果该问题是私密的  则B端回答时 不允许偷听
		$product_id        = $this->getProductidByAppid();
		$is_updated_answer = 0;//未更新过答案
		if ($questionInfo) {
			$whereRaw = " 1=1 ";
			//            $whereRaw = " answerer_content is null ";

			if (!Utils::isEmptyString($questionInfo->answerer_content) || !Utils::isEmptyString($questionInfo->answerer_imgs) || !Utils::isEmptyString($questionInfo->answerer_text)) {
				//                return response()->json(Utils::pack("", StringConstants::Code_Failed, "该问题已回答,请勿重复回答"));
				$is_updated_answer = 1;
			}
			//添加事务
			\DB::beginTransaction();

			$res = \DB::table('db_ex_business.t_que_question')
				->where('app_id', '=', $app_id)
				->where('id', '=', $question_id)
				->where('product_id', '=', $product_id)
				->whereRaw($whereRaw)
				->update([
					'answerer_content'    => $answerer_content,
					'phase'               => 2,
					'answerer_length'     => $answerer_length,
					'answerer_size'       => $answerer_size,
					'is_updated_answer'   => $is_updated_answer,
					'is_enable_eavesdrop' => $is_enable_eavesdrop,
					'answered_at'         => Utils::getTime(),
					'updated_at'          => Utils::getTime(),
				]);
			if ($res) {
				//回答成功之后的逻辑:
				//1-修改t_orders表中的que_check_state字段为1
				$change_que_check_state = \DB::table("db_ex_business.t_orders")
					->where('order_id', '=', $questionInfo->order_id)
					->where('app_id', '=', $this->app_id)
					->where('que_check_state', '!=', 1)
					->update(['que_check_state' => 1, 'updated_at' => Utils::getTime()]);
				if ($change_que_check_state || $is_updated_answer) {
					//2-向提问人发送小纸条

					//给用户发送一条消息
					$question = $questionInfo;
					$appId    = $this->app_id;
					//                    $product_id = $this->getProductidByAppid();
					$content                      = $question->content;
					$message                      = [];
					$message['app_id']            = $appId;
					$message['type']              = 0;
					$message['user_id']           = $question->questioner_id;
					$message['src_id']            = $question->id;
					$message['send_user_id']      = $question->answerer_id;
					$message['send_nick_name']    = $question->answerer_name . "回答了您的问题";
					$message['skip_type']         = 8;
					$message['skip_target']       = $this->getWholeUrl("5", "", $question_id, $product_id, "", $app_id);
					$message['content']           = $content;
					$message['content_clickable'] = "点击查看";
					$message['send_at']           = Utils::getTime();
					$message['created_at']        = Utils::getTime();
					$message_result               = \DB::table('db_ex_business.t_messages')->insert($message);
					if ($message_result) {
						$this->sendReplySms($appId, $product_id, $question->questioner_id, $question->answerer_id);
						\DB::commit();

						return response()->json(['code' => 0, 'msg' => 'ok']);
					} else {
						\DB::rollback();

						return response()->json(['code' => 1024, 'msg' => '发送小纸条失败']);
					}
				} else {

					\DB::rollback();

					return response()->json(['code' => 1024, 'msg' => '修改对应的订单状态失败']);
				}
			} else {
				\DB::rollback();

				return response()->json(['code' => 1024, 'msg' => '修改问题答案失败']);
			}
		} else {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, "该问题未找到"));
		}
	}

	private function getQuestionInfo ($que_id)
	{
		//在问题表t_que_question中查询该id对应的问题详情
		$queInfo = \DB::table("db_ex_business.t_que_question")
			->where("app_id", '=', $this->app_id)
			->where("id", '=', $que_id)
			->first();

		return $queInfo;
	}

	//修改问题状态(未回答已退款)

	private function getWholeUrl ($type, $resourceType, $resourceId, $productId, $channelId = null, $appId = null)
	{

		$content_url       = Utils::contentUrl($channelId, $type, $resourceType, $resourceId, $productId, $appId);
		$host              = AppUtils::getUrlHeader($this->app_id);
		$whole_content_url = $host . env('DOMAIN_DUAN_NAME') . $content_url;

		return $whole_content_url;
	}

	//修改订单状态

	private function sendReplySms ($appId, $product_id, $questioner_id, $answerer_id)
	{
		// 查询功能模块
		$appModule = AppUtils::getModuleInfo($appId);
		// 问答专区是否开启短信提醒设置  0-关闭  1-开启  默认开启
		if ($appModule && $appModule[0]->is_que_sms_remind == 1) {

			//寻找答主信息
			$answererInfo = DB::select("select * from t_que_answerer where app_id=? and product_id=? and answerer_id=? limit 1", [$appId, $product_id, $answerer_id]);

			$answerer_name = null;
			if ($answererInfo != null && count($answererInfo) > 0) {
				$answerer_name = $answererInfo[0]->answerer_name;
			} else {
				return false;
			}

			//寻找问者信息
			$questionerInfo = DB::select("select * from t_users where app_id=? and user_id=? limit 1", [$appId, $questioner_id]);

			$question_phone = null;
			if ($questionerInfo != null && count($questionerInfo) > 0) {
				$question_phone = $questionerInfo[0]->phone;
			} else {
				return false;
			}

			$message = '【小鹅通】亲爱的用户，答主' . $answerer_name . '已经回答了您的问题，您可直接在问答专区查看！';

			if ($question_phone && $message) {

				$nowTime           = Utils::getTime();
				$verify_code_param = [
					"app_id"     => $appId,
					"user_id"    => $questioner_id,
					"type"       => 4,
					"phone"      => $question_phone,
					"code"       => $message,
					"created_at" => $nowTime,
				];
				DB::table('t_verify_codes')->insert($verify_code_param);

				Utils::sendsms($question_phone, $message);
			}
		}
	}

	//获取订单信息

	/**
	 * 退款列表
	 * 参数:search_content(用户昵称)
	 */
	public function refundList ()
	{

		$search_content = Input::get("search_content", '');
		$whereRaw       = "1=1";
		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and questioner_name like '%" . $search_content . "%'";
		}

		//在问题表t_que_question中查询expire_at小于当前时间的所有记录
		$refund_record_list = \DB::table("db_ex_business.t_que_question")
			->where("app_id", '=', $this->app_id)
			->where("state", '!=', StringConstants::QUESTION_STATE_DELETE)
			->where("phase", '=', StringConstants::QUESTION_PAY_STATE_PAY)//待回答
			->where("expire_at", '<', Utils::getTime())
			->whereRaw($whereRaw)
			->paginate(10);

		//        dump($refund_record_list);

		$data['refund_record_list'] = $refund_record_list;
		if ($refund_record_list) {
			return $this->result($data);
		} else {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, "暂无数据"));
		}
	}

	//获取问题详情

	private function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	/**
	 * 确认退款
	 * 参数:que_id_list-问题id列表
	 */
	public function commitRefund ()
	{
		$que_id_list           = Input::get("que_id_list");
		$result_order_update   = 0;
		$result_question_state = 0;
		foreach ($que_id_list as $key => $que_id) {
			//查询问题详情
			$que_info = $this->getQuestionInfo($que_id);

			if ($que_info) {
				//根据问题id和所属的问答专区在订单表t_orders中查询对应的订单
				$question_id   = $que_info->question_id;//问题id
				$product_id    = $que_info->product_id;//问答专区id
				$questioner_id = $que_info->questioner_id;//提问人的id
				$order_info    = $this->getQueOrderInfo($question_id, $product_id, $questioner_id);//获取订单信息
				if ($order_info) {

					//获取订单的单价
					$order_price  = $order_info->price;//单位:分
					$order_charge = $order_price * 0;//微信手续费:0
					//判断该商户可提现余额(库db_ex_finance中的表t_usable_balance)是否比订单的手续费多
					$account_money = Utils::getAppAccountMoney();
					if ($account_money >= $order_charge) {//商户可提现余额大于手续费
						//向账户系统发送退款申请(参数:app_id/order_id/user_id)
						$wholeUrl = env('ADMIN_ACCOUNT_HTTP');

						//."/?app_id=".$this->app_id."&user_id=".$order_info->user_id."&order_id=".$order_info->orader_id
						$data = [
							'app_id'   => $this->app_id,
							'user_id'  => $order_info->user_id,
							'order_id' => $order_info->order_id,
						];

						$data = http_build_query($data);
						//                        $data = json_encode($data);
						$jsonString = file_get_contents($wholeUrl, 0, stream_context_create([
							'http' => [
								'timeout' => 30,
								'method'  => 'POST',
								'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
									"Content-length:" . strlen($data) . "\r\n" .
									"Cookie: foo=bar\r\n" .
									"\r\n",
								'content' => $data,
							],
						]));
						//                        $jsonString = file_get_contents($wholeUrl);

						$ret = json_decode($jsonString, true);

						if (array_key_exists('ret', $ret)) {
							if ($ret['ret'] == 0) {//退款成功
								//修改该笔订单的核算状态和支付状态
								//添加事务
								\DB::beginTransaction();
								$result_order_update = $this->changeOrderState($order_info->order_id, $order_info->user_id);
								//修改该问题的状态(phase)
								$result_question_state = $this->modifyQueState($que_id);

								if ($result_order_update && $result_question_state) {
									\DB::commit();
									//                                    return $this->result("退款成功");
								} else {
									\DB::rollBack();

									return response()->json(Utils::pack("", StringConstants::Code_Failed, "修改该问题的状态失败"));
								}
							} else {//退款操作返回失败
								return response()->json(Utils::pack("", StringConstants::Code_Failed, $ret['msg']));
							}
						} else {//账户系统返回错误
							return response()->json(Utils::pack("", StringConstants::Code_Failed, "账户系统返回错误"));
						}
					} else {//商户可提现余额小于手续费
						return response()->json(Utils::pack("", StringConstants::Code_Failed, "商户余额不足于支付订单手续费0.6%"));
					}
				} else {//订单不存在
					return response()->json(Utils::pack("", StringConstants::Code_Failed, "对应的订单不存在"));
				}

			} else {//该问题不存在
				return response()->json(Utils::pack("", StringConstants::Code_Failed, "暂无该退款数据"));
			}
		}

		if ($result_order_update && $result_question_state) {
			//            \DB::commit();
			return $this->result("退款成功");
		}

	}

	private function getQueOrderInfo ($question_id, $product_id, $questioner_id)
	{
		//在表t_orders中根据问题id和问答专区id以及提问人id查询该问题的支付订单信息
		$order_info = \DB::table("db_ex_business.t_orders")
			->where("app_id", '=', $this->app_id)
			->where("user_id", '=', $questioner_id)
			->where("payment_type", '=', StringConstants::PAYMENT_TYPE_QUESTION)
			->where("resource_id", '=', $question_id)
			->where("product_id", '=', $product_id)
			->where("order_state", '=', StringConstants::ORDER_STATE_PAY)
			->where("use_collection", '=', StringConstants::ORDER_STATE_PERSON)
			->where("que_check_state", '=', StringConstants::ORDER_QUE_CHECK_STATE_UNCHECK)
			->first();

		return $order_info;
	}

	private function changeOrderState ($order_id, $user_id)
	{
		//        $params['order_state'] = StringConstants::ORDER_STATE_REFUNDED;
		$params['que_check_state'] = StringConstants::ORDER_QUE_CHECK_STATE_CHECK_FAILED;
		$params['updated_at']      = Utils::getTime();

		$result = \DB::table("db_ex_business.t_orders")
			->where("app_id", '=', $this->app_id)
			->where("order_id", '=', $order_id)
			->where("user_id", '=', $user_id)
			//            ->where("order_state",'=',StringConstants::ORDER_STATE_PAY)
			->where("que_check_state", '=', StringConstants::ORDER_QUE_CHECK_STATE_UNCHECK)
			->update($params);
		if ($result) {
			//            \DB::commit();
			return true;
		} else {
			//            \DB::rollBack();
			return false;
		}
	}

	private function modifyQueState ($que_id)
	{

		$params['phase']      = StringConstants::QUESTION_PAY_STATE_REFUND;
		$params['updated_at'] = Utils::getTime();
		$result               = \DB::table("db_ex_business.t_que_question")
			->where('app_id', '=', $this->app_id)
			->where('id', '=', $que_id)
			->where('phase', '=', StringConstants::QUESTION_PAY_STATE_PAY)
			->update($params);
		if ($result) {
			//            \DB::commit();
			return true;
		} else {
			//            \DB::rollBack();
			return false;
		}
	}

	/**
	 *图片处理
	 *
	 * @param $image_url
	 * @param $table_name
	 * @param $image_id
	 * @param $image_width   压缩尺寸 宽度
	 * @param $image_height  压缩尺寸 高度
	 * @param $image_quality 压缩参数 质量值
	 * @param $compressed    缩略图存储字段
	 */
	private function imageDealo ($image_url, $table_name, $image_id, $image_width, $image_height, $image_quality, $compressed)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		Utils::asyncThread($host_url . '/downloadImaged?image_field=answerer_id&image_id=' . $image_id . '&app_id='
			. $app_id . '&table_name=' . $table_name . '&file_url=' . $image_url
			. '&image_width=' . $image_width . '&image_height=' . $image_height . '&image_quality=' . $image_quality
			. '&compressed=' . $compressed);

	}

}