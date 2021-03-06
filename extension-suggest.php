<?php

require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";

require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/resources/utils.php";

if(isset($_GET['q'])) {
  $extension = $_GET['q'];
  $domain = "";
  $uuid = NULL;
  if(strpos($_GET['q'], '@')) {
    list($extension, $domain) = explode("@", $_GET['q'], 2);
  }
  if(preg_match("/[\da-f]{8}-?([\da-f]{4}-?){3}[\da-f]{12}/i", $_GET['q']) == 1) {
    $uuid = $_GET['q'];
  }

  $results = do_sql("SELECT v_extensions.extension_uuid,v_extensions.extension,v_domains.domain_name FROM v_extensions FULL JOIN v_domains ON v_extensions.domain_uuid = v_domains.domain_uuid WHERE (v_extensions.extension LIKE :extension AND v_domains.domain_name LIKE :domain) OR (v_extensions.extension_uuid = :uuid) LIMIT 30;", array(
    ':extension' => "%".$extension."%",
    ':domain' => "%".$domain."%",
    ':uuid' => $uuid
  ));
  header('Conte-Type: application/json');
  echo json_encode($results);
} else {
  die("Specify a query, dumbass");
}
