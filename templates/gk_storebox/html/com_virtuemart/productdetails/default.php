<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz
 * @author RolandD,
 * @todo handle child products
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 5151 2011-12-19 17:10:23Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// addon for joomla modal Box

// JHTML::_('behavior.tooltip');

	vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
	vmJsApi::css('jquery.fancybox-1.3.4');
	$box = "$.fancybox({
				href: '" . $this->askquestion_url . "',
				type: 'iframe',
				height: '550'
			});";
	
$document = JFactory::getDocument();
$document->addScriptDeclaration("
	jQuery(document).ready(function($) {
		$('a.ask-a-question').click( function(){
			$.facebox({
				iframe: '" . $this->askquestion_url . "',
				rev: 'iframe|550|550'
			});
			return false ;
		});
	/*	$('.additional-images a').mouseover(function() {
			var himg = this.href ;
			var extension=himg.substring(himg.lastIndexOf('.')+1);
			if (extension =='png' || extension =='jpg' || extension =='gif') {
				$('.main-image img').attr('src',himg );
			}
		});*/
	});
");


$imageJS = '
jQuery(document).ready(function() {
	jQuery("a[rel=vm-additional-images]").fancybox({
		"titlePosition" 	: "inside",
		"transitionIn"	:	"elastic",
		"transitionOut"	:	"elastic"
	});
	jQuery(".additional-images .product-image").click(function() {
		jQuery(".main-image img").attr("src",this.src );
		jQuery(".main-image img").attr("alt",this.alt );
		jQuery(".main-image a").attr("href",this.src );
		jQuery(".main-image a").attr("title",this.alt );
	});
});
';
$document->addScriptDeclaration ($imageJS);
JHTML::_('behavior.modal');
if (isset($this->product->step_order_level))
	$step=$this->product->step_order_level;
else
	$step=1;
if($step==0)
	$step=1;
$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);

/* Let's see if we found the product */
if (empty($this->product)) {
    echo JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
    echo '<br /><br />  ' . $this->continue_link_html;
    return;
}
?>
<div class="productdetails-view">
	<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = $this->product->category_name ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = jtext::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
?>
	
	<a style="float: right;"><input type="button" name="volver" class="addtocart-button" onclick="history.back()" value="Volver" title="Volver"></a>	
		<div class="productDetails">
				<div class="width36 floatleft"><?php // Product Title ?>
	
							<h1><?php echo $this->product->product_name ?></h1>
							<?php // Product Title END ?>
							
							
						<?php if (!empty($this->product->images)) {
							$image = $this->product->images[0];
							?>
						<div class="main-image">
						
							<?php
								echo $image->displayMediaFull("",false,"rel='vm-additional-images'");
							?>
							
							<div class="product-overlay"><span><span><?php echo JText::_('TPL_GK_LANG_VM_ZOOM'); ?></span></span></div>
							<div class="clear"></div>
						</div>
						<?php
							$count_images = count ($this->product->images);
							if ($count_images > 1) {
								?>
						    <div class="additional-images">
								<?php
								for ($i = 0; $i < $count_images; $i++) {
									$image = $this->product->images[$i];
									?>
						            <div class="floatleft">
							            <?php
							                 echo $image->displayMediaFull('class="product-image" style="cursor: pointer"',false,"");
							            ?>
						            </div>
									<?php
								}
								?>
						        <div class="clear"></div>
						    </div>
							<?php
							}
						}
						  // Showing The Additional Images END ?>
				</div>
				<div class="width58 floatright">
						<?php // Product Title ?>
							<!---<h1><?php echo $this->product->product_name ?></h1>--->
							<?php // Product Title END ?>
							<?php // afterDisplayTitle Event
						echo $this->product->event->afterDisplayTitle ?>
							<?php // Product Edit Link
						echo $this->edit_link;
						// Product Edit Link END ?>
						
						<?php if($this->showRating || (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) || (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_button_enable'))): ?>
								<div class="product-additional-info">
										<?php if($this->showRating){
										    $maxrating = VmConfig::get('vm_maximum_rating_scale',5);
											$rating = empty($this->rating)? JText::_('COM_VIRTUEMART_RATING').' '.JText::_('COM_VIRTUEMART_UNRATED'):JText::_('COM_VIRTUEMART_RATING') . round($this->rating->rating) . '/'. $maxrating;
											echo   $rating;
										} ?>
										<?php // Manufacturer of the Product
										if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) { ?>
										<?php
											$link = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $this->product->virtuemart_manufacturer_id . '&tmpl=component', FALSE);
											$text = $this->product->mf_name;
						
											/* Avoid JavaScript on PDF Output */
											if (strtolower(JRequest::getWord('output')) == "pdf"){
												echo JHTML::_('link', $link, $text);
											} else { ?>
										<span class="manufacturer"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') ?></span> <a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $link ?>"><?php echo $text ?></a>
										<?php } ?>
										<?php } ?>
										
										<?php // PDF - Print - Email Icon
							    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_button_enable')) { ?>
										<?php
											    //$link = (JVM_VERSION===1) ? 'index2.php' : 'index.php';
											    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;
											    $MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
										
											    if (VmConfig::get('pdf_icon', 1) == '1') {
												echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_button_enable', false);
											    }
											    echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
											    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend');
											    ?>
										<?php } // PDF - Print - Email Icon END ?>
								</div>
								<?php endif; ?>
						
						<div class="spacer-buy-area">
								<?php
				if (is_array($this->productDisplayShipments)) {
					foreach ($this->productDisplayShipments as $productDisplayShipment) {
					echo $productDisplayShipment . '<br />';
					}
				}
				if (is_array($this->productDisplayPayments)) {
					foreach ($this->productDisplayPayments as $productDisplayPayment) {
					echo $productDisplayPayment . '<br />';
					}
				}	
						
				// Product Price
				if ($this->show_prices) { ?>
				<div class="product-price" id="productPrice<?php echo $this->product->virtuemart_product_id ?>">
									
			<?php
			if (!empty($this->product->prices['salesPrice'])) {
				
			}
			echo $this->currency->createPriceDiv ( 'salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $this->product->prices ); 	
			//vmdebug('view productdetails layout default show prices, prices',$this->product);
			if ($this->product->prices['salesPrice']<=0 and VmConfig::get ('askprice', 1) and isset($this->product->images[0]) and !$this->product->images[0]->file_is_downloadable) { ?>
				<a class="ask-a-question bold" href="<?php echo $this->askquestion_url ?>"><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
				<?php
			} else {
				if ($this->showBasePrice) {
					echo $this->currency->createPriceDiv ('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $this->product->prices);
					if (round($this->product->prices['basePrice'],$this->currency->_priceConfig['basePriceVariant'][1]) != $this->product->prices['basePriceVariant']) {
						echo $this->currency->createPriceDiv ('basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $this->product->prices);
					}
			
				}
				echo $this->currency->createPriceDiv ('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $this->product->prices);
				if (round($this->product->prices['basePriceWithTax'],$this->currency->_priceConfig['salesPrice'][1]) != $this->product->prices['salesPrice']) {
					echo '<span class="price-crossed" >' . $this->currency->createPriceDiv ('basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $this->product->prices) . "</span>";
				}
				echo $this->currency->createPriceDiv ( 'discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE', $this->product->prices );
				if (round($this->product->prices['salesPriceWithDiscount'],$this->currency->_priceConfig['salesPrice'][1]) != $this->product->prices['salesPrice']) {
					echo $this->currency->createPriceDiv ('salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $this->product->prices);
				}				
				if ($this->product->prices['discountedPriceWithoutTax'] != $this->product->prices['priceWithoutTax']) {
					echo $this->currency->createPriceDiv ('discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $this->product->prices);
				} else {
					echo $this->currency->createPriceDiv ('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $this->product->prices);
				}
				echo $this->currency->createPriceDiv ( 'discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $this->product->prices ); 
				$unitPriceDescription = JText::sprintf ('COM_VIRTUEMART_PRODUCT_UNITPRICE', JText::_('COM_VIRTUEMART_UNIT_SYMBOL_'.$this->product->product_unit));
				echo $this->currency->createPriceDiv ('unitPrice', $unitPriceDescription, $this->product->prices);
				
				if (!empty($this->product->customfieldsSorted['ontop'])) {
					$this->position='ontop';
					echo $this->loadTemplate('customfields');
				} // Product Custom ontop end
					
				
			}
			?>
			
			<?php 
				// Ask a question about this product
				if (VmConfig::get('ask_question', 1) == 1) :
			?>
			<div class="ask-a-question">
			    <a class="ask-a-question" href="<?php echo $this->askquestion_url ?>" ><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
			</div>
			<?php endif; ?>
			
				</div>
				<?php } ?>
						<?php // Add To Cart Button
							if (!VmConfig::get('use_as_catalog', 0)  ) { ?>
							<div class="addtocart-area">
									<form method="post" class="product js-recalculate" action="<?php echo JRoute::_('index.php'); ?>" >
											<?php // Product custom_fields
											if (!empty($this->product->customfieldsCart)) {  ?>
											<div class="product-fields">
													<?php foreach ($this->product->customfieldsCart as $field) { ?>
													<div class="product-field-type-<?php echo $field->field_type ?>">
															<label class="product-fields-title" ><?php echo  JText::_($field->custom_title) ?></label>
															<?php echo $field->display ?> </div>
													<?php } ?>
											</div>
											<?php }
											 /* Product custom Childs
											  * to display a simple link use $field->virtuemart_product_id as link to child product_id
											  * custom_value is relation value to child
											  */
						
											if (!empty($this->product->customsChilds)) {  
											?>
											<div class="product-fields">
													<?php foreach ($this->product->customsChilds as $field) {  ?>
													<div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field->field_type ?>"> <span class="product-fields-title" ><b><?php echo JText::_($field->field->custom_title) ?></b></span> <span class="product-field-desc"><?php echo JText::_($field->field->custom_value) ?></span> <span class="product-field-display"><?php echo $field->display ?></span> </div>
													<br />
													<?php } ?>
											</div>
											<?php } ?>
											
											<?php if ( VmConfig::get ('display_stock', 1) || $this->product->product_box) : ?>
											<dl class="productDetailInfo">
												<?php if ( VmConfig::get ('display_stock', 1)) : ?>
												<dt>
													<?php echo JText::_('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_TITLE_TIP'); ?>:
												</dt>
												<dd>
													<?php echo $this->product->product_in_stock; ?>
												</dd>
												<?php endif; ?>
												
												<?php if ($this->product->product_box) : ?>
												<dt>
													<?php echo JText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX'); ?>
												</dt>
												<dd>
													<?php echo $this->product->product_box; ?>
												</dd>
												<?php endif; ?>
											</dl>
											<?php endif; ?>
											
											<?php
												if (!VmConfig::get('use_as_catalog', 0) and !empty($this->product->prices['salesPrice'])) {
											?>
													<script type="text/javascript">
													function check(obj) {
												 		// use the modulus operator '%' to see if there is a remainder
														remainder=obj.value % <?php echo $step?>;
														quantity=obj.value;
												 		if (remainder  != 0) {
												 			alert('<?php echo $alert?>!');
												 			obj.value = quantity-remainder;
												 			return false;
												 		}
												 		return true;
											 		}
													</script> 
											
													<div class="addtocart-bar">
													<?php // Display the quantity box 
													$stockhandle = VmConfig::get('stockhandle', 'none');
														if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($this->product->product_in_stock - $this->product->product_ordered) < 1) {
														?>
													<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$this->product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
													<?php } else { ?>
													
													<label for="quantity<?php echo $this->product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label>
													
													<span class="quantity-box">
													<input type="text" class="quantity-input js-recalculate" name="quantity[]" onblur="check(this);" value="<?php if (isset($this->product->step_order_level) && (int)$this->product->step_order_level > 0) {
														echo $this->product->step_order_level;
													} else if(!empty($this->product->min_order_level)){
														echo $this->product->min_order_level;
													}else {
														echo '1';
													} ?>"/>
													</span>
													
													<?php // Display the quantity box END ?>
													<?php // Display the add to cart button ?>
													<span class="addtocart-button">
														<?php echo shopFunctionsF::getAddToCartButton($this->product->orderable); ?>
													</span>
													
													<?php } ?>
													<div class="clear"></div>
											</div>
											<?php }
													 // Display the add to cart button END  ?>
											<?php // Display the add to cart button END ?>
											<input type="hidden" class="pname" value="<?php echo htmlentities($this->product->product_name, ENT_QUOTES, 'utf-8') ?>" />
											<input type="hidden" name="option" value="com_virtuemart" />
											<input type="hidden" name="view" value="cart" />
											<noscript>
											<input type="hidden" name="task" value="add" />
											</noscript>
											<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>" />
											<?php /** @todo Handle the manufacturer view */ ?>
										</form>
									<div class="clear"></div>
							</div>
							<?php }  // Add To Cart Button END ?>
							
							<?php
									// Availability Image
									$stockhandle = VmConfig::get('stockhandle', 'none');
									
									if (($this->product->product_in_stock - $this->product->product_ordered) < 1) {
										$app = JFactory::getApplication();
										$templateName = $app->getTemplate();
										
										if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($this->product->product_availability)) {
										
										?>	<div class="availability">
										    <?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : JText::_(VmConfig::get('rised_availability')); ?>
										</div>
										<?php
											} else if (!empty($this->product->product_availability)) {
										?>
										<div class="availability">
										<?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability)) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability, $this->product->product_availability, array('class' => 'availability')) : JText::_($this->product->product_availability); ?>
										</div>
										<?php
										}
									}
									?>
					</div>
				</div>
				<div class="clear"></div>
		</div>
		<?php // event onContentBeforeDisplay
	echo $this->product->event->beforeDisplayContent; ?>
	
	<?php if(!empty($this->product->product_desc) || $this->allowRating || $this->showReview) : ?>
	<ul id="product-tabs">
		<?php if(!empty($this->product->product_desc)) : ?>
		<li data-toggle="product-description"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></li>
		<?php endif; ?>
		
		<?php if($this->allowRating || $this->showReview) : ?>
		<li data-toggle="customer-reviews"><?php echo JText::_('COM_VIRTUEMART_REVIEWS') ?></li>
		<?php endif; ?>
	</ul>
	
	<div id="product-tabs-content">
	<?php endif; ?>
	

	<?php if (!empty($this->product->product_desc)) : ?>
	<div class="product-description gk-product-tab">
			<?php echo $this->product->product_desc; ?> 
	</div>
	
	<?php endif; ?>
		
	<?php // Customer Reviews
		if($this->allowRating || $this->showReview) :
			$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
			$ratingsShow = VmConfig::get('vm_num_ratings_show', 3); // TODO add  vm_num_ratings_show in vmConfig
			//$starsPath = JURI::root().VmConfig::get('assets_general_path').'images/stars/';
			$stars = array();
			$showall = JRequest::getBool('showall', false);
			for ($num=0 ; $num <= $maxrating; $num++  ) :
				$title = (JText::_("COM_VIRTUEMART_RATING_TITLE") . $num . '/' . $maxrating) ;
				$stars[] = '<span class="vmicon vm2-stars'.$num.'" title="'.$title.'"></span>';
			endfor; ?>
			
		<div class="customer-reviews gk-product-tab">
			<form method="post" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE); ?>" name="reviewForm" id="reviewform">
		<?php endif; ?>
	
		<?php if($this->showReview) : ?>
		<div class="list-reviews">
			<?php
				$i = 0;
				$review_editable = TRUE;
				$reviews_published = 0;
				if ($this->rating_reviews) {
					foreach ($this->rating_reviews as $review) {						
						if ($i % 2 == 0) {
							$color = 'normal';
						} else {
							$color = 'highlight';
						}
		
						/* Check if user already commented */
						// if ($review->virtuemart_userid == $this->user->id ) {
						if ($review->created_by == $this->user->id && !$review->review_editable) {
							$review_editable = FALSE;
						}
						?>
		
						<?php // Loop through all reviews
						
						if (!empty($this->rating_reviews) /*&& $review->published*/) {
							$reviews_published++;
						?>
							<div class="<?php echo $color ?>">
								<span class="date"><?php echo JHTML::date ($review->created_on, JText::_ ('DATE_FORMAT_LC')); ?></span>
								<span class="vote"><?php echo $stars[(int)$review->review_rating] ?></span>
								<blockquote><?php echo $review->comment; ?></blockquote>
								<span class="bold"><?php echo $review->customer ?></span>
							</div>
							<?php
						}
						$i++;
						if ($i == $ratingsShow && !$showall) {
							/* Show all reviews ? */
							if ($reviews_published >= $ratingsShow) {
								$attribute = array('class'=> 'details', 'title'=> JText::_ ('COM_VIRTUEMART_MORE_REVIEWS'));
								echo JHTML::link ($this->more_reviews, JText::_ ('COM_VIRTUEMART_MORE_REVIEWS'), $attribute);
							}
							break;
						}
					}
		
				} else {
					// "There are no reviews for this product"
					?>
					<span class="step"><?php echo JText::_ ('COM_VIRTUEMART_NO_REVIEWS') ?></span>
					<?php
				}  ?>			
				<div class="clear"></div>
		</div>
		<?php endif; ?>
		
		<?php // Writing A Review
			if($this->allowReview ) : ?>
				<div class="write-reviews">
				<?php // Show Review Length While Your Are Writing
				$reviewJavascript = "
				function check_reviewform() {
					var form = document.getElementById('reviewform');
					var ausgewaehlt = false;
	
					for (var i=0; i<form.vote.length; i++) {
						if (form.vote[i].checked) {
							ausgewaehlt = true;
						}
					}
					if (!ausgewaehlt)  {
						alert('".JText::_('COM_VIRTUEMART_REVIEW_ERR_RATE',false)."');
						return false;
					}
					else if (form.comment.value.length < ". VmConfig::get('reviews_minimum_comment_length', 100).") {
						alert('". addslashes( JText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT1_JS', VmConfig::get('reviews_minimum_comment_length', 100)) )."');
						return false;
					}
					else if (form.comment.value.length > ". VmConfig::get('reviews_maximum_comment_length', 2000).") {
						alert('". addslashes( JText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT2_JS', VmConfig::get('reviews_maximum_comment_length', 2000)) )."');
						return false;
					}
					else {
						return true;
					}
				}
	
				function refresh_counter() {
					var form = document.getElementById('reviewform');
					form.counter.value= form.comment.value.length;
				}";
	
				$document->addScriptDeclaration($reviewJavascript);
	
				if($this->showRating) :
					if($this->allowRating && $review_editable) : ?>
						<h4><?php echo JText::_('COM_VIRTUEMART_WRITE_REVIEW')  ?><span><?php echo JText::_('COM_VIRTUEMART_WRITE_FIRST_REVIEW') ?></span></h4>
						<span class="step"><?php echo JText::_('COM_VIRTUEMART_RATING_FIRST_RATE') ?></span>
						<ul class="rating">
						<?php // Print The Rating Stars + Checkboxes
							for ($num=0 ; $num<=$maxrating;  $num++ ) : ?>
							<li id="<?php echo $num ?>_stars">
								<label for="vote<?php echo $num ?>"><?php echo $stars[ $num ]; ?></label>
								<?php $selected = ($num == 5) ? ' checked="checked"' : ''; ?>
								<input<?php echo $selected ?> id="vote<?php echo $num ?>" type="radio" value="<?php echo $num ?>" name="vote">
							</li>
						<?php endfor; ?>
						</ul>
						<?php
					endif;
				endif;
				
				if($review_editable ) : ?>
					<span class="step"><?php echo JText::sprintf('COM_VIRTUEMART_REVIEW_COMMENT', VmConfig::get('reviews_minimum_comment_length', 100), VmConfig::get('reviews_maximum_comment_length', 2000)); ?></span> <br />
					<textarea class="virtuemart" title="<?php echo JText::_('COM_VIRTUEMART_WRITE_REVIEW') ?>" class="inputbox" id="comment" onblur="refresh_counter();" onfocus="refresh_counter();" onkeyup="refresh_counter();" name="comment" rows="5" cols="60">
					<?php if(!empty($this->review->comment)) echo $this->review->comment; ?>
					</textarea>
					<br />
					<span><?php echo JText::_('COM_VIRTUEMART_REVIEW_COUNT') ?>
					<input type="text" value="0" size="4" class="vm-default" name="counter" maxlength="4" readonly="readonly" />
					</span> <br />
					<br />
					<input class="highlight-button" type="submit" onclick="return( check_reviewform());" name="submit_review" title="<?php echo JText::_('COM_VIRTUEMART_REVIEW_SUBMIT')  ?>" value="<?php echo JText::_('COM_VIRTUEMART_REVIEW_SUBMIT')  ?>" />
				</div>
				<?php
				else :
					echo '<strong>'.JText::_('COM_VIRTUEMART_DEAR').$this->user->name.',</strong><br />' ;
					echo JText::_('COM_VIRTUEMART_REVIEW_ALREADYDONE');
					echo '</div>';
				endif;
			endif;
		
		if($this->allowRating || $this->showReview) :
		?>
					<input type="hidden" name="virtuemart_product_id" value="<?php echo $this->product->virtuemart_product_id; ?>" />
					<input type="hidden" name="option" value="com_virtuemart" />
					<input type="hidden" name="virtuemart_category_id" value="<?php echo JRequest::getInt('virtuemart_category_id'); ?>" />
					<input type="hidden" name="virtuemart_rating_review_id" value="0" />
					<input type="hidden" name="task" value="review" />
			</form>
		<?php
		else :
			echo JText::_('COM_VIRTUEMART_REVIEW_LOGIN'); // Login to write a review!
		endif; ?>
	<?php if($this->allowRating || $this->showReview) : ?>
	</div>
	<?php endif; ?>
	
	<?php if(!empty($this->product->product_desc) || $this->allowRating || $this->showReview) : ?>
	</div><!-- #product-tabs-content -->
	<?php endif; ?>
	
		
	<?php if (!empty($this->product->customfieldsSorted['normal'])) :
	$this->position='normal';
    // Product custom_fields END ?>
		<div class="product-fields">
				<?php
	$custom_title = null ;
	if(is_array($this->product->customfields) && count($this->product->customfields)) :
		foreach ($this->product->customfields as $field) : ?>
			<div class="product-field product-field-type-<?php echo $field->field_type ?>">
				<?php if ($field->custom_title != $custom_title && $field->show_title) { ?>
					<span class="product-fields-title" ><?php echo JText::_($field->custom_title); ?></span>
					<?php if ($field->custom_tip) :
						echo JHTML::tooltip($field->custom_tip,  JText::_($field->custom_title), 'tooltip.png');
					endif;
				} ?>
					<span class="product-field-display"><?php echo $field->display ?></span> <span class="product-field-desc"><?php echo jText::_($field->custom_field_desc) ?></span> </div>
				<?php
			$custom_title = $field->custom_title;
		endforeach;
	endif; ?>
		</div>
		<?php
	endif; // Product custom_fields END
	?>
	
		<?php if (!empty($this->product->customfieldsRelatedProducts)) : ?>
		<div class="product-related-products">
			<h4 class="title"><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h4>
			<?php foreach ($this->product->customfieldsRelatedProducts as $field) : ?>
				<div class="product-field-type-<?php echo $field->field_type ?>"> <span class="product-field-display"><?php echo $field->display ?></span> <span class="product-field-desc"><?php echo jText::_($field->custom_field_desc) ?></span> </div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
		<?php if (!empty($this->product->customfieldsRelatedCategories)) : ?>
		<div class="product-related-categories">
			<h4 class="title"><?php echo JText::_('COM_VIRTUEMART_RELATED_CATEGORIES'); ?></h4>
			<?php foreach ($this->product->customfieldsRelatedCategories as $field) : ?>
			<div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field_type ?>"> 
				<span class="product-field-display"><?php echo $field->display ?></span> 
				<span class="product-field-desc"><?php echo jText::_($field->custom_field_desc) ?></span> 
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; // Product customfieldsRelatedCategories END

	// Show child categories
	if ( VmConfig::get('showCategory',1) ) {
		if ($this->category->haschildren) {
			$iCol = 1;
			$iCategory = 1;
			$categories_per_row = VmConfig::get ( 'categories_per_row', 3 );
			$category_cellwidth = ' width'.floor ( 100 / $categories_per_row );
			$verticalseparator = " vertical-separator"; ?>
		<div class="category-view">
				<?php // Start the Output
			if(!empty($this->category->children)){
			foreach ( $this->category->children as $category ) {

			// Show the horizontal seperator
			if ($iCol == 1 && $iCategory > $categories_per_row) { ?>
				<div class="horizontal-separator"></div>
				<?php }

			// this is an indicator wether a row needs to be opened or not
			if ($iCol == 1) { ?>
				<div class="row">
						<?php }

			// Show the vertical seperator
			if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
				$show_vertical_separator = ' ';
			} else {
				$show_vertical_separator = $verticalseparator;
			}

			// Category Link
			$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);

				// Show Category ?>
						<div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
								<div class="spacer">
										<h3 class="catProductTitle"> <a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>"> <?php echo $category->category_name ?> <br />
												<?php // if ($category->ids) {
								echo $category->images[0]->displayMediaThumb("",false);
							//} ?>
												</a> </h3>
								</div>
						</div>
						<?php
			$iCategory ++;

			// Do we need to close the current row now?
			if ($iCol == $categories_per_row) { ?>
						<div class="clear"></div>
				</div>
				<?php
			$iCol = 1;
			} else {
				$iCol ++;
			}
		}
		}
		// Do we need a final closing row tag?
		if ($iCol != 1) { ?>
				<div class="clear"></div>
		</div>
		<?php } ?>
</div>
<?php }
	if (!empty($this->product->customfieldsSorted['onbot'])) {
    	$this->position='onbot';
    	echo $this->loadTemplate('customfields');
    } // Product Custom ontop end	
} ?>
</div>