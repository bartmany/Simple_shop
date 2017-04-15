<?php

$files = glob('api/src/model/*.php');

foreach ($files as $file) {
  require_once($file);
}
