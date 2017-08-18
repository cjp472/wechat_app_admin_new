/**
 * Created by Stuphin on 2016/12/16.
 */
var params={};
$(document).ready(function()
{
    relate();

    //保存修改
    $("#finish").click(function()
    {
        //id
        params['id']=getUrlParam("id");

        //角色名
        params['role_name']=$("#role_name").val();
        if(params['role_name'].length==0)
        {
            baseUtils.show.redTip("亲,请输入角色名哦");
            return false;
        }

        //账号
        params['username']=$("#username").val();
        if(params['username'].length==0)
        {
            baseUtils.show.redTip("亲,请输入账号哦");
            return false;
        }

        //密码
        params['password']=$("#password").val();
        if(params['password'].length < 6)
        {
            baseUtils.show.redTip("亲,密码至少为6位哦");
            return false;
        }

        //两次输入密码不一致
        if( params['password'] != $("#passwordConfirm").val())
        {
            baseUtils.show.redTip("亲,密码输入不一致哦");
            return false;
        }

        // params['adder']=$("#adder").val();
        // if(params['adder'].length==0)
        // {
        //     baseUtils.show.redTip("亲,请输入添加人哦");
        //     return false;
        // }

        //没选择权限
        if($("input:checkbox:checked").length==0)
        {
            baseUtils.show.redTip("亲,请至少选择一项权限哦");
            return false;
        }

        //仪表盘
        if($("#dashboard_admin_access").is(":checked"))
        {
            params['dashboard_admin']=1;
        }
        else
        {
            params['dashboard_admin']=0;
        }

        //内容创建
        if($("#create_content_access").is(":checked"))
        {
            params['create_content']=1;
        }
        else
        {
            params['create_content']=0;
        }

        //内容列表
        if($("#content_list_access").is(":checked"))
        {
            params['content_list']=1;
        }
        else
        {
            params['content_list']=0;
        }

        //轮播图
        if($("#banner_access").is(":checked"))
        {
            params['banner']=1;
        }
        else
        {
            params['banner']=0;
        }

        //内容评论
        if($("#content_comment_access").is(":checked"))
        {
            params['content_comment']=1;
        }
        else
        {
            params['content_comment']=0;
        }

        //用户列表
        if($("#user_list_access").is(":checked"))
        {
            params['user_list']=1;
        }
        else
        {
            params['user_list']=0;
        }

        //消息列表
        if($("#message_admin_access").is(":checked"))
        {
            params['message_admin']=1;
        }
        else
        {
            params['message_admin']=0;
        }

        //反馈列表
        if($("#feedback_admin_access").is(":checked"))
        {
            params['feedback_admin']=1;
        }
        else
        {
            params['feedback_admin']=0;
        }

        //渠道分发
        if($("#channel_admin_access").is(":checked"))
        {
            params['channel_admin']=1;
        }
        else
        {
            params['channel_admin']=0;
        }

        //邀请码
        if($("#invitecode_admin_access").is(":checked"))
        {
            params['invitecode_admin']=1;
        }
        else
        {
            params['invitecode_admin']=0;
        }

        //财务管理
        if($("#money_admin_access").is(":checked"))
        {
            params['money_admin']=1;
        }
        else
        {
            params['money_admin']=0;
        }

        //账号管理
        if($("#account_admin_access").is(":checked"))
        {
            params['account_admin']=1;
        }
        else
        {
            params['account_admin']=0;
        }

        $.post("/updateadminuser",{"params":params},function(data)
        {
            if(data.ret==0)
            {
                baseUtils.show.blueTip("修改成功",function()
                {
                    window.location.href="/accountmanage";
                });
            }
            else
            {
                baseUtils.show.redTip("修改失败");
            }
        });

    });
});



//联动
function relate()
{
    //仪表盘
    $("#dashMenu").change(function()
    {
        if ($("#dashMenu").is(':checked'))
        {
            $("#dashboard_admin_access").attr("checked", true);
        }
        else
        {
            $("#dashboard_admin_access").attr("checked", false);
        }
    });

    //内容管理
    $("#contentMenu").change(function()
    {
        if ($("#contentMenu").is(':checked'))
        {
            $("#create_content_access").attr("checked", true);
            $("#content_list_access").attr("checked", true);
            $("#banner_access").attr("checked", true);
        }
        else
        {
            $("#create_content_access").attr("checked", false);
            $("#content_list_access").attr("checked", false);
            $("#banner_access").attr("checked", false);
        }
    });

    //用户管理
    $("#userMenu").change(function()
    {
        if ($("#userMenu").is(':checked'))
        {
            $("#content_comment_access").attr("checked", true);
            $("#user_list_access").attr("checked", true);
            $("#message_admin_access").attr("checked", true);
            $("#feedback_admin_access").attr("checked", true);
        }
        else
        {
            $("#content_comment_access").attr("checked", false);
            $("#user_list_access").attr("checked", false);
            $("#message_admin_access").attr("checked", false);
            $("#feedback_admin_access").attr("checked", false);
        }
    });

    //收入管理
    $("#incomeMenu").change(function()
    {
        if ($("#incomeMenu").is(':checked'))
        {
            $("#channel_admin_access").attr("checked", true);
            $("#invitecode_admin_access").attr("checked", true);
            $("#money_admin_access").attr("checked", true);
        }
        else
        {
            $("#channel_admin_access").attr("checked", false);
            $("#invitecode_admin_access").attr("checked", false);
            $("#money_admin_access").attr("checked", false);
        }
    });

    //账号管理
    $("#accountMenu").change(function()
    {
        if ($("#accountMenu").is(':checked'))
        {
            $("#account_admin_access").attr("checked", true);
        }
        else
        {
            $("#account_admin_access").attr("checked",false);
        }
    });
}
