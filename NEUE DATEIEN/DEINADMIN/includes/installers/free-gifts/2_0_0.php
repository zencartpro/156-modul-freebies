<?php
/**
 * @package Free Gifts
 * @copyright Copyright 2003-2017 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart-pro.at/license/2_0.txt GNU Public License V2.0
 * @version $Id: 2_0_0.php 2017-11-05 18:13:51Z webchills $
 */
 

// add image configuration
$db->Execute("INSERT IGNORE INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES
('Gifts Image Width', 'GIFTS_IMAGE_WIDTH', '150', 'The pixel width of free gift products images on the shopping cart page', 4, 800, NOW(), NOW(), NULL, NULL),
('Gifts Image Height', 'GIFTS_IMAGE_HEIGHT', '150', 'The pixel height of free gift products images on the shopping cart page', 4, 801, NOW(), NOW(), NULL, NULL)");

// add image configuration german
$db->Execute("REPLACE INTO ".TABLE_CONFIGURATION_LANGUAGE." (configuration_title, configuration_key, configuration_description, configuration_language_id) VALUES
('Freebie Artikel Bildbreite', 'GIFTS_IMAGE_WIDTH', 'Breite des Bildes für Freebie Artikel auf der Warenkorbseite in Pixel', 43),
('Freebie Artikel Bildhöhe', 'GIFTS_IMAGE_HEIGHT', 'Höhe des Bildes für Freebie Artikel auf der Warenkorbseite in Pixel', 43)");

//check if products_carrot column already exists - if not add it
    $sql ="SHOW COLUMNS FROM ".TABLE_PRODUCTS." LIKE 'products_carrot'";
    $result = $db->Execute($sql);
    if(!$result->RecordCount())
    {
        $sql = "ALTER TABLE ".TABLE_PRODUCTS." ADD products_carrot tinyint(1) default 0 AFTER products_status";
        $db->Execute($sql);
    }
    
//check if carrot column already exists - if not add it
    $sql ="SHOW COLUMNS FROM ".TABLE_CUSTOMERS_BASKET." LIKE 'carrot'";
    $result = $db->Execute($sql);
    if(!$result->RecordCount())
    {
        $sql = "ALTER TABLE ".TABLE_CUSTOMERS_BASKET." ADD carrot tinyint(1) default 0 AFTER customers_basket_date_added";
        $db->Execute($sql);
    }

// create new table
$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_CARROT . " (
`gift_id` tinyint(4) NOT NULL auto_increment,
`threshold` mediumint(9) NOT NULL default '0',
`products_id` mediumint(9) NOT NULL default '0',
PRIMARY KEY  (`gift_id`)  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

// add tools menu
if (!zen_page_key_exists($admin_page)) {
$db->Execute("INSERT IGNORE INTO " . TABLE_ADMIN_PAGES . " (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES 
('toolsFreeGifts','BOX_TOOLS_FREEGIFTS','FILENAME_FREE_GIFTS','','tools','Y',800)");
$messageStack->add('Freebies Administration erfolgreich hinzugefügt.', 'success');  
}