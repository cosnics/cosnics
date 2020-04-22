<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Path;
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
     * @throws \Exception
     */
    public function run()
    {
        $type = $this->getPostDataValue(self::PARAM_TYPE);
        $request = $this->getRequest();

        $properties = array();

        switch ($type)
        {
            // Retrieve platform paths
            case 'path' :
                if ($request->request->get(self::PARAM_PATH) != 'WEB_PATH')
                {
                    throw new Exception('Invalid Path parameter: ' . $request->request->get(self::PARAM_PATH));
                }

                $properties[self::PROPERTY_RESULT] = Path::getInstance()->getBasePath(true);
                break;

            // Retrieve the current theme
            case 'theme' :
                $properties[self::PROPERTY_RESULT] = $this->getThemePathBuilder()->getTheme();
                break;

            // Get a translation
            case 'translation' :
                $context = $request->request->get(self::PARAM_CONTEXT);
                $string = $request->request->get(self::PARAM_STRING);
                $parameters = (array) $request->request->get(self::PARAM_PARAMETERS);

                $string = (string) $this->getStringUtilities()->createString($string)->upperCamelize();
                $properties[self::PROPERTY_RESULT] = $this->getTranslator()->trans($string, $parameters, $context);
                break;

            // Get, set or clear a session variable
            case 'memory' :
                $action = $request->request->get(self::PARAM_ACTION);
                $sessionUtilities = $this->getSessionUtilities();

                switch ($action)
                {
                    case 'set' :
                        $sessionUtilities->register(
                            $request->request->get(self::PARAM_VARIABLE), $request->request->get(self::PARAM_VALUE)
                        );
                        break;
                    case 'clear' :
                        $sessionUtilities->unregister($request->request->get(self::PARAM_VARIABLE));
                        break;
                    case 'get' :
                    default :
                        $properties[self::PROPERTY_RESULT] =
                            $sessionUtilities->get($request->request->get(self::PARAM_VARIABLE));
                        break;
                }
                break;
            case 'platform_setting' :
                $properties[self::PROPERTY_RESULT] = $this->getConfigurationConsulter()->getSetting(
                    array(
                        $request->request->get(self::PARAM_CONTEXT),
                        $request->request->get(self::PARAM_VARIABLE)
                    )
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
