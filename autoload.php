<?php

$files = glob('api/src/*.php');

foreach ($files as $file) {
  require_once($file);
}
