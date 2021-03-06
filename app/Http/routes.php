<?php

/**登录相关*/
Route::get('/', function() { return redirect("/login"); });//登录页面
Route::get('/neo_alive_info', ['uses' => 'AliveController@neo_alive_info']);//neo 临时接口 修改推流直播一些数据
Route::get('/neo_has_stream_alive', ['uses' => 'AliveController@neo_has_stream_alive']);//neo 临时接口 修改业务的app_module的has_stream_alive状态
Route::get('/clear_manual_stop_at', ['uses' => 'AliveController@clear_manual_stop_at']);//neo 临时接口 清空手动直播结束时间
/*
//Route::get('/login', ['uses'=>'UserController@login']);//登录页面
//Route::post('/dologin', ['uses'=>'UserController@doLogin']);//登录操作
//Route::get('/codeinfo', ['uses'=>'UserController@codeinfo']);//获取二维码信息
//Route::get('/signup', ['uses'=>'UserController@signUp']);//微信黑色二维扫码页面
//Route::get('/loginout', ['uses'=>'UserController@loginOut']);//退出登录
//Route::get('/sign', ['uses'=>'UserController@sign']);//注册页面
//注册页面相关操作,无权限控制,只需登录态
//Route::get('/sendmsg', ['uses'=>'UserController@sendMsg']);//发送注册验证码
//Route::get('/identify', ['uses'=>'UserController@identify']);//校验注册短信码
//Route::post('/identifysubmit', ['uses'=>'UserController@identifySubmit']);//补全商户信息*/

// 登陆相关
Route::group(['namespace' => 'Users'], function() {
	Route::get('/login', ['uses' => 'RegisterController@login']);//登录跳转
	Route::post('/dologin', ['uses' => 'RegisterController@doLogin']);//登录账户验证
	Route::get('/codeinfo', ['uses' => 'RegisterController@codeinfo']);//获取二维码信息
	Route::get('/signup', ['uses' => 'RegisterController@signUp']);//微信黑色二维扫码页面

	Route::get('/sign', ['uses' => 'RegisterController@sign']);//补全商户信息页面
	Route::post('/identifysubmit', ['uses' => 'RegisterController@identifySubmit']);//补全商户信息

	Route::get('/sendmsg', ['uses' => 'RegisterController@sendMsg']);//发送注册验证码
	Route::get('/identify', ['uses' => 'RegisterController@identify']);//校验注册短信码

	Route::get('/loginout', ['uses' => 'RegisterController@loginOut']);//退出登录（清空session）

	// 特殊路由，生成左侧边栏跳转链接
	Route::get('/getRedirect/{id}', ['as' => '', 'uses' => 'OverviewController@redirectCurrentUrl']);
});

/**登录态校验*/
Route::group(['middleware' => ['admin']], function() {

	/* 概况 */
	Route::group(['namespace' => 'Users'], function() {
		Route::get('/index', ['uses' => 'OverviewController@index']);
		Route::get('/closeMessageReminder', ['uses' => 'OverviewController@closeMessageReminder']);

		Route::post('/changePhone', ['uses' => 'AdminKController@changePhone']);      //手机换绑
	});

	/*知识店铺*/
	Route::group(['as' => '100.'], function() {

		// 手机预览
		Route::group(['as' => '108.'], function() {
			Route::get('/interfacesetting', ['uses' => 'InterfaceSettingController@interfaceSetting']);//界面设置首页
			Route::get('/sethometitle', ['uses' => 'InterfaceSettingController@setHomeTitle']);//设置首页标题
		});

		// 店铺装修
		Route::group(['as' => '109.'], function() {
			Route::get('/shopIndexDiy', ['uses' => 'ShopDiyControlle@shopIndexDiy']);//店铺装修页面
			Route::any('/load_diy_setting', ['uses' => 'ShopDiyControlle@loadDiySetting']);//读取店铺装修配置
			Route::any('/save_new_part', ['uses' => 'ShopDiyControlle@saveNewPart', 'middleware' => 'ImageDeal']);//读取店铺装修配置
			Route::any('/save_diy_setting', ['uses' => 'ShopDiyControlle@saveDiySetting', 'middleware' => 'ImageDeal']);//读取店铺装修配置
			Route::any('/get_diy_module', ['uses' => 'ShopDiyControlle@getDiyModule']);//读取模块配置列表
			Route::any('/search_diy_module', ['uses' => 'ShopDiyControlle@searchDiyModule']);//搜索模块列表内容
			Route::any('/get_banner_resource', ['uses' => 'ShopDiyControlle@getBannerResource']);//获取banner图联动列表
		});
		// 分享设置
		Route::group(['as' => '110.'], function() {
			Route::get('/sharesetting', ['uses' => 'InterfaceSettingController@shareSetting']);//分享设置页面
			Route::post('/updateshareinfo', ['uses' => 'InterfaceSettingController@updateShareInfo', 'middleware' => 'ImageDeal']);//更新分享设置
		});

		// 公众号设置
		Route::group(['as' => '111.'], function() {
			Route::get('/wxaccountsetting', ['uses' => 'InterfaceSettingController@wxAccountSetting']);//公众号设置页面
			Route::post('/updatewxaccountinfo', ['uses' => 'InterfaceSettingController@uploadWXAccountInfo', 'middleware' => 'ImageDeal']);//更新公众号设置
		});

		// 功能管理
		Route::group(['as' => '112.'], function() {
			Route::any('/manage_function', ['uses' => 'InterfaceSettingController@manageFunction']);//功能管理页面
			Route::any('/category_switch', ['uses' => 'InterfaceSettingController@categorySwitch']);//分类导航开关
			Route::any('/category_setting', ['uses' => 'InterfaceSettingController@categorySetting']);//分类导航设置页面
			Route::any('/update_category_info', ['uses' => 'InterfaceSettingController@updateCategoryInfo']);//更新分类导航设置
			Route::any('/set_hid_sub', ['uses' => 'InterfaceSettingController@setHidSubCount']);//是否隐藏订阅数
			Route::any('/set_alert_message', ['uses' => 'InterfaceSettingController@setAlertMessage']);//是否隐藏消息弹窗提醒
			Route::any('/set_resource_count', ['uses' => 'InterfaceSettingController@setResourceCount']);//是否隐藏消息弹窗提醒
			Route::any('/set_service_notification', ['uses' => 'InterfaceSettingController@setServiceNotification']);//是否开启服务号通知
		});

	});

	/**内容列表*/
	Route::group(['as' => '101.'], function() {
		/**
		 * 直播中邀请嘉宾
		 */
		Route::any('/get_added_guest_list', ['uses' => 'AliveController@getAddedGuestList']);      //获取已添加的嘉宾列表
		Route::any('/get_all_guest_list', ['uses' => 'AliveController@getAllGuestList']);       //获取所有的嘉宾列表
		Route::any('/save_added_guest', ['uses' => 'AliveController@saveAddedGuest']);          //保存添加的嘉宾
		Route::any('/add_alive_guest', ['uses' => 'AliveController@addAliveGuest']);            //添加新的嘉宾
		Route::any('/delete_alive_guest', ['uses' => 'AliveController@deleteAliveGuest']);      //删除一个嘉宾
		Route::any('/invite_guest_url', ['uses' => 'AliveController@inviteGuestUrl']);      //邀请嘉宾链接

		Route::get('/alive', ['uses' => 'AliveController@alive']);//直播首页
		Route::get('/addalive', ['uses' => 'AliveController@addAlive']);//新增直播页面
		Route::post('/doaddalive', ['uses' => 'AliveController@doAddAlive']);//新增直播操作
		Route::get('/alive/offsale', ['uses' => 'AliveController@offSale']);//直播下架
		Route::get('/alive/delsale', ['uses' => 'AliveController@delSale']);//直播删除
		Route::get('/alive/onsale', ['uses' => 'AliveController@onSale']);//直播上架
		Route::get('/editalive', ['uses' => 'AliveController@editAlive']);//编辑直播页面
		Route::post('/updatealive', ['uses' => 'AliveController@updateAlive']);//更新直播操作
		Route::get('/zbsearch', ['uses' => 'AliveController@zbSearch']);//搜索讲师
		Route::get('/endalive', ['uses' => 'AliveController@endAlive']);//结束直播
		Route::any('/async_download_alive_voice', ['uses' => 'AliveController@asyncDownloadAliveVoice']);//异步完整语音

		Route::get('/article_list', ['uses' => 'ArticleController@getArticleList']);//图文首页
		Route::get('/article_create', ['uses' => 'ArticleController@createArticle']);//创建图文页
		Route::post('/addarticle', ['uses' => 'ArticleController@addArticle']);//新增图文操作
		Route::get('/article_edit', ['uses' => 'ArticleController@editArticle']);//编辑图文页面
		Route::post('/updatearticle', ['uses' => 'ArticleController@updateArticle']);//更新图文操作
		Route::post('/edit_article_save', ['uses' => 'ArticleController@saveResourceEdit']);//编辑图文资源[上下架删除]操作

		Route::get('/audio_list', ['uses' => 'ContentController@getAudioList']);//音频列表页
		Route::get('/audio_create', ['uses' => 'ContentController@createAudio']);//创建音频页
		Route::get('/audio_edit', ['uses' => 'ContentController@editAudio']);//音频编辑页
		Route::post('/upload_resource', ['uses' => 'ContentController@uploadResource']);//上传资源操作
		Route::post('/edit_resource_save', ['uses' => 'ContentController@saveResourceEdit']);//编辑资源操作

		Route::get('/video_list', ['uses' => 'VideoController@getVideoList']);//视频列表页
		Route::get('/video_create', ['uses' => 'VideoController@createVideo']);//创建视频页
		Route::post('/upload_video', ['uses' => 'VideoController@uploadResource']);//上传视频资源操作
		Route::get('/video_edit', ['uses' => 'VideoController@editVideo']);//视频编辑页
		Route::post('/update_video', ['uses' => 'VideoController@updateResource']);//更新视频资源操作
		Route::post('/edit_video_save', ['uses' => 'VideoController@saveVideoEdit']);//编辑视频资源[上下架删除]操作

		Route::get('/is_industry', ['uses' => 'VideoController@isGetIndustry']);    //会员页

		Route::get('/package_create', ['uses' => 'ProductController@createPackage']);//创建专栏页
		Route::get('/package_edit', ['uses' => 'ProductController@editPackage']);//专栏编辑页
		Route::post('/upload_package', ['uses' => 'ProductController@uploadResource']);//上传产品包操作
		Route::post('/edit_package_save', ['uses' => 'ProductController@saveResourceEdit']);//编辑产品包操作
		Route::post('/edit_package_finished', ['uses' => 'ProductController@savePackageFinishedState']);//专栏完结操作
		Route::post('/edit_package_weight', ['uses' => 'ProductController@savePackageWeight']);//专栏排序
		Route::post('/h5newest_hide', ['uses' => 'ProductController@h5newestHide']);//专栏最新列表是否展示
		Route::get('/package_list', ['uses' => 'ProductController@getPackageList']);//专栏列表页

		Route::get('/member_list', ['uses' => 'MemberController@getMemberList']);    //会员页

		/*************************************** 实现知识商品 ***********************************************/
		Route::group(['namespace' => 'ResManage'], function() {

			/**************************************************页面**************************************************/
			Route::get('/resource_list_page', ['uses' => 'ResourceController@getResourceList']);    //单品资源列表;参数:1-资源类型-resource_type:0-全部,1-图文,2-音频,3-视频,4-直播;2-搜索内容-search_content.3-page(分页页码);4.state-资源状态(-1:全部;0:已上架;1-已下架)
			Route::get('/create_resource_page', ['uses' => 'ResourceController@createResource',]);     //新建资源页面;参数1：type：1-article；2-audio；3-video；4-alive;2-upload_channel_type(新增的渠道:1-单品新增;2-专栏新增;3-会员新增)
			Route::get('/edit_resource_page', ['uses' => 'ResourceController@editResource']);         //编辑资源页面;参数1：type：1-article；2-audio；3-video；4-alive;参数2：resource_id:3-upload_channel_type(编辑的资源渠道:1-单品编辑;2-专栏单品编辑;3-会员单品编辑)
			Route::get('/has_industry', ['uses' => 'ResourceController@isGetIndustry']);    //判断是否将服务号行业设置为教育
			Route::get('/make_page_url', ['uses' => 'ResourceController@makePageUrl']);

			Route::get('/package_list_page', ['uses' => 'PackageController@getPackageList']);      //专栏列表 参数:1-serach_content;2-state(-1-全部,0-上架,1-下架)
			Route::get('/create_package_page', ['uses' => 'PackageController@createPackage']);     //新建专栏页面
			Route::get('/edit_package_page', ['uses' => 'PackageController@editPackage']);         //编辑专栏页面;参数:id(专栏id)
			Route::get('/package_detail_page', ['uses' => 'PackageController@packageDetail']);         //专栏详情页面;参数:id(专栏id);2-资源类型-resource_type:(0-全部,1-图文,2-音频,3-视频,4-直播);3-搜索内容-search_content;4.state-资源状态(-1:全部;0:已上架;1-已下架)
            Route::post('/visible_on_switch', ['uses' => 'PackageController@visibleSwitch']);

			Route::get('/member_list_page', ['uses' => 'MemberController@getMemberList']);      //会员列表
			Route::get('/create_member_page', ['uses' => 'MemberController@createMember']);     //新建会员页面
			Route::get('/edit_member_page', ['uses' => 'MemberController@editMember']);         //编辑会员页面
			Route::get('/member_detail_page', ['uses' => 'MemberController@memberDetail']);         //会员详情页面;参数:id(会员id)

			// 自定义专栏内容(吴晓波频道特殊逻辑)
			Route::get('/user_defined', ['uses' => 'AvailResourceController@availResourcePage']);
			Route::get('/user_defined_list', ['uses' => 'AvailResourceController@getResource']);
			Route::get('/user_defined_add', ['uses' => 'AvailResourceController@addResource']);
			Route::get('/user_defined_del', ['uses' => 'AvailResourceController@delResource']);

			//function(){return view("admin.resManage.userDefined");

			/***********************************************ajax 请求******************************************/
			/**
			 * 1.知识商品控制
			 */
			Route::any('/change_package_weight', ['uses' => 'GoodsManageController@changePackageWeight']);//上移、下移专栏;参数:1-package_id;2-order_type(0-上移,1-下移)
			Route::any('/change_goods_state', ['uses' => 'GoodsManageController@changeGoodsState']);//上架、下架;参数:1-goods_id;2-goods_type(0-专栏,1-图文,2-音频,3-视频,4-直播);3-operate_type(0-上架,1-下架)
			Route::any('/query_goods_state', ['uses' => 'GoodsManageController@queryGoodsState']);//查询商品存在的记录;参数:1-goods_id;2-goods_type(1-图文,2-音频,3-视频,4-直播,6-专栏);3-channel_type(0-单品,1-专栏内资源,2-会员内);4-package_id(当channel_type=1、2时有值)
			Route::any('/move_goods', ['uses' => 'GoodsManageController@moveGoods']);//移除;参数:1-goods_id;2-goods_type(1-图文,2-音频,3-视频,4-直播,6-专栏);3-channel_type(0-单品,1-专栏内资源,2-会员内);4-package_id(当channel_type=1、2时有值)
			Route::any('/set_package_resource_try', ['uses' => 'GoodsManageController@setPackageResourceTry']);//设为试听;参数:1-package_id;2-resource_type(2-音频);3-resource_id;4-try_state(0-取消试听,1-设为试听)
			Route::any('/end_alive', ['uses' => 'GoodsManageController@endAlive']);//结束直播;参数:1-alive_id;
			Route::any('/set_alive_config', ['uses' => 'GoodsManageController@setAliveConfig']);//设置直播配置
			Route::any('/query_profit_ratio', ['uses' => 'GoodsManageController@queryProfitRatio']);//查询分成比例;参数:1-alive_id;
			Route::any('/set_profit_ratio', ['uses' => 'GoodsManageController@setProfitRatio']);//分销设置-设置分成比例;参数:1-alive_id;2-分成比例(百分制:1-50)
			Route::any('/set_package_resource_top', ['uses' => 'GoodsManageController@setPackageResourceTop']);//置顶;参数:1-package_id;2-resource_type(0-专栏,1-图文,2-音频,3-视频,4-直播);3-resource_id;4-top_state(0-取消置顶,1-设置置顶)
			Route::any('/save_package_finished_state', ['uses' => 'GoodsManageController@savePackageFinishedState']);//修改专栏完结状态;参数:1-package_id;2-finished_state(0-未完结,1-已完结)
			Route::any('/set_h5_newest_hide', ['uses' => 'GoodsManageController@setH5NewestHide']);//设置专栏内容是否再最新列显示;参数:1-id(专栏、会员id);2-h5_newest_hide(是否在最新列表展示，0-显示,1-隐藏)
			Route::any('/set_resource_select_can', ['uses' => 'GoodsManageController@setResourceSelectCan']);//设置资源是否可以被复制;参数:1-id(资源id);2-resource_type(1-图文;2-音频;3-视频;4-直播)3-can_select(是否可以被复制，0-不允许,1-允许)

			// 检查当前资源 上架时间当天的推送状况
			Route::any('/check_goods_message_push/{product_id}/{date?}', ['uses' => 'GoodsManageController@checkGoodsMessagePush']);

			/**
			 * 资源新增、编辑、拉取资源库资源列表
			 */
			Route::any('/goods_upload_resource', ['uses' => 'ResourceController@uploadResource', 'middleware' => ['AudioDeal', 'ImageDeal']]);//保存上传资源;参数:1-params(基本信息+上架信息);2-resource_type(1-图文,2-音频,3-视频,4-直播);3-upload_channel_type(新增的渠道:1-单品新增;2-专栏新增;3-会员新增);4-package_id(当upload_channel_type=2或3时有值);5-resource_params(当资源为视频、直播时);6-roleParams
			Route::any('/edit_resource', ['uses' => 'ResourceController@updateResource', 'middleware' => ['AudioDeal', 'ImageDeal']]);//保存编辑资源;参数:1-params(基本信息+上架信息);2-resource_type(1-图文,2-音频,3-视频,4-直播);3-resource_params(当资源为视频、直播时);4-roleParams;5-upload_channel_type(编辑的渠道:1-单品;2-专栏;3-会员)
			Route::any('/choice_resource_list', ['uses' => 'ResourceController@choiceResourceList']);//选择已有的单品列表;参数:1-channel_type(1-单品,2-专栏,3-会员)2-搜索内容-search_content;3-resource_type(0-全部,1-图文,2-音频,3-视频,4-直播,6-专栏);4-page(分页页码);5-package_id(当channel_type=2、3时,有值)
			Route::any('/submit_choice_resource', ['uses' => 'ResourceController@submitChoiceResource']);//提交选择的已有单品;参数:1-channel_type(1-单品,2-专栏,3-会员);2-resource_list(选中的资源集合(数组),键值对的格式如:resource_id:a_kjjak345,resource_type:1);3-package_id(当channel_type=2、3时,有值);4-piece_price(但channel_type=1、payment_type=2时有值);5-submit_type(0-设为单卖;1-取消单卖);6-payment_type(1-免费;2-单卖)
			Route::any('/get_package_member_list', ['uses' => 'ResourceController@getPackageMemberList']);//获取专栏、会员列表;参数:1-search_content

			/**
			 * 保存上传专栏 + 保存编辑资源
			 */
			Route::any('/goods_upload_package', ['uses' => 'PackageController@uploadPackage', 'middleware' => 'ImageDeal']);//保存上传专栏;参数:1-params(基本信息+上架信息);2-category_type(分类信息)
			Route::any('/goods_edit_package', ['uses' => 'PackageController@updatePackage', 'middleware' => 'ImageDeal']);//保存编辑资源;参数:1-params(基本信息+上架信息);2-category_type(分类信息)

			/**
			 * 保存上传会员 + 保存编辑会员
			 */
			//            Route::any('/upload_member', ['uses' => 'MemberController@uploadMember']);//保存上传会员;参数:1-params(基本信息+上架信息);2-category_type(分类信息)
			//            Route::any('/edit_member', ['uses' => 'MemberController@updateMember']);//保存编辑会员;参数:1-params(基本信息+上架信息);2-category_type(分类信息)
			Route::any('/set_is_complete_info', ['uses' => 'MemberController@setIsCompleteInfo']);    //设置用户是否需要补全资料 0-否 1-是:参数：id(会员/专栏id)

			/**
			 *会员详情页权益:专栏、单品列表
			 */
			Route::any('/package_list_member', ['uses' => 'MemberController@getPackageListOfMember']);//会员的专栏列表页;参数:id(会员id);2-资源类型-resource_type:(0-全部,1-图文,2-音频,3-视频,4-直播);3-搜索内容-search_content;4.state-资源状态(-1:全部;0:已上架;1-已下架)
			Route::any('/singe_list_member', ['uses' => 'MemberController@getResourceListOfMember']);//会员的单品列表页;参数:1-id(会员id);2-资源类型-resource_type:(0-全部,1-图文,2-音频,3-视频,4-直播);3-搜索内容-search_content;4.state-资源状态(-1:全部;0:已上架;1-已下架)

		});
	});

	/*营销中心*/
	Route::group(['as' => '102.'], function() {
		/********************************营销中心**************************************/
		Route::any('/marketing', function() { return view('admin.marketing.typeSelect'); });//营销方式选择页面

		/**渠道分发*/
		Route::group(['as' => '113.'], function() {
			Route::get('/channel_admin', ['uses' => 'ChannelAdminController@channelAdmin']);//渠道首页
			Route::post('/submit_channel', ['uses' => 'ChannelAdminController@submitChannel']); //新增推广渠道
			Route::get('/open_detail', ['uses' => 'ChannelAdminController@openDetail']); //渠道开通数详情
			Route::get('/home_open_detail', ['uses' => 'ChannelAdminController@homeChannel']); //官网渠道详情
			Route::get('/sale', ['uses' => 'SaleController@sale']); //分销首页
			Route::post('/agreesale', ['uses' => 'SaleController@agreeSale']); //同意分销
			Route::post('/disagreesale', ['uses' => 'SaleController@disagreeSale']); //拒绝分销
			Route::any('/get_channel', ['uses' => 'SaleController@get_channel']); //

			Route::get('/channel/listen', ['uses' => 'ChannelAdminController@tryListener']);//试听渠道
			Route::get('/channel/addListen/{name}', ['uses' => 'ChannelAdminController@AddListenChannel']);//增加试听渠道
		});

		/**邀请码*/
		Route::group(['as' => '115.'], function() {
			Route::get('/invitecode', ['uses' => 'InviteCodeController@inviteCode']);//邀请码首页
			Route::get('/invite_list', ['uses' => 'InviteCodeController@inviteList']);//邀请码使用详情列表
			Route::get('/groupcode', ['uses' => 'InviteCodeController@groupCode']);//团购码首页
			Route::get('/group_list', ['uses' => 'InviteCodeController@groupList']);//团购使用详情列表
			Route::get('/giftcode', ['uses' => 'InviteCodeController@giftCode']);//买赠码首页
			Route::get('/gift_list', ['uses' => 'InviteCodeController@giftList']);//买赠使用详情列表
			Route::get('/addinvitecode', ['uses' => 'InviteCodeController@addInviteCode']);//新增邀请码页面
			Route::get('/getres', ['uses' => 'InviteCodeController@getRes']);//获取所有资源列表
			Route::get('/gift_invalid', ['uses' => 'InviteCodeController@giftInvalid']);//作废邀请码
			Route::post('/doaddinvite', ['uses' => 'InviteCodeController@doAddInvite']);//新增邀请码操作

			Route::get('/excel/invite', ['uses' => 'InviteCodeController@exportData']);//下载邀请码

		});

		// 推广员
		Route::group(['namespace' => 'Marketing'], function() {
			Route::group(['as' => '114.', 'prefix' => 'distribute'], function() {
				Route::any('/index', ['uses' => 'DistributionController@index']); //  分销设置页面
				Route::any('/switch', ['uses' => 'DistributionController@baseSwitch']);  // 分销设置主开关
				//设置
				Route::get('/set', ['uses' => 'DistributionController@set']);
				Route::any('/setEdit', ['uses' => 'DistributionController@setEdit']);
				// 招募计划
				Route::get('/recruit', ['uses' => 'DistributionController@recruit']);
				Route::post('/recruitEdit', ['uses' => 'DistributionController@recruitEdit']);

				// 业绩
				Route::get('/achieve', ['uses' => 'DistributionController@achieveList']);
				Route::get('/excel/achieve', ['uses' => 'DistributionController@achieveExport']);

				// 推广
				Route::any('/records', ['uses' => 'DistributionController@recordsList']);
				Route::any('/excel/records', ['uses' => 'DistributionController@recordsExport']);

				// goods
				Route::get('/goods', ['uses' => 'DistributionController@goodsList']);
				Route::post('/goodsSetting', ['uses' => 'DistributionController@goodsSetting']);

				// 审核信息
				Route::get('/audit', ['uses' => 'DistributionController@auditList']);
				Route::get('/auditing', ['uses' => 'DistributionController@auditing']);

				Route::get('/saler', ['uses' => 'DistributionController@salerList']);
				Route::get('/salerdel', ['uses' => 'DistributionController@salerDelete']);

				// 生成导出excel 所需的月数
				Route::get('/date', ['uses' => 'DistributionController@dateList']);

				// 内容分销相关
				Route::get('/chosen', ['uses' => 'DistributionController@chosen']);
				Route::get('/chosen_enable', ['uses' => 'DistributionController@chosenEnable']);
				Route::any('/set_xiaoe_distribute', ['uses' => 'DistributionController@setXiaoeDistribute']);
				Route::any('/addResourceChosen', ['uses' => 'DistributionController@addResourceChosen']);

				// 判断推广员设定分销比例 是否需关联内容分销
				Route::get('/judge/chosen/{default}/{percent?}', ['uses' => 'DistributionController@JudgeDistributePercent']);

			});

			// 邀请卡
			Route::group(['as' => '116.', 'prefix' => 'invite'], function() {
				Route::get('switch', ['uses' => 'InviteController@baseSwitch']);// 邀请卡总开关
				Route::get('index', ['uses' => 'InviteController@index']); // 邀请卡列表页
				Route::any('set', ['uses' => 'InviteController@set']);     // 设置邀请卡

				Route::any('shareUseList/{listType}', ['uses' => 'InviteController@shareUseList']);  // 分享免费听列表
				Route::any('setShareNum', ['uses' => 'InviteController@setShareNum']);     // 设置分享免费听
			});

			/***************************优惠券*******************************/
			Route::group(['as' => '117.', 'prefix' => 'coupon'], function() {
				/*优惠券列表页*/
				Route::any('/index', ['uses' => 'CouponController@index']); //  优惠券列表页
				Route::any('/end/{id}', ['uses' => 'CouponController@endCoupon']); // 结束优惠券

				/*优惠券创建*/
				Route::any('/close/{place}', ['uses' => 'CouponController@closeMessageReminder']); // 关闭优惠券提醒
				Route::any('/select', ['uses' => 'CouponController@select']); //  选择优惠券
				Route::any('/create', ['uses' => 'CouponController@create']); //  创建优惠券页面
				Route::any('/add_products', ['uses' => 'CouponController@addProducts']);//获取优惠券可用的产品包列表
				Route::any('/add', ['uses' => 'CouponController@createCoupon']); //  创建优惠卷

				/*优惠券的编辑*/
				Route::any('/edit', ['uses' => 'CouponController@edit']);//进入修改页面
				Route::any('/editCoupon', ['uses' => 'CouponController@editCoupon']);// 修改优惠券

				/*发放记录  暂弃用*/
				//            Route::any('/createPlanPage' ,['uses'=>'CouponController@createPlanPage']);//点击创建页面
				//            Route::any('/planIndex' ,['uses'=>'CouponController@planIndex']);//进入发放记录
				//            Route::any('/getCoupon' ,['uses'=>'CouponController@getCoupons']);//得到优惠券
				//            Route::any('/getResource' ,['uses'=>'CouponController@getResource']);//得到资源
				//            Route::any('/addPlan',['uses'=>'CouponController@addCouponPlan'])->middleware('couponPlan');   // 创建批量发送计划

			});

			/*******************************营销辅助工具******************************************/
			Route::group(['prefix' => 'assist'], function() {
				/*长链接转短链接*/
				Route::any('/short', ['uses' => 'AssistController@short']);
				Route::any('/st', ['uses' => 'AssistController@getShortUrl']);
			});

		});
	});

	// 社群运营
	Route::group(['as' => '103.'], function() {

		Route::any('/community_operate', function() { return view('admin.communityOperate.typeSelect'); }); //社群运营选择页

		// 评论互动
		Route::group(['as' => '118.'], function() {

			Route::get('/comment_admin', ['uses' => 'CommentAdminController@commentAdmin']);//评论首页
		});

		/**活动管理*/
		Route::group(['as' => '119.'], function() {
			Route::any('/activityManage', ['uses' => 'ActivityController@activityManage']);//活动管理首页-进行中;参数:searchContent
			Route::any('/activityListEnd', ['uses' => 'ActivityController@activityListEnd']);//活动管理首页-已结束;参数:searchContent
			Route::any('/createActivity', ['uses' => 'ActivityController@createActivity']);//创建活动页面
			Route::any('/editActivity', ['uses' => 'ActivityController@editActivity']);//编辑活动页面;参数:id(活动id)
			Route::any('/activityEnrollment', ['uses' => 'ActivityController@activityEnrollment']);//活动报名名单管理;参数:1--activity_id,2--activity_state,3--searchContent

			Route::any('/uploadActivity', ['uses' => 'ActivityController@uploadActivity', 'middleware' => 'ImageDeal']);//新增活动;参数:allParams--params、package_id、package_name、ticketParams(票种列表(包含:活动票种id 为 "" 、票种名称、价格、票总数、票种说明、是否需要审核))
			Route::any('/saveActivity', ['uses' => 'ActivityController@saveActivity', 'middleware' => 'ImageDeal']);//更新活动;参数:allParams--params、package_id、package_name、ticketParams(票种列表(包含:活动票种id、票种名称、价格、票总数、票种说明、是否需要审核))
			Route::any('/updateActivityState', ['uses' => 'ActivityController@updateActivityState']);//活动上架、下架、关闭;参数--1.type,2.activity_id
			Route::any('/passActivity', ['uses' => 'ActivityController@passActivity']);//活动报名-通过;参数--1.activity_id,2.user_id_list 数组
			Route::any('/denyActivity', ['uses' => 'ActivityController@denyActivity']);//活动报名-拒绝;参数--1.activity_id,2.user_id_list 数组,3.refuse_reason 拒绝理由
			//活动消息通知;
			//参数--
			//1.activity_id,
			//2.activity_state(0-全部,1-报名成功,2-已勾选(需传user_id_list))
			//3.sms_type(0-地址变更,1-时间变更,2-活动取消,3-通用)
			//4.notify_type(0-小纸条,1-短信)
			//5.notify_content(通知内容)
			Route::any('/activityNotify', ['uses' => 'ActivityController@activityNotify']);

			Route::any('/excel/activity', ['uses' => 'ActivityController@activityExcel']);//活动报名名单-导出excle;参数--1.activity_id,2.activity_state(0-全部,1-待审核,2-已报名成功,3-已关闭)

			Route::any('/get_enrollment_page', ['uses' => 'ActivityController@getEnrollmentPage']);   //获取报名管理数据;参数:1.activity_id, 2.activity_state(0-全部,1-待审核,2-已报名成功,3-已关闭)
			Route::any('/get_attendance_page', ['uses' => 'ActivityController@getAttendancePage']);   //获取签到管理数据;参数:1.activity_id
			Route::any('/change_sign_state', ['uses' => 'ActivityController@changeActivityActorState']);   //改变活动人员状态;参数:1.activity_id 2.user_id 3.state
			Route::any('/excel/attendance', ['uses' => 'ActivityController@excelAttendance']);   //签到导出
		});

		//  小社群
		Route::group(['prefix' => 'smallCommunity', 'namespace' => 'Community', 'as' => '120.'], function() {

			Route::get('/communityList', ['uses' => 'SmallCommunityController@communityList']);//社群列表   [ruler] 0：上架 1：下架   [search]：搜索内容
			Route::get('/createCommunity', ['uses' => 'SmallCommunityController@createCommunity']);//新增社群
			Route::any('/uploadCommunity', ['uses' => 'SmallCommunityController@uploadCommunity', 'middleware' => 'ImageDeal']);//新增社群接口  title  describe img_url piece_price  product_id（专栏或者会员id）（piece_price 和 product_id 二选一，至少传一个）
			Route::get('/editCommunity', ['uses' => 'SmallCommunityController@editCommunity']);//编辑社群  id
			Route::any('/updateCommunity', ['uses' => 'SmallCommunityController@updateCommunity', 'middleware' => 'ImageDeal']); //编辑社群处理接口   id title  describe img_url piece_price  product_id（专栏或者会员id） （piece_price 和 product_id 二选一，至少传一个）
			Route::any('/changeCommunityState', ['uses' => 'SmallCommunityController@changeCommunityState']); //上下架社群接口   id community_state 0：上架 1：下架
			Route::any('/setCommunityAdmin', ['uses' => 'SmallCommunityController@setCommunityAdmin']); //设置群主     id user_id
			Route::get('/communityDetail', ['uses' => 'SmallCommunityController@communityDetail']); //社群详情页面    id
			Route::any('/getUserInfo', ['uses' => 'SmallCommunityController@getUserInfo']); //获取用户信息
			Route::any('/getCommunityLinkSetAdmin', ['uses' => 'SmallCommunityController@getCommunityLinkSetAdmin']); //二维码
			Route::get('/isCommunityHaveAdmin', ['uses' => 'SmallCommunityController@isCommunityHaveAdmin']); //二维码

			Route::get('/dynamicList', ['uses' => 'SmallCommunityController@dynamicList']);//社群-动态列表    [ruler] 0：全部动态 1：精选动态  2：群主动态  [search] 搜索内容
			Route::get('/dynamicDetail', ['uses' => 'SmallCommunityController@dynamicDetail']);//动态详情
			Route::post('/commentDynamic', ['uses' => 'SmallCommunityController@commentDynamic']);//评论动态   id 动态id或者评论id comment 评论内容
			Route::post('/deleteDynamicComment', ['uses' => 'SmallCommunityController@deleteDynamicComment']);//删除评论接口 参数:id(评论id)
			Route::get('/createDynamic', ['uses' => 'SmallCommunityController@createDynamic']);//新建动态
			Route::get('/editDynamic', ['uses' => 'SmallCommunityController@editDynamic']);//编辑动态
			Route::post('/uploadDynamic', ['uses' => 'SmallCommunityController@uploadDynamic']);//新建动态接口   title content
			Route::post('/updateDynamic', ['uses' => 'SmallCommunityController@updateDynamic']);//编辑动态接口   id title content
			Route::post('/changeDynamicState', ['uses' => 'SmallCommunityController@changeDynamicState']);//动态移入(出)精选接口  删除  设为群公告   ------ id is_chosen 0：普通状态  1：精选状态  ------- id feeds_state  0：可见  1：隐藏  2：删除 -------- id is_notice 0：普通状态  1：公告状态
			Route::post('/dynamicPraise', ['uses' => 'SmallCommunityController@dynamicPraise']);//动态点赞 参数:1-id(社群动态id);2-state(0-取消点赞;1-点赞)

			Route::get('/userList', ['uses' => 'SmallCommunityController@userList']);//社群-用户列表  [ruler] 0：全部 1：黑名单  [search] ：搜索内容（手机或者昵称）
			Route::post('/changeUserState', ['uses' => 'SmallCommunityController@changeUserState']);//加入（移出）黑名单  user_id    state 0:正常 1：删除 2：黑名单
			Route::any('/queryCommunityRoomer', ['uses' => 'SmallCommunityController@queryCommunityRoomer']);//查询社群的群主;参数:community_id(社群id)

			// 查询当前动态是否可推送
			Route::any('/checkFeedsMessagePush/{community_id}', ['uses' => 'SmallCommunityController@checkFeedsMessagePush']);
			//            Route::get('/setting/{id}',['uses'=>'SmallCommunityController@setting']);  //社群功能设置页面
			//            Route::post('/set',['uses'=>'SmallCommunityController@setFunction']);  //社群功能设置页面
		});

		//  问答
		Route::group(['prefix' => 'QA', 'namespace' => 'Community', 'as' => '121.'], function() {

			/**
			 * 页面文件
			 */
			Route::get('/createQuestionAndAnswer', ['uses' => 'QuestionAndAnswerController@createQuestionAndAnswer']);        //创建问答页
			Route::get('/editQuestionAndAnswer', ['uses' => 'QuestionAndAnswerController@editQuestionAndAnswer']);            //编辑问答页
			Route::get('/questionAndAnswerDetail', ['uses' => 'QuestionAndAnswerController@questionAndAnswerDetail']);        //问答详情页
			Route::get('/getResponderList', ['uses' => 'QuestionAndAnswerController@getResponderList']);                      //答主列表
			Route::get('/getQuestionList', ['uses' => 'QuestionAndAnswerController@getQuestionList']);                        //问题列表
			Route::get('/getSettingPage', ['uses' => 'QuestionAndAnswerController@getSettingPage']);                          //设置页面
			Route::get('/editAnswerer', ['uses' => 'QuestionAndAnswerController@editAnswerer']);                        //编辑答主

			/**
			 * Ajax 请求
			 */
			Route::any('/saveQuestionAndAnswer', ['uses' => 'QuestionAndAnswerController@saveQuestionAndAnswer', 'middleware' => 'ImageDeal']);        //创建问答页
			Route::any('/inviteAnswerer', ['uses' => 'QuestionAndAnswerController@inviteAnswerer']);        //创建问答页
			Route::any('/changeStateQueProducts', ['uses' => 'QuestionAndAnswerController@changeStateQueProducts']);        //创建问答页
			Route::any('/changeQuestionState', ['uses' => 'QuestionAndAnswerController@changeQuestionState']);        //创建问答页
			Route::any('/changeAnswererState', ['uses' => 'QuestionAndAnswerController@changeAnswererState']);        //创建问答页
			Route::any('/refundList', ['uses' => 'QuestionAndAnswerController@refundList']);                        //退款列表
			Route::any('/commitRefund', ['uses' => 'QuestionAndAnswerController@commitRefund']);                        //确认退款
			Route::any('/isHaveQA', ['uses' => 'QuestionAndAnswerController@isHaveQA']);                        //是否有问答
			Route::any('/saveAnswerer', ['uses' => 'QuestionAndAnswerController@saveAnswerer', 'middleware' => 'ImageDeal']);                        //保存答主
			Route::any('/commitAnswer', ['uses' => 'QuestionAndAnswerController@commitAnswer']);                        //提交问答回答音频

			Route::post('/commitSetting', ['uses' => 'QuestionAndAnswerController@commitSetting']);           //提交问答设置

		});

		//  作业系统
		Route::group(['prefix' => 'exercise', 'namespace' => 'Community', 'as' => '121.'], function() {
			/**
			 * 页面文件
			 */
			Route::any('/exercise_book_list', ['uses' => 'ExerciseController@exerciseBookList']);   //作业本列表
			Route::any('/create_exercise_book', ['uses' => 'ExerciseController@createExerciseBook']);   //创建作业本
			Route::any('/edit_exercise_book', ['uses' => 'ExerciseController@editExerciseBook']);   //编辑作业本

			Route::any('/exercise_list', ['uses' => 'ExerciseController@exerciseList']);   //作业列表

			/**
			 * Ajax 请求
			 */
			Route::any('/upload_exercise_book', ['uses' => 'ExerciseController@uploadExerciseBook']);   //上传作业本;参数:见控制器注释
			Route::any('/update_exercise_book', ['uses' => 'ExerciseController@updateExerciseBook']);   //更新作业本;参数:见控制器注释
			Route::any('/get_resource_list', ['uses' => 'ExerciseController@getResourceListByType']);   //拉取资源列表;参数:资源类型
			Route::any('/set_exercise_book_role', ['uses' => 'ExerciseController@setExerciseBookRole']);   //设置作业本角色;参数:见控制器注释
			Route::any('/change_exercise_state', ['uses' => 'ExerciseController@changeExerciseState']);   //修改作业状态;参数:见控制器注释
			Route::any('/set_exercise_book_system_state', ['uses' => 'ExerciseController@setExerciseBookSystemState']);   //设置作业系统开关状态;参数:无

		});

	});

	//查看评论,多处共用,无限制
	Route::get('/alivecomment', ['uses' => 'AliveController@aliveComment']);//查看直播评论
	Route::get('/changealivecomment', ['uses' => 'AliveController@changeAliveComment']);//改变直播评论状态
	Route::get('/update_comment_state', ['uses' => 'CommentAdminController@updateCommentState']);//更新评论状态
	Route::post('/submit_admin_comment', ['uses' => 'CommentAdminController@submitAdminComment']);//提交管理员评论

	// 用户管理
	Route::group(['as' => '104.'], function() {

		/**用户列表*/
		Route::group(['as' => '122.'], function() {
			Route::get('/customer', ['uses' => 'CustomerController@customer']);//用户首页
			//用户详情+编辑+更新,多处共用,无限制
			Route::get('/customerdetail', ['uses' => 'CustomerController@customerDetail']);//用户详情页
			Route::get('/customeredit', ['uses' => 'CustomerController@customerEdit']);//用户编辑页
			Route::get('/customerupdate', ['uses' => 'CustomerController@customerUpdate']);//用户编辑功能
		});

		Route::group(['as' => '123.'], function() {
			// 开通记录
			Route::get('/pay_admin', ['uses' => 'MoneyAdminController@payAdmin']);//开通记录
			Route::post('/delete_purchase', ['uses' => 'MoneyAdminController@deletePurchase']);//删除订购记录
			Route::get('/excel/purchase', ['uses' => 'TaskController@exportExcel']);//导出开通记录
		});

		/**消息列表*/
		Route::group(['as' => '124.'], function() {
			Route::get('/message', ['uses' => 'MessageController@message']);//消息首页
			Route::get('/messageadd', ['uses' => 'MessageController@messageAdd']);//新增消息页面
			Route::post('/messagesave', ['uses' => 'MessageController@messageSave']);//保存消息
			Route::get('/messageedit', ['uses' => 'MessageController@messageEdit']);//编辑消息页面
			Route::post('/messageupdate', ['uses' => 'MessageController@messageUpdate']);//更新消息
			Route::get('/messagedelete', ['uses' => 'MessageController@messageDelete']);//撤回消息
		});

		//给用户发消息以及消息模板,多处共用,无限制
		Route::post('/customermsg', ['uses' => 'CustomerController@customerMsg']);//用户发消息
		Route::post('/modelchange', ['uses' => 'CustomerController@modelChange']);//回复模板更新/插入

		/**反馈列表*/
		Route::group(['as' => '125.'], function() {
			Route::get('/feedback', ['uses' => 'FeedBackController@feedBack']);//反馈首页

		});
		Route::post('/forbid', ['uses' => 'FeedBackController@forbiduser']);//用户快捷白名单处理

	});

	/**仪表盘*/
	Route::group(['as' => '105.'], function() {
		Route::get('/dashboard', ['uses' => 'DashBoardController@dashboard']);//仪表盘首页
		Route::get('/getGrowthTrend', ['uses' => 'DashBoardController@getGrowthTrend']);//3个echarts
	});

	// 订单记录
	Route::group(['as' => '128.'], function() {
		Route::get('/order_list', ['uses' => 'MoneyAdminController@orderList']);//订单记录
		Route::get('/excel/order', ['uses' => 'TaskController@exportOrderExcel']);//订单记录下载
		Route::get('/excel/list', ['uses' => 'TaskController@exportExcelList']);      // 导出订单记录列表页
	});

	/**财务管理*/
	Route::group(['as' => '106.'], function() {
		// 个人模式和企业模式收入列表
		Route::group(['prefix' => 'income'], function() {
			Route::get('/company', ['as' => '126.', 'uses' => 'MoneyAdminController@companyIncomeList']);
			Route::get('/person', ['as' => '127.', 'uses' => 'MoneyAdminController@personalIncomeList']);
		});

		// 提现
		Route::group(['as' => '129.'], function() {
			//请求提现记录页面
			Route::get('/withdraw_page', ['uses' => 'WithdrawAdminController@withdrawPage']);
			//请求申请提现页面
			Route::get('/apply_withdraw_page', ['uses' => 'WithdrawAdminController@applyWithdrawPage']);
			//请求绑定提现微信账号页面
			Route::get('/bind_wx_account_page', ['uses' => 'WithdrawAdminController@bindWxAccountPage']);
			//1.获取“可提现余额”
			Route::get('/get_account_balance', ['uses' => 'WithdrawAdminController@getAccountBalance']);
			//2.获取提现记录列表：参数：1-起止时间，2-状态
			Route::get('/get_withdraw_list', ['uses' => 'WithdrawAdminController@getWithdrawList']);
			//3.查看单条提现记录详情：
			Route::get('/get_withdraw_detail', ['uses' => 'WithdrawAdminController@getWithdrawDetail']);
			//4.判断该客户是否有认证信息-返回布尔类型
			Route::get('/get_certificate_info', ['uses' => 'WithdrawAdminController@getCertificateInfo']);
			//5.添加认证信息
			Route::post('/add_certificate_info', ['uses' => 'WithdrawAdminController@addCertificateInfo']);
			//6.编辑认证信息
			Route::post('/edit_certificate_info', ['uses' => 'WithdrawAdminController@editCertificateInfo']);
			//7.删除认证信息
			Route::post('/del_certificate_info', ['uses' => 'WithdrawAdminController@delCertificateInfo']);
			//8.“提现”申请确认
			Route::any('/confirm_withdraw', ['uses' => 'WithdrawAdminController@confirmWithdraw']);
			//发送验证码
			Route::get('/send_sms', ['uses' => 'WithdrawAdminController@sendSms']);
			//绑定微信账号
			Route::post('/bind_wx_account', ['uses' => 'WithdrawAdminController@bindWxAccount']);
			//查询提现绑定微信号
			Route::get('/query_saomiao_result', ['uses' => 'WithdrawAdminController@querySaomiaoResult']);
			//发送ajax请求生成一条记录在表t_bind_account_wx中
			Route::get('/create_wx_account_by_appid', ['uses' => 'WithdrawAdminController@createWxAccountByAppid']);
		});

	});

	/**账户管理*/
	Route::group(['as' => '107.'], function() {
		// 账户一览
		Route::group(['as' => '130.'], function() {

			Route::any('/accountview', ['uses' => 'UserController@accountView']);//账户一览请求页面
			Route::any('/flow_detail_list', ['uses' => 'UserController@flow_detail_list']);//流量明细请求页面
			Route::any('/storage_detail_list', ['uses' => 'UserController@storage_detail_list']);//存储量明细请求页面
			Route::any('/sms_detail_list', ['uses' => 'UserController@sms_detail_list']);//短信明细请求页面
		});

		Route::group(['namespace' => 'Users'], function() {
			/******主账户*******/
			Route::group(['as' => '999.'], function() {
				Route::any('/changeAdmin', ['uses' => 'AdminKController@changeAdmin']);//主账号设置
				Route::post('/admin/addAdminAccount', ['uses' => 'AdminKController@addAdminAccount']);      //新增/修改主账户操作
				Route::get('/admin/changePasswordPage', ['uses' => 'AdminKController@changePasswordPage']);      //主账户修改密码页面
				Route::post('/admin/isAcountRepeat', ['uses' => 'AdminKController@isAcountRepeat']);      //检测账号是否唯一
				Route::get('/admin/changeWxAccount', ['uses' => 'AdminKController@changeWxAccount']);      //更改绑定的微信商户
				Route::post('/admin/changePhone', ['uses' => 'AdminKController@changePhone']);   //更改绑定手机号

				//发票流程相关
				//申请开票
				Route::get('/manage_invoice', function() {
					return view('admin.invoiceManage.invoiceManage');
				});
				Route::get('/invoice_info', ['uses' => 'InvoiceController@getInvoiceInfo']);//获取发票信息
				Route::post('/create_invoice', ['uses' => 'InvoiceController@create']);//发票创建
			});
			/******子账户操作*******/
			Route::group(['as' => '131.', 'prefix' => 'admin'], function() {
				Route::any('/child', ['uses' => 'AdminController@childPage']);
				Route::any('/child/{action}/{id?}', ['uses' => 'AdminController@adminChildPage']); //新增/编辑子账户页面
				Route::any('/doChild/{action}/{id?}', ['uses' => 'AdminController@adminChild']);      //新增/编辑子账号操作
				Route::any('/del/{id}', ['uses' => 'AdminController@delAdminChild']);    //删除子账户操作
			});

			Route::get('/checkUsername/{username}', ['uses' => 'AdminController@checkUsername']);  // 校验用户名是否已存在

			// 测试脚本路由
			Route::get('/iiitest', "AdminController@changePrivilege");

			// 小程序配置
			Route::group(['as' => '133.', 'prefix' => 'mini'], function() {
				// 小程序集
				Route::any('/configure', ['uses' => 'MiniProgramController@configure']); // 小程序配置页
				Route::any('/person', ['uses' => 'MiniProgramController@person']); // 信息页面
				Route::any('/switch', ['uses' => 'MiniProgramController@personSwitch']); // 开关

				// 独立小程序
				Route::get('/index', ['uses' => 'MiniProgramController@index']);// 信息页面
				Route::get('/guide', ['uses' => 'MiniProgramController@guide']);// 引导页
				Route::get('/info', ['uses' => 'MiniProgramController@info']); // 详情页



				// 独立小程序  ajax
				Route::any('/authority', ['uses' => 'MiniProgramController@authority']);  // 授权(小程序)
				Route::any('/checkAuth', ['uses' => 'MiniProgramController@checkAuth']);     // 验证授权
				Route::any('/updateBind', ['uses' => 'MiniProgramController@updateBind']);     // 验证授权
				Route::any('/userCheck', ['uses' => 'MiniProgramController@userCheck']); // 验证用户信息
				Route::any('/merchant', ['uses' => 'MiniProgramController@merchantCheck']); // 验证商户信息
				Route::any('/resubmitAudit', ['uses' => 'MiniProgramController@reSubmitAudit']); // 重新提交审核


				Route::any('/changePayShow', ['uses' => 'MiniProgramController@changePayShow']); // 显示隐藏付费内容

                //代注册小程序
                Route::get('/main_guide',['uses'=>'MiniProgramController@mainGuide']);// 主引导页（选择手动和代注册）
                Route::any('/proxy_create_platform', ['uses' => 'MiniProgramController@proxy_create_platform']);  //创建开放平台
                Route::any('/authorityForPublic', ['uses' => 'MiniProgramController@authorityForPublic']);  // 公众号重新授权取账号管理权限
                Route::any('/authorityForRegister', ['uses' => 'MiniProgramController@authorityForRegister']);  // 公众号授权快速注册小程序权限
			});

			// 开放平台配置
			Route::group(['prefix' => 'open'], function() {

				Route::any('apisetting', ['uses' => 'OpenController@openApiSetting']); // 开放平台配置页
				Route::any('modifySetting', ['uses' => 'OpenController@modifySetting']); // 修改开放平台配置

			});

		});

		// 运营模式
		Route::group(['as' => '132'], function() {
			Route::get('/personmodel', ['uses' => 'UserController@personModel']);//个人模式页面
			Route::get('/companymodel', ['uses' => 'UserController@companyModel']);//企业模式页面
			Route::any('/confirm_order_get_paytype', ['uses' => 'UserController@confirmOrderGetPaytype']);//验证支付授权目录
			Route::any('/confirm_order', ['uses' => 'UserController@confirmOrder']);//验证支付授权目录
			Route::get('/set_wxpay_page', ['uses' => 'UserController@set_wxpay_page']);//公众号设置支付信息页面
			Route::get('/updateifauth', ['uses' => 'UserController@updateIfAuth']);//更新授权态
			Route::any('/check_auth_result', ['uses' => 'UserController@checkAuthResult']);//检查更新授权态
			Route::any('/updateCollection', ['uses' => 'UserController@updateCollection']);//更新商户的运营模式
			Route::get('/updatemerchantstep2', ['uses' => 'UserController@updateMerchantStep2']);//更新商户信息  设为企业模式  在用户设置好支付授权目录再更新为企业模式

		});

		Route::get('/accountmanage', ['uses' => 'UserController@accountManage']);//账号设置

		Route::get('/edit_wx_name', ['uses' => 'UserController@editWXName']);//修改商户名
		Route::post('/bind', ['uses' => 'UserController@bind']);//设置账号密码
		Route::post('/updatewxinfo', ['uses' => 'UserController@updateWxInfo']);//更新微信配置
		Route::post('/updateprimary', ['uses' => 'UserController@updatePrimary']);//更新主账号密码
		Route::post('/doaddprimary', ['uses' => 'UserController@doAddPrimary']);//添加主账号
		Route::post('/updatemerchant', ['uses' => 'UserController@updateMerchant']);//更新商户信息

		Route::any('/get_recharge_page', ['uses' => 'UserController@get_recharge_page']);//充值请求页面
		Route::any('/openNewGrowUpVersionPage', ['uses' => 'UserController@openNewGrowUpVersionPage']);//开通成长版
		Route::any('/openNewVipVersionPage', ['uses' => 'UserController@openNewVipVersionPage']);     //开通专业版

		Route::any('/update_version_page', ['uses' => 'UserController@update_version_page']); //升级请求页面
		Route::any('/upgrade_account', ['uses' => 'UserController@upgradeAccount']);          //新账户版本升级页面
		Route::any('/open_growUp_version_page', ['uses' => 'UserController@open_growUp_version_page']);//开通成长版请求页面
		Route::any('/open_vip_version_page', ['uses' => 'UserController@open_vip_version_page']);//开通专业版请求页面

		Route::any('/pre_wechatPay', ['uses' => 'WalletController@pre_wechatPay']);//微信预支付请求

		Route::any('/getPayResult', ['uses' => 'WalletController@getPayResult']);//获取订单支付状态
		/**  检查升级成长版、专业版或充值的订单有无遗漏 */
		Route::any('/check_orders_state', ['uses' => 'UserController@check_orders_state']);
		//        Route::get('/account_recharge_pre_wechatPay', ['uses'=>'UserController@accountView']);//账户充值-微信预支付请求
		//        Route::get('/account_recharge_wechatPay', ['uses'=>'UserController@accountView']);//账户充值-微信支付请求

		//旧小程序路由
		Route::get('/smallprogramsetting', ['uses' => 'UserController@smallProgramSetting']);//小程序设置首页
		Route::post('/updateSmallProgram', ['uses' => 'UserController@updateSmallProgram']);//更新小程序配置
	});

	//内容分销
	Route::group(['prefix' => 'chosen'], function() {
		/*********** 页面 ***********/

		Route::any('/homepage', ['uses' => 'ChosenController@homepage']);                    //首页
		Route::any('/get_goods_list', ['uses' => 'ChosenController@getGoodsListPage']);       //推广商品页面
		Route::any('/get_record_list', ['uses' => 'ChosenController@getRecordListPage']);     //推广订单页面
		Route::any('/manage_content', ['uses' => 'ChosenController@mangeContent']);

		/*********** AJA请求 ***********/
		Route::any('/chosen_enable', ['uses' => 'ChosenController@chosenEnable']);                 //开启小鹅通精选
		Route::any('/search_resource', ['uses' => 'ChosenController@searchResource']);             //获取可以添加的推广商品列表
		Route::any('/add_resource_chosen', ['uses' => 'ChosenController@addResourceChosen']);      //添加精选商品

		// 获取商品的 推广员 分销信息
		Route::get('/get_resource_distribute_info/{resource_type}/{resource_id}', ['uses' => 'ChosenController@getGoodsDistributeInfo']);

		Route::any('/set_xiaoe_distribute', ['uses' => 'ChosenController@setXiaoeDistribute']);    //设置单个商品梯度
		Route::any('/get_xiaoe_distribute', ['uses' => 'ChosenController@getXiaoeDistribute']);

		Route::any('/set_classify', ['uses' => 'ChosenController@setClassify']);                   //设置单个商品分类
		Route::any('/commit_content', ['uses' => 'ChosenController@commitContent']);              //推广订单页面

		Route::any('/data_2', ['uses' => 'ChosenController@data_2']);

	});

	/**轮播图*/
	//    Route::group(['middleware' => ['banner']], function ()
	//    {
	//        Route::get('/getBannerList', ['uses'=>'BannerAdminController@getBannerList']);//轮播图首页
	//        Route::get('/banner_create', ['uses'=>'BannerAdminController@bannerCreate']);//创建轮播图页
	//        Route::post('/upload_banner', ['uses'=>'BannerAdminController@upload_banner']);//新建轮播图
	//        Route::get('/edit_banner', ['uses'=>'BannerAdminController@edit_banner']);//编辑轮播图页
	//        Route::post('/save_edit_banner', ['uses'=>'BannerAdminController@save_edit_banner']); //轮播图编辑保存
	//    });

	/**上传域名验证文件需要转发文件(不加入权限控制)*/
	Route::any('/uploadVerifyFile', ['uses' => 'UploadController@uploadVerifyFile']); //上传域名验证文件

	/**上传到腾讯云存储,发送签名,多处上传共用,无限制*/
	Route::get('/getUploadSign', ['uses' => 'UploadController@getUploadSign']);

	/**获取跳转的资源列表,轮播栏和消息栏共用,无限制**/
	Route::get('/banner/getResourceList', ['uses' => 'BannerAdminController@getResourceList']);

	/**旧的圈子请求,暂时不用*/
	Route::get('/blogComment_admin', ['uses' => 'BlogCommentAdminController@blogCommentAdmin']);//帖子评论管理
	Route::get('/update_bComment_state', ['uses' => 'BlogCommentAdminController@updateBlogCommentState']); //更新帖子评论状态
	Route::get('/blog_admin', ['uses' => 'BlogAdminController@blogAdmin']);//帖子管理
	Route::get('/update_blog_state', ['uses' => 'BlogAdminController@updateBlogState']); //更新帖子状态

	/**上传到腾讯云点播,发送签名,直播+视频共用,无限制*/
	Route::get('/getsig', ['uses' => 'ContentController@getSign']); //视频页签名验证
	Route::post('/getsigv4', ['uses' => 'ContentController@getSignV4']); //视频页签名验证

	//    //红包模块
	//    Route::get('/redPacket_admin', ['uses' => 'RedPacketAdminController@redPacket']);//红包列表
	//    Route::get('/export_Redpacket', ['uses' => 'RedPacketAdminController@exportExcel']);//导出红包记录

	Route::get('/pvuvsearch', ['uses' => 'VisitCountController@visitSearch']);//pvUV搜索入口
	//吴晓波生成体验链接
	Route::get('/experience', ['uses' => 'ExperienceController@experience']);//体验链接首页
	Route::get('/addExperience', ['uses' => 'ExperienceController@addExperience']);//增加体验链接
	Route::post('/doaddExperience', ['uses' => 'ExperienceController@doaddExperience']);//新增体验链接操作
});

Route::any('/passwordtest', ['uses' => 'TestController@passwordTest']);//密码更改
Route::any('/resetAppPassword', ['uses' => 'TestController@resetAppPassword']);//密码重置

/**通知中心*/
Route::any('/notice_list', ['uses' => 'noticeController@topBarNotice']); //通知列表，加入show_all=1返回一个页面
Route::any('/change_view_state', ['uses' => 'noticeController@changeNoticeState']); //更改阅读状态
Route::any('/templateNotice', ['uses' => 'noticeController@templateNotice']);

//流量数据导入
//Route::any('/import_flow_data', ['uses'=>'TestController@import_flow_data']);//流量数据导入

/**微信扫码支付回调*/
Route::any('/wechatPay_hook', ['uses' => 'WalletController@wechatPay_hook']);//微信支付结果回调异步通知请求
//Route::any('/query_order', ['uses'=>'WalletController@query_order']);//查询订单

/**下载视频,服务器访问,异步线程,没有登录态,不能校验*/
Route::get('/downloadVideo', ['uses' => 'DownloadController@downloadVideo']);
Route::get('/downloadImage', ['uses' => 'DownloadController@downloadImage']);
Route::get('/downloadImaged', ['uses' => 'DownloadController@downloadImaged']);
Route::get('/downloadAudio', ['uses' => 'DownloadController@downloadAudio']);
Route::get('/mp3tom3u8', ['uses' => 'DownloadController@mp3tom3u8']);//异步mp3变为m3u8格式
Route::get('/oneAudiotom3u8', ['uses' => 'ContentController@oneAudioasync']);//异步mp3变为m3u8格式
Route::get('/batch2m3u8', ['uses' => 'ContentController@batchStartMp3tom3u8']);//异步批量mp3变为m3u8格式
Route::get('/batchmp3tom3u8', ['uses' => 'DownloadController@batchMp3tom3u8']);//批量异步mp3变为m3u8格式
Route::get('/downloadAliveVoice', ['uses' => 'DownloadController@downloadAliveVoice']);

Route::get('/shihanbing', ['uses' => 'TestController@shihanbing']);
Route::get('/shidian', ['uses' => 'TestController@shidian']);

/**科学队长迁移*/
Route::get('/scienceinvitecode', ['uses' => 'ScienceController@scienceInviteCode']);//科学队长邀请码
Route::get('/scienceorder', ['uses' => 'ScienceController@scienceOrder']);//科学队长订单
Route::get('/scienceuser', ['uses' => 'ScienceController@scienceUser']);//科学队长用户
Route::get('/sciencepurchase', ['uses' => 'ScienceController@sciencePurchase']);//科学队长订购关系
Route::get('/sciencereadd', ['uses' => 'ScienceController@scienceReAdd']);//科学队长根据订购关系重新添加套餐
Route::get('/sciencedeal', ['uses' => 'ScienceController@scienceDeal']);//订购处理

Route::get('/audiocompressurl', ['uses' => 'DownloadController@audioCompressUrl']);
Route::get('/audiom3u8url', ['uses' => 'DownloadController@audioM3u8Url']);

/**自动脚本*/
//Route::get('/BShell/alive_deal', ['uses' => 'BShellController@alive_deal']);
Route::get('/BShell/insertTDashStat', ['uses' => 'BShellController@insertTDashStat']);//更新时段各统计数据
Route::get('/BShell/SumCountIncomeDayCount', ['uses' => 'BShellController@insertTDashStatSumCountIncome']);//更新日总用户|总收入统计数据
Route::get('/BShell/insertTDashStatTest', ['uses' => 'BShellController@insertTDashStatTest']);//检测全网时段统计数据
Route::get('/BShell/insertTDashStatPaidActive', ['uses' => 'BShellController@insertTDashStatPaidActive']);//更新时段[付费活跃用户]统计数据
Route::get('/BShell/insertTDashStatPayCount', ['uses' => 'BShellController@insertTDashStatPayCount']);//更新时段[付费用户]统计数据
Route::get('/BShell/insertTDashStatDayCount', ['uses' => 'BShellController@insertTDashStatDayCount']);//更新[按日统计活跃用户]统计数据
Route::get('/BShell/TDashStatPaidActiveDayCount', ['uses' => 'BShellController@insertTDashStatPaidActiveDayCount']);//更新[按日统计付费(活跃)用户]统计数据
Route::get('/BShell/PaidActiveOneDayCount', ['uses' => 'BShellController@insertTDashStatPaidActiveOneDayCount']);//更新[单日统计付费(活跃)用户]统计数据
Route::get('/BShell/insertTDashStatPayDayCount', ['uses' => 'BShellController@insertTDashStatPayDayCount']);//更新[按日统计付费用户]统计数据
Route::get('/BShell/PayOneDayCount', ['uses' => 'BShellController@insertTDashStatPayOneDayCount']);//更新[单日统计付费用户]统计数据
Route::get('/BShell/insertTDashPage', ['uses' => 'BShellController@insertTDashPage']);//更新全网统计数据
Route::get('/BShell/insertApiLogTest', ['uses' => 'BShellController@insertApiLogTest']);//模拟测试客户端分表插入日志数据
/**手动脚本|单用户数据|吴晓波*/
Route::get('/BShell/runOneAppStatHourCount', ['uses' => 'BShellController@runOneAppStatHourCount']);//小时统计
Route::get('/BShell/runOneAppStatHourSum', ['uses' => 'BShellController@runOneAppStatHourSum']);//小时截至总数统计+付费活跃
Route::get('/BShell/runOneAppStatDayCount', ['uses' => 'BShellController@runOneAppStatDayCount']);//天统计
Route::get('/BShell/runOneAppStatDaySum', ['uses' => 'BShellController@runOneAppStatDaySum']);//天截至总数统计+付费活跃
Route::get('/BShell/updatePackageResourceCount', ['uses' => 'CrontabShellController@updatePackageResourceCount']);///**专栏期数*/
Route::get('/BShell/updateOneAppPackageCount', ['uses' => 'CrontabShellController@updateOneAppPackageCount']);///**单用户专栏期数*/
Route::get('/BShell/MoveProResRelation', ['uses' => 'CrontabShellController@MoveProResRelation']);///**资源多对多关系转移*/

/**腾讯云转码回调*/
Route::any('/alivetranscode', ['uses' => 'ContentController@aliveTranscode']); //直播视频转码成功回调
Route::any('/transcode_notify', ['uses' => 'ContentController@transCodeNotify']); //腾讯云点播视频转码成功回调

Route::any('/qcloudcallback', ['uses' => 'QCloudController@qCloudCallback']); //腾讯云点播视频转码成功回调

/**去超级管理员界面*/
Route::get('/to_super_page', ['uses' => 'SuperAdminController@toSuperPage']);
Route::get('/super_login', ['uses' => 'SuperAdminController@superLogin']);

/**小鹅通平台 使用文档*/
Route::get('/help', ['uses' => 'HelpController@join']); //接入指引
Route::get('/help/index', ['uses' => 'HelpController@index']); //文档首页
Route::get('/help/instructions', ['uses' => 'HelpController@instructions']); //使用文档
Route::get('/help/qs', ['uses' => 'HelpController@qs']); //帮助答疑
Route::get('/help/explainDoc', ['uses' => 'HelpController@explainDoc']); //说明文档
Route::get('/help/system_update', ['uses' => 'HelpController@systemUpdate']); //系统升级说明
/**帮助中心*/
Route::get('/help_document', ['uses' => 'help\HelpController@helpPage']); //帮助中心页面显示
Route::get('/help_mobile', ['uses' => 'help\HelpController@helpPageMobile']); //帮助中心手机版页面显示
Route::any('/get_help_content', ['uses' => 'help\HelpController@helpContent']); //帮助中心内容拉取

/**小鹅通协议*/
Route::any('/charge_protocol_page', ['uses' => 'UserController@charge_protocol_page']);//小鹅服务协议内容
/**跨服务器上传域名验证文件需要转发文件(不加入权限控制)*/
Route::any('/remote_uploadVerifyFile', ['uses' => 'UploadController@remote_uploadVerifyFile']); //上传域名验证文件

/**
 * 帮助中心（新）
 */
Route::any('/helpCenter', ['uses' => 'help\NewHelpController@helpCenter']); //帮助中心公共页

Route::any('/helpCenter/index', ['uses' => 'help\NewHelpController@index']); //首页

Route::any('/helpCenter/freshMan', ['uses' => 'help\NewHelpController@freshMan']); //新手专区

Route::any('/helpCenter/problem', ['uses' => 'help\NewHelpController@problem']); //问题汇总

Route::any('/helpCenter/document_detail_page', ['uses' => 'help\NewHelpController@getDocumentDetail']);//文档详情

Route::any('/helpCenter/search', ['uses' => 'help\NewHelpController@searchByContent']);//搜索

// 超级管理员
Route::group(['middleware' => ['super_admin']], function() {

	//灰度、删除用户
	Route::any('/company_to_personal', ['uses' => 'UserController@huidu']);
	Route::any('/query_account_by_phone', ['uses' => 'UserController@query_account_by_phone']);//查询用户信息
	Route::any('/set_huidu_by_phone', ['uses' => 'UserController@set_huidu_by_phone']);//设置灰度

	Route::any('/set_app_https', ['uses' => 'SuperAdminController@set_app_https']);
	Route::any('/set_close', ['uses' => 'SuperAdminController@set_close']);
});

/**
 * 问答-自动退款
 */
Route::any('/autoRefund', ['uses' => 'TestController@autoRefund']);//定时任务:问答-自动退款
//每日任务-短信通知答主有人向他提问
Route::any('/sendNotifySmsToAnswer', ['uses' => 'TestController@sendNotifySmsToAnswer']);

/**
 *定时任务:扣费-每日
 ************************************/
Route::any('/createChargeRecords', ['uses' => 'chargeShellController@createChargeRecords']);//定时任务1:生成扣费记录
Route::any('/createMiddleRecords', ['uses' => 'chargeShellController@createMiddleRecords']);//脚本1-1:生成中间表（扣费记录）
Route::any('/createFlowRecords', ['uses' => 'chargeShellController@createFlowRecords']);//脚本1-2:生成流量记录
Route::any('/createStorageRecords', ['uses' => 'chargeShellController@createStorageRecords']);//脚本1-3:生成存储记录

//短信扣费、提成扣费
Route::any('/createYesterdaySmsRecords', ['uses' => 'chargeShellController@createYesterdaySmsRecords']);
Route::any('/doCharge', ['uses' => 'chargeShellController@doCharge']);//定时任务2:系统扣费
Route::any('/doChargeSingle', ['uses' => 'chargeShellController@doChargeSingle']);//任务3:新增基础版开通充值50块
Route::any('/doSubsidy', ['uses' => 'chargeShellController@doSubsidy']);//任务4:小鹅通补贴
Route::any('/doUpdateImageSize', ['uses' => 'chargeShellController@doUpdateImageSize']);//任务5:更新流量记录表中的img_size_total字段
Route::any('/doGenDataUsageApp', ['uses' => 'chargeShellController@doGenDataUsageApp']);//任务6:生成业务流量中间表记录

/***分销统计中间表**/
Route::any('/doDistributeStat', ['uses' => 'TaskShell\DoneDiStatController@doDistributeStat']);

////测试备份表的功能
//Route::any('/bakAppConf',['uses'=>'chargeShellController@bakAppConf']);

/****************API 接口********************/
Route::group(['prefix' => 'api', 'namespace' => 'APIController', 'middleware' => 'APIMiddleware'], function() {
	// 手机端管理台注册接口
	Route::any('/sign', ['uses' => 'ApiSignController@mobile_sign']);
});

/**
 * 测试使用
 */
Route::group(['prefix' => 'test'], function() {
	//测试cdn刷新url
	Route::any('/updateAudio0Size', ['uses' => 'TestController@updateAudio0Size']);

	Route::any('/updateAudioImageSize', ['uses' => 'TestController@updateAudioImageSize']);
	Route::any('/updateVideoImageSize', ['uses' => 'TestController@updateVideoImageSize']);
	Route::any('/updateTextImageSize', ['uses' => 'TestController@updateTextImageSize']);

	Route::any('/getAudioSize', ['uses' => 'TestController@getAudioSize']);
	Route::any('/updateVideoLengthAndVbitrate', ['uses' => 'ContentController@updateVideoLengthAndVbitrate']);
	Route::any('/updateAliveLengthAndVbitrate', ['uses' => 'ContentController@updateAliveLengthAndVbitrate']);
	Route::any('/culAudioAndVideoFlow', ['uses' => 'TestController@culAudioAndVideoFlow']);

	Route::any('/makeAppDirInCos', ['uses' => 'CosV3ToV4Controller@makeAppDirInCos']);

	Route::any('/test', ['uses' => 'TestController@test123']);
	Route::any('/updateVideoState', ['uses' => 'TestController@updateVideoState']);
	Route::any('/updateAliveState', ['uses' => 'TestController@updateAliveState']);
	Route::any('/tempCompressBanner', ['uses' => 'TestController@tempCompressBanner']);
	Route::any('/tempCompressPro', ['uses' => 'TestController@tempCompressPro']);
	Route::any('/tempCompressAudio', ['uses' => 'TestController@tempCompressAudio']);

	//TOP60的 专栏/会员  销售额排序（订阅数乘以单价），以及每个专栏/会员对应的总评论数；
	Route::any('/Top60', ['uses' => 'TestController@Top60']);
	Route::any('/choiceChosenResourceExcel', ['uses' => 'TestController@choiceChosenResourceExcel']);
	Route::any('/updateOrderProducts', ['uses' => 'TestController@updateOrderProducts']);
	//科学队长所有节目的完播量和播放量；
	Route::any('/scienceLeaderAudioStatistics', ['uses' => 'TestController@scienceLeaderAudioStatistics']);

	//成长版、专业版退款
	Route::any('/refund', ['uses' => 'TestController@refund']);
	//问答相关数据导出
	Route::any('/exportQuestionDataExcel', ['uses' => 'TestController@exportQuestionDataExcel']);

	//导出小鹅通全部问题反馈
	Route::any('/exportAllQuestionListExcel', ['uses' => 'TestController@exportAllQuestionListExcel']);
});


/*Route::group(['prefix' => 'test','namespace' => 'Test'],function(){
   Route::any('/notice',['uses'=>'TestController@createNotice']);
   Route::any('/count',['uses'=>'TestController@getCount']);
});*/
