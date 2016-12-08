<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class TranslationComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {
        $application = $_POST['application'];
        $string = $_POST['string'];
        
        $string = (string) StringUtilities::getInstance()->createString($string)->upperCamelize();
        
        if ($application && $application != 'undefined')
        {
            $namespace = $application;
        }
        else
        {
            $namespace = Utilities::COMMON_LIBRARIES;
        }
        
        echo Translation::get($string, null, $namespace);
    }
}