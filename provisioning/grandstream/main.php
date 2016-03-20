<?php
require_once __DIR__."/../../resources/utils.php";
require_once __DIR__."/../../vendor/autoload.php";
require_once "resources/templates/engine/smarty/Smarty.class.php";
use IcyApril\CryptoLib;

function do_provision($extension, $token) {
  $method = "AES-256-CBC"; // Grandstream wants AES-256-CBC
  openssl_encrypt($data, $method, $password);
}

function do_initial_provision($mac) {
  // check for pre-existing shared secret
  $existing = do_sql("SELECT * FROM better_provisioning_tokens WHERE mac = :mac", array(':mac' => $mac));
  if(count($existing) > 0) {
    http_response_code(401);
    echo "Unauthorized";
    // really, we should be doing normal provisioning, despite lack of secret. We assume you can't break aes-256-cbc
  } else {
    $token = uuid();
    $secret = bin2hex(openssl_random_pseudo_bytes(16));
    $adminpw = CryptoLib::randomString(10);
    $userpw = CryptoLib::randomString(10);
    do_sql("INSERT INTO better_provisioning_tokens (token_uuid, secret, mac, admin_pw, user_pw) VALUES (:token, :secret, :mac, :adminpw, :userpw)",
           array(':token' => $token, ':secret' => $secret, ':mac' => $mac, ':adminpw' => $adminpw, ':userpw' => $userpw));
    $smarty = new Smarty();
    $smarty->assign("secret", $secret);
    $smarty->assign("adminpw", $adminpw);
    $smarty->assign("userpw", $userpw);
    $smarty->assign("provisioning_uri", $_SERVER['HTTP_HOST'].$_SERVER['DOCUMENT_URI']."?");
    $smarty->assign("firmware_uri", $_SERVER['HTTP_HOST'].$_SERVER['DOCUMENT_URI']."?");
    $smarty->display(__DIR__."/initialize_crypto.xml");
  }
}
