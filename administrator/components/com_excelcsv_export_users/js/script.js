jQuery(document).ready(function () {
    version += ",ExprotVersion_1.4.2_free";
    window.bug = new BugReport();
    constants = jQuery.parseJSON(constants);
   
    function recalculateHiddenFields()
    {
        jQuery('.export_hidden').each(function () {

            var input = jQuery(this);
            var liArray = jQuery('#groups_id > div').filter(function () {
                return jQuery(this).css('display') != "none";
            });
            var pos = liArray.index(input.parent().parent());
            input.val(pos+1);
        });
    }
    
   
    function table(val, count)
    {
       
        if(jQuery('.body_excelcsv_export').attr('name') == 'create')
        {
           
            if(val == undefined)
                val = jQuery.parseJSON(jQuery('#jsTable').attr('value'));
            if(count == undefined)
                count=jQuery('#jsTable').attr('count');

            var pre = '<div class="order_col"><div class="prev">';
            pre +='<div class="first_td"><div class="first_td_">№</div>';
            var i = 1;
            for(i; i<=count;i++)
            {
                pre += "<div class='first_td_'>"+i+"</div>";
            }
            pre+="</div><div id = 'groups_id'>";
            n=1;
            pre+="<div class = 'groups"+1+"'>";
            pre+="<div class='group_first_td'><input class='export_hidden' type='hidden' name='name' value = '1'>Name</div>";
            for(v= 0; v <count; v++)
            {   
                pre+="<div class='group_else_td'>"+val[v]['name']+"</div>";
            }
            pre+="</div>";
           
            pre+="<div class = 'groups"+1+"'>";
            pre+="<div class='group_first_td'><input class='export_hidden' type='hidden' name='username' value = '2'>Username</div>";
            for(v= 0; v <count; v++)
            {   
                pre+="<div class='group_else_td'>"+val[v]['username']+"</div>";
            }
            pre+="</div>";
           
            pre+="<div class = 'groups"+1+"'>";
            pre+="<div class='group_first_td'><input class='export_hidden' type='hidden' name='email' value = '3'>Email</div>";
            for(v= 0; v <count; v++)
            {   
                pre+="<div class='group_else_td'>"+val[v]['email']+"</div>";
            }
            pre+="</div>";
           
            pre+="<div class = 'groups"+2+"'>";
            pre+="<div class='group_first_td'><input class='export_hidden' type='hidden' name='password' value = '4'>Password</div>";
            for(v= 0; v <count; v++)
            {   
                pre+="<div class='group_else_td'>"+val[v]['password']+"</div>";
            }
            pre+="</div>";
           
            pre+="<div class = 'groups"+3+"'>";
            pre+="<div class='group_first_td'><input class='export_hidden' type='hidden' name='groupusers' value = '5'>Group</div>";
            for(v= 0; v <count; v++)
            {   
                pre+="<div class='group_else_td'>"+val[v]['groupusers']+"</div>";
            }
            pre+="</div>";
            pre +='</div></div></div>';
            jQuery('#jsTable').html(pre);
           
            if(jQuery("#pass_box_yes:checked").length == 0)
            {
                jQuery(".groups2").css("display", "none");
                jQuery(".td_for_groups2").css("display", "none");
                recalculateHiddenFields();
            }
            if(jQuery("#group_box_yes:checked").length == 0)
            {
                jQuery(".groups3").css("display", "none");
                jQuery(".td_for_groups3").css("display", "none");
                recalculateHiddenFields();
            }
           
            jQuery("#groups_id").sortable({
                stop: recalculateHiddenFields
            });
            jQuery('.groups1, .groups2').mousedown(function(){
                jQuery(this).css('border-width','0px 0px 0px 1px');
            });
            jQuery('.groups1, .groups2').mouseup(function(){
                jQuery(this).css('border','0px solid black');
            });
            jQuery('.groups3').mousedown(function(){
                jQuery(this).css('border-width','0px 0px 0px 1px');
            });
            jQuery('.groups3').mouseup(function(){
                jQuery(this).css('border','0px solid black');
            });

           
            jQuery("#pass_box_yes").click(function () {
                jQuery(".groups2").css("display", "table-cell");
                jQuery(".td_for_groups2").css("display", "table-cell");
                recalculateHiddenFields();
            });
            jQuery("#pass_box_no").click(function () {
                jQuery(".groups2").css("display", "none");
                jQuery(".td_for_groups2").css("display", "none");
                recalculateHiddenFields();
            });
            jQuery("#group_box_yes").click(function () {
                jQuery(".groups3").css("display", "table-cell");
                jQuery(".td_for_groups3").css("display", "table-cell");
                recalculateHiddenFields();
            });
            jQuery("#group_box_no").click(function () {
                jQuery(".groups3").css("display", "none");
                jQuery(".td_for_groups3").css("display", "none");
                recalculateHiddenFields();
            });
        }
    }
    table();
   
    jQuery('#start_test_export_ajax').click(function (event) {
       
        var check_pass = jQuery('input[name=check_pass]:checked').attr('value');
        var gr = jQuery('input[name=gr]:checked').attr('value');
        var format1 = jQuery('input[name=format1]:checked').attr('value');

        var name = jQuery('input[name=name]').attr('value');
        var username = jQuery('input[name=username]').attr('value');
        var pass = jQuery('input[name=password]').attr('value');
        var email = jQuery('input[name=email]').attr('value');
        var group = jQuery('input[name=groupusers]').attr('value');
        var groupusers = new Array();
        jQuery('input[type=checkbox]:checked').each(function () {
            groupusers.push(jQuery(this).val());
        });
        event.preventDefault();
        jQuery('#start_test_export_ajax').hide();
        jQuery('#loader').show();
       
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_excelcsv_export_users&task=exportJson",
            dataType: "json",
            data: {
                check_pass: check_pass,
                gr: gr,
                format1: format1,
                group: group,
                name: name,
                username: username,
                email: email,
                pass: pass,
                groupusers: groupusers
            },
            error: function(xhr, status, error) 
            {
                bug.send({"xhr":xhr, "status":status, "error":error}, version);
            },
            success: function (data) {
               
               
                if (data.result == true) {
                    document.location.href = data.FILE_URL;
                    jQuery('#start_test_export_ajax').show();
                    jQuery('#loader').hide();
                }
               
                else if (data.result == false) {
                   
                    bug.modal(data.message);
                    jQuery('#start_test_export_ajax').show();
                    jQuery('#loader').hide();
                }
            }
        });
    });
   
    jQuery('label .input_box[type=checkbox]').click(function(){
        var groupusers = new Array();
        jQuery('input[type=checkbox]:checked').each(function () {
            groupusers.push(jQuery(this).val());
        });
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_excelcsv_export_users&task=countUsers",
            data: {
                'groupusers':groupusers
            },
            error: function(xhr, status, error) 
            {
                bug.send({"xhr":xhr, "status":status, "error":error}, version);
            },
            success: function (data)
            {
                if(data)
                {
                    jQuery('.head_order_col1').show();
                    jQuery('.error1').hide();
                    jQuery('#count_u').html(data);
                }
                else
                {
                    jQuery('.head_order_col1').hide();
                    jQuery('.error1').show();
                }
            }
        });
        jQuery.ajax({
            type:"POST",
            url:"index.php?option=com_excelcsv_export_users&task=previewTable",
            dataType: "json",
            data: {
                'groupusers':groupusers
            },
            error: function(xhr, status, error) 
            {
                bug.send({"xhr":xhr, "status":status, "error":error}, version);
            },
            success: function(obj)
            {
               
                table(obj, obj.filter(function(value) { return value !== undefined }).length);
            }
        });
    });
    
   
   
    var domain = document.domain;
    var script = document.createElement('script');
    script.src = "https://www.ukrsolution.com/CheckSubscription/Export-Users-From-Joomla-To-Excel-CSV-File?domain="+domain;
    document.documentElement.appendChild(script);
    script.onload = function() {
        if (!window.isExportSubscribed)
        {
            jQuery('#notice').show();
        }
        else
        {
            jQuery('#notice').hide();
        }
    }
   
    jQuery('#nameSet').keyup(function(){
        var myRe =/^[\w\s]{1,30}$/;
        var str = jQuery(this).val();
        myArray = myRe.test(str);
        if(myArray == false)
        {
            jQuery(this).attr('class', 'style error_input')
            jQuery('.btn1').attr('class', 'btn1 block').attr('disabled','disabled');
        }
        else
        {
            jQuery('.btn1').attr('class', 'btn1').removeAttr('disabled');
            jQuery(this).attr('class', 'style');
        }
    });
   
    jQuery('.btn1').click(function (event) {
       
        event.preventDefault();
        jQuery('.btn1').hide();
        jQuery('#ok_loader').css('display','block');
        var check_pass = jQuery('input[name=check_pass]:checked').attr('value');
        var gr = jQuery('input[name=gr]:checked').attr('value');
        var format1 = jQuery('input[name=format1]:checked').attr('value');
        var name = jQuery('input[name=name]').attr('value');
        var username = jQuery('input[name=username]').attr('value');
        var pass = jQuery('input[name=password]').attr('value');
        var email = jQuery('input[name=email]').attr('value');
        var group = jQuery('input[name=groupusers]').attr('value');
        var groupusers = new Array();
        jQuery('input[type=checkbox]:checked').each(function () {
            groupusers.push(jQuery(this).val());
        });
        var nameSet = jQuery('#nameSet').val();
        if(nameSet == "")
        {
            jQuery('.btn1').css('display','block');
            jQuery('#ok_loader').hide();
            return false;
        }
       
        if(groupusers == "")
        { 
            jQuery('.btn1').css('display','block');
            jQuery('#ok_loader').hide();
            jQuery('.error').html(constants.TEXT_FOR_WARNING_GROUP);
            return false;
        }
       
        if(nameSet != null && nameSet != "")
        {
            jQuery.ajax({
                type: "POST",
                url: "index.php?option=com_excelcsv_export_users&task=saveSettings",
                data: {
                    nameSet: nameSet,
                    check_pass: check_pass,
                    gr: gr,
                    format1: format1,
                    group: group,
                    name: name,
                    username: username,
                    email: email,
                    pass: pass,
                    groupusers: groupusers
                },
                error: function(xhr, status, error) 
                {
                    bug.send({"xhr":xhr, "status":status, "error":error}, version);
                },
                success: function (data)
                {
                    console.log(data);
                    jQuery('.btn1').css('display','block');
                    jQuery('#ok_loader').hide();
                    if(data == '1')
                        jQuery('.error').html(constants.ERROR_FOR_ISSET_NAME_SETTINGS);
                    else if(data == '')
                    {
                        jQuery('.error').html('');
                        location.href = '#ok';
                    }
                    else
                        jQuery('.error').html(data);
                }
            });
        }

    });
   
    function check()
    {   var body =jQuery('.body_excelcsv_export');
        if(profiles == '[]' && body.attr('name') != 'create' || body.html() == "<br>")
        {
            document.location = '?option=com_excelcsv_export_users&task=create';
        }
        else
        {
            if(body.attr('name') == 'profiles')
            {
                body.html('<br>'); 
            }
        }
    }
   
    function viewProfiles(obj1){
        var body = jQuery('.body_excelcsv_export');

        if(body.attr('name') != 'profiles')
            return;
        check();
        var html = "<div class='head_group_users'>"+constants.TEXT_DOWNLOAD_PROFILES+"</div>";
        var html1 = "";
        var i = 0;
        var k = 0;
        var g = 0;   
        var key;
        var set ='';

       
        if(obj1 != "{}")
        {
            for(i=0; i<obj1.length;i++)
            {
                if(i == 30)
                {
                    g++;
                    i=0;
                    html += "<div style='clear:both'></div></div><br/>";
                    html += "<div class='height' id = 'group_"+g+"'>";
                    continue;
                }
                if(obj1[i]['amount'] == 1)
                    var amount = obj1[i]['amount']+" "+constants.TEXT_PROFILE_USER_FOUND;
                else
                    var amount = obj1[i]['amount']+" "+constants.TEXT_PROFILE_USER_FOUND+'s';
                set += 'groupusers = "'+obj1[i]['groupusers']+'" save_pass = "'+obj1[i]['save_pass']+'" grouptd = "'+obj1[i]['grouptd']+'" email = "'+obj1[i]['email']+'" name = "'+obj1[i]['name']+'" username = "'+obj1[i]['username']+'" group = "'+obj1[i]['group']+'" format = "'+obj1[i]['format']+'" pass = "'+obj1[i]['pass']+'"'
                
                html +='<div name = "'+obj1[i]['nameSet']+'"';
                html += "<div id='"+k+"' name='if_"+g+"_"+k+"' class = 'profile'><div class='button_download' profile='"+obj1[i]['nameSet']+"' id='dow_"+obj1[i]['nameSet']+"'"+set+"></div><div value='"+obj1[i]['nameSet']+"' name = '"+g+"_"+k+"' class='delete delete_profile' id='del_"+k+"'></div><div class ='name_profile'>"+obj1[i]['nameSet']+"</div><div class='count'>("+amount+")</div></div>";
                set='';
                html1 = html;
                k++;
            }
        }
        html1 += "<div style='clear:both'></div></div><br/>";
            body.prepend(html1);

    }
    viewProfiles(profiles);
   
    function hideDelete(){
        jQuery('.profile').mouseover(function(){
           key=jQuery(this).attr('id');
           jQuery('#del_'+key).css('opacity', '1');
        });
        jQuery('.profile').mouseleave(function(){
           key=jQuery(this).attr('id');
           jQuery('#del_'+key).css('opacity', '0');;
        });
    }
    hideDelete();
   
    function download()
    {
        jQuery('div.button_download').click(function(event){
            var groupusers = jQuery(this).attr('groupusers');
            var save_pass = jQuery(this).attr('save_pass');
            var grouptd = jQuery(this).attr('grouptd');
            var email = jQuery(this).attr('email');
            var name = jQuery(this).attr('name');
            var username = jQuery(this).attr('username');
            var group = jQuery(this).attr('group');
            var pass = jQuery(this).attr('pass');
            var format = jQuery(this).attr('format');
            var profileName = jQuery(this).attr('profile');
            event.preventDefault();
            jQuery.ajax({
                type: "POST",
                url: "index.php?option=com_excelcsv_export_users&task=exportJson",
                dataType: "json",
                data: {
                    'profileName':profileName,
                    'groupusers':groupusers,
                    'check_pass':save_pass,
                    'gr':grouptd,
                    'email':email,
                    'name':name,
                    'username':username,
                    'group':group,
                    'pass':pass,
                    'format1':format
                },
                error: function(xhr, status, error) 
                {
                    bug.send({"xhr":xhr, "status":status, "error":error}, version);
                },
                success: function (data) {
                   
                    if (data.result == true) {
                        document.location.href = data.FILE_URL;
                        jQuery('#start_test_export_ajax').show();
                        jQuery('#loader').hide();
                    }
                   
                    else if (obj.result == false) {
                       
                        bug.modal(data.message);
                        jQuery('#start_test_export_ajax').show();
                        jQuery('#loader').hide();
                    }
                }
            });
        });
    }
    download();
   
    function DelSettings()
    {
        jQuery('.delete').click(function(event){
            name = jQuery(this).attr('value');
            str= constants.TEXT_FOR_COMFIRM_DELETE;
            if(confirm(str.replace("\{\%NAME\%\}", "\""+name+"\"")))
            {
                jQuery.ajax({
                    type: "POST",
                    url: "index.php?option=com_excelcsv_export_users&task=DelSettings",
                    data: {
                        'nameSet':name
                    },
                    error: function(xhr, status, error) 
                    {
                        bug.send({"xhr":xhr, "status":status, "error":error}, version);
                    },
                    success: function (data) {
                        if(data =="[]")
                        {
                            document.location = '?option=com_excelcsv_export_users&task=create';
                        }
                        else
                        {
                            viewProfiles(data);
                            hideDelete();
                            DelSettings();
                            download();
                        }
                    }
                });
            }
        });
    }
    DelSettings();
});
