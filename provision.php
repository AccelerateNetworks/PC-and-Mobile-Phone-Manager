<?php
require_once "root.php";
require_once "resources/require.php";
require_once __DIR__."/resources/utils.php";

if(isset($_GET['secret'])) {
  $token = do_sql("SELECT * FROM better_provisioning_tokens WHERE secret = :secret", array(":secret" => $_GET['secret']));
  if(count($token) > 0) {
    $extension = do_sql("SELECT * FROM v_extensions WHERE extension_uuid = :extension_uuid", array(':extension_uuid' => $token[0]['extension']));
    if(file_exists(__DIR__."/provisioning/".$token[0]['type']."/main.php")){
      require(__DIR__."/provisioning/".$token[0]['type']."/main.php");
      do_provision($extension[0], $token[0]);
    }
  }
} else {
  $oui_prefixes = array("000b82" => "grandstream");
  $mac_regex;
  preg_match("/\/cfg(?<mac>[a-z0-9]{12})\.xml/", $_SERVER['QUERY_STRING'], $mac_regex);
  if(isset($mac_regex['mac'])) {
    $prefix = substr($mac_regex['mac'], 0, 6);
    if(array_key_exists($prefix, $oui_prefixes)) {
      $type = $oui_prefix[$prefix];
      require(__DIR__."/provisioning/".$type."/main.php");
      do_initial_provision($mac_regex['mac']);
    } else {
      die("OUI Prefix ".$prefix." not recognized!");
    }
  } else {
    die("Please specify a secret!");
  }
}
