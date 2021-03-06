(function($){
	$.alert = $.alert || {};
	$.alert = function(popHtml, type, options) {
	    var btnType = $.alert.btnEnum;
		var eventType = $.alert.eventEnum;
		var popType = {
			info: {
				title: "提示",
				icon: "blue",//蓝色i
				btn: btnType.okcancel
			},
			error: {
				title: "错误",
				icon: "red",//红色i
				btn: btnType.cancel
			},
			success: {
				title: "成功",
				icon: "green",//绿色勾
				btn: btnType.ok
			},
			custom: {
				title: "",
				icon: "",
				btn: 3
			}
		};
		var itype = type ? type instanceof Object ? type : popType[type] || {} : {};//格式化输入的参数:弹窗类型
		var config = $.extend(true, {
			//属性
			title: "提示", //自定义的标题
			icon: "blue", //图标
			btn: 3, //按钮
			//链接
			link:'',//超链接文本
			href:'',//超链接路径
			//事件
			onOk: $.noop,//点击确定的按钮回调
			onCancel: $.noop,//点击取消的按钮回调
			onClose: $.noop//弹窗关闭的回调,返回触发事件
		}, itype, options);

		var icon = config.icon;

		var $link = $("<a>").html(config.link).prop('href',config.href);

		var $txt = $("<p>").html(popHtml);//弹窗文本dom
		if(config.link.length > 0){
            $txt.append($link);
        }

		var btn = config.btn;//按钮组生成参数

		var popId = creatPopId();//弹窗索引

		var $box = $("<div>").addClass("_xcConfirm");//弹窗插件容器
		// var $layer = $("<div>").addClass("xc_layer");//遮罩层
		var $popBox = $("<div>").addClass("_popBox");//弹窗盒子
		var $ttBox = $("<div>").addClass("_ttBox");//弹窗顶部区域
		var $iconContent = $("<div>").addClass("_iconContent");//弹出提示和标题
		var $icon = icon ?
				$("<img>").attr({
					'src':'/images/alert/'+icon+'_info_prompt.svg',
					'width': '60px'
				}) : "";
		var $title = $("<p>").addClass("title").text(config.title);  //标题

		var $txtBox = $("<div>").addClass("_txtBox");//弹窗内容主体区
		var $btnArea = $("<div>").addClass("btnArea");//按钮区域
		var $ok = $("<a>").addClass("_sgBtn").addClass("_ok").text(config.oktext||"确定");//确定按钮
		var $cancel = $("<a>").addClass("_sgBtn").addClass("_cancel").text(config.canceltext||"取消");//取消按钮
		var $input = $("<input>").addClass("inputBox");//输入框
		var $clsBtn = $("<a>").addClass("_clsBtn");//关闭按钮

		//建立按钮映射关系
		var btns = {
			cancel: $cancel,
			ok: $ok
		};

		init();

		function init(){
			creatDom();
			var $popbox = $box.find('._popBox');
			$popbox.css('margin-top', -($popbox.height()/1.5));
			$box.css('opacity', 1);
			bind();
		}

		function creatDom(){
			$popBox.append(
				$clsBtn
			).append(
				$iconContent.append($icon).append($title)
			).append(
				$txtBox.append($txt)
			).
			append(
				$btnArea.append(creatBtnGroup(btn))
			);
			$box.attr("id", popId).append($popBox);
			$("body").append($box);
		}

		function bind(){
			//点击确认按钮
			$ok.click(doOk);

			//回车键触发确认按钮事件
			$(window).bind("keydown", function(e){
				if(e.keyCode == 13) {
					if($("#" + popId).length == 1){
						doOk();
					}
				}
			});

			//点击取消按钮
			$cancel.click(doCancel);

			//点击关闭按钮
			$clsBtn.click(doClose);
		}

		//确认按钮事件
		function doOk(){
			var $o = $(this);
			var v = $.trim($input.val());
			if ($input.is(":visible"))
		        config.onOk(v);
		    else
		        config.onOk();

			closeBox(popId);
			config.onClose(eventType.ok);
		}

		//取消按钮事件
		function doCancel(){
			var $o = $(this);
			config.onCancel();
			closeBox(popId);
			config.onClose(eventType.cancel);
		}

		//关闭按钮事件
		function doClose(){
			closeBox(popId);
			config.onClose(eventType.close);
			$(window).unbind("keydown");
		}

		function closeBox(id) {
			$("#" + id).on('transitionend webkitTransitionEnd mozTransitionEnd', function() {
				$(this).remove();
			}).css('opacity', 0);
		}

		//生成按钮组
		function creatBtnGroup(tp){
			var $bgp = $("<div>").addClass("btnGroup");
			$.each(btns, function(i, n){
				if( btnType[i] == (tp & btnType[i]) ){
					$bgp.append(n);
				}
			});
			return $bgp;
		}

		//重生popId,防止id重复
		function creatPopId(){
			var i = "pop_" + (new Date()).getTime()+parseInt(Math.random()*100000);//弹窗索引
			if($("#" + i).length > 0){
				return creatPopId();
			}else{
				return i;
			}
		}
	};

	//按钮类型
	$.alert.btnEnum = {
		ok: 2, //确定按钮
		cancel: 1, //取消按钮
		okcancel: 3 //确定&&取消
	};

	//触发事件类型
	$.alert.eventEnum = {
		ok: 1,
		cancel: 2,
		close: 3
	};

	//弹窗类型
	$.alert.typeEnum = {
		info: "info",
		error:"error",
		custom: "custom"
	};

})(jQuery);