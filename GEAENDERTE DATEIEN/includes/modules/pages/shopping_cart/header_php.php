<?php
/**
 * shopping_cart header_php.php
 *
 * @package page
 * @copyright Copyright 2003-2020 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: header_php.php for Free Gifts 2020-07-24 17:05:16Z webchills $
 */

// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_SHOPPING_CART');

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$breadcrumb->add(NAVBAR_TITLE);
if (isset($_GET['jscript']) && $_GET['jscript'] == 'no') {
  $messageStack->add('shopping_cart', PAYMENT_JAVASCRIPT_DISABLED, 'error');
}
// Validate Cart for checkout
$_SESSION['valid_to_checkout'] = true;
$_SESSION['cart_errors'] = '';
$_SESSION['cart']->get_products(true);

// used to display invalid cart issues when checkout is selected that validated cart and returned to cart due to errors
if (isset($_SESSION['valid_to_checkout']) && $_SESSION['valid_to_checkout'] == false) {
  $messageStack->add('shopping_cart', ERROR_CART_UPDATE . $_SESSION['cart_errors'] , 'caution');
}

// build shipping with Tare included
$shipping_weight = $_SESSION['cart']->show_weight();
$totalsDisplay = '';
switch (true) {
  case (SHOW_TOTALS_IN_CART == '1'):
  $totalsDisplay = TEXT_TOTAL_ITEMS . $_SESSION['cart']->count_contents() . TEXT_TOTAL_WEIGHT . $shipping_weight . TEXT_PRODUCT_WEIGHT_UNIT . TEXT_TOTAL_AMOUNT . $currencies->format($_SESSION['cart']->show_total());
  break;
  case (SHOW_TOTALS_IN_CART == '2'):
  $totalsDisplay = TEXT_TOTAL_ITEMS . $_SESSION['cart']->count_contents() . ($shipping_weight > 0 ? TEXT_TOTAL_WEIGHT . $shipping_weight . TEXT_PRODUCT_WEIGHT_UNIT : '') . TEXT_TOTAL_AMOUNT . $currencies->format($_SESSION['cart']->show_total());
  break;
  case (SHOW_TOTALS_IN_CART == '3'):
  $totalsDisplay = TEXT_TOTAL_ITEMS . $_SESSION['cart']->count_contents() . TEXT_TOTAL_AMOUNT . $currencies->format($_SESSION['cart']->show_total());
  break;
}


$flagHasCartContents = ($_SESSION['cart']->count_contents() > 0);
$cartShowTotal = $currencies->format($_SESSION['cart']->show_total());

$flagAnyOutOfStock = false;

$products = $_SESSION['cart']->get_products();
for ($i=0, $n=sizeof($products); $i<$n; $i++) {
  $flagStockCheck = '';
  if (($i/2) == floor($i/2)) {
    $rowClass="rowEven";
  } else {
    $rowClass="rowOdd";
  }
  switch (true) {
    case (SHOW_SHOPPING_CART_DELETE == 1):
    $buttonDelete = true;
    $checkBoxDelete = false;
    break;
    case (SHOW_SHOPPING_CART_DELETE == 2):
    $buttonDelete = false;
    $checkBoxDelete = true;
    break;
    default:
    $buttonDelete = true;
    $checkBoxDelete = true;
    break;
    
  } // end switch
  $attributeHiddenField = "";
  $attrArray = false;
  $productsName = $products[$i]['name'];
  // Push all attributes information in an array
  if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
    if (PRODUCTS_OPTIONS_SORT_ORDER=='0') {
      $options_order_by= ' ORDER BY LPAD(popt.products_options_sort_order,11,"0")';
    } else {
      $options_order_by= ' ORDER BY popt.products_options_name';
    }
    foreach ($products[$i]['attributes'] as $option => $value) {
      $attributes = "SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
                     FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                     WHERE pa.products_id = :productsID
                     AND pa.options_id = :optionsID
                     AND pa.options_id = popt.products_options_id
                     AND pa.options_values_id = :optionsValuesID
                     AND pa.options_values_id = poval.products_options_values_id
                     AND popt.language_id = :languageID
                     AND poval.language_id = :languageID " . $options_order_by;

      $attributes = $db->bindVars($attributes, ':productsID', $products[$i]['id'], 'integer');
      $attributes = $db->bindVars($attributes, ':optionsID', $option, 'integer');
      $attributes = $db->bindVars($attributes, ':optionsValuesID', $value, 'integer');
      $attributes = $db->bindVars($attributes, ':languageID', $_SESSION['languages_id'], 'integer');
      $attributes_values = $db->Execute($attributes);
      //clr 030714 determine if attribute is a text attribute and assign to $attr_value temporarily
      if ($value == PRODUCTS_OPTIONS_VALUES_TEXT_ID) {
        $attributeHiddenField .= zen_draw_hidden_field('id[' . $products[$i]['id'] . '][' . TEXT_PREFIX . $option . ']',  $products[$i]['attributes_values'][$option]);
        $attr_value = htmlspecialchars($products[$i]['attributes_values'][$option], ENT_COMPAT, CHARSET, TRUE);
      } else {
        $attributeHiddenField .= zen_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
        $attr_value = $attributes_values->fields['products_options_values_name'];
      }

      $attrArray[$option]['products_options_name'] = $attributes_values->fields['products_options_name'];
      $attrArray[$option]['options_values_id'] = $value;
      $attrArray[$option]['products_options_values_name'] = $attr_value;
      $attrArray[$option]['options_values_price'] = $attributes_values->fields['options_values_price'];
      $attrArray[$option]['price_prefix'] = $attributes_values->fields['price_prefix'];
    }
  } //end foreach [attributes]
  // Stock Check
  if (STOCK_CHECK == 'true') {
    $qtyAvailable = zen_get_products_stock($products[$i]['id']);
    // compare against product inventory, and against mixed=YES
    if ($qtyAvailable - $products[$i]['quantity'] < 0 || $qtyAvailable - $_SESSION['cart']->in_cart_mixed($products[$i]['id']) < 0) {
        $flagStockCheck = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
      $flagAnyOutOfStock = true;
    }
  }
  $linkProductsImage = zen_href_link(zen_get_info_page($products[$i]['id']), 'products_id=' . $products[$i]['id']);
  $linkProductsName = zen_href_link(zen_get_info_page($products[$i]['id']), 'products_id=' . $products[$i]['id']);
  $productsImage = (IMAGE_SHOPPING_CART_STATUS == 1 ? zen_image(DIR_WS_IMAGES . $products[$i]['image'], $products[$i]['name'], IMAGE_SHOPPING_CART_WIDTH, IMAGE_SHOPPING_CART_HEIGHT) : '');
  $show_products_quantity_max = zen_get_products_quantity_order_max($products[$i]['id']);
  $showFixedQuantity = (($show_products_quantity_max == 1 or zen_get_products_qty_box_status($products[$i]['id']) == 0) ? true : false);
  $showFixedQuantityAmount = $products[$i]['quantity'] . zen_draw_hidden_field('cart_quantity[]', $products[$i]['quantity']);
  $showMinUnits = zen_get_products_quantity_min_units_display($products[$i]['id']);
  $quantityField = zen_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4" class="cart_input_'.$products[$i]['id'].'"');
  $ppe = $products[$i]['final_price'];
  $ppe = zen_round(zen_add_tax($ppe, zen_get_tax_rate($products[$i]['tax_class_id'])), $currencies->get_decimal_places($_SESSION['currency']));
  $ppt = $ppe * $products[$i]['quantity'];
  $productsPriceEach = $currencies->format($ppe) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
  $productsPriceTotal = $currencies->format($ppt) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
  $buttonUpdate = ((SHOW_SHOPPING_CART_UPDATE == 1 or SHOW_SHOPPING_CART_UPDATE == 3) ? zen_image_submit(ICON_IMAGE_UPDATE, ICON_UPDATE_ALT) : '') . zen_draw_hidden_field('products_id[]', $products[$i]['id']);
  $productArray[$i] = array('attributeHiddenField'=>$attributeHiddenField,
                            'flagStockCheck'=>$flagStockCheck,
                            'flagShowFixedQuantity'=>$showFixedQuantity,
                            'linkProductsImage'=>$linkProductsImage,
                            'linkProductsName'=>$linkProductsName,
                            'productsImage'=>$productsImage,
                            'productsName'=>$productsName,
                            'showFixedQuantity'=>$showFixedQuantity,
                            'showFixedQuantityAmount'=>$showFixedQuantityAmount,
                            'showMinUnits'=>$showMinUnits,
                            'quantityField'=>$quantityField,
                            'buttonUpdate'=>$buttonUpdate,
                            'productsPrice'=>$productsPriceTotal,
                            'productsPriceEach'=>$productsPriceEach,
                            'rowClass'=>$rowClass,
                            'buttonDelete'=>$buttonDelete,
                            'checkBoxDelete'=>$checkBoxDelete,
                            'id'=>$products[$i]['id'],
			    'carrot'=>$products[$i]['carrot'],
                            'attributes'=>$attrArray);
} // end FOR loop

$cart = $_SESSION['cart'];
$num_in_cart = $_SESSION['cart']->show_total();
$products = $_SESSION['cart']->get_products();
$gift =  $db->Execute("SELECT fg.*, p.products_id, p.products_model, p.products_price, p.products_image, p.products_status, pd.products_name FROM (" . TABLE_CARROT . " fg, " . TABLE_PRODUCTS . "  p)
LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (pd.products_id=fg.products_id)
WHERE pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
AND p.products_id = fg.products_id
ORDER BY fg.threshold ASC");
$threshold = 0;
$p=0;
$gift_price=0;
$gift_exists=0;

while (!$gift->EOF) {  // loop through the current gifts

if ($gift_exists == 0){
for ($i=0, $n=count($products); $i<$n; $i++) {
	   if ($products[$i]['id'] == $gift->fields['products_id']) { // gift already in cart
     $gift_exists = $gift->fields['products_id'];
 
      $gift_price = $gift->fields['products_price'];
      $deficit = $gift->fields['threshold'] - $num_in_cart + $gift_price;
	  
      break;
	  
    	} else {
      $deficit = $gift->fields['threshold'] - $num_in_cart;
	  }
	  }
	} else {
    $deficit = $gift->fields['threshold'] - $num_in_cart + $gift_price;
  }
  
  if ( $deficit < 20 && $deficit > 0 ) {
    $near_limit = 1;
  } else {
    $near_limit = 0;
  }
  if ($products[$i]['id'] == $gift->fields['products_id'] && $num_in_cart < $gift->fields['threshold']){ 
  	
  	
  	$cart->remove($gift->fields['products_id']); 
  	$messageStack->add_session('header', CAUTION_FREE_GIFT_REMOVED, 'caution');
  	zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
  	}
  if ($num_in_cart >= $gift->fields['threshold'] && $deficit <= 0) {
    // cart could qualify for this gift
    // check to see if in cart already
    // add to gift list if not in cart
   if ($gift->fields['products_id'] != $gift_exists && $deficit <= 0) { // this particular gift is not in cart but qualifies
      $freebie[$p]['message'] .= sprintf(TEXT_QUALIFIED_FOR_GIFT, $currencies->display_price($gift->fields['threshold'],zen_get_tax_rate($gift->fields['products_tax_class_id'])));
      $freebie[$p]['link'] = '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $gift->fields['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_ADD_TO_CART, BUTTON_ADD_TO_CART_ALT, 'class="listingBuyNowButton"' . $gift->fields['products_id'] ) . '</a>';
      $freebie[$p]['name']    		= $gift->fields['products_name'];
      $freebie[$p]['id']      		= $gift->fields['products_id'];
      $freebie[$p]['image']   		= $gift->fields['products_image'];
      $p++;
	  }
 } elseif ($near_limit) {
 
 if ($gift->fields['products_id'] != $gift_exists) { // this particular gift is not in cart
 $freebie[$p]['message'] .= sprintf(TEXT_CLOSE_TO_FREE_GIFT, $currencies->display_price($deficit,zen_get_tax_rate($gift->fields['products_tax_class_id'])));
$freebie[$p]['link'] = '';
        $freebie[$p]['name']    		= $gift->fields['products_name'];
        $freebie[$p]['id']      		= $gift->fields['products_id'];
        $freebie[$p]['image']   		= $gift->fields['products_image'];
        $p++;
} else {
        // cart cannot qualify for this gift
        // remove if in cart
        $cart->remove($gift->fields['products_id']);
      }
}
 $gift->MoveNext();
}
// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_SHOPPING_CART');