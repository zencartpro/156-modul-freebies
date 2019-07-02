#############################################################################################
# Freebies 2.0.0 Uninstall - 2017-11-08 - webchills
# NUR AUSFÜHREN FALLS SIE DAS MODUL VOLLSTÄNDIG ENTFERNEN WOLLEN!!!
#############################################################################################

ALTER TABLE products DROP products_carrot;
ALTER TABLE customers_basket DROP carrot;
DELETE FROM configuration_group WHERE configuration_group_title = 'Free Gifts';
DELETE FROM configuration WHERE configuration_key = 'FREEGIFTS_MODUL_VERSION';
DELETE FROM configuration WHERE configuration_key = 'GIFTS_IMAGE_WIDTH';
DELETE FROM configuration WHERE configuration_key = 'GIFTS_IMAGE_HEIGHT';
DELETE FROM configuration_language WHERE configuration_key = 'GIFTS_IMAGE_WIDTH';
DELETE FROM configuration_language WHERE configuration_key = 'GIFTS_IMAGE_HEIGHT';
DELETE FROM admin_pages WHERE page_key='toolsFreeGifts';