<?php
function sanatize($input, $type="string") {
  switch($type) {
    case "usd":
      return "$".number_format(floatval($input), 2);
    case "int":
      return intval($input);
    case "string":
      return htmlspecialchars($input);   // further sanatization to come...
      break;
    case "interval":
      $interval = new DateInterval($input);
      $result = array();
      if ($interval->y) { $result[] = $interval->format("%y year".pluralize($interval->y)); }
      if ($interval->m) { $result[] = $interval->format("%m month".pluralize($interval->m)); }
      if ($interval->d) { $result[] = $interval->format("%d day".pluralize($interval->y)); }
      if ($interval->h) { $result[] = $interval->format("%h hour".pluralize($interval->h)); }
      if ($interval->i) { $result[] = $interval->format("%i minute".pluralize($interval->i)); }
      if ($interval->s) { $result[] = $interval->format("%s second".pluralize($interval->s)); }
      return implode(", ", $result);
      break;
    default:
      return "Unknown sanatization type ".sanatize($type);
      break;
  }
}


function render_frequency_picker($frequency, $name="frequency", $disabled=False) {
  $parts = array(
    "y" => "years",
    "m" => "months",
    "d" => "days",
    "h" => "hours",
    "i" => "minutes"
  );
  $output = array();
  foreach($parts as $key=>$part) {
    if($disabled) {
      $output[] = "<b>".sanatize($frequency->$key, "int")."</b> $part";
    } else {
      $output[] = "<input type=\"text\" name=\"".$name."[".$key."]\" value=\"".sanatize($frequency->$key, "int")."\" size=\"1\"/> $part";
    }
  }
  echo implode(", ", $output)." <!-- because php -->";
}

function parse_frequency_picker($input) {
  $output = new DateInterval("PT0S");
  foreach($input as $key=>$value) {
    $output->$key = $value;
  }
  return $output;
}

function pluralize($i) {
  if($i != 1) {
    return "s";
  } else {
    return "";
  }
}

function generate_domain_picker($db, $name='domain_uuid', $default="") {
  $sql = ("SELECT domain_uuid,domain_name FROM v_domains");
  $statement = $db->prepare($sql);
  if($statement) {
    echo "<select name=\"$name\">\n";
    $result = $statement->execute();
    if(!$result) {
      error_log($statement->errorInfo()[2]);
    }
    while($row = $statement->fetch()) {
      $selected = "";
      if($default == $row['domain_uuid']) {
        $selected = " selected";
      }
      echo "<option value=\"".sanatize($row['domain_uuid'])."\"$selected>".sanatize($row['domain_name']);
    }
    echo "</select>";
  } else {
    error_log($db->errorInfo()[2]);
    echo "<b>Failed to execute SQL to make domain picker! SQL: <code>$sql</code>";
  }
}

function upsert_bill_item($db, $description, $quantity, $unit_price, $domain_to_bill=NULL, $item_uuid=NULL, $source=NULL, $invoice=NULL) {
  $sql = "UPDATE bill_item SET domain_uuid = :domain_uuid, description = :description, quantity = :quantity, unit_price = :unit_price, source = :source, invoice = :invoice WHERE item_uuid = :item_uuid";
  if($item_uuid == NULL) {
    $item_uuid = uuid();
    $sql = "INSERT INTO bill_item (item_uuid,domain_uuid,description,quantity,unit_price,source,timestamp,invoice) VALUES ";
    $sql .= "(:item_uuid, :domain_uuid, :description, :quantity, :unit_price, :source, NOW(), :invoice);";
  }
  $statement = $db->prepare(check_sql($sql));
  if ($statement) {
    $statement_result = $statement->execute(array(
      ':item_uuid' => $item_uuid,
      ':domain_uuid' => $domain_to_bill,
      ':description' => $description,
      ':quantity' => intval($quantity),
      ':unit_price' => floatval($unit_price),
      ':source' => $source,
      ':invoice' => $invoice
    ));
    if($statement_result) {
      return $item_uuid;
    } else {
      $err = $statement->errorInfo()[2];
      throw new Exception("Failed to execute statement: ".$err);
    }
  } else {
    $err = $db->errorInfo()[2];
    throw new Exception("Failed to prepare statement: ".$err);
  }
}


function upsert_recurring_item($db, $description, $quantity, $unit_price, $frequency, $domain_to_bill=NULL, $item_uuid=NULL) {
  $sql = "UPDATE recurring_bill SET domain_uuid = :domain_uuid, description = :description, quantity = :quantity, unit_price = :unit_price, frequency = :frequency WHERE item_uuid = :item_uuid";
  if($item_uuid == NULL) {
    $item_uuid = uuid();
    $sql = "INSERT INTO recurring_bill (item_uuid,domain_uuid,description,quantity,unit_price,frequency) VALUES ";
    $sql .= "(:item_uuid, :domain_uuid, :description, :quantity, :unit_price, :frequency);";
  }
  $statement = $db->prepare(check_sql($sql));
  if ($statement) {
    $statement_result = $statement->execute(array(
      ':item_uuid' => $item_uuid,
      ':domain_uuid' => $domain_to_bill,
      ':description' => $description,
      ':quantity' => intval($quantity),
      ':unit_price' => floatval($unit_price),
      ':frequency' => $frequency->format("P%yY%mM%dDT%hH%iM%sS")
    ));
    if($statement_result) {
      return $item_uuid;
    } else {
      $err = $statement->errorInfo();
      throw new Exception("Failed to execute statement: ".$err[2]." (<code>".$err[0]."</code>)");
    }
  } else {
    $err = $db->errorInfo()[2];
    throw new Exception("Failed to prepare statement: ".$err);
  }
}

function append_to_crontab($line, $check=True) {
  $output;
  $exit_value;
  $current_crontab;

  exec('crontab -l', $current_crontab);

  $tempfile = tempnam("/tmp", "cron");
  $fp = fopen($tempfile, "w");
  $current_crontab[] = $line;
  fwrite($fp, implode("\n", $current_crontab)."\n");
  fclose($fp);

  $command = "crontab $tempfile";
  exec($command, $output, $exit_value);

  if($exit_value == 0) {
    return array("success" => True, "result" => "Successfully installed: ".implode("\\n", $output));
  } else {
    return array("success" => False, "result" => "Failed to run ".$command);
  }
}

function check_cron($line) {
  $current_crontab;
  $current_crontab_exit_value;
  $check_passed = False;
  $result = "Unknown error occured while checking for the existance of our cronjob";
  exec('crontab -l', $current_crontab, $current_crontab_exit_value);
  if($current_crontab_exit_value == 0) {
    foreach($current_crontab as $cronline) {
      if($cronline == $line) {
        $check_passed = True;
        $result = "Our cronjob is installed";
      } else {
        error_log($crontline);
      }
    }
    if(!$check_passed) {
      $result = "Some cronjobs are installed, but ours is not one of them";
    }
  } else {
    $result = "crontab exited with a non-zero status. This probably means no cronjobs have been installed";
  }
  return array("success" => $check_passed, "result" => $result);
}

function install_cron($test=False) {
  $line = "*/5 * * * * cd ".$_SERVER['DOCUMENT_ROOT']." && php ".__DIR__."/cron.php";
  if($test) {
    return check_cron($line);
  } else {
    return append_to_crontab($line);
  }
}

// executes an SQL query
function do_sql($query, $args=array()) {
  global $db;
  $statement = $db->prepare(check_sql($query));
  if($statement) {
    $result = $statement->execute($args);
    if($result) {
      $out = [];
      while($row = $statement->fetch()) {
        $out[] = $row;
      }
      return $out;
    } else {
      die("Failed to execute SQL statement <code>$query</code>! SQLSTATE: ".$statement->errorInfo()[0].", <b><code>Error ".$statement->errorInfo()[1].": ".$statement->errorInfo()[2]."</code></b>");
    }
  } else {
    die("Failed to prepare the SQL statement <code>$query</code>! <b><code>".$db->errorInfo()[2]."</code></b>");
  }
}

function check_xml_cdr_db($db) {
  $sql = "SELECT column_name FROM information_schema.columns WHERE table_name='v_xml_cdr' and (column_name='call_sell' or column_name='call_buy' or column_name='carrier_name');";
  $result = $db->query($sql);
  if($result->rowCount() == 3) {
    return array("success"=>true, "result"=>"Column 'call_sell' found in 'v_xml_cdr'");
  } else {
    return array("success"=>false, "result" => "No column 'call_sell' found in 'v_xml_cdr'");
  }
}

function fix_xml_cdr_db($db) {
  $queries = array();
  $queries[] = "ALTER TABLE v_xml_cdr ADD COLUMN call_sell int default 0;";
  $queries[] = "ALTER TABLE v_xml_cdr ADD COLUMN call_buy int default 0;";
  $queries[] = "ALTER TABLE v_xml_cdr ADD COLUMN carrier_name int default 0;";
  $success = NULL;
  foreach($queries as $query) {
    if($success == NULL || $success = true) {
      $success = $db->query($query);
    }
  }
  return $success;
}
