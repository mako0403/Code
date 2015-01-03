$(document).ajaxStart(function(){
	$("button:submit").addClass("log-in").attr("disabled", true);
})
.ajaxStop(function(){
	$("button:submit").removeClass("log-in").attr("disabled", false);
});

/*浏览器类型获取*/
function getOS(){ 
   var OsObject = ""; 
   if(navigator.userAgent.indexOf("MSIE")>0) { 
        return "MSIE"; 
   } 
   if(isFirefox=navigator.userAgent.indexOf("Firefox")>0){ 
        return "Firefox"; 
   } 
   if(isSafari=navigator.userAgent.indexOf("Safari")>0) { 
        return "Safari"; 
   } 
   if(isCamino=navigator.userAgent.indexOf("Camino")>0){ 
        return "Camino"; 
   } 
   if(isMozilla=navigator.userAgent.indexOf("Gecko")>0){ 
        return "Gecko"; 
   } 
}

//POST 提交并弹出返回信息
function ajax_call(the_url,the_param){
	$.ajax({
		type:'POST',
		url:the_url,
		data:the_param,
		success:function(html){alert(html);},
		error:function(html){
			alert("提交数据失败，代码:" +html.status+ "，请稍候再试");
		}
	});
}


//POST 提交并重载页面
function ajax_call_and_refresh(the_url, the_param){
	$.ajax({
		type:'POST',
		url:the_url,
		data:the_param,
		success:function(html){location.reload();},
		error:function(html){
			alert("提交数据失败，代码:" +html.status+ "，请稍候再试");
		}
	});
}

/**
 * 使用ajax post提交数据
 */
function ajax_post(the_url,the_param,succ_callback,callbackdataType){
	$.ajax({
		type	: 'POST',
		cache	: false,
		url		: the_url,
		data	: the_param,
		dataType: callbackdataType,
		success	: succ_callback,
		error	: function(html){
			alert("提交数据失败，代码:" +html.status+ "，请稍候再试");
		}
	});
}

/**
 * 使用ajax get异步请求获取数据
 */
function ajax_get(the_url,error_tip,succ_callback,callbackdataType){
	$.ajax({
		type	: 'GET',
		cache	: false,
		url		: the_url,
		success	: succ_callback,
		dataType: callbackdataType,
		error	: function(html){
			if(error_tip)
			alert("获取数据失败，代码:" +html.status+ "，请稍候再试");
		}
	});
}

// 提示信息窗体 依赖blockUI
function showMsg(message,type,time){
	var timeout = time?time:'';
	var style ='';
	if(type=='success'){
		timeout = time?time:5000;
		style = {width:'auto', padding:'8px 16px', left:'45%', top:'3%', border:'1px solid #FBC695', backgroundColor:'#FDE999', color:'#000', cursor:'pointer','-webkit-border-radius':'2px', '-moz-border-radius':'2px','border-radius':'2px', 'opacity':'1', 'font-weight':'bold', 'box-shadow': '0px 2px 4px #aaa','z-index':'999999'};
	}else if(type=='error'){
		timeout = time?time:15000;
		style = {width:'auto', padding:'8px 16px', left:'45%', top:'3%', border:'1px solid #FBC695', backgroundColor:'#FDE999', color:'#c00', cursor:'pointer','-webkit-border-radius':'2px', '-moz-border-radius':'2px','border-radius':'2px', 'opacity':'1', 'font-weight':'bold', 'box-shadow': '0px 2px 4px #aaa','z-index':'999999'};
	}else{
		timeout = time?time:12000;
		style = {width:'auto', padding:'8px 16px', left:'45%', top:'3%', border:'1px solid #FBC695', backgroundColor:'#FDE999', color:'#000', cursor:'pointer','-webkit-border-radius':'2px', '-moz-border-radius':'2px','border-radius':'2px', 'opacity':'1', 'font-weight':'bold', 'box-shadow': '0px 2px 4px #aaa','z-index':'999999'};
	}
	$.blockUI({
		css		:	style,
		message :	message,
		centerX :	true,
		timeout	:	timeout,
		showOverlay: false
	});
	$('.blockMsg').css({'max-width':$(window).width()/2,'left':($(window).width()/2)-(($('.blockMsg').width()+32)/2)});
	$('.blockUI').click(function(){
		$.unblockUI();
	});
}

// 获取今天日期
function gettoday(){
	var date = new Date();
	var month = date.getMonth()<10?'0'+(date.getMonth()+1):date.getMonth();
	var day = date.getDate()<10?'0'+date.getDate():date.getDate();
	return date.getFullYear()+'-'+month+'-'+day;
}

// 高精度加法运算
function bcadd(arg1, arg2) {
	var r1, r2, m;
	try { r1 = arg1.toString().split(".")[1].length } catch (e) { r1 = 0 }
	try { r2 = arg2.toString().split(".")[1].length } catch (e) { r2 = 0 }
	m = Math.pow(10, Math.max(r1, r2))
	return (Number(arg1)*m + Number(arg2)*m) / m;
}

// 高精度减法运算
function bcsub(arg1, arg2) {
	var r1, r2, m, n;
	try { r1 = arg1.toString().split(".")[1].length } catch (e) { r1 = 0 }
	try { r2 = arg2.toString().split(".")[1].length } catch (e) { r2 = 0 }
	m = Math.pow(10, Math.max(r1, r2));
	//动态控制精度长度
	n = (r1 >= r2) ? r1 : r2;
	return ((Number(arg1)*m - Number(arg2)*m) / m);
}

// 高精度乘法运算
function bcmul(arg1, arg2) {
	var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
	try { m += s1.split(".")[1].length } catch (e) { }
	try { m += s2.split(".")[1].length } catch (e) { }
	return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);
}

// 高精度除法运算
function bcdiv(arg1, arg2) {
	var t1 = 0, t2 = 0, r1, r2;
	try { t1 = arg1.toString().split(".")[1].length } catch (e) { }
	try { t2 = arg2.toString().split(".")[1].length } catch (e) { }
	with (Math) {
		r1 = Number(arg1.toString().replace(".", ""))
		r2 = Number(arg2.toString().replace(".", ""))
		return (r1 / r2) * pow(10, t2 - t1);
	}
}

//截取小数 , 不超过lenth 位,4舍5入
function cutNumber(number, cutlength) {
	number = Number(number);
	var n = number.toString();
	//传入的数值没有小数点时,直接返回
	if (n.indexOf(".") < 0) {
		return Number(number);
	}
	
	var temp = n.substring(n.indexOf(".") + 1);
	//传入的数值小数位数不大于要截取的位数时,直接返回
	if (temp.length <= cutlength) {
		return Number(number);
	}
	
	var needNumer = n.substring(0, n.indexOf(".") + 1 + cutlength);
	var afterCutTemp = temp.substr(cutlength, 1);
	
	//4舍5入
	if (Number(afterCutTemp) > 4) {
		if (cutlength > 0) {
			var needtoadd = "0.";
			for (var i = 0; i < cutlength - 1; i++) {
				needtoadd = needtoadd + "0";
			}
			needtoadd = needtoadd + "1";
			needNumer = bcadd(needNumer, needtoadd);
		} else {
			needNumer = bcadd(needNumer, 1);
		}
	}
	return Number(needNumer);
}

// 全部选中/不选中  废弃
function SetCheckBoxAllChecked(CheckBoxName, IsChecked)
{
    var item = document.getElementsByName(CheckBoxName);
    if (null == item)
        return;

    for (var i = 0; i < item.length; i++)
        item[i].checked = IsChecked;
}

// checkbox 全选与反选  废弃
function checkAll(name){
	var names=document.getElementsByName(name);
	var len=names.length;
	if(len>0){
		var i=0;
		for(i=0;i<len;i++){
			 if(names[i].checked)
			 	names[i].checked=false;
			 else
			 	names[i].checked=true;
		}
	}
}

// 全选 反选 全不选  
!function ($) {  
    $("#selAll").click(function () {  
        $(".lists :checkbox").not(':disabled').prop("checked", true);  
    });  
    $("#unSelAll").click(function () {  
        $(".lists :checkbox").not(':disabled').prop("checked", false);  
    });  
    $("#reverSel").click(function () {  
        //遍历.lists 下的 checkbox;  
        $(".lists :checkbox").not(':disabled').each(function () {  
            $(this).prop("checked", !$(this).prop("checked"));  
        });  
    });  
}(jQuery);

// 获得选中的checkbox的value
function GetCheckBoxCheckedValue(CheckBoxName)
{
    var arrIndex = new Array();
    var item = document.getElementsByName(CheckBoxName);
    if (null == item)
        return arrIndex;

    for (var i = 0; i < item.length; i++)
    {
        if (item[i].checked)
        {
            arrIndex[arrIndex.length] = item[i].value + "&0";
        }
        else
        {
            arrIndex[arrIndex.length] = item[i].value + "&1";
        }
    }
    return arrIndex;
}

// 获得选中的值
function GetSelectedValue(SelectID) {
	var item = document.getElementById(SelectID);
	if ((null == item) || (item.selectedIndex < 0))
		return "";

	return item.options[item.selectedIndex].value.trim();
}

// 获得选中的内容
function GetSelectedText(SelectID) {
	var item = document.getElementById(SelectID);
	if ((null == item) || (item.selectedIndex < 0))
		return "";

	var Text = item.options[item.selectedIndex].text.trim();
	var Value = GetSelectedValue(SelectID) + "-";
	var Index = Text.indexOf(Value);

	return Text.substring(Index);
}

//判断字符串长度
function charLength(str)
{
	var i,sum;
	sum=0;
    for(i=0;i<str.length;i++)
    {
         if ((str.charCodeAt(i)>=0) && (str.charCodeAt(i)<=255))
             sum=sum+1;
         else
             sum=sum+2;
    }
    return sum;
}

//取字符串字节长度（处理中文特殊情况）obj 为字符串
function lenX(obj){
    theLen = 0;
    for( i = 0; i < obj.length; i++)  {       
       if (obj.charCodeAt(i) > 255 ){  
          theLen=theLen + 2;
       }else{
          theLen=theLen + 1;
       }    
    }
    return theLen;	
}

//判断字符串字节长度是否超过定长（处理中文特殊情况）
//true 超过定长，false不超过定长
function overLength(obj,leng){    
   if(lenX(obj)> leng){
      return true	
   }
   return false;
}

//布局等高
;(function($){
	if($.fn.EqualHeight){
		return;
	}
	$.fn.EqualHeight = function(options){
		var defaults = {      
			equalitem	:	'',
			difference	:	''  
		};      
		// Extend our default options with those provided.      
		var options = $.extend(defaults, options);
		var height = $(this).outerHeight()-options.difference;
		$('.'+options.equalitem).each(function(){
			$(this).css({'height':height+'px'});
		});
	}
})(jQuery);

//弹出式提醒
// $().ajaxMessage({});
;(function($) {
	if ($.fn.ajaxMessage) {
		return;
	}
	$.fn.ajaxMessage = function(options){
		var options = $.extend({}, {
			id		:	'messagebox',
			message :	'',
			timeout	:	5000,
			top		:	'30',
			color	:	'000'
		},options);
		this.each(function(){
			var fater = $(this);
			var messagebox = '#'+options.id;
			if(document.getElementById(options.id)!=null){
				$('#'+options.id).remove();
				clearTimeout(timer);
			}
			var html='<div id="'+options.id+'" style="position:absolute; display:none; margin-top:'+options.top+'px; z-index:99999; padding:6px 15px; overflow:hidden; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; box-shadow: 0px 2px 3px #aaa; text-align:cente; background:#FDE999; border:1px #FBC695 solid; font-weight:bold; cursor:pointer; color:#'+options.color+';"></div>';
			$(this).prepend(html);
			$(messagebox).html(options.message);
			var body_width=$(fater).outerWidth();
			var message_width=$(messagebox).outerWidth();
			var margin = (body_width/2)-(message_width/2);
			$(messagebox).css({'margin-left':margin}).slideDown(300).fadeIn(300);
			$(messagebox).click(function(){
				$(messagebox).fadeOut(300, function(){
					$('#'+options.id).remove();
					clearTimeout(timer);
				});
			});
			timer=setTimeout(function(){$(messagebox).remove();}, options.timeout);
		});
	};
})(jQuery);

//计算两个坐标之间的距离，返回单位为米的数值
function beelineDistance(lat1, lng1, lat2, lng2){
	//d * Math.PI / 180.0; 计算弧度
	var Lat1 = lat1 * Math.PI / 180.0; 
	var Lng1 = lng1 * Math.PI / 180.0;
	var Lat2 = lat2 * Math.PI / 180.0;
	var Lng2 = lng2 * Math.PI / 180.0;
	var a = Lat1 - Lat2;
	var b = Lng1 - Lng2;
	var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a/2),2) + Math.cos(Lat1)*Math.cos(Lat2)*Math.pow(Math.sin(b/2),2)));
	s = s * 6378.137; // 6378.137 地球半径，单位为公里
	s = Math.round(s * 10000) / 10000;
	return s*1000; //输出单位为米
}

// 向浏览器标题添加闪烁提醒
;(function($) {
	var options = $.extend({}, {
		message : '新消息',
	},options);
	$.extend({
		/**
		* 调用方法： 
		* var timerArr = $.blinkTitle.show();
		* $.blinkTitle.clear(timerArr);
		*/
		blinkTitle : {
			show : function(message) { //有新消息时在title处闪烁提示
				var title_msg = message?message:options.message;
				var tk="";
				for(i=0; i<title_msg.length; i++){
					tk=tk+"　";
				}
				var step=0, _title = document.title;
				var timer = setInterval(function() {
					step++;
					if (step==3) {step=1};
					if (step==1) {document.title = "【"+tk+"】" + _title};
					if (step==2) {document.title = "【"+title_msg+"】" + _title};
				}, 500);
				return [timer, _title];
			},
		
		/**
		* @param timerArr, timer标记
		* @param timerArr, 初始的title文本内容
		*/
			clear : function(timerArr) { //去除闪烁提示，恢复初始title文本
				if(timerArr) {
					clearInterval(timerArr[0]);
					document.title = timerArr[1];
				};
			}
		}
	});
})(jQuery);
