/**
 * Created by jserk on 2017/7/31.
 */
$(function () {
    pageIndex.init();
    problem.init();
    //回车搜索
    $(document).keypress(function (e) {
        if (e.which == 13) {
            $('.searchBtn').trigger("click");
        }
    });
});


/**
 * 公共页
 */
var pageIndex = function () {
    var pageIndex = {};

    pageIndex.init = function () {


        $(".wxIcon").mouseenter(function () {
            $(".wxQrcodeBox").show();
        });

        $(".wxIcon").mouseleave(function () {
            $(".wxQrcodeBox").hide();
        });

        $(".searchBtn").click(function () {
            var searchContent = $('.searchInput').val();
            window.location.href = '/helpCenter/problem?search_content=' + searchContent;
        })
    };

    return pageIndex;
}();


/**
 * 问题汇总页
 */
var problem = function () {
    var problem = {};
    var firstId = '';   //一级目录id
    var secondId = '';   //二级目录id
    var documentId = '';  //文档id
    var searchContent = '';  //搜索内容

    var changeUrl = function (fId, sId, dId) {
        history.replaceState(null, "", "/helpCenter/problem?first_id=" + fId + "&second_id=" + sId + "&document_id=" + dId);
    }

    var linkUrl = function (fId, sId, dId) {
        window.location.href = "/helpCenter/problem?first_id=" + fId + "&second_id=" + sId + "&document_id=" + dId;
    };

    var getSecondProblemList = function (fId, sId, dId) {
        $('.documentVideo').hide();
        $(".loadingPartial").fadeIn(100);
        var params = {
            first_id: fId,
            second_id: sId,
            document_id: dId,
        };
        console.log(params);
        $.post('/helpCenter/document_detail_page', params, function (data) {
            if (data.code == 0) {
                $('.ptcList').html('');
                //若查询的是目录信息
                if (dId == '') {
                    $('.documentPart').hide();
                    $('.ptcTitle').text(data.data.second_index.title);
                    for (var i = 0; i < data.data.document_list.length; i++) {
                        $('.ptcList').append("<div class='ptcPart' data-documentid='" + data.data.document_list[i].id + "'><span class='listCircle'>" + "</span>" + data.data.document_list[i].name + "</div>")
                    }
                    $('.problemThreeContent').fadeIn(200);
                } else {
                    $('.problemThreeContent').hide();
                    $('.documentHeader').html("<div class='problemSecondSkip'>" + data.data.second_index.title + "</div>&nbsp;&nbsp;/&nbsp;&nbsp;详情");
                    $('.documentTitle').html(data.data.document_detail.name);
                    $('.documentTime').html(data.data.document_detail.updated_at);
                    $('.documentContent').html(data.data.document_detail.org_content);
                    if(data.data.document_detail.video_url!=''){
                        $('.videoSource').attr('src',data.data.document_detail.video_url);
                        $('#example_video_1_html5_api').attr('src',data.data.document_detail.video_url);
                    $('.documentVideo').show();
                    }

                    $('.documentPart').fadeIn(200);
                }
                $(".loadingPartial").hide();


            }
        })
    }

    problem.init = function () {
        firstId = $("#nowFirstId").val();
        secondId = $("#nowSecondId").val();
        documentId = $("#nowDocumentId").val();
        searchContent = $("#nowSearchContent").val();
        $('.searchInput').val(searchContent);
        if (searchContent != '') {
            $(".loadingPartial").fadeIn(100);
            $.post('/helpCenter/search', {
                search_content: searchContent
            }, function (data) {
                if (data.code == 0) {
                    $('.documentPart').hide();
                    $('.ptcTitle').html('<span style="font-size:12px">搜索“' + '<span style="color:red">' + searchContent + '</span>' + '”的相关结果共' + data.data.length + '条</span>');
                    if (data.data.length == 0) {

                    } else {
                        for (var i = 0; i < data.data.length; i++) {
                            $('.ptcList').append("<div class='ptcPart1' data-documentid='" + data.data[i].id + "' data-firstindex='" + data.data[i].first_index + "' data-secondindex='" + data.data[i].second_index + "'><span class='listCircle'>" + "</span><span class='ptcPart1Title'>" + data.data[i].name + "</span></div>");
                            //当搜索内容加载完毕后，遍历元素，并将关键字标记为红色
                            if (i == data.data.length - 1) {
                                //取得所有CSS样式为 ".right_a_a1" 的对象，并对默认自定义的方法进行遍历操作

                                $(".ptcPart1Title").each(function () {
                                    //取得标签的文本
                                    var t = $(this).text();
                                    //取得需要查出的关键字，我们这里假定是多关键字以","间隔
                                    var array = searchContent.split("");
                                    //开始用关键字遍历标签文本
                                    for (var i = 0; i < array.length; i++) {
                                        //判断标签是否包含关键字
                                        if (t.indexOf(array[i]) > -1) {
                                            //定义正则表达式对象  array[i]是关键字   "g"是指全局范围
                                            var a = new RegExp(array[i], "g")
                                            //对标签文本进行全局替换，包含关键字的位置替换为加红字span对象
                                            t = t.replace(a, ("<span style='color:#F00'>" + array[i] + "</span>"));
                                            //将替换完的文本对象赋给此对象中A标签对象的html值中
                                            $(this).html(t);
                                        }
                                    }
                                });
                            }
                        }
                    }
                    $(".loadingPartial").fadeOut(100);
                    $('.problemThreeContent').fadeIn(200);
                }
            });
        } else {
            getSecondProblemList(firstId, secondId, documentId);
        }

        $('.problemNavOne').click(function () {
            if ($(this).next().css('display') == 'none') {
                $(this).next().slideDown(300);
                $(this).addClass('problemNavOneActive');
            } else if ($(this).next().css('display') == 'block') {
                $(this).next().slideUp(300);
                $(this).removeClass('problemNavOneActive');
            }
        });

        $('.problemNavTwoPart').click(function () {
            $('.problemNavTwoPart').removeClass('problemNavTwoPartActive');
            $(this).addClass('problemNavTwoPartActive');
            firstId = $(this).data('firstid');
            secondId = $(this).data('secondid');
            getSecondProblemList(firstId, secondId, '');
            changeUrl(firstId, secondId, '');
        });

        $('body').on('click', '.ptcPart', function () {
            documentId = $(this).data('documentid');
            getSecondProblemList(firstId, secondId, documentId);
            changeUrl(firstId, secondId, documentId);
        })

        $('body').on('click', '.ptcPart1', function () {
            var linkDocumentId = $(this).data('documentid');
            var linkFirstIndex = $(this).data('firstindex');
            var linkSecondIndex = $(this).data('secondindex');
            linkUrl(linkFirstIndex, linkSecondIndex, linkDocumentId);
        })

        $('body').on('click', '.problemSecondSkip', function () {
            getSecondProblemList(firstId, secondId, '');
        })

    };

    return problem;
}();