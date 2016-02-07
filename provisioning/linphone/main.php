<?php
function do_provision($extension, $token, $db) {
  if(!isset($_GET['noredirect'])) {
    $redirect_uri = "linphone-config://";
    $redirect_uri .= "http";
    if($_SERVER['HTTPS'] != "") {
      $redirect_uri .= "s";
    }
    $redirect_uri .= "%3A//".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $redirect_uri .= "?secret=".$_GET['secret']."&noredirect=";
    header("Location: $redirect_uri");
    echo "<h1>We're provisioning Linphone, sit tight...";
  } else {
    require_once "resources/templates/engine/smarty/Smarty.class.php";
    $smarty = new Smarty();
    $smarty->assign("extension", $extension);
    $smarty->assign("token", $token);
    if($_GET['noredirect'] == "debug") {
      $smarty->debugging = true;
    }
    $smarty->display(__DIR__."/linphone-config.xml");
  }
}
