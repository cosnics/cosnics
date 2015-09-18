<?php
require_once __DIR__ . '/../../TestBootstrap.php';
error_reporting(E_ALL & ~ E_DEPRECATED); // needed because of old libraries
require_once $argv[1];
