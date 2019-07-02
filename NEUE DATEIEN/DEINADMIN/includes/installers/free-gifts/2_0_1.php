<?php
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '2.0.1' WHERE configuration_key = 'FREEGIFTS_MODUL_VERSION' LIMIT 1;");