<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Exception;

/**
 * @package Chamilo\Libraries\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UtilitiesComponent extends Manager
{
    public const PARAM_ACTION = 'action';
    public const PARAM_CONTEXT = 'context';
    public const PARAM_PARAMETERS = 'parameters';
    public const PARAM_PATH = 'path';
    public const PARAM_STRING = 'string';
    public const PARAM_TYPE = 'type';
    public const PARAM_VALUE = 'value';
    public const PARAM_VARIABLE = 'variable';

    public const PROPERTY_RESULT = 'result';

    /**
     * @throws \Exception
     */
    public function run()
    {
        $type = $this->getPostDataValue(self::PARAM_TYPE);
        $request = $this->getRequest();

        $properties = [];

        switch ($type)
        {
            // Retrieve platform paths
            case 'path' :
                if ($request->request->get(self::PARAM_PATH) != 'WEB_PATH')
                {
                    throw new Exception('Invalid Path parameter: ' . $request->request->get(self::PARAM_PATH));
                }

                $properties[self::PROPERTY_RESULT] = $this->getWebPathBuilder()->getBasePath();
                break;

            // Retrieve the current theme
            case 'theme' :
                $properties[self::PROPERTY_RESULT] = $this->getThemeWebPathBuilder()->getTheme();
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
                $session = $this->getSession();

                switch ($action)
                {
                    case 'set' :
                        $session->set(
                            $request->request->get(self::PARAM_VARIABLE), $request->request->get(self::PARAM_VALUE)
                        );
                        break;
                    case 'clear' :
                        $session->remove($request->request->get(self::PARAM_VARIABLE));
                        break;
                    case 'get' :
                    default :
                        $properties[self::PROPERTY_RESULT] =
                            $session->get($request->request->get(self::PARAM_VARIABLE));
                        break;
                }
                break;
            case 'platform_setting' :
                $properties[self::PROPERTY_RESULT] = $this->getConfigurationConsulter()->getSetting(
                    [
                        $request->request->get(self::PARAM_CONTEXT),
                        $request->request->get(self::PARAM_VARIABLE)
                    ]
                );
                break;
        }

        $result = new JsonAjaxResult(200);
        $result->set_properties($properties);
        $result->display();
    }

    /**
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_TYPE];
    }
}
