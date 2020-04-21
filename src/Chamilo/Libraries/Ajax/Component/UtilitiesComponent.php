<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UtilitiesComponent extends Manager
{
    const PARAM_ACTION = 'action';
    const PARAM_CONTEXT = 'context';
    const PARAM_PARAMETERS = 'parameters';
    const PARAM_PATH = 'path';
    const PARAM_STRING = 'string';
    const PARAM_TYPE = 'type';
    const PARAM_VALUE = 'value';
    const PARAM_VARIABLE = 'variable';

    const PROPERTY_RESULT = 'result';

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $type = $this->getPostDataValue(self::PARAM_TYPE);

        $properties = array();

        switch ($type)
        {
            // Retrieve platform paths
            case 'path' :
                if (Request::post(self::PARAM_PATH) != 'WEB_PATH')
                {
                    throw new Exception('Invalid Path parameter: ' . Request::post(self::PARAM_PATH));
                }

                $properties[self::PROPERTY_RESULT] = Path::getInstance()->getBasePath(true);
                break;

            // Retrieve the current theme
            case 'theme' :
                $properties[self::PROPERTY_RESULT] = $this->getThemePathBuilder()->getTheme();
                break;

            // Get a translation
            case 'translation' :
                $context = Request::post(self::PARAM_CONTEXT);
                $string = Request::post(self::PARAM_STRING);
                $parameters = Request::post(self::PARAM_PARAMETERS);

                $string = (string) StringUtilities::getInstance()->createString($string)->upperCamelize();
                $properties[self::PROPERTY_RESULT] = Translation::get($string, $parameters, $context);
                break;

            // Get, set or clear a session variable
            case 'memory' :
                $action = Request::post(self::PARAM_ACTION);

                switch ($action)
                {
                    case 'set' :
                        Session::register(Request::post(self::PARAM_VARIABLE), Request::post(self::PARAM_VALUE));
                        break;
                    case 'clear' :
                        Session::unregister(Request::post(self::PARAM_VARIABLE));
                        break;
                    case 'get' :
                    default :
                        $properties[self::PROPERTY_RESULT] = Session::retrieve(Request::post(self::PARAM_VARIABLE));
                        break;
                }
                break;
            case 'platform_setting' :
                $properties[self::PROPERTY_RESULT] = Configuration::get(
                    Request::post(self::PARAM_CONTEXT), Request::post(self::PARAM_VARIABLE)
                );
                break;
        }

        $result = new JsonAjaxResult(200);
        $result->set_properties($properties);
        $result->display();
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_TYPE);
    }
}
