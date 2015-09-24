require.config({
	baseUrl : "catalog/view/javascript/",
	urlArgs : "ver=16",
	paths : {
		"swiper" : 'swiper/swiper.min',
		"mustache" : 'mustache/mustache.min',
		"iscroll" : 'iscroll/iscroll.min',
		"swipeImg" : 'my/swipeImg',
		"category" : 'my/category',
		"products" : 'my/products',
		"delayImg" : 'my/delayImg',
		"user" : 'my/user',
		"invitecode" : 'my/invitecode',
		"requires" : "my/requires",
		"userlevel" : "my/userlevel",
		"apply" : "my/apply"
	},
	shim : {
		"swiper" : {
			deps : ['css!../javascript/swiper/swiper.min.css']
		}
	}
});

$.isMobile = window.navigator.userAgent.toLowerCase().indexOf('mobile') != -1;
$.sClick = $.isMobile ? 'tap' : 'click';

$.baseUrl = $('base').attr('href');
$.alertPopup = $("#alertPopup");
$.alertPopup.enhanceWithin().popup({history:false,defaults:true,dismissible:false});
$.alertPopup.mypopup = function(text, dofunc) {
	$(this).find("p").text(text);
	$(this).find("a").off($.sClick);
	if (dofunc) {
		$(this).find("a").on($.sClick, function() {
			dofunc();
		});
	}
	$(this).popup("open", {transition: "pop"});
}

if ((typeof loginpage !== "undefined") || (typeof registerpage !== "undefined")) {
	require(['user'], function(u) {
		u.init();
		u.load();
	});
}

if (typeof homepage !== "undefined") {
	require(['category'], function (c) {
		c.init();
	});
}

if (typeof userlevelpage !== "undefined") {
	require(['userlevel'], function (ulevel) {
		ulevel.init();
	});
}

if (typeof applylistpage !== "undefined") {
	require(['apply'], function (apply) {
		apply.init();
	});
}
