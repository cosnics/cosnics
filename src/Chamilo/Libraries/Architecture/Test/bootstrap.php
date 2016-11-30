<?php

/**
 * Bootstrap for test file Require config.inc.php and set the include path to the original include path.
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
$original_include_path = get_include_path();

require_once __DIR__ . '/../Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap::getInstance()->setup();

ini_set('include_path', get_include_path() . PATH_SEPARATOR . $original_include_path);
