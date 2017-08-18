<?php
namespace App\Http\Controllers;

//use App\Userinfo;
use EasyWeChat;
use Log;

class WechatController extends Controller
{
	//    public function get()
	//    {
	//        $wechat = app('wechat');
	//
	//        return $wechat->server->serve();
	//    }

	public function index ()
	{
		Log::info("into index function!");

		$wechat = app('wechat');

		$menu = EasyWeChat::menu(); // 用户服务

		$buttons = [
			[
				"type" => "view",
				"name" => "分销内容",
				"url"  => env('ADMIN_HTTPS') . "saleHomePage",
			],
		];

		$menu->add($buttons);

		//        //得到xml数据包信息
		//        $postStr = "";
		//        if( isset( $GLOBALS['HTTP_RAW_POST_DATA'] ) )
		//        {
		//            $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
		//        }
		//        Log::info( "#############poststr=>" . $postStr );
		//        if(!empty($postStr)) {
		//
		//            //安全保护
		//            libxml_disable_entity_loader(true);
		//            //postStr数据解析
		//            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		//            $fromUsername = $postObj->FromUserName;  //这一句便是得到谁发给公众号，我们之后处理回复信息就需要这个；
		//
		//            $toUsername = $postObj->ToUserName;  //这一句得到用户发送给谁，这里这个指的就是我们这个微信公众号了；
		//
		//            $keyword = trim($postObj->Content);  //这一句就是用来判断用户发送过来的信息了，这里有一个函数trim，这个函数是用来去除Content消息左右两边的空格的，这样就不会影响我们对消息的判断；我们有了keyword，就可以针对用户发送过来的消息做相应的响应操作了，即判断keyword的内容，响应响应的操作；
		//
		//            $time = time();  //这个是时间函数，得到时间信息；
		//
		//            $textTpl = "
		//                        <xml>
		//                        <ToUserName><![CDATA[%s]]></ToUserName>
		//                        <FromUserName><![CDATA[%s]]></FromUserName>
		//                        <CreateTime>%s</CreateTime>
		//                        <MsgType><![CDATA[%s]]></MsgType>
		//                        <Content><![CDATA[%s]]></Content>
		//                        <FuncFlag>0</FuncFlag>
		//                        </xml>
		//                        ";
		//            if(!empty($keyword)) {
		//                 if($keyword == "设置菜单"){
		//
		//                    Log::info( "设置菜单" );
		//                    //添加菜单
		//                    $menu = EasyWeChat::menu(); // 用户服务
		//
		//                    $buttons =  [
		//                        [
		//                            "type" => "view",
		//                            "name" => "分销内容",
		//                            "url"  => "https://admin.inside.xiaoe-tech.com/saleHomePage"
		//                        ]
		//                    ];
		//
		//                    $menu->add($buttons);
		//                }else{
		//                    $msgType = "text";
		////                    $contentStr = '首次登录成功,分销内容！';
		//                    $contentStr = '请点击：<a href="https://admin.inside.xiaoe-tech.com/saleHomePage">分销内容</a>';
		//                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		//                    echo $resultStr;
		//                }
		//            }
		//        }

		return $wechat->server->serve();
	}

	public function userinfo ()
	{
		$user = session('wechat.oauth_user'); // 拿到授权用户资料

		dd($user);

		return "";
	}
}

?>
