<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Utilities\StringUtilities;

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
        $request = $this->getRequest();
        $application = $request->request->get('application');
        $string = $request->request->get('string');

        $string = (string) $this->getStringUtilities()->createString($string)->upperCamelize();

        if ($application && $application != 'undefined')
        {
            $namespace = $application;
        }
        else
        {
            $namespace = StringUtilities::LIBRARIES;
        }

        echo $this->getTranslator()->trans($string, [], $namespace);
    }
}