<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator;

use Chamilo\Libraries\File\Path;

/**
 * Settings for dataclass generator
 */
// $application['location'] = Path :: getInstance()->getBasePath() .
// 'common/libraries/php/util/application_generator/examples/linker/';
// $application['name'] = 'linker';
$application['location'] = Path::getInstance()->getBasePath() . 'application/test/';
$application['name'] = 'test';

$application['author'] = '';
$application['options']['link']['table'] = 1;
