/* JCE Editor - 2.5.1 | 26 May 2015 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2015 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
(function(){var each=tinymce.each,cookie=tinymce.util.Cookie,DOM=tinymce.DOM;tinymce.create('tinymce.plugins.KitchenSink',{init:function(ed,url){var self=this,state=false,h=0,el=ed.getElement(),s=ed.settings;function toggle(){var row=DOM.getParents(ed.id+'_kitchensink','table.mceToolbar');if(!row){return;}
var n=DOM.getNext(row[0],'table.mceToolbar');while(n){if(DOM.isHidden(n)){DOM.setStyle(n,'display','');state=true;}else{DOM.hide(n);state=false;}
n=DOM.getNext(n,'table.mceToolbar');}
h=s.height||el.style.height||el.offsetHeight;if(h){DOM.setStyle(ed.id+'_ifr','height',h);}
ed.controlManager.setActive('kitchensink',state);}
ed.addCommand('mceKitchenSink',toggle);ed.addButton('kitchensink',{title:'kitchensink.desc',cmd:'mceKitchenSink'});ed.onPostRender.add(function(ed,cm){if(DOM.get('mce_fullscreen')){state=true;return;}
toggle();});ed.onInit.add(function(ed){ed.controlManager.setActive('kitchensink',state);});},getInfo:function(){return{longname:'Kitchen Sink',author:'Ryan Demmer',authorurl:'http://www.joomlacontenteditor.net/',infourl:'http://www.joomlacontenteditor.net/',version:'2.5.1'};}});tinymce.PluginManager.add('kitchensink',tinymce.plugins.KitchenSink);})();