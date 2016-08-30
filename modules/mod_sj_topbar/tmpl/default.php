<?php
/**
 * @package SJ Topbar
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */
defined('_JEXEC') or die;
JHtml::stylesheet('modules/' . $module->module . '/assets/css/style.css');
if (!defined('SMART_JQUERY') && $params->get('include_jquery', 0) == "1") {
    JHtml::script('modules/' . $module->module . '/assets/js/jquery-1.9.1.min.js');
    JHtml::script('modules/' . $module->module . '/assets/js/jquery-noconflict.js');
    define('SMART_JQUERY', 1);
}
JHtml::script('modules/' . $module->module . '/assets/js/jquery.timeTo.js');

$tag_id = 'sj_topbar_'.rand().time();
$count_down = 'topbar_countdown_'.$module->id.rand().time();
// process data
	$tzone = $params->get('gettimezone','Asia/Bangkok');
	date_default_timezone_set($tzone);
	$_timezone = new DateTimeZone($tzone);
	// get GMT
	$offset = $_timezone->getOffset(new DateTime("now"));
	$offset = ($offset < 0 ? '' : '+').($offset/3600);
	$offset = (strpos($offset,'.5') !== false)? str_replace('.5',':30',$offset) : $offset;
	$gmt = $offset = ' GMT'.$offset;
	$current = date("D M d Y H:i:s");
	$current_full  = $current.' '.$tzone;
	$totime_current = strtotime($current);
	$totime_start_date = strtotime($params->get('start_date'));
	$start_date = date("D M d Y  H:i:s", $totime_start_date);
	$totime_end_date = strtotime($params->get('end_date'));
	$end_date = date("D M d Y  H:i:s", $totime_end_date);
	$condition_show_countdown = (int)$params->get('display_countdown',1) && ($totime_start_date <= $totime_current) && ($totime_end_date >= $totime_current) && ($totime_start_date <= $totime_end_date );
	if($condition_show_countdown)
	{
		$datetime_end = date_create($end_date);
		$datetime_current = date_create($current );
		$interval = date_diff($datetime_end, $datetime_current);
		$date_add = ($interval->days > 999) ?  date("D M d Y", strtotime($current . " + 999 days")) : $end_date;
		//$end_date = $date_add.'  GMT+0700 (SE Asia Standard Time)';
		$end_date = $date_add.$gmt;
		$display_Days = ($interval->days > 100) ? 3 :  2;
		$display_Days =  ($interval->days <= 0 ) ? 'false': $display_Days;
	}
	
	$display_panel = (int)$params->get('display_panel', 1);
	$time_cookie = ($params->get('time_cookie',0));
	if($display_panel == 3 || $display_panel == 2){
		if(isset($_COOKIE['sj_topbar_'.$module->id])) { 
		 $arr = explode("|", $_COOKIE["sj_topbar_".$module->id]);
			if(isset( $arr[1])) {
			}else{
				setcookie("sj_topbar_".$module->id,null, time() - 1  ,'/'); 
			}
		}
	}else{
		if(isset($_COOKIE["sj_topbar_".$module->id])) { 
			setcookie("sj_topbar_".$module->id,null, time() - 1  ,'/'); 
		}
	}
	
	$datetime_end = date_create($end_date);
	$datetime_current = date_create($current );
	$interval_primary = date_diff($datetime_end, $datetime_current);
if(strtotime($end_date) >= strtotime($current)) {
//add Css
ob_start();	?>
	@media all and (min-width: 1200px) {
		#<?php echo $tag_id; ?> .topbar-container{
			width:<?php echo $params->get('container_width'); ?>px; 
		}
	}
<?php
	echo $params->get('custom_css'); 
$css = ob_get_contents();
ob_end_clean();
$docs = JFactory::getDocument();
$docs->addStyleDeclaration($css);
$position_show = (int)$params->get('position_show',1);
$position_cls = $position_show ? ' topbar-top ' : ' topbar-bottom';
$on_top = (int)$params->get('on_top',1);
$on_top_cls = ($on_top && $position_show) ?' topbar-ontop' : '';

$show_topbar_right = ((int)$params->get('display_btn',1) && trim($params->get('link_btn')) != '' ) || $condition_show_countdown;
$class_hiden_right = ($show_topbar_right) ? '': ' topbar-right-hiden';
$class_hiden_right .=  $condition_show_countdown ? '' : ' topbar-date-off';
?>

<div id="<?php echo $tag_id ?>" class="sj-topbar <?php echo $position_cls.' '.$on_top_cls; ?>">
	<div class="topbar-container cf">
		<div class="topbar-left cf <?php  echo $class_hiden_right; ?>">
			<?php
			if(trim($params->get('content_topbar')) != '')	
			{
			?>
				<div class="topbar-left-inner">
					<?php echo trim($params->get('content_topbar')); ?>
				</div>
			<?php
			}
			?>
			
		</div>
		<?php if($show_topbar_right)
		{ ?>
		<div class="topbar-right">
			<div class="topbar-right-inner">
				<?php 
				if((int)$params->get('display_btn',1) && trim($params->get('link_btn')) != '')
				{
				?>
					<a target="<?php echo $params->get('target_btn','_self') ?>" href="<?php echo trim($params->get('link_btn')); ?>" target="_blank" class="topbar-button" ><?php echo JText::_('VIEW_OFFER') ?></a>
				<?php 
				}
				if($condition_show_countdown)
				{ ?>
					<div id="<?php echo $count_down; ?>"></div>
				<?php 
				} ?>
			</div>
		</div>
		<?php 
		} ?>
	</div>
	<?php 
	
	$cls = ($display_panel != 1) ? 'btn-close close-type-'.$display_panel : '';
	if($display_panel != 1)
	{ ?>
	<span class="topbar-close <?php echo $cls; ?>"> </span>
	<?php 
	}?>
</div>

<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready(function ($) {
		;(function(element){
			var $el = $(element);
			 var  _getCookie = function (cookieName) {
				var i, name, equal, numCookies, cookies = document.cookie
					.split(";");

				if (cookies) {
					numCookies = cookies.length;
					for (i = 0; i < numCookies; i++) {
						equal = cookies[i].indexOf("=");
						name = cookies[i].substr(0, equal);
						if (name.replace(/^\s+|\s+$/g, "") === cookieName) {
							return unescape(cookies[i].substr(equal + 1));
						}
					}
				}

				return false;
			}
			function _loadPanel(){
				var $height_el = $el.height();
				var pos_show = <?php echo $position_show; ?>,
					display_panel = <?php echo $display_panel; ?>;
					var _get_Cookie = _getCookie('sj_topbar_<?php echo $module->id; ?>');
				 if(pos_show == 1) {
						if(_get_Cookie != false) {
							var _getcookievalue = _get_Cookie.split('|');
							if( typeof _getcookievalue[1] != 'undefined') 
							{ 
								$('body').css('margin-top','');
								$('body').css('position','static');
								$el.css('top',-$height_el);
								if(display_panel == 2){
									$btn_close.removeClass('btn-close').addClass('btn-open');
									$btn_close.css('top',$height_el);
									$el.addClass('topbar-open');
								}
								if(display_panel == 3){
									$el.remove();
								}
							 }
							 else
							 {
								_setCookie("sj_topbar_<?php echo $module->id; ?>",null, time() - 1  ,'/'); 
							 } 
						} else { 
							$el.stop().css('top','-999px').animate({
								top:0
							}, {
								duration: 500,
								complete: function(){
									$('body').css('margin-top',$height_el);
									$('body').css('position','static');
									$el.addClass('topbar-close');
								}
							});
						} 
				} else 
				{
					if(_get_Cookie != false) {
						var _getcookievalue = _get_Cookie.split('|');
						if( typeof _getcookievalue[1] != 'undefined') 
						{ 
							$el.css('bottom',-$height_el);
							if(display_panel == 2){
								$btn_close.removeClass('btn-close').addClass('btn-open');
								$btn_close.css('bottom',$height_el);
								$el.addClass('topbar-open');
							}
							if(display_panel == 3){
								$el.remove();
							}
						 }
						 else
						 {
							_setCookie("sj_topbar_<?php echo $module->id; ?>",null, time() - 1  ,'/'); 
						 } 
					} else { 
						$el.stop().css('bottom',-$height_el).animate({
							bottom:0
						}, {
							duration: 500,
							complete: function(){
								$el.addClass('topbar-close');
							}
						});
					} 
					
				 }
			}
			var _timer ;
			$(window).load(function(){
				if(_timer) clearTimeout(_timer);
				_timer = setTimeout(_loadPanel(),500);
			});
			var $btn_close = $('.topbar-close',$el);
			if($btn_close.length > 0){
				$btn_close.click(function(){
					var $this = $(this), $height_panel = $el.height();
					var pos_show = <?php echo $position_show; ?>,
						display_panel = <?php echo $display_panel; ?>;
						if(pos_show == 1) 
						{ 
							if(display_panel == 2)
							{
								if($this.hasClass('btn-close')){
									var value = 'sj_topbar_<?php echo $module->id ;?>',
									time = <?php echo $time_cookie ; ?>;
									_setCookie('sj_topbar_<?php echo $module->id ;?>',value , time );
									$el.animate({
										top:-$height_panel
									}, {
										duration: 400,
										complete: function(){
											$this.removeClass('btn-close').addClass('btn-open');
											$this.css('top','0').animate({
												top:$height_panel
											},{
												duration: 200
											});
											$el.removeClass('topbar-close').addClass('topbar-open');
										}
									});
									$('body').animate({
										'margin-top':''
									},{ 
										duration:400,
										queue:false
									});
								}

								if($this.hasClass('btn-open')){
									var value = 'sj_topbar_<?php echo $module->id ;?>',
									time = 0;
									_setCookie('sj_topbar_<?php echo $module->id ;?>',value , time );
									$this.css('top','0').animate({
										top:0
									},{
										duration: 400
									});

									$el.css('top',-$height_panel).animate({
										top:0
									}, {
										duration:400,
										complete: function(){
											$this.removeClass('btn-open').addClass('btn-close');
											$el.removeClass('topbar-open').addClass('topbar-close');
										}
									})

									$('body').animate({
										'margin-top': $height_panel
									},{ 
										duration:400,
										queue:false
									});
								}
							}

							if(display_panel == 3)
							{ 
								$('body').animate({
									'margin-top':''
								},400);
								$el.stop().css('top','0').animate({
									top:-$height_panel
								}, {

									duration: 400,
									complete: function(){
										$el.remove();
									}
								})

								var value = 'sj_topbar_<?php echo $module->id ;?>',
									time = <?php echo $time_cookie ; ?>;
									_setCookie('sj_topbar_<?php echo $module->id ;?>',value , time );

							} 
					} 
					else
					{
						if(display_panel == 2)
						{
							if($this.hasClass('btn-close')){
								var value = 'sj_topbar_<?php echo $module->id ;?>',
								time = <?php echo $time_cookie ; ?>;
								_setCookie('sj_topbar_<?php echo $module->id ;?>',value , time );
								$el.stop().css('bottom','0').animate({
									bottom:-$height_panel
								}, {
									duration: 400,
									complete: function(){
										$this.removeClass('btn-close').addClass('btn-open');
										$this.css('bottom','0').animate({
											bottom:$height_panel
										},{
											duration: 400
										});
										$el.removeClass('topbar-close').addClass('topbar-open');
									}
								})
							}

							if($this.hasClass('btn-open'))
							{
								var value = 'sj_topbar_<?php echo $module->id ;?>',
								time = 0;
								_setCookie('sj_topbar_<?php echo $module->id ;?>',value , time );
								$this.css('bottom',-$height_panel).animate({
									bottom:'0'
								},{
									duration: 500
								});

								$el.stop().css('bottom',-$height_panel).animate({
									bottom:'0'
								}, {
									duration: 300,
									complete: function(){
										$this.removeClass('btn-open').addClass('btn-close');
										$el.removeClass('topbar-open').addClass('topbar-close');
									}
								})
							}
						}

						if(display_panel == 3)
						{ 
							$el.stop().css('bottom','0').animate({
								bottom:-$height_panel
							}, {
								duration: 400,
								complete: function(){
									$el.remove();
								}
							})

							var value = 'sj_topbar_<?php echo $module->id ;?>',
								time = <?php echo $time_cookie ; ?>;
								_setCookie('sj_topbar_<?php echo $module->id ;?>',value , time );
						} 
					} 
				});
			}

			var _setCookie = function (name, value, time) {
				var date, expires = '';
				if (time) {
					date = new Date();
					date.setTime(date.getTime() + (time*60 * 60 * 1000));
					expires = "; expires=" + date.toString();
					value = value+'|'+ date.toString();
				}

				// set the actual cookie
				document.cookie = name + "=" + value + expires + "; path=/";
				return true;

			}
			
			

			<?php
			if($condition_show_countdown)
			{ ?>
			 var $count_down = $('#<?php echo $count_down; ?>');
				 $count_down.timeTo({
					timeTo: new Date('<?php echo $end_date ?>'),
					displayDays: <?php echo $display_Days ?>,
					theme: "white",
					displayCaptions: true,
					fontSize: 24,
					captionSize: 10,
					countdown:true,
					callback: function(){
						$el.remove();
						$('body').animate({
								'margin-top':''
							},400);
					}
				});
			<?php 
			} ?>
			$(window).resize(function(){
				var pos_show = <?php echo $position_show; ?>,
					display_panel = <?php echo $display_panel; ?>;
				var $height_el = $el.height();
						
				if($el.hasClass('topbar-top')){
					if(pos_show == 1) {
						if(display_panel ==  2) {
							if(!$el.hasClass('topbar-open')){
								$('body').animate({
											'margin-top':$height_el
										},0);
								//$btn_close.css('top',$height_el);		
							}else{
								$el.css('top',-$height_el);
								if($btn_close.hasClass('btn-open')){
									$btn_close.css('top',$height_el);		
								}	
							}
						}
					}
					
				}
				
				if($el.hasClass('topbar-bottom')){
					if(pos_show == 0) {
						if(display_panel ==  2) {
							if($el.hasClass('topbar-open')){
								$el.css('bottom',-$height_el);
								if($btn_close.hasClass('btn-open')){
									$btn_close.css('bottom',$height_el);		
								}	
							}
							else{
								$el.css('bottom',0);
							}	
						}
						
					}
					
				}
			});
		})('#<?php echo $tag_id ?>')
	});
	//]]>
</script>
<?php  }
?>