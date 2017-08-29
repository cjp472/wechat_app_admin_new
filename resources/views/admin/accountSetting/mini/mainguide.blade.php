<?php
$pageData = [];
$pageData['sideActive'] = 'account_admin';
$pageData['barTitle'] = '账户管理';

$tabData = ['tabTitle'=>'miniSetting', 'model'=>'company'];
?>
@extends('admin.baseLayout',$pageData)

@section('page_css')
    <link type=text/css rel="stylesheet" href="../css/admin/accountSetting/mini/mainguide.css?{{env('timestamp')}}"/>
@endsection


@section('page_js')
    <script type="text/javascript" src="../js/admin/accountSetting/mini/mainguide.js?{{env('timestamp')}}"></script>
@endsection

@section('base_mainContent')
    @include("admin.accountSetting.baseTab", $tabData)
    {{--公众号设置--}}
    <div class="content" >

        <div class="topBox">
            <a href="/mini/index">独立小程序</a>
            &gt;
            小程序配置
        </div>

        {{--dump($auth)--}}
        <input id="app_id" type="hidden" value="{{$app_id}}">
        <input id="auth" type="hidden" value="{{$auth}}">
        <div class="proj-main clearfix">
            {{-- <a href="/mini/agentRegister" class="bindNow">代理注册小程序1</a>
            <a href="/mini/guide" class="bindNow">手动配置小程序</a> --}}

            {{-- 代收小程序 --}}
            <div class="choose-box choose-box-left">
                <div class="hd-icon-wrap">
                    <img src="/images/admin/accountSetting/agent_register.png">
                </div>
                <div class="hd-title">
                    代理注册小程序
                </div>
                <div class="box-content">
                    <div class="">
                        同时您的服务号需要满足以下条件
                    </div>
                    <div class="box-content-item">
                        一、已认证的企业/媒体/政府/其他组织类型公众号
                    </div>
                    <div class="box-content-item">
                        二、需要授权微信开放平台管理权限
                    </div>
                    <div class="box-content-item">
                        三、尚未绑定任何其他开放平台
                    </div>
                </div>
                <div class="btn-wrap">
                    <button id="agentRegister" class="bindNow btnBlue">选择并配置</button>
                </div>
            </div>

            {{-- 手动配置小程序 --}}
            <div class="choose-box">
                <div class="hd-icon-wrap">
                    <img src="/images/admin/accountSetting/self_register.png">
                </div>
                <div class="hd-title">
                    手动配置小程序
                </div>
                <div class="box-content">
                    <div class="">
                        请您预先完成以下配置
                    </div>
                    <div class="box-content-item">
                        一、已创建微信开放平台账号
                    </div>
                    <div class="box-content-item">
                        二、已创建小程序账号
                    </div>
                    <div class="box-content-item">
                        三、已将服务号和小程序关联至微信开放平台
                    </div>
                </div>
                <div class="btn-wrap">
                    <a href="/mini/guide" id="selfRegister" class="bindNow btnBlue">选择并配置</a>
                </div>
            </div>

        </div>
    </div>
@stop

{{-- 选择小程序代注册协议弹框 --}}
<div id="agentRegisterBox" class="agent-register-box">
    <div id="agentMb" class="agent-box-mb"></div>
    <div class="agreement-box-main">
        <div class="agreement-box">
            <div class="agreement-title">
                服务协议
            </div>
            <div class="agreement-cnt">
                一、提醒条款</br>
                充分阅读 小鹅通是由 深圳小鹅网络技术有限公司（以下称 “ 小鹅公司 ” ）开发、 运营的一款专注于自媒体知识服务与社群运营的软件工具，小鹅公司 同意按照本协议的规定及其不时发布的操作规 则提供接入服务（以下称 “ 服 务 ” ）， 您 （包括“企业模式商户 ” 、 “ 个人模式商户 ” ，以下统称为“商户”） 为获得小鹅通的服务 ， 应当充分、审慎阅读小鹅通官方网站上“立即体验”栏目下 “ 帮助中心 ” （ https://admin.xiaoe-tech.com/help ）中的内容及本协议各条款的内容，特别是限制或免除责任的条款，在点击同意本协议之前， 请您务必仔细审查、充分理解协议的每一条款， 如您对协议有任何疑问，可向小鹅通的客服咨询。
                协议的接受及订立 如发生以 下 A 、 B 情形中的任一情形，均表示您接受了本协议的全部内容及小鹅通的服务内容及服务模式，成为受本协议制约的小鹅通用户： A. 您按照小鹅通注册页面的提示填写信息、阅读并点击 “ 我已阅读并同意《小鹅通服务协议》 ” 选项框完成注册程序后，即表示您已充分阅读、理解并接受本协议的全部内容及小鹅通的服务内容及服务模式； B. 如果您在小鹅通网站、小鹅通提供的移动应用或软件上使用小鹅通的服务，便视为您接受了本协议的全部内容及小鹅通的服务内容及服务模式。您在阅读本协议的过程中，如果不同意本协议任何内容或不能接受小鹅通的服务内容、服务模式，您应立即停止注册程序、停止使用小鹅通。您有违反本协议的任何行为时，小鹅公司有权依照违反情况，随时单方限制、中止或终止向您提供本服务，并有权追究您的相关责任。
                协议的变更 您同意，小鹅公司有权随时对本协议内容进行单方面的变更，并以在小鹅通网站公告的方式予以公布，无需另行单独通知您，变更后的协议条款自其公布之日起立即生效；若您在本协议内容公告变更后继续使用本服务的，表示您已充分阅读、理解并接受变更后的协议内容，也将遵循变更后的协议内容使用小鹅通服务；若您不同意变更后的协议内容，您应停止使用本服务。小鹅通及您的一切行为、 争议应依据具体行为、争议发生时最新生效版本的《小鹅通服务协议》作出并进行解释。
                商户账号及密码的保管 您在小鹅通注册成功后，您的账户名称和密码由您自行负责保管，您应当对以该账号进行的所有活动和事件负法律责任。
                具有完全民事行为能力 您声明，您同意接受本协议并注册开通小鹅通商户时，您是具有法律规定的完全民事权利能力和民事行为能力，能够独立承担民事责任的自然人、法人或其他组织，不具备前述条件的，您应立即终止注册或停止使用本服务。
            </div>
        </div>
        <div class="checkbox-wrap">
            <div class="checkbox-group">
                <input class="checkbox-default" id="agreementCheckbox" name="group1" type="checkbox">
                <label id="agree_agreement" for="agreementCheckbox" class="columnShow">
                    我已阅读并同意此协议
                </label>
            </div>
        </div>
        <div class="agreement-button-wrap">
            <button id="agentSubmitBtn" class="bindNow btnBlue" type="button" disabled="disabled">下一步</button>
        </div>

    </div>
</div>



{{-- 微信开放平台授权状态弹框 --}}
<div class="modal fade" id="bindModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 480px;height: 198px;border-radius: 10px;margin: 0 auto;margin-top: 200px;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">提示</h4>
            </div>

            <div class="modal-body" style="text-align:center;height: 70px;line-height: 40px;">
                请在新窗口中完成微信公众号授权！<a target="_blank" href="/help#hp2">查看授权教程</a>
            </div>

            <div class="modal-footer">
                <button type="button" id="bindSuccess" class="bindSuccess">授权成功</button>
                <button type="button" id="bindFail" class="bindFail">授权失败，重试</button>
            </div>
        </div>
    </div>
</div>

{{-- 小程序资质授权状态弹框 --}}
<div class="modal fade" id="bindProjModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 480px;height: 198px;border-radius: 10px;margin: 0 auto;margin-top: 200px;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">提示</h4>
            </div>

            <div class="modal-body" style="text-align:center;height: 70px;line-height: 40px;">
                请在新窗口中完成微信服务号复用认证资质授权！<a target="_blank" href="/help#hp2">查看授权教程</a>
            </div>

            <div class="modal-footer">
                <button type="button" id="bindProjSuccess" class="bindSuccess">授权成功</button>
                <button type="button" id="bindProjFail" class="bindFail">授权失败，重试</button>
            </div>
        </div>
    </div>
</div>
