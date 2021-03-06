<?php
/**
 * Created by PhpStorm.
 * User: fuhaiwen
 * Date: 2017/1/16
 * Time: 19:46
 */

namespace App\Http\Controllers\Tools;

class StringConstants
{

	// 错误码
	const Code_Succeed = 0;
	const Code_Failed = 1;
	const Code_Invalid_Operation = 2;
	const Code_Not_Login = 3;
	const Code_Not_Bond = 4;
	const Code_Not_Company = 5;
	const Code_Not_Lady = 6;
	const Code_Missing_Para = 7;
	const Code_Wrong_Para = 8;
	const Code_DB_Error = 9;
	const Code_Logic_Error = 10;
	const Code_Not_Admin = 11;
	const Code_No_Data = 12;
	const Code_No_Enough_Money = 13;

	const Code_Exist_Fields = 20;

	const Code_Error_Pay_Channel = 30;
	const Code_Error_Pay_Exception = 31;
	const Code_Error_Pay_Failed = 32;
	const Code_Error_Pay_Password = 35;
	const Code_Error_Pay_Amount_Less = 40;

	const Msg_Failed = '操作失败';
	const Msg_Succeed = '操作成功';
	const Cash_Status_Checking = 0;
	const Cash_Status_INIT = -1;
	const Cash_Status_Limit = 5;

	const BINDING = 0;
	const UNBINDED = 1;

	//业务错误代码
	const ERR_UNBIND_WX_ACCOUNT = 10001;

	//活动状态
	const ACTIVITY_GOING = 0;           //进行中
	const ACTIVITY_END = 1;              //已结束
	const ACTIVITY_LISTING = 0;          //上架
	const ACTIVITY_LIST_OUT = 1;         //下架
	const ACTIVITY_CLOSED = 2;           //关闭
	const ACTIVITY_NOT_NEED_CONFIRM = 0; //不需要审核
	const ACTIVITY_NEED_CONFIRM = 1;    //需要审核
	const ACTIVITY_CONFIRMING = 0;    //待审核
	const ACTIVITY_CONFIRM_PASS = 1;    //审核通过
	const ACTIVITY_CONFIRM_SINGED = 5;    //已签到
	const ACTIVITY_CONFIRM_UPPASS = 2;    //审核不通过
	const ACTOR_ALL = -1;              //全部状态

	//活动保存
	const ACTIVITY_ADD = 0;//新增活动
	const ACTIVITY_UPDATE = 1;//更新活动
	const ACTIVITY_UPDATE_SUCCESSED = 0;//更新活动成功
	const ACTIVITY_UPDATE_FAILED = -1;//更新活动失败
	const ACTIVITY_ADD_SUCCESSED = 0;//新增活动成功
	const ACTIVITY_ADD_FAILED = -1;//新增活动失败
	const ACTIVITY_EXPIRE = 4;//活动过期

	//活动报名状态
	const ACTIVITY_ACTOR_ALL = 0;//活动报名的全部用户
	const ACTIVITY_ACTOR_CONFIRMING = 1;//活动报名审核中的用户
	const ACTIVITY_ACTOR_PASS = 2;//活动报名已成功的用户
	const ACTIVITY_ACTOR_UNPASS = 3;//活动报名已关闭的用户
	const ACTIVITY_ACTOR_SIGN = 4;//活动报名已签到的用户
	const ACTIVITY_ACTOR_UNSIGN = 5;//活动报名未签到的用户
	//知识商品
	const SINGLE_GOODS_SALING = 0;//已上架的单品
	const SINGLE_GOODS_NOT_SALE = 1;//已下架的单品
	const SINGLE_GOODS_DELETE = 2;//已删除的单品
	const SINGLE_GOODS_PAYMENT_TYPE_UPDATE = 3;//修改payment_type

	//资源类型(0-全部,1-图文,2-音频,3-视频,4-直播);
	const SINGLE_GOODS_ALL = 0;//全部
	const SINGLE_GOODS_ARTICLE = 1;//图文
	const SINGLE_GOODS_AUDIO = 2;//音频
	const SINGLE_GOODS_VIDEO = 3;//视频
	const SINGLE_GOODS_ALIVE = 4;//直播
	const SINGLE_GOODS_PACKAGE = 6;//专栏
	const SINGLE_GOODS_MEMBER = 5;//会员

	//关系状态
	const RELATION_NORMAL = 0;// 正常
	const RELATION_DELETED = 1;// 删除

	//专栏上移、下移
	const PACKAGE_MOVE_UP = 0;//专栏上移
	const PACKAGE_MOVE_DOWN = 1;//专栏下移

	//资源关系移除渠道
	const RESOURCE_CHANNEL_SINGLE = 0;//单品
	const RESOURCE_CHANNEL_PACKAGE = 1;//专栏内资源
	const RESOURCE_CHANNEL_MEMBER = 2;//会员内资源

	//资源是否试听
	const RESOURCE_AUDIO_TRY = 1;//试听
	const RESOURCE_AUDIO_NOT_TRY = 0;//不试听

	//资源新增、编辑
	const RESOURCE_ADD = 0;//新增资源
	const RESOURCE_EDIT = 1;//编辑资源

	//专栏新增、编辑
	const PACKAGE_ADD = 0;//新增专栏
	const PACKAGE_EDIT = 1;//编辑专栏

	//新增时的渠道来源
	const ADD_CHANNEL_SINGLE = 1;//单品新增
	const ADD_CHANNEL_PACKAGE = 2;//专栏新增
	const ADD_CHANNEL_MEMBER = 3;//会员新增
	//选择单品时的渠道来源
	const CHOICE_CHANNEL_SINGLE = 1;//单品
	const CHOICE_CHANNEL_PACKAGE = 2;//专栏
	const CHOICE_CHANNEL_MEMBER = 3;//会员

	const SINGLE_LIST = 4;//单品列表
	const PACKAGE_SINGLE_LIST = 5;//专栏中的单品列表

	//会员相关
	const NOT_MEMBER = 0;//专栏不兼做会员
	const AS_MEMBER = 1;//专栏兼做会员
	//关系表中资源类型
	const PRO_RES_PACKAGE = 6;//会员-专栏关系
	const PRO_RES_ACTIVITY = 5;//专栏(或会员)-活动关系

	//设置单卖
	const GOODS_SINGLE_SALE = 0;//设为单卖
	const GOODS_SINGLE_UNSALE = 1;//取消单卖

	//购买方式
	const PAYMENT_TYPE_FREE = 1;//免费
	const PAYMENT_TYPE_SINGLE = 2;//单卖
	const PAYMENT_TYPE_PACKAGE = 3;//专栏包
	const PAYMENT_TYPE_QUESTION = 7;//问答提问

	//动态类型
	const DYNAMIC_TYPE_TEXT = 0;//图文动态
	const DYNAMIC_TYPE_TEXTAREA = 1;//富文本动态

	//创建动态的用户身份类型
	const DYNAMIC_USER_TYPE_NORMAL = 0;//普通用户
	const DYNAMIC_USER_TYPE_ROOMER = 1;//群主

	//创建动态的来源
	const DYNAMIC_CHANNEL_TYPE_APP = 0;//手机端
	const DYNAMIC_CHANNEL_TYPE_PC = 1;//PC端

	//评论类型
	const COMMENT_TYPE_MAIN = 0;//主评论
	const COMMENT_TYPE_SUB = 1;//附属评论

	//评论记录的状态
	const COMMENT_STATE_DELETE = 2;//删除

	//问题状态
	const QUESTION_STATE_SHOW = 0;//显示
	const QUESTION_STATE_HIDE = 1;//隐藏
	const QUESTION_STATE_DELETE = 2;//删除

	//问题支付状态
	const QUESTION_PAY_STATE_UNPAY = 0;//未支付
	const QUESTION_PAY_STATE_PAY = 1;//预支付待回答
	const QUESTION_PAY_STATE_SOLVED = 2;//已回答并入账
	const QUESTION_PAY_STATE_REFUND = 3;//未回答已退款

	//订单状态(订单状态：0-未支付 1-支付成功 2-支付失败 3-退款)
	const ORDER_STATE_UNPAY = 0;//未支付
	const ORDER_STATE_PAY = 1;//支付成功
	const ORDER_STATE_PAY_FAILED = 2;//支付失败
	const ORDER_STATE_REFUNDED = 3;//退款

	//订单类型
	const ORDER_STATE_PERSON = 1;//个人模式
	const ORDER_STATE_COMPANY = 0;//企业模式

	//订单核算状态(问答订单核算状态；0-未核算；1-已核算；2-核算失败)
	const ORDER_QUE_CHECK_STATE_UNCHECK = 0;//订单未核算,即问题未被回答
	const ORDER_QUE_CHECK_STATE_CHECKED = 1;//订单已核算,即问题被答主回答
	const ORDER_QUE_CHECK_STATE_CHECK_FAILED = 2;//订单核算失败,即问题未被回答,且已退款成功

	/**
	 * 首页模块序号 1-轮播图模块、2-分类导航模块、3-社群模块、4-会员及专利模块 5-问答模块 6-直播模块 7-最新模块
	 */
	const IndexBannerPart = 1;
	const IndexCategoryPart = 2;
	const IndexCommunityPart = 3;
	const IndexMemberPart = 4;
	const IndexQuestionPart = 5;
	const IndexAlivePart = 6;
	const IndexRecommendPart = 7;
	const IndexActivityPart =8;

	/**
	 * 作业本操作类型
	 */
	const  EXERCISE_BOOK_ADD = 0;//新增作业本
	const  EXERCISE_BOOK_EDIT = 1;//编辑作业本
	/**
	 * 作业本角色类型
	 */
	const EXERCISE_BOOK_ROLE_TYPE_TEACHER = 0;//老师
	const EXERCISE_BOOK_ROLE_TYPE_ASSISTANT = 1;//助教

}