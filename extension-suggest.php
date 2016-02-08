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
  $results = do_sql($db, "SELECT v_extensions.extension_uuid,v_extensions.extension,v_extensions.number_alias,v_extensions.accountcode,v_extensions.enabled,v_extensions.description,v_domains.domain_name,v_domains.domain_enabled FROM v_extensions FULL JOIN v_domains ON v_extensions.domain_uuid = v_domains.domain_uuid WHERE v_extensions.extension LIKE (%:extension% OR v_extensions.number_alias LIKE %:number_alias%) AND v_domains.domain_name LIKE %:domain%;", array(
    ':extension' => $extension,
    ':number_alias' => $extension,
    ':domain' => $domain
  ));
  echo json_encode($results);
} else {
  die("Specify a query, dumbass");
}
