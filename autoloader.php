<?php
require_once('api/src/abstract/activeRecord.php');
require_once('api/src/util/db.php');

$files = glob('api/src/model/*.php');
foreach ($files as $file) {
  require_once($file);
}

