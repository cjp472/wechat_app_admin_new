
var search_content;
var order_attr;
var ChannelTitle;

$(document).ready(function () {

    keyEnter($('#pay_search_btn'));
    $("tbody tr").mouseover(function()
    {
        $(this).css({'background-color':'#f5f5f5'});
    }).mouseout(function()
    {
        $(this).css({'background-color':'#fff'});
    });

});


// //模糊搜索
// function orderSearch() {
//     showLoading();
//     var orderAttr = $("#order_attr").val(); //获取到选中的值
//     //获取搜索内容
//     var search_content = $("#order_search_content").val(); //获取到选中的值
//
//     if(search_content.length==0){
//         var url = "/pay_admin?order_attr=" +  encodeURI(orderAttr) ;
//     }else{
//         var url = "/pay_admin?order_attr=" +  encodeURI(orderAttr) + "&search_content=" + encodeURI(search_content);
//     }
//
//     window.location = url;
// }
//
// //回显输入框和选择框的值
// function reBack(){
//     var order_search_content = document.getElementById("order_search_content");
//
//     if(search_content!=""){
//         $('#order_search_content').val(search_content);
//         // order_search_content.html = search_content;
//         var order_attr_element = document.getElementById("order_attr");
//         if(order_attr_element.length>0){
//             for(var i=0;i<order_attr_element.options.length;i++){
//                 if(order_attr_element.options[i].value==order_attr){
//                     order_attr_element.options[i].selected=true;
//                     break;
//                 }
//             }
//         }
//     }else{
//         // console.log("null>>>>>>>"+search_content);
//     }
// }
