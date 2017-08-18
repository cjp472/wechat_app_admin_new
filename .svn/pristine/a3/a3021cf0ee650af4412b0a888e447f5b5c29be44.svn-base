<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 29/03/2017
 * Time: 16:28
 */

namespace App\Http\Controllers\Tools;

use DB;

/***
 * api工具类
 * Class APIUtils
 * @package App\Http\Controllers\Tools
 */
class APIUtils
{
	// 注册接口
	public static function api_sign ($merchant_id)
	{

		//merchant_conf 商户配置表数据
		$merchantConfig                   = [];
		$merchantConfig['merchant_id']    = $merchant_id;
		$merchantConfig['certify_status'] = '2';
		$merchantConfig['wx_app_name']    = '';
		$merchantConfig['created_at']     = Utils::getTime();

		//app_conf  应用配置表数据 ,2条
		$appConfig                           = [];
		$appConfig['app_id']                 = 'app' . str_random(8) . random_int(1000, 9999);
		$appConfig['wx_app_type']            = 0;
		$appConfig['use_collection']         = 1;   // 默认为个人模式
		$appConfig['secrete_key']            = str_random(32);
		$appConfig['merchant_id']            = $merchantConfig['merchant_id'];
		$appConfig['wx_app_name']            = '';  // 暂为空，补全商户信息时 填入
		$merchantConfig['wx_app_name']       = '';
		$appConfig['balance']                = 5000;
		$appConfig['pay_directory_verified'] = 0;
		$appConfig['version_type']           = 1;
		$appConfig['created_at']             = Utils::getTime();

		// app_module 配置记录表数据
		$appModule               = [];
		$appModule['app_id']     = $appConfig['app_id'];
		$appModule['created_at'] = date('Y-m-d H:i:s');
		$appModule['updated_at'] = date('Y-m-d H:i:s');

		//balance_charge 扣费表赠送记录数据
		$charge                    = [];
		$charge['id']              = Utils::getOrderId();
		$charge['app_id']          = $appConfig['app_id'];
		$charge['charge_type']     = 102;
		$charge['fee']             = 5000;
		$charge['account_balance'] = 5000;
		$charge['charge_at']       = date('Y-m-d', time());
		$charge['charge_time']     = Utils::getTime();
		$charge['created_at']      = Utils::getTime();

		// 事务批量插入数据
		//        DB::enableQuerylog();
		// 手动处理事务
		DB::beginTransaction();
		try {
			//商户配置表
			DB::table("db_ex_config.t_merchant_conf")->insert($merchantConfig);
			//生成小程序对应记录
			DB::table("db_ex_config.t_app_conf")->insert($appConfig);
			//生成H5对应记录
			$appConfig['wx_app_type']            = 1;  //h5记录
			$appConfig['pay_directory_verified'] = 1;
			DB::table("db_ex_config.t_app_conf")->insert($appConfig);

			// 生成配置记录
			DB::table('db_ex_config.t_app_module')->insert($appModule);
			// 赠送记录
			DB::table("db_ex_finance.t_balance_charge")->insert($charge);

			DB::commit();

		} catch (\Exception $e) {
			DB::rollback();

			return json_encode(['ret' => -1, 'msg' => '系统错误']);
		}

		// 创建cos文件夹
		V4UploadUtils::createAppAllV4Folder($appConfig['app_id']);
		// 为用户创建资源
		self::addResource($appConfig['app_id']);

		return json_encode(['ret' => 0, 'msg' => '注册成功']);
	}

	// 添加登陆表记录

	public static function addResource ($app_id)
	{
		// 添加一个专栏和两个图文
		// 专栏数据
		$product                       = [];
		$product['app_id']             = $app_id; // 应用id
		$product['id']                 = Utils::getUniId('p_');  // 专栏标识id
		$product['name']               = '关于小鹅通(体验内容，支付后可提现)';  // 产品包名
		$product['img_url']            = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/1216698be5fd077f15d4f3c16c94d03c.jpg";  // 图片url
		$product['img_url_compressed'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/compress/1216698be5fd077f15d4f3c16c94d03c.jpg";
		$product['summary']            = '简要介绍小鹅通能为您做什么。';  // 简介
		$product['descrb']             = <<<'DES'
[{"type":0,"value":"\n小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。\n\n小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/88116100_1490103610.jpg"},{"type":0,"value":"\n\n在使用小鹅通的过程中如果遇到了问题，您可以查看"},{"type":0,"value":"a href=\"http:\/\/mp.weixin.qq.com\/s\/BJBghr7vSr5J4EIDHJJrCQ\" target=\"_self\" style=\"color: rgb(42, 117, 237); font-size: 16px; text-decoration: underline;\""},{"type":0,"value":"《常见问题》。\n\n\n如果您对我们有任何意见或者建议，欢迎随时反馈给我们：\n\n产品鹅初号机：\nTEL：18124689845\n微信：exiaomei1994\n\n产品鹅贰号机：\nTEL：18126391294\n微信：chanpine2\n\n官网网址：https:\/\/www.xiaoe-tech.com\/ \n\n微信公众号：小鹅通（微信ID：xiaoeservice）\n"}]
DES;
		$product['org_content']        = <<<'CON'
<p style="white-space: normal;"><br/></p><p style="white-space: normal;">小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。</p><p style="white-space: normal;"><br/></p><p style="white-space: normal;">小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。</p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/88116100_1490103610.jpg" title=".jpg" alt="bg_banner3.jpg"/><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><span style="color: rgb(70, 71, 73); font-size: 16px;">在使用小鹅通的过程中如果遇到了问题，您可以查看</span><a href="http://mp.weixin.qq.com/s/BJBghr7vSr5J4EIDHJJrCQ" target="_self" style="color: rgb(42, 117, 237); font-size: 16px; text-decoration: underline;"><span style="font-size: 16px;">《常见问题》</span></a><span style="color: rgb(70, 71, 73); font-size: 16px;">。<br/></span></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><span style="color: rgb(70, 71, 73); font-size: 16px;">如果您对我们有任何意见或者建议，欢迎随时反馈给我们：</span></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">产品鹅初号机：</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">TEL：18124689845</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">微信：exiaomei1994</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><br/></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">产品鹅贰号机：</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">TEL：18126391294</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">微信：chanpine2</span></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;">官网网址：https://www.xiaoe-tech.com/&nbsp;</p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;">微信公众号：小鹅通（微信ID：xiaoeservice）</p>
CON;
		$product['period']             = null;
		$product['price']              = 100;
		$product['state']              = 0;
		$product['resource_count']     = 1;
		$product['created_at']         = Utils::getTime();
		$product['updated_at']         = Utils::getTime();

		// 图文数据
		$image1                       = [];
		$image1['app_id']             = $app_id; // 应用id
		$image1['id']                 = Utils::getUniId('i_');
		$image1['title']              = "专栏教程";
		$image1['img_url']            = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/81012b12cac8d53e12f9bf333084b363.jpg";
		$image1['img_url_compressed'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/81012b12cac8d53e12f9bf333084b363.jpg";
		$image1['content']            = <<<'AAA'
[{"type":0,"value":"小鹅通是什么？\n\n\n小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。\n\n小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。\n\n搭建付费专栏\n\n若您想做知识服务产品的连续化、系列化输出，或已积累一定数量单品内容想要打包售卖，可以选择小鹅通“专栏售卖”这种付费形式。一个专栏内可汇聚同一类别或不同类别的内容，例如：图文付费专栏，或混搭了音频、直播等内容承载形式的付费专栏。\n\n现在我们来搭建您的第一个付费专栏。\n\nSTEP 1. 登陆管理台admin.xiaoe-tech.com\/login；\n\n点击左侧内容列表-专栏-新增专栏，跳转至专栏创建页面。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/87837800_1490103020.png"},{"type":0,"value":"\n\nSTEP 2. 为您的付费专栏添加名称及填写专栏简介；\n\n专栏简介将显示在专栏详情页、专栏分享提示信息等处。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/97020100_1490103218.png"},{"type":0,"value":"\n\nSTEP 3. 完善专栏描述、专栏封面，设置专栏价格。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/10033200_1490103803.png"},{"type":0,"value":"\n\nSTEP 4. 若您设置了首页分类导航功能，可以选择该专栏想显示的相关分类。点击保存，您已拥有了第一个专属付费专栏。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/45571500_1490103435.png"},{"type":0,"value":"\n\n"}]
AAA;
		$image1['org_content']        = <<<'BBB'
<p>小鹅通是什么？</p><p><br/></p><p><br/></p><p>小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。</p><p><br/></p><p>小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。</p><p><br/></p><p><strong>搭建付费专栏</strong></p><p><br/></p><p>若您想做知识服务产品的连续化、系列化输出，或已积累一定数量单品内容想要打包售卖，可以选择小鹅通“专栏售卖”这种付费形式。一个专栏内可汇聚同一类别或不同类别的内容，例如：图文付费专栏，或混搭了音频、直播等内容承载形式的付费专栏。</p><p><br/></p><p>现在我们来搭建您的第一个付费专栏。</p><p><br/></p><p>STEP 1. 登陆管理台admin.xiaoe-tech.com/login；</p><p><br/></p><p>点击左侧内容列表-专栏-新增专栏，跳转至专栏创建页面。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/87837800_1490103020.png" title=".png" alt="5.png"/></p><p><br/></p><p>STEP 2. 为您的付费专栏添加名称及填写专栏简介；</p><p><br/></p><p>专栏简介将显示在专栏详情页、专栏分享提示信息等处。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/97020100_1490103218.png" title=".png" alt="6.png"/></p><p><br/></p><p>STEP 3. 完善专栏描述、专栏封面，设置专栏价格。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/10033200_1490103803.png" title=".png" alt="7.png"/></p><p><br/></p><p>STEP 4. 若您设置了首页分类导航功能，可以选择该专栏想显示的相关分类。点击保存，您已拥有了第一个专属付费专栏。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/45571500_1490103435.png" title=".png" alt="8.png"/></p><p><br/></p>
BBB;
		$image1['img_size_total']     = 0;
		$image1['payment_type']       = 3;
		$image1['product_id']         = $product['id'];
		$image1['product_name']       = '关于小鹅通(体验内容，支付后可提现)';
		$image1['created_at']         = Utils::getTime();
		$image1['updated_at']         = Utils::getTime();
		$image1['start_at']           = Utils::getTime();

		// 图文数据
		$image2                       = [];
		$image2['app_id']             = $app_id; // 应用id
		$image2['id']                 = Utils::getUniId('i_');
		$image2['title']              = "图文教程(体验内容，支付后可提现)";
		$image2['img_url']            = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/4e1b9e6b2a4b45a4ec57c1d6e5c306e7.jpg";
		$image2['img_url_compressed'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/compress/4e1b9e6b2a4b45a4ec57c1d6e5c306e7.jpg";
		$image2['content']            = <<<'AAA'
[{"type":0,"value":"小鹅通是什么？\n\n\n小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。\n\n小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。\n\n搭建付费图文\n\n作为入门，我们来搭建一篇付费图文内容作为小店的第一款商品。\n\nSTEP 1. 登陆管理台admin.xiaoe-tech.com\/login\n\n点击左侧内容列表-图文-新增图文，跳转至内容创建页面；\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/62231000_1490102191.png"},{"type":0,"value":"\n\nSTEP 2. 为您的付费图文添加名称，选择收费形式。\n\n        若选择专栏，则需将图文移动至所属专栏，方便做系列化产品的输出。\n\n        若选择单卖，则表明该单品不隶属于任何系列，需要为其单独定价。\n   \n        也可选择免费作为试阅。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/24271500_1490102350.png"},{"type":0,"value":"\n\nSTEP 3. 完善封面信息、详细内容等。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/76407600_1490102436.png"},{"type":0,"value":"\n\nSTEP 4. 调整上架时间，若需立即售卖，请选择早于目前自然日的时间段。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/58726100_1490102528.png"},{"type":0,"value":"\n\nSTEP 5. 点击默认。恭喜！您已经拥有自己的第一款付费产品了，现在请移步前端展示页面欣赏预览。\n\n"}]
AAA;
		$image2['org_content']        = <<<'BBB'
<p>小鹅通是什么？</p><p><br/></p><p><br/></p><p>小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。</p><p><br/></p><p>小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。</p><p><br/></p><p><strong>搭建付费图文</strong></p><p><br/></p><p>作为入门，我们来搭建一篇付费图文内容作为小店的第一款商品。</p><p><br/></p><p>STEP 1. 登陆管理台admin.xiaoe-tech.com/login</p><p><br/></p><p>点击左侧内容列表-图文-新增图文，跳转至内容创建页面；</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/62231000_1490102191.png" title=".png" alt="1.png"/></p><p><br/></p><p>STEP 2. 为您的付费图文添加名称，选择收费形式。</p><p><br/></p><p>&nbsp; &nbsp; &nbsp; &nbsp; 若选择专栏，则需将图文移动至所属专栏，方便做系列化产品的输出。</p><p><br/></p><p>&nbsp; &nbsp; &nbsp; &nbsp; 若选择单卖，则表明该单品不隶属于任何系列，需要为其单独定价。</p><p>&nbsp; &nbsp;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; 也可选择免费作为试阅。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/24271500_1490102350.png" title=".png" alt="2.png"/></p><p><br/></p><p>STEP 3. 完善封面信息、详细内容等。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/76407600_1490102436.png" title=".png" alt="3.png"/></p><p><br/></p><p>STEP 4. 调整上架时间，若需立即售卖，请选择早于目前自然日的时间段。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/58726100_1490102528.png" title=".png" alt="4.png"/></p><p>&nbsp;</p><p>STEP 5. 点击默认。恭喜！您已经拥有自己的第一款付费产品了，现在请移步前端展示页面欣赏预览。</p><p><br/></p>
BBB;
		$image2['img_size_total']     = 0;
		$image2['payment_type']       = 2;
		$image2['piece_price']        = 100;
		$image2['created_at']         = Utils::getTime();
		$image2['updated_at']         = Utils::getTime();
		$image2['start_at']           = Utils::getTime();

		$res1                  = [];
		$res1['app_id']        = $app_id;
		$res1['product_id']    = $product['id'];
		$res1['product_name']  = "关于小鹅通(体验内容，支付后可提现)";
		$res1['resource_type'] = 1;
		$res1['resource_id']   = $image1['id'];
		$res1['created_at']    = Utils::getTime();

		$res2                = $res1;
		$res2['resource_id'] = $image2['id'];

		// 专栏图文数据及关系表数据
		DB::transaction(function() use ($product, $image1, $image2, $res1) {
			DB::connection('mysql')->table('t_pay_products')->insert($product);
			DB::connection('mysql')->table('t_image_text')->insert($image1);
			DB::connection('mysql')->table('t_image_text')->insert($image2);
			DB::connection('mysql')->table('t_pro_res_relation')->insert($res1);
		});
	}

	public static function addLogin ($unionID, $pc_openid, $mobile_openid, $wx_nick_name, $wx_gender, $wx_logo)
	{
		//  登陆表
		$merchant_id = 'mch' . str_random(8);
		// mgr_login 登陆表数据
		$mgr_login                  = [];
		$mgr_login['openid']        = $pc_openid;
		$mgr_login['union_id']      = $unionID;
		$mgr_login['mobile_openid'] = $mobile_openid;
		$mgr_login['nick_name']     = $wx_nick_name;
		$mgr_login['gender']        = $wx_gender;
		$mgr_login['logo']          = $wx_logo;
		$mgr_login['merchant_id']   = $merchant_id;
		$mgr_login['created_at']    = date('Y-m-d H:i:s');
		$mgr_login['login_id']      = Utils::getLoginId();

		if ($mobile_openid) {
			$mgr_login['sign_client'] = 1;
			// 设定注册渠道为 手机端
			$mgr_login['channel'] = "mobile";
			// 清空openid 使用默认值
			unset($mgr_login['openid']);
		} else {
			// 注册渠道
			if (array_key_exists('channel', $_COOKIE)) {
				$mgr_login['channel'] = $_COOKIE['channel'];
			} else {
				$mgr_login['channel'] = "unknown";
			}
			// 清空 mobile_openid 时候用默认值
			unset($mgr_login['mobile_openid']);
		}

		$insert = DB::table('db_ex_config.t_mgr_login')->insert($mgr_login);

		if ($insert) {
			return $merchant_id;
		} else {
			return false;
		}
	}

}