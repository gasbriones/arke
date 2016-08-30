function BugReport ()
{
	var BR = this;
	this.Init = function() {
		return this;
	}

    this.modal = function(h2)
    {
        jQuery('.modalWindow').css('display','block');
        jQuery('.modalWindow > div > h2').html(h2);
        jQuery('.close').click(function(){
        	jQuery('.modalWindow').css('display','none');
        });
    }
	this.send = function(errMess, version) {
		var domain = document.domain;
		if ("xhr" in errMess && "status" in errMess && "error" in errMess) {
			if(errMess.xhr.responseText)
			{

			}
			errMess = "status:"+errMess.xhr.status+";responseText:"+errMess.xhr.responseText;

		}
		else if ("message" in errMess) {
			errMess = errMess.message;
		}

		jQuery.ajax({
            type: "POST",
			url:"https://www.ukrsolution.com/ExtensionsErrors/report",
			dataType:"json",
			data:{
				'domain':domain,
				'extension': version,
				'message': errMess
			},
			success: function(data){
				BR.modal(data.message);
			},
			error: function(status){
				BR.modal(status);
			}
		});
	}
	this.Init();
}