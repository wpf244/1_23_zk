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
