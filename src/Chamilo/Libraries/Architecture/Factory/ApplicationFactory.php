<?php
namespace Chamilo\Libraries\Architecture\Factory;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;
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
     * @var \Symfony\Component\HttpFoundation\Request
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Libraries\Platform\Translation $translation
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, StringUtilities $stringUtilities,
        Translation $translation)
    {
        $this->request = $request;
        $this->stringUtilities = $stringUtilities;
        $this->translation = $translation;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
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
     *
     * @param string $context
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration $applicationConfiguration
     */
    public function getApplication($context, ApplicationConfiguration $applicationConfiguration)
    {
        $application = $this->createApplication($context, $applicationConfiguration);
        $application->get_breadcrumb_generator()->generate_breadcrumbs();
        return $application;
    }

    /**
     *
     * @param string $action
     * @return string
     */
    public function getClassName($context, ApplicationConfiguration $applicationConfiguration, $action = null)
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
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    protected function getParentApplication(ApplicationConfiguration $applicationConfiguration)
    {
        return $applicationConfiguration->getApplication();
    }

    protected function createApplication($context, ApplicationConfiguration $applicationConfiguration)
    {
        $action = $this->getAction($context, $applicationConfiguration);
        $className = $this->getClassName($context, $applicationConfiguration, $action);

        $application = new $className($applicationConfiguration);

        $application->set_parameter($this->getActionParameter($context), $action);

        if (! $this->getParentApplication($applicationConfiguration) instanceof Application)
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
     *
     * @param string $context
     * @return string
     */
    protected function getAction($context, ApplicationConfiguration $applicationConfiguration)
    {
        $actionParameter = $this->getActionParameter($context);
        $managerClass = $this->getManagerClass($context);
        $level = $this->determineLevel($applicationConfiguration->getApplication());
        $actions = $this->getRequestedAction($actionParameter, $context);

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
     * @throws \Exception
     * @return string
     */
    protected function getManagerClass($context)
    {
        $managerClass = $context . '\Manager';

        if (! class_exists($managerClass))
        {
            throw new \Exception(
                $this->getTranslation()->getTranslation(
                    'NoManagerFound',
                    array('CONTEXT' => $this->getContext()),
                    'Chamilo\Libraries'));
        }

        return $managerClass;
    }

    /**
     *
     * @param Application $application
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
     *
     * @param string $actionParameter
     * @param string $context
     * @return string
     */
    protected function getRequestedAction($actionParameter, $context)
    {
        $request = $this->getRequest();

        $getAction = $request->query->get($actionParameter);

        if (! $getAction)
        {
            $postAction = $request->request->get($actionParameter);

            if (! $postAction)
            {
                $managerClass = $this->getManagerClass($context);
                return $managerClass::DEFAULT_ACTION;
            }
            else
            {
                return $postAction;
            }
        }
        else
        {
            return $getAction;
        }
    }

    private function buildClassName($context, $action)
    {
        $className = $context . '\Component\\' . $action . 'Component';

        if (! class_exists($className))
        {
            // TODO: Temporary fallback for backwards compatibility
            $componentName = (string) $this->getStringUtilities()->createString($action)->upperCamelize();
            $className = $context . '\Component\\' . $componentName . 'Component';

            if (! class_exists($className))
            {
                // TODO: Do we still need this
                // $trail = BreadcrumbTrail::get_instance();
                // $trail->add(new Breadcrumb('#', Translation::get($classname)));

                throw new ClassNotExistException($className);
            }
        }

        return $className;
    }
}
