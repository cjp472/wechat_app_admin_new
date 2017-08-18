
$(function(){
    $recordList.init();
});

var $recordList = {

    // 搜索值保存
    search: '',

    // 初始化的一些方法
    init: function(){
        $('body').on('click','#searchBtn',function(){
            // 获得搜索的值
            var search_value = $("#search_content").val();
            if($chosenShop.tabState === 'record'){
                $recordList.search = search_value;
                $chosenShop.getNewPage('record',{'search':search_value});
            }
        });
        document.onkeydown = function(e){
            var ev = document.all ? window.event : e;
            if(ev.keyCode == 13){
                var input_el = $("#search_content");
                var search_value = input_el.val();
                if(input_el.is(':focus')){
                    $recordList.search = search_value;
                    $chosenShop.getNewPage('record',{'search':search_value});
                }
            }
        }
    }
};