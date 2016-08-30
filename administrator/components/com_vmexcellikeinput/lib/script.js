function pelm_setCookie(c_name, value, exdays) {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
	document.cookie = c_name + "=" + c_value;
}

function pelm_getCookie(c_name) {
	var i, x, y, ARRcookies = document.cookie.split(";");
	for (i = 0; i < ARRcookies.length; i++) {
		x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
		x = x.replace(/^\s+|\s+$/g, "");
		if (x == c_name) {
			return unescape(y);
		}
	}
}

jQuery(document).ready(function(){
	jQuery('.save-state').each(function(i){
	    var val =  pelm_getCookie( 'pelm_' + jQuery(this).attr('id') );
		if(val)
			jQuery(this).val( val );
	});
});

function pelmStoreState(){
	jQuery('.save-state').each(function(i){
		pelm_setCookie('pelm_' + jQuery(this).attr('id'), jQuery(this).val(), 30);  
	});
}