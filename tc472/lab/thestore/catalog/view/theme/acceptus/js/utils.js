/*--------------------------------------------------------------------------/
* @Author		KulerThemes.com http://www.kulerthemes.com
* @Copyright	Copyright (C) 2012 - 2013 KulerThemes.com. All rights reserved.
* @License		KulerThemes.com Proprietary License
/---------------------------------------------------------------------------*/

$(document).ready(function () {

	// Product hover
	$('#container .box .product-grid > div').hover(function () {
		var totalHeight = 0;
		$(this).find('.details').children().each(function() {
			totalHeight = totalHeight + $(this).height();
		});
		$(this).find('.details').height(totalHeight);
		$(this).css('marginBottom', -1 * totalHeight);
	}, function () {
		$(this).css('marginBottom', 0);
		$(this).find('.details').height(0);
	});

});