<?php
if(!isset($_GET['noredirect'])) {
  $redirect_uri = "linphone-config://";
  $redirect_uri .= "http";
  if($_SERVER['HTTPS'] != "") {
    $redirect_uri .= "s";
  }
  $redirect_uri .= "%3A//".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
  $redirect_uri .= "?token=".$_GET['token'];
  header("Location: $redirect_uri");
} else {
  require_once "resources/templates/engine/smarty/Smarty.class.php";
  $smarty = new Smarty();
  $smarty->assign("extension", $extension);
  $smarty->render("linphone-config.xml");
}
