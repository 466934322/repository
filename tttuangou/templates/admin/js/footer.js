/**
$(document).ready(function(){
	$.notify.loading();
	$(window).data('events')['beforeunload'] || $(window).bind('beforeunload',  wf_beforeunload);
});
function wf_page_loading(ctrl2Switch)
{
	$__SHOW_REQUEST_LOADING = ctrl2Switch;
}
function wf_beforeunload()
{
	if (!$__SHOW_REQUEST_LOADING) return;
	$.notify.loading('正在请求中...', true);
	$.browser.msie && setTimeout(function(){$.notify.loading()}, 300);
}