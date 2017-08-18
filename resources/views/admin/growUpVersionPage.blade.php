
<html>
<head>
    <meta charset="utf-8">
    <title>小鹅通，专注于知识服务与社群运营的聚合型工具</title>
    <link rel='icon' href='logo-64.ico' type='image/x-ico' />
    <link type="text/css" rel="stylesheet" href="../css/admin/growupVersionPage.css?{{env('timestamp')}}">
    <script src="../js/external/jquery-1.11.3.js"></script>
    {{--生成二维码--}}
    <script type="text/javascript" src="../js/external/qrcode.js?{{env('timestamp')}}"></script>

    {{--公共支付js--}}
    <script type="text/javascript" src="../js/utils/WeXinPay.js?{{env('timestamp')}}"></script>


</head>
    <body>
    <input id="version_type_c"  type="hidden" data-version_type="{{session('version_type')}}">
    <div class="header">
        <div class="header-pic">
            <img src="images/xiaoeTC_logo.png" alt="小鹅通图标">
        </div>
        <div class="user">
            <div class="user_logo button_click"><img src="@if(session('wx_share_image')){{ session('wx_share_image') }}@else{{ session('avatar') }}@endif" /></div>
            <div class="user_name button_click">@if(session('wx_app_name')){{ session('wx_app_name') }}@else{{ session('nick_name') }}@endif</div>
        </div>
        <div class="return_btn button_click" onclick="window.location.href='/accountview'">返回管理台</div>
    </div>
    <div class="content">
        <div class="content_part1">
            <div class="content_pic"><img src="images/pic_price_chenzhang.svg"></div>
            <div class="content_intro">升级为小鹅通成长版用户，您将获得会员模式、首页分类导航、试听分享、赠送好友等众多强大功能</div>
        </div>
        <div class="content_part2">
            <div style="width:192px;height: 45px;line-height: 45px;margin-left: 30px;margin-top: 58px;font-size: 32px;color: #2bade0">小鹅通成长版</div>
            <div style="width:96px;height: 22px;line-height: 22px;margin-left: 30px;margin-top: 4px;font-size: 14px;color: #999999;cursor: pointer;" onclick="window.location.href='/upgrade_account'">查看所有类型</div>
            <div class="content_charge_num">¥100.00<div class="content_charge_num_t"><img src="images/payment-tick.png"></div></div>
            <div class="charge_intro">付款后该笔钱将直接充到您的流量账户余额中，可用来抵扣小鹅通平台的营运费用</div>
            <!-- 服务协议部分 -->
<!--             <input type="checkbox" id="checkFlag" checked="checked"/> -->
                <span class="agree">点击微信支付即代表您已阅读并同意
                    <span style="color: #00a0e9;cursor: pointer;">《小鹅通服务协议》</span>
                </span>
                <!-- 协议内容 -->
            <div class="seperate"></div>
            <div class="agreeModal">
            <h3>小鹅通服务协议</h3>
            <div class="agreeModalContent">
                <h4 style="margin-bottom: 5px">欢迎您使用小鹅通微信接入服务！</h4>
                <p>
                为使用小鹅通微信接入服务（可简称“本服务”），您应当阅读并遵守《小鹅通服务协议》（以下简称:本协议）。请您务必审慎阅读、充分理解各条款内容，特别是限制或免除责任的条款，以及开通或使用某项服务的单独协议、规则。
                除非您已阅读并接受本协议及相关协议、规则等的所有条款，否则，您无权使用本服务。一旦您选择“同意协议并提交”，或您以任何方式使用本服务，即视为您已阅读并同意上述协议、规则等的约束。
                </p>

                <h4 style="margin-bottom: 5px;margin-top: 5px;">声明与承诺</h4>
                <p>一、您确认，在您申请开通小鹅通商户的微信支付个人版和提现服务之前，您已充分阅读、理解并接受本协议的全部内容，一旦您使用本服务，即表示您同意遵循本协议的所有约定。</p>
                <p>二、您同意，小鹅通有权随时对本协议内容进行单方面的变更，并以在小鹅通网站公告的方式予以公布，无需另行单独通知您；若您在本协议内容公告变更后继续使用本服务的，表示您已充分阅读、理解并接受修改后的协议内容，也将遵循修改后的协议内容使用本服务；若您不同意修改后的协议内容，您应停止使用本服务。</p>
                <p>三、您声明，在您同意接受本协议并注册开通小鹅通商户时，您是具有法律规定的完全民事权利能力和民事行为能力，能够独立承担民事责任的自然人、法人或其他组织；本协议内容不受您所属国家或地区的排斥。不具备前述条件的，您应立即终止注册或停止使用本服务。</p>

                <h4 style="margin-bottom: 5px;margin-top: 5px;">小鹅通商户账户</h4>
                <h5>一、注册相关</h5>
                <p>
                    1、在您注册小鹅通商户账户时，您需提供手机号码，并正确填写验证码及相关信息，方能成功注册小鹅通商户。
                </p>
                <p>
                    2、您注册完成，取得小鹅通提供给您的“小鹅通商户账户”（以下简称该账户）并接受小鹅通微信支付个人版和提现服务后，方可使用本服务。且使用本服务时，您同意：
                </p>
                <p>
                    （1）、 依本服务注册表之提示准确提供并在取得该账户后及时更新正确、最新及完整的资料。一旦小鹅通发现您提供的资料错误、不实、过时或不完整的，小鹅通有权暂停或终止向您提供部分或全部“小鹅通服务”，由此产生的任何直接或间接费用由您自行承担，小鹅通对此不承担任何责任。
                </p>
                <p>
                    （2）、 因您未及时更新资料，导致本服务不能提供或提供时发生任何错误的，您承担因此产生的一切后果，小鹅通不承担任何责任。
                </p>
                <p>
                    （3）、 您应对您的小鹅通商户账户负责，只有您或您指定的管理员可以使用您的小鹅通商户账号。在您决定不再使用该账户时，您应将该账户下所对应的可用款项全部提现，并向小鹅通申请删除该账户。您同意，若您丧失全部或部分民事权利能力或民事行为能力，小鹅通有权根据有效法律文书（包括但不限于生效的法院判决、生效的遗嘱等）处置您的小鹅通商户账户相关的款项。
                </p>

                <h5>二、账户安全</h5>
                <p>您将对使用该账户及密码进行的一切操作及言论负完全的责任，因此您同意：</p>
                <p>1、不向其他任何人泄露该账户及密码，亦不使用其他任何人的“小鹅通商户账户”及密码。</p>
                <p>2、账户下其他管理员的操作，视为您授权其进行管理。</p>
                <p>3、如您发现有他人冒用或盗用您的账户及密码或任何其他未经合法授权之情形时，应立即修改密码并妥善保管，或立即以有效方式（包括但不限于电话、邮件等方式）通知小鹅通，要求小鹅通暂停相关服务。如要求小鹅通暂停相关服务的，小鹅通将根据您的情况，暂停提供相关服务。但是，在小鹅通对您的请求采取行动所需的合理期限内，小鹅通对已执行的指令及(或)所导致的您的损失不承担任何责任。</p>
                <p>4、因黑客行为或您的保管疏忽导致账号非法使用，小鹅通概不承担任何责任。</p>

                <h5>三、服务费用</h5>
                <p>小鹅通微信接入服务结算方式、计费标准以小鹅网络技术官网公布价格为准，以人民币计算，双方另有约定的除外。 本服务的结算规则可能分为预付费和后付费等类型，您应当遵守您选购的服务的结算规则，否则，会导致您开通的服务被中断、终止。采用预付费规则的服务，您需及时向账户充值，以保证顺利使用服务。采用后付费规则的服务，您需在服务规则指定的期限内及时支付费用。 您可以通过您的小鹅通帐号直接在线付款购买服务，也可以通过对公账户向小鹅网络技术公司以下指定账户支付服务费： 户 名：深圳小鹅网络技术有限公司，账 户：755925097410902，开户行：招商银行深圳科技园支行，汇款备注：小程序服务费。</p>
                <h5 style="margin-bottom: 5px;margin-top: 5px;">小鹅通微信支付个人版和提现服务概要</h5>
                <p>一、小鹅通商户账户：指在您使用本服务时，小鹅通向您提供的商户唯一编号。您可自行设置密码，发布内容商品，并用以查询或计算您的货款。</p>
                <p>二、小鹅通微信支付个人版服务：是指买卖双方使用本系统，且交易款由买方通过本系统集成的第三方支付网关以电子货币的方式支付到小鹅通第三方支付网关的账户，小鹅通第三方支付网关的账户在收到该款项后将交易货款记录到您的商户账户记录中，但实际由小鹅通代为收取该款项的一种服务。在您使用本服务时，除适用小鹅通微信支付个人版服务的相关约定外，还将优先适用以下条款：</p>
                <p>1、小鹅通为您微信支付个人版的交易货款系由您的交易对方通过第三方支付网关以电子货币付款的方式支付至小鹅通的第三方支付网关账户，通过小鹅通系统记录到您的小鹅通商户账户记录内。您理解并同意，在您的交易对方通过第三方支付网关将电子货币支付至小鹅通第三方支付网关账户的过程需要一定的时间，在第三方支付网关告知小鹅通已收到您的交易对方支付的交易货款后，小鹅通将向您的小鹅通商户账户记录该笔交易货款。</p>
                <p>2、小鹅通为您通过微信支付个人版产生的交易货款系由买家通过第三方支付网关以电子货币付款的方式支付至小鹅通第三方支付网关账户，第三方支付网关会因此向您单独收取费用，您理解并同意，该费用是第三方支付网关基于其向您提供的支付服务所收取的费用，与小鹅通向您提供的本项服务无关。</p>
                <p>三、小鹅通代收服务：即小鹅通向您提供货款代收的服务，其中包含：</p>
                <p>1、货款提现：您可以要求小鹅通向您支付自己的货款。当您向小鹅通做出提现指示时，必须提供一个您指定的有效微信账户，小鹅通将于收到指示后的五个工作日内，将相应的款项汇入您提供的有效微信账户。除本条约定外，小鹅通不提供其它提现方式。</p>
                <p>2、 系统查询：小鹅通将对您在本系统中的所有操作进行记录，不论该操作之目的最终是否实现。您可以在本系统中实时查询其小鹅通商户账户名下的提现记录，若您认为记录有误的，您可向小鹅通提出异议，小鹅通将向您提供小鹅通按照您的指示操作产生的提现记录。并且您认同此记录为您交易记录的最终依据，不再对此有异议。另外，您理解并同意您最终收到款项的服务是由您提供的微信账户对应的服务方提供的，您需向该服务方请求查证。</p>
                <p>3、 款项专属：对通过您小鹅通商户账户收到的货款，小鹅通将予以妥善保管，除本协议另行规定外，不作任何其它非您指示的用途。小鹅通通过您的用户名和密码识别您的指示，请您妥善保管您的用户名和密码，对于因密码泄露所致的损失，由您自行承担。本服务所涉及到的任何款项只以人民币计结，不提供任何形式的外币兑换业务。</p>
                <p>4、 异常交易处理：您使用本服务时，可能由于微信本身系统问题或其他不可抗拒因素，造成暂时无法提供本服务。</p>
                <p>四、根据我国法律及税收政策，平台所有用户有义务就本人在平台上的劳务报酬所得按时、足额的缴纳个人所得税。因《个人所得税代扣代缴暂行办法》已于2016年5月29日全文失效，且替代性政策文件尚未出台，故小鹅通暂时采取用户个人自行申报并缴纳税款的形式执行。小鹅通将随时根据税务部门的最新政策或指导意见，即时调整用户个人所得税税款缴纳问题的执行方式。</p>
                <p>用户在此确认如下，小鹅通保有如下所示单项权利：若相关税务部门要求小鹅通提供用户账户信息以核实用户自行申报的在平台收入具体数额，平台有权不经用户同意而进行披露，并根据国家法律配合相关税务部门的其他合法要求。</p>

                <h5 style="margin-top: 5px;margin-bottom: 5px;">小鹅通微信支付个人版和提现服务使用规则</h5>
                <p>为有效保障您使用本服务的合法权益，您理解并同意接受以下规则：</p>
                <p>一、一旦您使用本服务，您即允许小鹅通代理您及（或）您的公司在您及（或）您指定的人符合指定条件或状态时，提现给您及（或）您指定的人。</p>
                <p>二、小鹅通可通过以下方式接受来自您提现的指令：您在小鹅通网站上依照本服务预设流程申请提现。您通过以上方式向小鹅通发出的指令，是不可撤回或撤销的，且成为小鹅通代理您提现的唯一指令。</p>
                <p>三、您同意在您与第三方发生交易纠纷时，小鹅通无需征得您的同意，有权自行判断并决定将争议货款的全部或部分结算给交易一方或双方。</p>
                <p>四、您在使用本服务过程中，本协议内容、网页上出现的关于提现操作的提示或小鹅通发送到其手机的信息（短信或电话等）内容是您使用本服务的相关规则，您使用本服务即表示您同意接受本服务的相关规则。小鹅通无须征得您的同意，有权单方修改本服务的相关规则，修改后的服务规则应以您使用服务时的页面提示（或发送到该手机的短信或电话等）为准。</p>
                <p>五、小鹅通会以短信（或电子邮件等）方式通知您提现进展情况，但小鹅通不保证您能够收到或者及时收到该短信（或电子邮件等），且不对此承担任何后果。</p>
                <p>六、小鹅通会将与您小鹅通商户账户相关的资金，独立于小鹅通营运资金之外，且不会将该资金用于非您指示的用途，但本条第（九）项约定的除外。</p>
                <p>七、小鹅通并非银行或其它金融机构，本服务也非金融业务，本协议项下的资金移转均通过微信来实现，你理解并同意您的资金于流转途中的合理时间。</p>
                <p>八、您完全承担您使用本服务期间由小鹅通保管的款项的货币贬值风险及可能的孳息损失。</p>
                <p>九、您同意，基于运行和交易安全的需要，小鹅通可以暂时停止提供或者限制本服务部分功能，或提供新的功能，在任何功能减少、增加或者变化时，只要您仍然使用本服务，表示您仍然同意本协议或者变更后的协议。</p>
                <p>十、您不得将本服务用于非小鹅通许可的其他用途。</p>
                <p>十一、当您通过本服务进行各项交易或接受交易款项时，若您或交易对方未遵从本服务条款或网站说明、交易页面中之操作步骤，或您或交易对方存在欺诈等违反法律法规或小鹅通禁止的行为时，小鹅通有权拒绝为您与交易对方提供相关服务，或要求您、交易对方配合小鹅通处理，且小鹅通不承担损害赔偿责任。若发生上述状况，而款项已先行划付至您的小鹅通商户余额中或您绑定的微信账户名下的，您同意小鹅通有权直接冻结您的商户余额或自您商户余额中扣回款项，并且您不享有要求小鹅通支付此笔款项之权利。此款项若已汇入您的微信账户，您同意小鹅通有向您事后索回之权利，因您的原因导致小鹅通事后追索的，您应当承担小鹅通合理的追索费用。</p>
                <p>十二、在您申请提现时，小鹅通有权按照第三方支付网关转账规定，扣除相关手续费用（该手续费为订单交易金额的0.6%）。 小鹅通有权对小鹅通的收费进行调整，具体的收费方案以您使用本服务时小鹅通网站上所列之收费公告或您与小鹅通达成的其他书面协议为准；若在收费调整后您继续使用本服务的，表示您已完全知晓并接受小鹅通调整后的收费方案，也将遵循调整后的收费方案支付费用；若您不同意调整后的收费方案，您应停止使用本服务。 除非另有说明或约定，您同意小鹅通有权自您委托小鹅通个人版的款项中直接扣除上述手续费用。</p>

                <h5 style="margin-top: 5px;margin-bottom: 5px;">小鹅通个人版和提现服务使用限制</h5>
                <p>一、您在使用本服务时应遵守中华人民共和国相关法律法规，以及您所在国家或地区之法令及相关国际惯例，不将本服务用于任何非法目的（包括用于禁止或限制交易商品的交易），也不以任何非法方式使用本服务。</p>
                <p>二、您同意将不会利用本服务进行任何违法或不正当的活动，如有此类行为小鹅通有权直接做删除内容、商品下架等处理，并且小鹅通对此类行为不承担任何责任，由您自行承担由此引起的一切责任。若有导致小鹅通或小鹅通雇员受损的，您亦应对此承担赔偿责任。此类行为包括但不限于下列行为∶</p>
                <p>1、侵害他人名誉权、隐私权、商业秘密、商标权、著作权等合法权益；</p>
                <p>2、违反依法定或约定之保密义务；</p>
                <p>3、冒用他人名义使用本服务；</p>
                <p>4、从事不法交易行为，如传播黄色淫秽内容及其它小鹅通认为不得使用本服务进行交易的内容商品等。</p>
                <p>5、提供赌博资讯或以任何方式引诱他人参与赌博。</p>
                <p>6、进行与您或交易对方宣称的交易内容不符的交易，或不真实的交易。</p>
                <p>7、从事任何可能含有电脑病毒或是可能侵害本服务系统、资料之行为。</p>
                <p>8、含有中国法律、法规、规章、条例以及任何具有法律效力之规范所限制或禁止的其它内容的</p>
                <p>9、其它小鹅通有正当理由认为不适当之行为。</p>

                <h4 style="margin-bottom: 5px;margin-top: 5px;">违约责任</h4>
                <p>一、因您的过错导致的任何损失由您自行承担，该过错包括但不限于：不按照交易提示操作，遗忘或泄漏密码，密码被他人破解，您使用的计算机被他人侵入。</p>
                <p>二、因您未及时更新资料，导致本服务不能提供或提供时发生任何错误，您须自行承担因此产生的一切后果，小鹅通不承担任何责任。</p>
                <p>三、如小鹅通发现您存在欺诈、套现等违反法律、法规规定、本协议或相关服务条款或存在小鹅通认为不适当的行为，小鹅通有权根据情节严重程度，对您处以警告、限制或禁止使用部分或全部功能、封禁您商户账户等处罚；由此导致或产生第三方主张的任何索赔、要求或损失，须由您自行承担一切损失，与小鹅通无关；如小鹅通因此也遭受损失的，您也应当一并赔偿。</p>
                <p>四、即使本服务终止，您仍应对您使用本服务期间的一切行为承担可能的违约或损害赔偿责任。</p>


                <h4 style="margin-bottom: 5px;margin-top: 5px;">免责声明</h4>
                <p>一、您确保所输入的您的资料无误，如果因资料错误造成小鹅通于异常交易发生时，无法及时通知您相关交易后续处理方式的，小鹅通不承担任何损害赔偿责任。</p>
                <p>二、您理解并同意，小鹅通不对因下述任一情况导致的任何损害赔偿承担责任，包括但不限于利润、商誉、使用、数据等方面的损失或其他无形损失的损害赔偿：</p>
                <p>1、小鹅通有权基于单方判断，包含但不限于小鹅通认为您已经违反本协议的明文规定及精神，暂停、中断或终止向您提供本服务或其任何部分，并移除您的资料。</p>
                <p>2、小鹅通在发现交易异常或您有违反法律规定或本协议约定的行为时，有权不经通知先行暂停或终止该账户的使用（包括但不限于对该账户名下的款项进行调账等限制措施），并拒绝您使用本服务之部分或全部功能。</p>
                <p>3、在必要时，小鹅通无需事先通知即可终止提供本服务，并暂停、关闭或删除该账户及您账号中所有相关资料及档案，并将您滞留在该账户的全部合法资金退回到您的微信账户。</p>
                <p>三、如因小鹅通根据本协议声明与承诺中的第二条对本协议内容进行单方面变更，您不同意变更后的协议内容停止使用本服务，双方终止合作的，小鹅通不承担任何损害赔偿责任。</p>
                <p>四、系统中断或故障：系统因下列状况无法正常运作，导致您无法使用各项服务的，小鹅通不承担损害赔偿责任，该状况包括但不限于：</p>
                <p>1、小鹅通在小鹅通网站公告之系统停机维护期间。</p>
                <p>2、电信设备出现故障不能进行数据传输的。</p>
                <p>3、因台风、地震、海啸、洪水、停电、战争、恐怖袭击等不可抗力之因素，造成小鹅通系统障碍不能执行业务的。</p>
                <p>4、由于黑客攻击、电信部门技术调整或故障、网站升级、微信方面的问题等原因而造成的服务中断或者延迟。</p>
                <p>五、对下列情形，小鹅通不承担任何责任：</p>
                <p>1、并非由于小鹅通的故意而导致本服务未能提供的。</p>
                <p>2、由于您的故意或过失导致您及/或任何第三方遭受损失的。</p>


                <h4 style="margin-bottom: 5px;margin-top: 5px;">终止服务</h4>
                <p>一、如您需要删除自己的小鹅通账户的，应先向小鹅通申请删除，经小鹅通审核同意后方可删除小鹅通账户。小鹅通同意删除该账户的，即表明小鹅通与您之间的协议解除，但您仍应对使用本服务期间的行为承担违约或损害赔偿责任。</p>
                <p>二、如果小鹅通发现或收到他人举报或投诉您违反本协议约定的，小鹅通有权不经通知随时对相关内容进行删除、屏蔽，并视行为情节对您处以包括但不限于警告、限制或禁止使用部分或全部功能、封禁直至删除商户账号的处罚。</p>
                <p>三、在下列情况下，小鹅通可以通过封禁商户或删除您账户的方式终止服务：</p>
                <p>1、因您违反本服务协议相关规定，被小鹅通终止提供服务的，后您再一次直接或间接或以他人名义注册为小鹅通用户的，小鹅通有权再次单方面终止向您提供服务；</p>
                <p>2、一旦小鹅通发现您注册数据中主要内容（身份信息、联系方式等）是虚假的，小鹅通有权随时终止向您提供服务；</p>
                <p>3、本服务协议更新时，您明示不愿接受新的服务协议的；</p>
                <p>4、您存在本协议其他条款约定的小鹅通终止向您提供本服务的情形的；</p>
                <p>5、其它小鹅通认为需终止服务的情况。</p>
                <p>四、服务中断、终止之前您交易行为的处理：因您违反法律法规或者违反本服务协议规定而致使小鹅通中断、终止对您提供服务的，对于服务中断、终止之前您的交易行为依下列原则处理：</p>
                <p>1、服务中断、终止之前，您已经上传至小鹅通的内容商品，小鹅通有权在中断、终止服务的同时删除此商品的相关信息。</p>
                <p>2、服务中断、终止之前，您已经在小鹅通平台产生的交易，小鹅通可以不删除交易记录，但小鹅通有权在中断、终止服务的同时将您被中断或终止服务的情况通知您的交易对方。</p>



                <h4 style="margin-bottom: 5px;margin-top: 5px;">商标、知识产权的保护</h4>
                <p>一、本产品及相关网站上所有内容，包括但不限于著作、图片、档案、资讯、资料、网站架构、网站画面的安排、网页设计，均由本公司或本公司关联企业依法拥有其知识产权，包括但不限于商标权、专利权、著作权、商业秘密等。</p>
                <p>二、非经本公司或本公司关联企业书面同意，任何人不得擅自使用、修改、复制、公开传播、改变、散布、发行或公开发表本网站程序或内容。</p>
                <p>三、尊重知识产权是您应尽的义务，如有违反，您应承担损害赔偿责任。</p>



                <h4 style="margin-bottom: 5px;margin-top: 5px;">附则</h4>
                <p>一、本协议的签订地为广东省深圳市福田区。</p>
                <p>二、本协议自您在勾选“我已阅读并同意”后生效，有效期涵盖双方整个合作期限。除非双方另有约定，本协议在您使用本服务期间持续有效。</p>
                <p>三、小鹅通于您过失或违约时放弃本协议规定的权利的，不得视为其对您的其它或以后同类之过失或违约行为弃权。</p>
                <p>四、本协议的成立、生效、履行、解释及纠纷解决，适用中华人民共和国大陆地区法律（不包括冲突法）。</p>
                <p>五、因本协议产生之争议，双方应首先协商解决；双方未能以诚意协商解决的，任何一方均应将纠纷或争议提交本协议签订地有管辖权的人民法院管辖。</p>
                <p>六、本协议的拟定、解释均以中文为准。除双方另有约定外，任何有关本协议的翻译不得作为解释本协议或判定双方当事人意图之依据。</p>
                <p>七、如本协议的任何条款被视作无效或无法执行，则上述条款可被分离，其余条款则仍具有法律效力。</p>
                <p>八、本协议最终解释权及修订权归小鹅通所有。</p>


                <h5 style="margin-bottom: 5px;margin-top: 5px;">本协议将于2016年10月10日起执行。</h5> 
                <h5>（正文完）</h5>
                <h5>深圳小鹅网络技术有限公司</h5>
            </div>
            <div style="width:100%;height:42px;">
                <button type="button" class="layerButton" style="width: 30%;margin-left: 35%;margin-top: 10px;"
                id="iAgree">已阅读并同意协议</button>
            </div>
            </div>
            <div id="pay_by_wechat" style="position: relative;" class="charge_btn button_click"><div class="pay_by_wechat_icon"><img src="images/icon_wechat_pay.svg"></div>微信支付</div>
        </div>
    </div>
    <!--支付扫描二维码-->
    <div class="scan_screen " style="display: none">
        <div class="scan_screen_content" >
            <div class="pop-up_close">
                <img src="images/icon_Pop-ups_close.svg">
            </div>
            <div class="scan_screen_words">请用微信扫描二维码完成支付</div>
            <div id="qr_code" class="scan_screen_pic"></div>
            <div class="scan_screen_notice">二维码有效时长为半小时，请尽快完成支付</div>
        </div>
        <div class="scan_status scan_status_success" style="display: none">
            <div class="scan_status_icon"><img src="images/version_charge_success.png"></div>
            <div class="scan_status_word">支付成功</div>
            <div class="scan_status_countdown"><span>3</span>秒后跳回到账户预览...<a href="/accountview">立即返回</a></div>
        </div>
        <div class="scan_status scan_status_fail " style="display: none">
            <div class="pop-up_close">
                <img src="images/icon_Pop-ups_close.svg">
            </div>
            <div class="scan_status_icon"><img src="images/version_charge_fail.png"></div>
            <div class="scan_status_word">支付失败</div>
            <div class="scan_status_return">重新支付</div>
        </div>
    </div>
        <script type="text/javascript" src="../js/admin/growupVersionPage.js?201702456"></script>
    </body>

</html>
