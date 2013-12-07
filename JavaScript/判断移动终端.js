// JavaScript Document

// 判断IOS设备并做设置
function iosBtnHide(){  
if((navigator.userAgent.indexOf('iPhone') != -1)||(navigator.userAgent.indexOf('iPod') != -1)||(navigator.userAgent.indexOf('iPad') != -1)){
		$('.flashBtn').hide();
	}
};

// 判断移动终端进行跳转
$(function(){
   //PC与移动端访问检测
	if (/Android|webOS|iPhone|iPod|IEMobile|BlackBerry/i.test(navigator.userAgent)) {
		window.location.href = "http://wap.kfc.com.cn";
		return ;
	}
});


