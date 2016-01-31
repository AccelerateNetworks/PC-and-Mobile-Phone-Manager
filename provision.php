<?php
require_once "root.php";
require_once "resources/require.php";
require_once __DIR__."/resources/utils.php";

if(isset($_GET['secret'])) {
  $token = do_sql($db, "SELECT * FROM better_provisioning_tokens WHERE secert = :secret", array(":secret" => $_GET['secret']));
  if(count($token) > 0) {
    $token = $token[0];
    $extension = do_sql($db, "SELECT * FROM v_extensions WHERE extension_uuid = :extension_uuid", array(':extension_uuid' => $token['extension_uuid']));
    if(file_exists(__DIR__."/provisioning/".$token['type']."/main.php")){
      require(__DIR__."/provisioning/".$token['type']."/main.php");
    }
  }
}
