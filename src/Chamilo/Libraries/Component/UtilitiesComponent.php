<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UtilitiesComponent extends Manager implements NoVisitTraceComponentInterface
{
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
    public function run(?User $currentUser = null): Response
    {
        $request = $this->getRequest();
        $type = $request->getFromQueryOrRequest(self::PARAM_TYPE);

        $properties = [];

        switch ($type) {
            // Retrieve platform paths
            case 'path' :
                if ($request->request->get(self::PARAM_PATH) != 'WEB_PATH') {
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

                switch ($action) {
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
        }

        $result = new JsonAjaxResult(200);
        $result->setProperties($properties);

        return $result->getResponse();
    }
}
