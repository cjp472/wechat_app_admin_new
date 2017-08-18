$(function() {
	var nowPage = GetQueryString('id'),
		$side = $('#sidebarNav'),
		$sideUl = $side.find('ul'),
		$title = $('#toolbar_title'),
        $titleMobile = $('#toolbar_title_mobile'),
		$frame = $('#frameContent'),
		$mainContent = $('#mainContent'),//文本内容区
        $mainContentMoblie = $('#mainContentMoblie'),//文本内容区
		$loader = $('#base_loading');

	$sideUl.css('height', 0);

	$side.on('click', 'p.firstKey', function(e) {
		var $ele = $(e.target),
			$ul = $ele.next(),
			liLen = $ul.find('li').length;
		// $ele.parent().addClass('open').siblings().removeClass('open');
		//展开当前，隐藏其它
		$sideUl.not($ul).css({'height': 0});
		$ul.css({'height': liLen*34});
	}).on('click', 'ul p.secondKey', function(e) {
		var $ele = $(e.target),
			type = $ele.data('type');
		// $side.find('ul p.secondKey').removeClass('active');
		// $ele.addClass('active');
		//$title.text(title);
		if( type==0 ) {
			var id = $ele.data('id');
			getNewPage(id);
		} else if( type==1 ){
			var link = $ele.data('link');
			showOutLink(link);
		}
	});

	$frame.on('load',function(e) {
		var $iframe = $(e.target),
			iframeHeight = $iframe.contents().find("body").height();
		$iframe.height(iframeHeight);
		hideLoading();
	});

	if(!nowPage) {
		var firstLi = $side.find('li.firstKeyContent:first');
		firstLi.find('p.firstKey').click()
			.next().find('p.secondKey:first').click();
	} else {
		$side.find('p[data-id='+nowPage+']').click().parents('.firstKeyContent').find('p.firstKey').click();
		if( $side.find('p[data-id='+nowPage+']').length<=0 ) getNewPage(nowPage);
	}

	function getNewPage(id) { //请求帮助文档页面
		showLoading();
		LinkToggle(false);
		$.ajax('/get_help_content', {
			type: 'POST',
			dataType: 'json',
			data: {id: id},
			success: function(json) {
				// console.info(json);
				var data = json.data;
				if(json.code==0) {
					$title.text(data.head_name);
                    $titleMobile.text(data.head_name);
					$mainContent = $('#mainContent').html(data.org_content);
                    $mainContentMoblie = $('#mainContentMoblie').html(data.org_content);
					$('a').prop('target', '_blank');  //让页面所有的超链接跳转到新页面
					hideLoading();
				} else {
					hideLoading();
				}
			},
			fail: function() {
				hideLoading();
			}
		});
	}

	function showOutLink(link) { //请求外链
		LinkToggle(true);
		$frame.attr('src', link);
	}

	function LinkToggle(toggle) { //外链的开关
		if(toggle) {
			$frame.removeClass('hide').addClass('show');
			$mainContent.removeClass('show').addClass('hide');
		} else {
			$frame.removeClass('show').addClass('hide');
			$mainContent.removeClass('hide').addClass('show');
		}
	}

	function GetQueryString(name) { //获取地址栏参数
	    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	    var r = window.location.search.substr(1).match(reg);
	    if(r!=null) {
	    	return decodeURIComponent(r[2]);
	    } else {
	    	return null;
	    };
	}

	function showLoading() {
		$loader.show();
	}
	function hideLoading() {
		$loader.hide();
	}
});
