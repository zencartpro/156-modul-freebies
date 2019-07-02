<?php
/*
  Ported from osCommerce. Recognition to Jack York for his work on the original mod
  edited by: Jack York @ www.oscommerce-solution.com
 **
 * @package admim
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart-pro.at/license/2_0.txt GNU Public License V2.0
 * @version $Id: gift_add.php 2019-07-02 08:13:01 webchills
 */
 
  require('includes/application_top.php');
  if(isset($_SESSION['languages_id'])){
        $rl_language = $_SESSION['languages_id'];
    } else {
        $rl_language=43;
    }
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if (zen_not_null($action)) {  
    switch ($action) {
	
	case 'addGift':
	$id = '';
	  $action_gift = (isset($_GET['free_gifts']) ? $_GET['free_gifts'] : '');
      if (zen_not_null($action_gift))	{
	   $action_gift = zen_db_prepare_input($_GET['free_gifts']); 
	   $product_query = "select products_id, language_id, products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '$rl_language' and products_name = '" . $action_gift ."'";
        $productID = $db->Execute($product_query);
		$id = $productID->fields['products_id'];
       }
	 	 $threshold = zen_db_prepare_input($_GET['threshold']);
		
		if ($id && $threshold) {
	  		 $db->Execute("insert into " . TABLE_CARROT . " (threshold, products_id) values ('".$threshold."','".$id."')");
		} else {
			$message = '<font color="red">FORM ERROR</font>';
	}
		zen_redirect('gift_add.php');
		break;
	 						
	  case 'delete' :
	  	$id = $_GET['ID'];
		if ($id) {
		    $db->Execute("delete from " . TABLE_CARROT . " where products_id = '".$id."'");
		}
		zen_redirect('gift_add.php');
        break;
    }
  }
 
  $freeGifts = array();
  $sql = "select p.products_id, p.products_carrot, pd.products_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.language_id = '$rl_language'
			and p.products_id = pd.products_id and p.products_carrot = '1' order by pd.products_name ASC";
  $gift_list = $db->Execute($sql);
  while (!$gift_list->EOF) {
  $freeGifts[] = array('id' => $gift_list->fields['products_name'], 'text' => $gift_list->fields['products_name']);
  $gift_list ->MoveNext();
 } 
 
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script src="includes/menu.js"></script>
    <script src="includes/general.js"></script>

    <script>
      function init() {
          cssjsmenu('navbar');
          if (document.getElementById) {
              var kill = document.getElementById('hoverJS');
              kill.disabled = true;
          }
      }
    </script>
    
  </head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<div class="container-fluid">
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo TEXT_GIFT_HEADER; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
       
	   
	   
      <tr>
       <td><table border="0">
        <?php	
             $gift = $db->Execute("SELECT fg.*, p.products_id, p.products_model, p.products_price, p.products_image, p.products_status, pd.language_id, pd.products_name FROM (" . TABLE_CARROT . " fg, " . TABLE_PRODUCTS . " p)
LEFT JOIN  " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (pd.products_id=fg.products_id)
WHERE p.products_id = fg.products_id
AND pd.language_id = '$rl_language'
ORDER BY fg.threshold ASC");
			while (!$gift->EOF) {
			       
			echo '<form action="gift_add.php" method="GET"><tr>
			<td class="smallText"><input type="text" size="4" name="threshold" value="'.$gift->fields['threshold'].'"></td>
			
			<td class="smallText">'.$gift->fields['products_name'].'</td> 
			<td><a href="gift_add.php?ID=' . $gift->fields['products_id'] . '&action=delete">' . zen_image_button('button_delete.gif', 'Löschen') . '</a></td>
			</tr></form>';
			$gift->MoveNext();	
			}
			?>
		
     </table></td>
     </tr>
	  <tr><td height="30"></td></tr>		    
    <tr>
     <td><table border="0">     
  	  <tr><td colspan="4" class="pageHeading"><p><?php echo TEXT_NEW_GIFT_ADD; ?><br><?php echo $message; ?></td></tr>
  	  <tr><td colspan="4" class="smalltext"><p><?php echo TEXT_GIFT_ADD_CARROT; ?></td></tr>
	  		
	    <!-- next provide for additional free gifts to be added -->
      <form name="newGift" method="GET">
	    <tr>
	     <td class="smallText"> <input type="text" name="threshold" size="4"><?php echo ' ' . TEXT_THRESHOLD; ?></td>
       <td witdh="40">&nbsp;</td>
	     <td align="left"><?php echo zen_draw_pull_down_menu('free_gifts', $freeGifts, '', '', false);?></td>
	     <td class="smallText"><input type="hidden" name="action" value="addGift"></td>
	     <td class="smallText"><input type="submit" name="Submit" value=<?php echo '"' . TEXT_ADD . '"' ?>></td>
	    </tr>
	    <tr>
       <td colspan="4"></td>
      </tr>
	    </form>      
     </table></td>
    </tr> 
		
	 </table></td>   
	</tr>
 </table>
  <!-- body_text_eof //-->

      <!-- body_eof //-->
    </div>
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>