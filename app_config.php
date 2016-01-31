<?php
//application details
$apps[$x]['name'] = "Better Provisioning";
$apps[$x]['uuid'] = "ceb64794-b6f6-4be5-a65c-a30d2a32cda4";
$apps[$x]['category'] = "App";
$apps[$x]['subcategory'] = "";
$apps[$x]['version'] = "0.1";
$apps[$x]['license'] = "GNU General Public License v3";
$apps[$x]['url'] = "https://git.callpipe.com/fusiobpbx/better-provisioning";
$apps[$x]['description']['en-us'] = "CallPipe Provisioning App";
$apps[$x]['description']['es-cl'] = "";
$apps[$x]['description']['de-de'] = "";
$apps[$x]['description']['de-ch'] = "";
$apps[$x]['description']['de-at'] = "";
$apps[$x]['description']['fr-fr'] = "";
$apps[$x]['description']['fr-ca'] = "";
$apps[$x]['description']['fr-ch'] = "";
$apps[$x]['description']['pt-pt'] = "";
$apps[$x]['description']['pt-br'] = "";

$y = 0;
$z = 0;
$apps[$x]['db'][$y]['table'] = "better_provisioning_tokens";
$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "token_uuid";
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "primary";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "The unique, secret token";

$z++;
$apps[$x]['db'][$y]['fields'][$z]['name'] = "secret";
$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "The secret required to get this token";


$z++;
$apps[$x]['db'][$y]['fields'][$z]['name'] = "extension";
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_extensions";
$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "extension_uuid";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "The extension this token is associated with";

$z++;
$apps[$x]['db'][$y]['fields'][$z]['name'] = "type";
$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "The type of provisioning this is";
