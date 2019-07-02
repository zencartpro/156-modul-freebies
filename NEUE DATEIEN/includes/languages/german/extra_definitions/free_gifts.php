<?php 
define('BOX_HEADING_GIFTS', 'Geschenke');  
define('WARENKORB_FREEBIES', 'Freebies');  
define('BUTTON_RETURN_TO_CART', 'button_return_to_cart.gif');
define('BUTTON_RETURN_TO_CART_ALT', 'zurück zum Warenkorb');
define('TEXT_REMOVE_GIFT', '<font color="red">Bitte erst andere Geschenke aus Ihrem Warenkorb entfernen</font>');
define('TEXT_MAX_1_GIFT', '(Maximal 1 Geschenk pro Bestellung)');
define('TEXT_QUALIFIED_FOR_GIFT', 'Wenn Sie um mehr als %s bestellen, können Sie sich folgendes Geschenk gratis aussuchen!');
define('TEXT_CLOSE_TO_FREE_GIFT', '<font color="red">Bestellen Sie nur um %s mehr um sich folgendes Geschenk aussuchen zu können ...</font>');
define('TEXT_GIFT_IN_CART','Ihr gewähltes Geschenk');
define('CAUTION_FREE_GIFT_REMOVED','Ihr gewähltes Geschenk ist für den aktuellen Bestellwert nicht mehr gültig und wurde aus dem Warenkorb entfernt. Bitte ein neues Geschenk aussuchen.');
//fill variables to output required results
$gift = $db->Execute("select p.products_tax_class_id, p.products_carrot, p.products_price from " . TABLE_PRODUCTS . " p where p.products_id = '" . (int)$_GET['products_id'] . "' ");
if ($gift->fields['products_carrot'] == 1) {
$product_gift = $db->Execute("select g.gift_id, g.threshold, g.products_id from " . TABLE_CARROT . " g where g.products_id = '" . (int)$_GET['products_id'] . "'");
}
define('TEXT_FREE_GIFT', 'Kostenloses Geschenk mit einem ' . $currencies->display_price($product_gift->fields['threshold'], zen_get_tax_rate($gift->fields['products_tax_class_id'])) . ' Einkauf.');
define('TEXT_DISCOUNT_GIFT', 'Nur ' . $currencies->display_price($gift->fields['products_price'], zen_get_tax_rate($gift->fields['products_tax_class_id'])) . ' mit ' . $currencies->display_price($product_gift->fields['threshold'], zen_get_tax_rate($gift->fields['products_tax_class_id'])) . ' Einkauf.');