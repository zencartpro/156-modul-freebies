<?php 
define('BOX_HEADING_GIFTS', 'Special Gifts');  
define('WARENKORB_FREEBIES', 'Freebies');  
define('BUTTON_RETURN_TO_CART', 'button_return_to_cart.gif');
define('TEXT_REMOVE_GIFT', '<font color="red">Please remove other gifts/offers from your cart first</font>');
define('TEXT_MAX_1_GIFT', '(Maximum 1 gift per order)');
define('TEXT_QUALIFIED_FOR_GIFT', 'By spending over %s you have qualified for the following gift!');
define('TEXT_CLOSE_TO_FREE_GIFT', '<font color="red">Spend just %s more to get ...</font>');
define('TEXT_GIFT_IN_CART','Your chosen gift');
define('CAUTION_FREE_GIFT_REMOVED','Your selected gift is not valid for the current purchase value and has been removed from your cart. Please chose a new gift.');
//fill variables to output required results
$gift = $db->Execute("select p.products_tax_class_id, p.products_carrot, p.products_price from " . TABLE_PRODUCTS . " p where p.products_id = '" . (int)$_GET['products_id'] . "' ");
if ($gift->fields['products_carrot'] == 1) {
$product_gift = $db->Execute("select g.gift_id, g.threshold, g.products_id from " . TABLE_CARROT . " g where g.products_id = '" . (int)$_GET['products_id'] . "'");
}
define('TEXT_FREE_GIFT', 'Free gift with ' . $currencies->display_price($product_gift->fields['threshold'], zen_get_tax_rate($gift->fields['products_tax_class_id'])) . ' purchase.');
define('TEXT_DISCOUNT_GIFT', 'Only ' . $currencies->display_price($gift->fields['products_price'], zen_get_tax_rate($gift->fields['products_tax_class_id'])) . ' with ' . $currencies->display_price($product_gift->fields['threshold'], zen_get_tax_rate($gift->fields['products_tax_class_id'])) . ' purchase.');