<?php

namespace Chamilo\Libraries\Architecture\Factory;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Architecture\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ApplicationFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Translation
     */
    private $translation;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Libraries\Platform\Translation $translation
     */
    public function __construct(ChamiloRequest $request, StringUtilities $stringUtilities, Translation $translation)
    {
        $this->request = $request;
        $this->stringUtilities = $stringUtilities;
        $this->translation = $translation;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Translation $translation
     */
    public function setTranslation(Translation $translation)
    {
        $this->translation = $translation;
    }

    /**
     * @param string $context Package namespace
     * @param ApplicationConfiguration $applicationConfiguration
     * @param string $defaultActionWhenNotAvailable When no action is available in request, use this if you want an action other then the default
     * @param string|null $forceAction Force an action no matter what, override the request to go to this action, only usable in very specific cases because
     * no further navigation is possible in the application once this action is set
     *
     * @return Application
     * @throws ClassNotExistException
     */
    public function getApplication(
        string $context, ApplicationConfiguration $applicationConfiguration,
        string $defaultActionWhenNotAvailable = null
    )
    {
        $application = $this->createApplication($context, $applicationConfiguration, $defaultActionWhenNotAvailable);

        if(!$applicationConfiguration->isEmbeddedApplication())
        {
            $application->get_breadcrumb_generator()->generate_breadcrumbs();
        }

        return $application;
    }

    /**
     * @param string $context
     * @param ApplicationConfiguration $applicationConfiguration
     * @param string|null $action
     *
     * @return string
     * @throws ClassNotExistException
     * @throws \Exception
     */
    public function getClassName(
        string $context, ApplicationConfiguration $applicationConfiguration, string $action = null
    )
    {
        if (is_null($action))
        {
            $action = $this->getAction($context, $applicationConfiguration);
        }

        return $this->buildClassName($context, $action);
    }

    /**
     *
     * @param ApplicationConfiguration $applicationConfiguration
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    protected function getParentApplication(ApplicationConfiguration $applicationConfiguration)
    {
        return $applicationConfiguration->getApplication();
    }

    /**
     * @param string $context
     * @param ApplicationConfiguration $applicationConfiguration
     * @param string $defaultActionWhenNotAvailable
     *
     * @return mixed
     * @throws ClassNotExistException
     * @throws \Exception
     */
    protected function createApplication(
        string $context, ApplicationConfiguration $applicationConfiguration,
        string $defaultActionWhenNotAvailable = null
    )
    {
        $action = $this->getAction($context, $applicationConfiguration, $defaultActionWhenNotAvailable);
        $className = $this->getClassName($context, $applicationConfiguration, $action);

        $application = new $className($applicationConfiguration);

        $application->set_parameter($this->getActionParameter($context), $action);

        if (!$this->getParentApplication($applicationConfiguration) instanceof Application)
        {
            $application->set_parameter(Application::PARAM_CONTEXT, $context);
        }

        $parameters = $application->get_additional_parameters();

        foreach ($parameters as $parameter)
        {
            $application->set_parameter($parameter, $this->getRequest()->get($parameter));
        }

        return $application;
    }

    /**
     * @param string $context
     * @param ApplicationConfiguration $applicationConfiguration
     * @param string|null $fallBackAction
     *
     * @return string
     * @throws \Exception
     */
    protected function getAction(
        string $context, ApplicationConfiguration $applicationConfiguration, string $fallBackAction = null
    )
    {
        $actionParameter = $this->getActionParameter($context);
        $managerClass = $this->getManagerClass($context);
        $level = $this->determineLevel($applicationConfiguration->getApplication());

        $actions = $this->getRequestedAction($actionParameter, $context, $fallBackAction);

        if (is_array($actions))
        {
            if (isset($actions[$level]))
            {
                $action = $actions[$level];
            }
            else
            {
                // TODO: Catch the fact that there might not be a default action
                $action = $managerClass::DEFAULT_ACTION;
            }
        }
        else
        {
            $action = $actions;
        }

        return $action;
    }

    /**
     *
     * @param string $context
     *
     * @return string
     */
    protected function getActionParameter($context)
    {
        $managerClass = $this->getManagerClass($context);

        return $managerClass::PARAM_ACTION;
    }

    /**
     *
     * @param string $context
     *
     * @return string
     * @throws \Exception
     */
    protected function getManagerClass($context)
    {
        $managerClass = $context . '\Manager';

        if (!class_exists($managerClass))
        {
            throw new UserException(
                Translation::get('InvalidApplication', array('CONTEXT' => $context), 'Chamilo\Libraries')
            );
        }

        return $managerClass;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     *
     * @return integer
     */
    protected function determineLevel(Application $application = null)
    {
        if ($application instanceof Application)
        {
            $level = $application->get_level();
            $level ++;
        }
        else
        {

            $level = 0;
        }

        return $level;
    }

    /**
     * @param string $actionParameter
     * @param string $context
     * @param string|null $fallBackAction
     *
     * @return string
     * @throws \Exception
     */
    protected function getRequestedAction(string $actionParameter, string $context, string $fallBackAction = null)
    {
        $request = $this->getRequest();

        $getAction = $request->query->get($actionParameter);

        if ($getAction)
        {
            return $getAction;
        }

        $postAction = $request->request->get($actionParameter);

        if ($postAction)
        {
            return $postAction;
        }

        if ($fallBackAction)
        {
            return $fallBackAction;
        }

        return $this->getDefaultAction($context);
    }

    /**
     * @param $context
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getDefaultAction($context)
    {
        $managerClass = $this->getManagerClass($context);

        return $managerClass::DEFAULT_ACTION;
    }

    /**
     *
     * @param string $context
     * @param string $action
     *
     * @return string
     * @throws ClassNotExistException
     */
    private function buildClassName($context, $action)
    {
        $className = $context . '\Component\\' . $action . 'Component';

        if (!class_exists($className))
        {
            // TODO: Temporary fallback for backwards compatibility
            $componentName = (string) $this->getStringUtilities()->createString($action)->upperCamelize();
            $className = $context . '\Component\\' . $componentName . 'Component';

            if (!class_exists($className))
            {
                // TODO: Do we still need this
                // $trail = BreadcrumbTrail::getInstance();
                // $trail->add(new Breadcrumb('#', Translation::get($classname)));

                throw new ClassNotExistException($className);
            }
        }

        return $className;
    }
}
