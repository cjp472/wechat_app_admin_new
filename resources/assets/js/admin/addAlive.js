/**
 * Created by Stuphin on 2016/11/5.
 */
//腾讯云上传
//var bucketName = window.cos_bucket_name;
//var cos = new CosCloud(window.cos_app_id);
var cos = new InitCosCloud();
var params={};//提交的直播参数集
var roleParams={};//提交的直播角色参数集
var shaFlag=false;//计算sha值的标志,正确才能上传视频
var addIndex;//"点击添加"时其所在的行
var is_single_sale=0;//默认非单卖
var category_type;  //  所属分类

var url_app_id = "";
var curSelected, hasSelectAlert = false;
$(document).ready(function()
{
    init();

    //checkbox"专栏外单卖"
    $("#checkbox-img").click(function () {
        var checkedState = $("#checkbox-img").attr("checked-state");
        if (checkedState == "unchecked") {
            $("#checkbox-img").attr("checked-state", "checked");
            $("#checkbox-img").css("backgroundPosition", "-104px 0");
            $("#single_price_div").removeClass('hide');


        } else if (checkedState == "checked") {
            $("#checkbox-img").attr("checked-state", "unchecked");
            $("#checkbox-img").css("backgroundPosition", "-78px 0");
            $("#single_price_div").addClass('hide');

        }
        //  处理所属分类
        if (checkedState == "unchecked") {                // 由unchecked =>　checked 专栏外单卖
            $(".upload_item_div").removeClass("hide");
        } else {                                        //  只是专栏
            $(".upload_item_div").addClass("hide");
        }
        isShowPush(); //判断是否显示推送
    });

    homePicHandle();
    picHandle();
    typeHandle();
    aliveTypeHandle();
    videoHandle();
    userHandle();
    modalHandle();
    initUpload();


    isShowPush(); //判断是否显示推送
    $('#packageInfo').on('change' ,function() {
        isShowPush();
    });
    curSelected = $('#push_ahead option:selected').index();
    $('#push_ahead').on('change', function (e) {
        var ele = $(this),
            selectVal = ele.val(),
            nowSelected = $('#push_ahead option:selected').index();
        if(!hasSelectAlert && selectVal !== -1) {
            $('#push_ahead option').eq(curSelected).prop('selected', true);
            var txt=  "您需要到微信公众号后台将您的服务号所在行业设置为“教育/培训”，设置完成后，开启服务号通知方可正常发送模板消息。"+
            "<br/><a target='_blank' href='/help/instructions#hp5_alive_prompt' style='margin-top:10px;'>什么是开课提醒？</a>";
            var option = {
                title: "提示", //弹出框标题
                btn: 3, //确定&&取消
                oktext: '我已设置',
                canceltext: '关闭',
                icon: 'blue',
                onOk: function () {
                    curSelected = nowSelected;
                    $('#push_ahead option').eq(curSelected).prop('selected', true);
                    hasSelectAlert = true;
                }
            }
            $.alert(txt, "custom", option);
            return true;
        } else if(hasSelectAlert){
            curSelected = nowSelected;
        }
    });


    //新增直播
    $("#finish").click(function()
    {
        //标题
        params['title']=$("#title").val().trim();
        if(params['title'].length == 0)
        {
            baseUtils.show.redTip('亲,请输入直播标题哦~');
            return false;
        }

        //简介
        params['summary']=$("#summary").val().trim();
        if(params['summary'].length == 0)
        {
            baseUtils.show.redTip('亲,请输入直播简介哦~');
            return false;
        }

        //选择收费形式
        var typeIndex=$(".blue").index();
        if(typeIndex == 2)//免费
        {
            params['payment_type']=1;
            params['piece_price']=0;
            params['product_id']='';
            params['product_name']='';
            params['product_state']=0;
        }
        else if(typeIndex == 1)//单个
        {
            var singlePrice=$("#piece_price").val().trim();
            if(singlePrice.length == 0)
            {
                baseUtils.show.redTip('亲,请输入单个付费价格哦~');
                return false;
            }else if (singlePrice <= 0) {
                baseUtils.show.redTip('价格需高于0.00!');
                return false;
            }
            params['payment_type']=2;
            params['piece_price']=singlePrice*100;
            params['product_id']='';
            params['product_name']='';
            params['product_state']=0;
        }
        else if(typeIndex == 0)//专栏
        {
            var packageName=$("#packageInfo").find("option:selected").text().trim();
            if(packageName.length == 0)
            {
                baseUtils.show.redTip('亲,请选择专栏内容哦~');
                return false;
            }
            params['payment_type']=3;
            params['piece_price']=$("#packageInfo").find("option:selected").attr("price");
            params['product_id']=$("#packageInfo").find("option:selected").val();
            params['product_name']=packageName;
            params['product_state']=$("#packageInfo").find("option:selected").attr("state");

            //TODO:是否单卖
            var checkedState = $("#checkbox-img").attr("checked-state");
            if (checkedState == "unchecked") {
                is_single_sale = 0;//不单卖

            } else if (checkedState == "checked") {
                is_single_sale = 1;//单卖
                var reg = /^((0)|([1-9]{1}\d*))(\.\d{1,2})?$/;

                var single_price = $("#single_price").val().trim();

                if(single_price.length>0)
                {
                    if(reg.test(single_price))
                    {
                        if (single_price == 0 || single_price == 0.0 || single_price == 0.00) {
                            // util.showError('#withdraw_amount-err', '提现金额不能为0！', '#withdraw_amount', true);
                            baseUtils.show.redTip('金额不能为0!');
                            return false;
                        }
                        params['piece_price'] = single_price*100;
                    }else{
                        baseUtils.show.redTip( '请输入正确的金额！');
                        return false;
                    }
                }else{
                    baseUtils.show.redTip('请输入单价!');
                    return false;
                }

            }

        }

        //直播开始时间
        params['zb_start_at']=$("#zb_start_at").val();
        if(params['zb_start_at'].length == 0)
        {
            baseUtils.show.redTip('亲,请输入直播开始时间哦~');
            return false;
        }

        //直播关闭时间
        params['zb_stop_at']=$("#zb_stop_at").val();
        if(params['zb_stop_at'].length == 0)
        {
            baseUtils.show.redTip('亲,请输入直播关闭时间哦~');
            return false;
        }

        if(params['zb_start_at'] > params['zb_stop_at'])
        {
            baseUtils.show.redTip('亲,关闭时间小于开始时间哦~');
            return false;
        }

        //上架时间
        params['start_at']=$("#start_at").val();
        if(params['start_at'].length == 0)
        {
            baseUtils.show.redTip('亲,请输入上架时间哦~');
            return false;
        }

        //开课提醒
        params['push_ahead']=$("#push_ahead").val();
        if(params['push_ahead'] == -1) //不提醒
        {
            params['if_push']=1;
        }
        else //提醒
        {
            params['if_push']=0;
        }

        //直播类型
        var aliveTypeIndex=$(".aliveBlue").index();
        params['alive_type']=aliveTypeIndex;

        //直播描述
        var ue = UE.getEditor('descrb');
        params['org_content']=ue.getContent();
        params['descrb']=ue.getPlainTxt();
        if(params['descrb'].length == 0)
        {
            baseUtils.show.redTip('亲,请输入直播描述哦~');
            return false;
        }

        //直播角色的值
        for(var i=0;i<$(".user_name").length;i++)
        {
            var tmp={};
            tmp["role_name"]=$(".center").eq(i).val().trim();
            tmp["user_name"]=$(".user_name").eq(i).html().trim();
            tmp["user_id"]=$(".user_id").eq(i).val().trim();
            if(tmp["user_id"].length!=0)
            {
                roleParams[i]=tmp;
            }
        }

        //首页封面是否上传
        if($("#alive_img_url").val().length == 0)
        {
            baseUtils.show.redTip('亲,请上传直播首页封面哦~');
            return false;
        }

        //详情封面是否上传
        if($("#img_url").val().length == 0)
        {
            baseUtils.show.redTip('亲,请上传直播详情封面哦~');
            return false;
        }

        //视频直播:视频文件是否上传
        if(aliveTypeIndex == 1)
        {
            if($("#videoName").html().length == 0)
            {
                baseUtils.show.redTip('亲,请上传直播视频哦~');
                return false;
            }
        }

        //  分类导航 - 所属分类 的判断
        category_type = [];

        if (! $(".upload_item_div").hasClass("hide")) {       //  显示所属分类时
            var i = 0;
            $(".checkBoxWrapper :checkbox:checked").each(function () {
                category_type[i++] = $(this).attr("value");

            });
        }

        //先执行：首页封面图上传
        uploadHomePic();
    });
});

//初始化
function init()
{
    aliveTimeConfig("#start_at");
    aliveTimeConfig("#zb_start_at");
    aliveTimeConfig("#zb_stop_at");
    $.cookie('content_create')? setTopUrlInfo('content_create') : setTopUrlInfo('alive_listop');
}

//首页封面处理
function homePicHandle()
{
    //回显图片
    $("#alive_img_url").change(function()
    {
        var src=getObjectURL(this.files[0]);
        $(".homePicClose").removeClass('hide');
        $(".homePicAdd").addClass('hide');
        $(".homePicShow").removeClass('hide');
        $(".homePicShow").attr('src',src);
    });

    //关闭图片
    $(".homePicClose").click(function()
    {
        $("#alive_img_url").val('');
        $(".homePicClose").addClass('hide');
        $(".homePicAdd").removeClass('hide');
        $(".homePicShow").addClass('hide');
        $(".homePicShow").attr("src","");
    });
}

//详情封面处理
function picHandle()
{
    //回显图片
    $("#img_url").change(function()
    {
        var src=getObjectURL(this.files[0]);
        $(".picClose").removeClass('hide');
        $(".picAdd").addClass('hide');
        $(".picShow").removeClass('hide');
        $(".picShow").attr('src',src);
    });

    //关闭图片
    $(".picClose").click(function()
    {
        $("#img_url").val('');
        $(".picClose").addClass('hide');
        $(".picAdd").removeClass('hide');
        $(".picShow").addClass('hide');
        $(".picShow").attr("src","");
    });
}

//收费形式的处理
function typeHandle()
{
    $(".typeSelect").click(function()
    {
        $(this).addClass('blue');
        $(this).siblings().removeClass('blue');
        var index=$(this).index();
        if(index == 2) //点击免费
        {
            $(".payShow").addClass('hide');
            $(".packageShow").addClass('hide');

            if(!($('.package_side_pay').hasClass('hide')))
            {

                $('.package_side_pay').addClass('hide');
            }

            //  处理所属分类
            $(".upload_item_div").removeClass("hide");
        }
        else if(index == 1) //点击单卖
        {
            $(".payShow").removeClass('hide');
            $(".packageShow").addClass('hide');

            if(!($('.package_side_pay').hasClass('hide')))
            {

                $('.package_side_pay').addClass('hide');
            }

            //  处理所属分类
            $(".upload_item_div").removeClass("hide");
        }
        else//专栏
        {
            $(".payShow").addClass('hide');
            $(".packageShow").removeClass('hide');
            $('.package_side_pay').removeClass('hide');

            //  处理所属分类
            var checkedState = $("#checkbox-img").attr("checked-state");
            if (checkedState == "checked") {                //  专栏外单卖
                $(".upload_item_div").removeClass("hide");
            } else {                                        //  只是专栏
                $(".upload_item_div").addClass("hide");
            }
        }


        isShowPush(); //判断是否能推送

    });
}

//直播类型的处理
function aliveTypeHandle()
{
    $(".aliveTypeSelect").click(function()
    {
        $(this).addClass('aliveBlue');
        $(this).siblings().removeClass('aliveBlue');
        var index=$(this).index();
        if(index == 0) //语音
        {
            $("#alive_video_url").addClass("hide");
            $(".aliveTypeArea").next().html("语音直播：讲师是可以发语音和文字和图片进行直播，不需要上传录制好的视频");
        }
        else//视频
        {
            $("#alive_video_url").removeClass("hide");
            $(".aliveTypeArea").next().html("视频直播：需要上传录制好的视频，直播形式为直播视频时间+互动答疑时间，互动答疑时间为语音直播形式");
        }
    });
}

//视频处理函数
function videoHandle()
{
    //删除按钮
    $(".deleteVideo").click(function()
    {
        $("#videoName").html('');
        $(".progress").addClass('hide');
        $(".deleteVideo").addClass('hide');
    });
}

//直播用户管理相关
function userHandle()
{
    //点击添加
    $(".user_name").click(function()
    {
        if($(this).html()==='点击添加')//文本为"点击添加"才让弹框
        {
            addIndex=($(".user_name").index($(this)));
            $("#zbModal").modal("show");
        }
    });

    //清空
    $(".clear").click(function()
    {
        var tempIndex=$(".clear").index($(this));
        $(".user_name").eq(tempIndex).html("点击添加");
        $(".user_id").eq(tempIndex).val('');
        $(".user_name").eq(tempIndex).css({"color":"#a3abba"});
        $(".center").eq(tempIndex).css({"color":"#a3abba"});
    });

    //添加一行,又要重新绑定部分方法
    $(".addLine").click(function()
    {
        var line_length=$(".user_name").length;
        $(".eachLine").eq(line_length-1).after('' +
        '<div class="eachLine">'+
            '<div class="user_name">点击添加</div>'+
            '<div class="roleNameArea">'+
                '<input type="text" class="form-control center" value="讲师" maxlength="16"/>'+
            '</div>'+
            '<div class="clear" data-userid="">清空</div>'+
            '<div class="shuxian">|</div>'+
            '<div class="delete">删除</div>'+
            '<input type="hidden" class="user_id" />'+
        '</div>');

        //点击添加
        $(".user_name").click(function()
        {
            if($(this).html()==='点击添加')
            {
                addIndex=($(".user_name").index($(this)));
                $("#zbModal").modal("show");
            }
        });

        //清空
        $(".clear").click(function()
        {
            var tempIndex=$(".clear").index($(this));
            $(".user_name").eq(tempIndex).html("点击添加");
            $(".user_id").eq(tempIndex).val('');
            $(".user_name").eq(tempIndex).css({"color":"#a3abba"});
            $(".center").eq(tempIndex).css({"color":"#a3abba"});
        });

        //删除
        $(".delete").click(function()
        {
            var tempIndex=$(".delete").index($(this));
            if(tempIndex>=0)
            {
                $(".eachLine").eq(tempIndex+3).remove();
            }
        });
    });
}

//Modal处理
function modalHandle()
{
    //搜索
    $("#searchButton").click(function()
    {
        //判空
        var search=$("#search").val();
        if(search.length==0)
        {
            baseUtils.show.redTip("请输入搜索内容");
            return false;
        }
        //搜素
        $.get("/zbsearch",{"search":search},function(data)
        {
            $(".modal-body").find("table").remove();
            if(data.data.length > 0)
            {
                //表头
                $(".modal-body").eq(0).append("" +
                "<table class='table table-hover' id='zbTable'>" +
                    "<thead><tr><th>选择</th><th>头像</th><th>昵称</th><th>性别</th><th>电话</th></tr></thead>"+
                    "<tbody></tbody>"
                +"</table>");
                //表体
                for(var i=0;i<data.data.length;i++)
                {
                    $("#zbTable").children("tbody").append("" +
                    "<tr>" +
                        "<td><input type='radio' name='eachZb' value="+data.data[i].user_id+" ></td>"+
                        "<td><img src="+data.data[i].wx_avatar+" /></td>"+
                        "<td>"+data.data[i].wx_nickname+"</td>"+
                        "<td>"+data.data[i].wx_gender+"</td>"+
                        "<td>"+data.data[i].phone+"</td>"+
                    "</tr>");
                }
            }
        });
    });

    //点击确定后
    $(".btn-blue").click(function()
    {
        //获取选择值
        var chosenUserId=$("input[type='radio']:checked").val();
        if(chosenUserId==undefined)
        {
            baseUtils.show.redTip("亲，请选择人员哦~");
            return false;
        }
        //判断人员是否重复
        var sameFlag=true;
        $(".user_id").each(function()
        {
            if($(this).val() == chosenUserId)
            {
                sameFlag=false;
            }
        });
        if(sameFlag == false)
        {
            baseUtils.show.redTip("亲，请勿重复选择人员哦~");
            return false;
        }

        var chosenUserName=$("input[type='radio']:checked").parent().nextAll().eq(1).html();
        $(".user_name").eq(addIndex).html(chosenUserName);
        $(".user_id").eq(addIndex).val(chosenUserId);
        $(".user_name").eq(addIndex).css({"color":"#000"});
        $(".center").eq(addIndex).css({"color":"#000"});
        $("#zbModal").modal("hide");
    });
}

//获取上传文件的url
function getObjectURL(file)
{
    var url = null;
    if (window.createObjectURL != undefined)
    {
        url = window.createObjectURL(file);
    }
    else if (window.URL != undefined)
    {
        url = window.URL.createObjectURL(file);
    }
    else if (window.webkitURL != undefined)
    {
        url = window.webkitURL.createObjectURL(file);
    }
    return url;
}

//获取文件的后缀名
function getSuffix(filename)
{
    var names = filename.split('.');
    return names[names.length - 1];
}

//上传首页封面
function uploadHomePic()
{
    showLoading();
    var homePic = $('#alive_img_url').prop('files');
    browserMD5File(homePic[0], function (err, md5)
    {
        var remotePath = get_cos_image_path() + md5 + "." + getSuffix(homePic[0]['name']);
        cos.uploadFileWithoutPro(homePicUploadSuccess, function (result)//失败回调
        {
            baseUtils.show.redTip("上传失败!");
        }, remotePath, homePic[0], 0);
    });
}

//首页封面上传成功,接着上传详情封面
homePicUploadSuccess=function(result)
{
    //params['alive_img_url'] = $.parseJSON(result).data.access_url;
    params['alive_img_url'] = result.data.access_url;
    uploadPic();
}

//上传详情封面
function uploadPic()
{
    var pic = $('#img_url').prop('files');
    browserMD5File(pic[0], function (err, md5)
    {
        var remotePath = get_cos_image_path() + md5 + "." + getSuffix(pic[0]['name']);
        cos.uploadFileWithoutPro(picUploadSuccess, function (result)//失败回调
        {
            baseUtils.show.redTip("上传失败!");
        }, remotePath, pic[0], 0);
    });
}

//详情封面上传成功,接着上传视频
picUploadSuccess=function(result)
{
    //params['img_url'] = $.parseJSON(result).data.access_url;
    params['img_url'] = result.data.access_url;
    if(params['alive_type'] == 0)//语音：直接插
    {
        sendRequest();
    }
    else //视频：再上传视频
    {
        uploadVideo();
    }
}

//发请求
function sendRequest()
{
    $.post("/doaddalive",{
        "params":params,
        "roleParams":roleParams,
        "is_single_sale":is_single_sale,
        "category_type":category_type
    },function(data)
    {
        hideLoading();
        if(data.ret==0)
        {
            baseUtils.show.blueTip('新增成功',function()
            {
                window.location.href='/alive';
            });
        }
        else
        {
            baseUtils.show.redTip('新增失败');
        }
    });
}



//初始化直播上传
function initUpload()
{
    //检测浏览器是否支持
    var $ = qcVideo.get('$');
    var Version = qcVideo.get('Version');
    if( !qcVideo.uploader.supportBrowser() )
    {
        if(Version.IS_MOBILE)
        {
            alert('当前浏览器不支持上传，请升级系统版本或者下载最新的chrome浏览器');
        }
        else
        {
            getInternetExplorerVersion();
            // alert('当前浏览器不支持上传，请升级浏览器或者下载最新的chrome浏览器');
        }
        return;
    }
    //绑定按钮及回调处理
    accountDone('alive_video_url',secretId,1,1,aliveTransUrl,null);
}

/**
 *
 * @param upBtnId 上传按钮ID
 * @param secretId 云api secretId
 * @param isTranscode 是否转码
 * @param isWatermark 是否设置水印
 * @param [transcodeNotifyUrl] 转码成功后的回调
 * @param [classId] 分类ID
 */
function accountDone(upBtnId,secretId, isTranscode, isWatermark,transcodeNotifyUrl,classId)
{
    var $ = qcVideo.get('$'),
    ErrorCode = qcVideo.get('ErrorCode'),
    Log = qcVideo.get('Log'),
    JSON = qcVideo.get('JSON'),
    util = qcVideo.get('util'),
    Code = qcVideo.get('Code'),
    Version = qcVideo.get('Version');

    qcVideo.uploader.init(
        {
            web_upload_url: 'https://vod.qcloud.com/v2/index.php',
            secretId: secretId, // 云api secretId

            getSignature: function (argStr, done)
            {//注意：出于安全考虑， 服务端接收argStr这个参数后，需要校验其中的Action参数是否为 "MultipartUploadVodFile",用来证明该参数标识上传请求
                $.ajax({
                    'dataType': 'json',
                    'url': '/getsig?args=' + encodeURIComponent(argStr),
                    'success': function (d)
                    {
                        done(d['result']);
                    }
                });
            },

            upBtnId: upBtnId, //上传按钮ID（任意页面元素ID）
            isTranscode: isTranscode,//是否转码
            isWatermark: isWatermark,//是否设置水印
            after_sha_start_upload: false,//sha计算完成后，开始上传 (默认关闭立即上传)
            sha1js_path: '/calculator_worker_sha1.js', //计算sha1的位置
            disable_multi_selection: false, //禁用多选 ，默认为false
            transcodeNotifyUrl: transcodeNotifyUrl + "?url_app_id=" + url_app_id,//(转码成功后的回调地址)isTranscode==true,时开启； 回调url的返回数据格式参考  http://www.qcloud.com/wiki/v2/MultipartUploadVodFile
            classId: classId,
            // mime_types, 默认是常用的视频和音频文件扩展名，如MP4, MKV, MP3等, video_only 默认为false，可允许音频文件上传
            filters: {max_file_size: '8gb', mime_types: [], video_only: true},
            //forceH5Worker: !!parseInt(getParameterByName('forceH5Worker')) || false,
            forceH5Worker: true
        }
        //2: 回调
        , {

            /**
             * 更新文件状态和进度 code:1、准备计算SHA 2、等待上传 3、SHA计算中 4、即将上传 5、上传进度更新 6、上传完成
             * @param args { id: 文件ID, size: 文件大小, name: 文件名称, status: 状态, percent: 进度 speed: 速度, errorCode: 错误码,serverFileId: 后端文件ID }
             */
            onFileUpdate: function (args)
            {
                if(args.code == 1 || args.code == 3)//计算SHA中
                {
                    console.log(args);
                    $("#videoName").html(args.name);
                    $(".progress").removeClass('hide');
                    $(".deleteVideo").removeClass('hide');
                }
                else if(args.code == 2) //计算完SHA
                {
                    shaFlag=true;
                }
                else if(args.code == 5 )//上传中
                {
                    console.log(args);
                    var percent=args.percent+'%';
                    $(".progress-bar").css({'width':percent});
                }
                else if(args.code == 6 )//上传完成
                {
                    console.log(args);
                    $(".progress-bar").css({'width':'100%'});
                    //取得回调的视频serverFileId，用于后面更新字段用
                    params['file_id']=args.serverFileId;
                    params['video_size']=args.size/(1024*1024);
                    sendRequest();
                }
            },

            /**
             * 文件状态发生变化，暂时不用
             * @param info  { done: 完成数量 , fail: 失败数量 , sha: 计算SHA或者等待计算SHA中的数量 , wait: 等待上传数量 , uploading: 上传中的数量 }
             */
            onFileStatus: function (info)
            {
                $('#count').text('各状态总数-->' + JSON.stringify(info));
            },

            /**
             *  上传时错误文件过滤提示,暂时不用
             * @param args {code:{-1: 文件类型异常,-2: 文件名异常} , message: 错误原因 ， solution: 解决方法}
             */
            onFilterError: function (args)
            {
                var msg = 'message:' + args.message + (args.solution ? (';solution==' + args.solution) : '');
                console.log(msg);
            }
        }
    );
}

//视频文件上传,大文件SHA值计算期间不能上传
function uploadVideo()
{
    setTimeout(function()
    {
        if(shaFlag == true)
        {
            qcVideo.uploader.startUpload();
        }
        else
        {
            uploadVideo();
        }
    },1000);
}


function isShowPush() { //判断是否显示消息推送选项

    var $alivePush = $('#courseRemind');
        selectType = $(".typeSelect.blue").index();
    if( selectType == 0 ) {
        var checkedState = $("#checkbox-img").attr("checked-state");

        if (checkedState == "unchecked") {
            var notFree = $('#packageInfo option:selected').attr('price')>0;

            console.log([$('#packageInfo option:selected').attr('price'),notFree]);
            if(notFree) {
                showPush();
            } else {
                hidePush();
            }
        } else { //专栏外单卖
            showPush();
        }
    } else if( selectType == 1 ) {
        showPush();
    } else {
        hidePush();
    }

    function showPush() {
        $('#push_ahead option').eq(curSelected).prop('selected', true);
        $alivePush.show();
    }

    function hidePush() {
        $alivePush.hide();
        $('#push_ahead option').eq(0).prop('selected', true);
    }
}



