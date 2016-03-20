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
      do_provision($extension[0], $token[0], $db);
    }
  }
}
