<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationComponent extends Manager
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
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