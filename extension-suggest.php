<?php

require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";

require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/resources/utils.php";

if(isset($_GET['q'])) {
  $extension = $_GET['q'];
  $domain = "";
  if(strpos($_GET['q'], '@')) {
    list($extension,$domain) = explode("@", $_GET['q'], 2);
  }

  $results = do_sql($db, "SELECT v_extensions.extension_uuid,v_extensions.extension,v_domains.domain_name FROM v_extensions FULL JOIN v_domains ON v_extensions.domain_uuid = v_domains.domain_uuid WHERE (v_extensions.extension LIKE :extension AND v_domains.domain_name LIKE :domain) OR (v_extensions.extension_uuid = :query) LIMIT 30;", array(
    ':extension' => "%".$extension."%",
    ':domain' => "%".$domain."%",
    ':query' => $_GET['q']
  ));
  header('Conte-Type: application/json');
  echo json_encode($results);
} else {
  die("Specify a query, dumbass");
}
