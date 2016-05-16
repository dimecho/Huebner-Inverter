<?php

require('php_serial.class.php');
require('config.inc.php');
require('InverterTerminal.class.php');

header ("Content-Type: text/snapshot");
header ("Content-Disposition: attachment; filename=\"snapshot.txt\"");

$term = new InverterTerminal($serial);

$term->getValues($values);

foreach ($values as $key => &$value)
    $value = $value->value;

echo json_encode($values, JSON_PRETTY_PRINT);

?>