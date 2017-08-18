/**
 * Created by Stuphin on 2016/9/26.
 */

$(document).ready(function()
{

    //新增页面
    $("#addAdminUser").click(function()
    {
        window.location.href="/addadminuser";
    });

    //编辑管理员账号
    $("#editPrimarySubmit").click(function()
    {
        var editName=$("#editName").val().trim();
        var editOldPassword=$("#editOldPassword").val().trim();
        if( editOldPassword.length == 0 )
        {
            baseUtils.show.redTip("亲,请输入旧的密码");
            return false;
        }
        var editNewPassword=$("#editNewPassword").val().trim();
        if( editNewPassword.length == 0 )
        {
            baseUtils.show.redTip("亲,请输入新的密码");
            return false;
        }
        if( editNewPassword != $("#editNewConfirm").val().trim() )
        {
            baseUtils.show.redTip("亲,两次密码输入不一致");
            return false;
        }
        $.post("/updateprimary",{"editName":editName,"editOldPassword":editOldPassword,
        "editNewPassword":editNewPassword},function(data)
        {
            if(data.ret == 0)
            {
                baseUtils.show.blueTip("修改成功",function()
                {
                    window.location.reload();
                });
            }
            else if(data.ret == 2)
            {
                baseUtils.show.redTip("旧密码不匹配");
            }
            else
            {
                baseUtils.show.redTip("更新失败");
            }
        });
    });

    //新增账号时检测账号唯一性
    $("#addName").keyup(function()
    {
        var addName=$("#addName").val().trim();
        if( addName.length == 0 )
        {
            return;
        }
        else
        {
            $.get("/checkusername",{"username":addName},function(data)
            {
                if(data.ret == 0)
                {
                    $(".checkImg").eq(0).removeClass("hide");
                    $(".checkImg").eq(1).addClass("hide");
                    $("#addPrimarySubmit").attr("disabled",false);
                }
                else
                {
                    $(".checkImg").eq(1).removeClass("hide");
                    $(".checkImg").eq(0).addClass("hide");
                    $("#addPrimarySubmit").attr("disabled",true);
                }
            });
        }
    });

    //新增管理员账号
    $("#addPrimarySubmit").click(function()
    {
        var addName=$("#addName").val().trim();
        if( addName.length == 0 )
        {
            baseUtils.show.redTip("亲,请输入账号哦");
            return false;
        }
        var addPassword=$("#addPassword").val().trim();
        if( addPassword.length == 0 )
        {
            baseUtils.show.redTip("亲,请输入密码哦");
            return false;
        }
        if( addPassword != $("#addConfirm").val().trim())
        {
            baseUtils.show.redTip("亲,两次密码不一致");
            return false;
        }
        $.post("/doaddprimary",{"addName":addName,"addPassword":addPassword},function(data)
        {
            if(data.ret==0)
            {
                baseUtils.show.blueTip("新增成功",function()
                {
                   window.location.reload();
                });
            }
            else
            {
                baseUtils.show.redTip("新增账户失败");
            }
        });
    });
});

//编辑
function editAdminUser(id)
{
    window.location.href="/editadminuser?id="+id;
}

//删除
function deleteAdminUser(id)
{
    $.get("/deleteadminuser",{"id":id},function(data)
    {
        if(data.ret==0)
        {
            baseUtils.show.blueTip("删除成功",function()
            {
                window.location.reload();
            });
        }
        else
        {
            baseUtils.show.redTip("删除失败");
        }
    });
}




