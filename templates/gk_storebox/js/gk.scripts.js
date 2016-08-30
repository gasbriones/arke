//
var page_loaded = false;
//
window.addEvent('load', function() {
	//
	page_loaded = true;
	// smooth anchor scrolling
	new SmoothScroll(); 
	// style area
	if(document.id('gkStyleArea')){
		document.id('gkStyleArea').getElements('a').each(function(element,i){
			element.addEvent('click',function(e){
	            e.stop();
				changeStyle(i+1);
			});
		});
	}
	// font-size switcher
	if(document.id('gkTools') && document.id('gkMainbody')) {
		var current_fs = 100;
		var content_fx = new Fx.Tween(document.id('gkMainbody'), { property: 'font-size', unit: '%', duration: 200 }).set(100);
		document.id('gkToolsInc').addEvent('click', function(e){ 
			e.stop(); 
			if(current_fs < 150) { 
				content_fx.start(current_fs + 10); 
				current_fs += 10; 
			} 
		});
		document.id('gkToolsReset').addEvent('click', function(e){ 
			e.stop(); 
			content_fx.start(100); 
			current_fs = 100; 
		});
		document.id('gkToolsDec').addEvent('click', function(e){ 
			e.stop(); 
			if(current_fs > 70) { 
				content_fx.start(current_fs - 10); 
				current_fs -= 10; 
			} 
		});
	}
	// K2 font-size switcher fix
	if(document.id('fontIncrease') && document.getElement('.itemIntroText')) {
		document.id('fontIncrease').addEvent('click', function() {
			document.getElement('.itemIntroText').set('class', 'itemIntroText largerFontSize');
		});
		
		document.id('fontDecrease').addEvent('click', function() {
			document.getElement('.itemIntroText').set('class', 'itemIntroText smallerFontSize');
		});
	}
	// change the login
	if(document.getElement('a[title="login"]')) {
		document.getElement('a[title="login"]').setProperty('id', 'btnLogin');
	}
	// login popup
	if(document.id('gkPopupLogin') || document.id('gkPopupCart')) {
		var popup_overlay = document.id('gkPopupOverlay');
		popup_overlay.setStyles({'display': 'block', 'opacity': '0'});
		popup_overlay.fade('out');

		var opened_popup = null;
		var popup_login = null;
		var popup_login_h = null;
		var popup_login_fx = null;
		var popup_cart = null;
		var popup_cart_h = null;
		var popup_cart_fx = null;
		
		if(document.id('gkPopupLogin') && document.id('btnLogin')) {
			popup_login = document.id('gkPopupLogin');
			popup_login.setStyle('display', 'block');
			popup_login_h = popup_login.getElement('.gkPopupWrap').getSize().y;
			popup_login_fx = new Fx.Morph(popup_login, {duration:200, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'height': 0 }); 
			document.id('btnLogin').addEvent('click', function(e) {
				new Event(e).stop();
				popup_overlay.fade(0.45);
				popup_login_fx.start({'opacity':1, 'height': popup_login_h});
				opened_popup = 'login';
				
				(function() {
					if(document.id('modlgn-username')) {
						document.id('modlgn-username').focus();
					}
				}).delay(600);
			});
		}
		
		if(document.id('gkPopupCart')) {
			var btn = document.id('btnCart');
			
			window.addEvent('scroll', function(e) {
				var scroll = window.getScroll().y;
				var max = document.id('gkMainWrap').getSize().y;
				var final = 0;
				if(scroll > 70) {
					if(scroll < max - 122) {
						final = scroll - 50;
					} else {
						final = max - 172;
					}
				} else {
					final = 20;
				}
				btn.setStyle('top', final + "px");
			});
			
			popup_cart = document.id('gkPopupCart');
			popup_cart.setStyle('display', 'block');
			popup_cart_h = popup_cart.getElement('.gkPopupWrap').getSize().y;
			popup_cart_fx = new Fx.Morph(popup_cart, {duration:200, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'height': 0 }); 
			var wait_for_results = true;
			var wait = false;
			
			document.id('btnCart').addEvent('click', function(e) {
				new Event(e).stop();	
				
				if(!wait) {
					new Request.HTML({
						url: $GK_URL + document.id('btnCart').get('data-url'),
						onRequest: function() {
							document.id('btnCart').addClass('loading');
							wait = true;
						},
						onComplete: function() {
							var timer = (function() {
								if(!wait_for_results) {
									popup_overlay.fade(0.45);
									popup_cart_fx.start({'opacity':1, 'height': popup_cart_h});
									opened_popup = 'cart';
									wait_for_results = true;
									wait = false;
									clearInterval(timer);
									document.id('btnCart').removeClass('loading');
								}
							}).periodical(200);
						},
						onSuccess: function(nodes, xml, text) {
							document.id('gkAjaxCart').innerHTML = text;
							popup_cart.setStyle('display', 'block');
							popup_cart_h = popup_cart.getElement('.gkPopupWrap').getSize().y;
							popup_cart_fx = new Fx.Morph(popup_cart, {duration:200, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'height': 0 }); 
							wait_for_results = false;
							wait = false;
						}
					}).send();
				}
			});
		}
		
		popup_overlay.addEvent('click', function() {
			if(opened_popup == 'login')	{
				popup_overlay.fade('out');
				popup_login_fx.start({
					'opacity' : 0,
					'height' : 0
				});
			}
			
			if(opened_popup == 'cart')	{
				popup_overlay.fade('out');
				popup_cart_fx.start({
					'opacity' : 0,
					'height' : 0
				});
			}	
		});
	}
});

// function to set cookie
function setCookie(c_name, value, expire) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expire);
	document.cookie=c_name+ "=" +escape(value) + ((expire==null) ? "" : ";expires=" + exdate.toUTCString());
}
// Function to change styles
function changeStyle(style){
	var file1 = $GK_TMPL_URL+'/css/style'+style+'.css';
	var file2 = $GK_TMPL_URL+'/css/typography/typography.style'+style+'.css';
	var file3 = $GK_TMPL_URL+'/css/typography/typography.iconset.style'+style+'.css';
	new Asset.css(file1);
	new Asset.css(file2);
	new Asset.css(file3);
	Cookie.write('gk_storebox_j25_style', style, { duration:365, path: '/' });
}

/* VirtueMart addons */
window.addEvent('domready', function() {
	var tabs = document.id('product-tabs');
	// if tabs exists
	if(tabs) {
		// initialization
		tabs.getElement('li').addClass('active');
		var contents = document.id('product-tabs-content');
		contents.getChildren('div').setStyle('display', 'none');
		contents.getElement('div').addClass('active');
		// add events to the tabs
		tabs.getElements('li').each(function(tab, i) {
			tab.addEvent('click', function() {
				var toggle = tab.getProperty('data-toggle');
				contents.getChildren('div').removeClass('active');
				contents.getElement('.' + toggle).addClass('active');
				tabs.getElements('li').removeClass('active');
				tab.addClass('active');		
			});
		});
	}
	
	var products = document.getElements('.browse-view .product');
	var categories = document.getElements('.category-view .category');
	var f_products = document.getElements('.featured-view .product');
	var l_products = document.getElements('.latest-view .product');
	var t_products = document.getElements('.topten-view .product'); 
	var r_products = document.getElements('.recent-view .product');
	
	[products, categories, f_products, l_products, t_products, r_products].each(function(p) {
		if(p.length > 0) {
			p.each(function(item,i) {
				item.addEvents({
					"mouseenter": function() {
						item.addClass('active');
					},
					"mouseleave": function() {
						item.removeClass('active');
					}
				});
			}); 
		}
	});
	
	var productZoom = document.getElements('.productDetails .main-image');
	if(productZoom.length > 0) {
		productZoom.each(function(item,i) {
			var overlay = item.getElement('.product-overlay');
			var link = item.getElement('a');
			if(overlay) {
				overlay.inject(link, 'bottom');
			}
			item.addEvents({
				"mouseenter": function() {
					item.addClass('active');
				},
				"mouseleave": function() {
					item.removeClass('active');
				}
			});
		}); 
	} 
});

window.addEvent('touchstart', function(e) {
	if(e.target.hasClass('modal') || e.target.hasClass('ask-a-question')) {
		window.location.href = e.target.getProperty('href');
	}
});

window.addEvent('load', function() {
	setTimeout(function() {
		document.getElements('.modal').removeEvents();
		document.getElements('.ask-a-question').removeEvents();
	}, 2000);
});