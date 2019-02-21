// 当前时间
setInterval(function () {
	var currentTime = new Date()
	$('#now_date').html(currentTime.toLocaleString())
}, 1000)

// 自制选项卡方法
function commonTab(ele1, ele2, clas) {
	var index = ele1.index()
	ele1.addClass(clas).siblings().removeClass(clas)
	ele2.eq(index).addClass(clas).siblings().removeClass(clas)
}

$(function () {
	// 判断是否为ie浏览器
	if (!!window.ActiveXObject || "ActiveXObject" in window) {
		$('.footer_cell:last-child').css('marginRight', 0)
		$('.affairs_left_item:nth-child(3n+3)').css('marginRight', 0)
		$('.affairs_mid .hd ul li:last-child').css('marginRight', 0)
		$('.affairs_right_top li:nth-child(3n+3)').css('marginRight', 0)
		$('.service_btm_list li:nth-child(5n+5)').css('marginRight', 0)
		$('.interact_btm li:last-child').css('marginRight', 0)
		$('footer_box').css({
			'position': 'absolute',
			"bottom": 0,
			"left": 0
		})
		$('.huicui_list li:nth-child(3n+3)').css('marginRight', 0)
		$('.zk-rightdian tbody tr td table td:last-child').css('width','114px')
		$('.huicui_btm li:last-child').css('marginRight', 0)
		$('.huicui_btm li:nth-child(1)').css('backgroundColor', '#d45757')
		$('.huicui_btm li:nth-child(2)').css('backgroundColor', '#f0a920')
		$('.huicui_btm li:nth-child(3)').css('backgroundColor', '#68c531')
		$('.huicui_btm li:nth-child(4)').css('backgroundColor', '#5ea8dc')
		$('.wenjian_item:nth-child(3n+3)').css('marginRight', 0)
	}

	// 限制详情页表格宽度100%
	$('.article_details table').css('width','100%');

})