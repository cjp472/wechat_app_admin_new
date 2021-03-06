{{--编辑后离开页面的提示--}}
<div class="modal fade" id="upload_Modal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding-left: 10px">
            <div class="modal-body">
                <div style="text-align: center;font-size: 1.4em">
                    <p>此时离开将丢失已编辑的内容，是否离开？</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btnMid xeBtnDefault"
                        data-dismiss="modal">
                    取消
                </button>
                <button id="leavePage" type="button" class="btnMid btnBlue" style="margin-left: 10px">
                    确定
                </button>
            </div>
        </div>
    </div>
</div>
<style>
    .preview_con{
        display: flex;
        position:fixed;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.5);
        justify-content:center;
        align-items:center;
        z-index: 9999999;
        top:0;
        left:0;
        visibility: hidden;
        -webkit-transition-property: opacity,visibility;
        transition-duration:0.5s;
        opacity:0;
    }
    .preview_con.active{
        visibility: visible;
        opacity:1;
    }

    .preview_box{
        width:350px;
        overflow: hidden;
        border-radius: 40px;
        background-image:url("/images/admin/communityOperate/pic_mockup.png");
        background-repeat:no-repeat;
        background-size: cover;
        height:730px;
        position:relative;
        margin:100px auto;
        top:-200px;
        transition-property:top,visibility,opacity;
        transition-duration:0.5s;
        visibility: hidden;
        opacity:0;
    }
    .preview_box.active{
        opacity:1;
        visibility:visible;
        top:0;
    }
    #preview_content{
        width: 310px;
        background: #fff;
        height: 514px;
        margin: 126px 0 0 21px;
        overflow-y: auto;
        overflow-x:hidden;
        padding:0 5px;
    }
    /*编辑器默认*/
    table.sortEnabled tr.firstRow th,table.sortEnabled tr.firstRow td{padding-right:20px;background-repeat:no-repeat;background-position:center right;}#preview_content .selectTdClass{background-color:#edf5fa!important}table.noBorderTable td,table.noBorderTable th,table.noBorderTable caption{border:1px dashed #ddd!important}#preview_content table{margin-bottom:10px;border-collapse:collapse;display:table}#preview_content caption{border:1px dashed #DDD;border-bottom:0;padding:3px;text-align:center}#preview_content th{border-top:1px solid #BBB;background-color:#f7f7f7}#preview_content table tr.firstRow th{border-top-width:2px}.ue-table-interlace-color-single{background-color:#fcfcfc}.ue-table-interlace-color-double{background-color:#f7faff}#preview_content td p{margin:0;padding:0}#preview_content .pagebreak{display:block;clear:both!important;cursor:default!important;width:100%!important;margin:0}#preview_content ol,#preview_content ul{margin:0;pading:0;width:95%}#preview_content li{clear:both}#preview_content ol.custom_cn{list-style:none}#preview_content ol.custom_cn li{background-position:0 3px;background-repeat:no-repeat}li.list-cn-paddingleft-1{padding-left:25px}li.list-cn-paddingleft-2{padding-left:40px}li.list-cn-paddingleft-3{padding-left:55px}ol.custom_cn1{list-style:none}ol.custom_cn1 li{background-position:0 3px;background-repeat:no-repeat}li.list-cn1-paddingleft-1{padding-left:30px}li.list-cn1-paddingleft-2{padding-left:40px}li.list-cn1-paddingleft-3{padding-left:55px}ol.custom_cn2{list-style:none}ol.custom_cn2 li{background-position:0 3px;background-repeat:no-repeat}li.list-cn2-paddingleft-1{padding-left:40px}li.list-cn2-paddingleft-2{padding-left:55px}li.list-cn2-paddingleft-3{padding-left:68px}ol.custom_num{list-style:none}ol.custom_num li{background-position:0 3px;background-repeat:no-repeat}li.list-num-paddingleft-1{padding-left:25px}ol.custom_num1{list-style:none}ol.custom_num1 li{background-position:0 3px;background-repeat:no-repeat}li.list-num1-paddingleft-1{padding-left:25px}ol.custom_num2{list-style:none}ol.custom_num2 li{background-position:0 3px;background-repeat:no-repeat}li.list-num2-paddingleft-1{padding-left:35px}li.list-num2-paddingleft-2{padding-left:40px}li.list-dash{}ul.custom_dash{list-style:none}ul.custom_dash li{background-position:0 3px;background-repeat:no-repeat}li.list-dash-paddingleft{padding-left:35px}li.list-dot{}ul.custom_dot{list-style:none}ul.custom_dot li{background-position:0 3px;background-repeat:no-repeat}li.list-dot-paddingleft{padding-left:20px}.list-paddingleft-1{padding-left:0}.list-paddingleft-2{padding-left:30px}.list-paddingleft-3{padding-left:60px}
    .preview_content table,.preview_content tbody{
        width:100%!important;
    }
    #preview_content *{
        max-width: 100%!important;
    }
    #preview_content img{
        height:auto!important;
    }
    #preview_content tr td,#preview_content th td{
        border:1px solid windowtext;
    }
</style>
<div class="preview_con">
    <div class="preview_box">
        <div id="preview_content">

        </div>
    </div>
</div>

