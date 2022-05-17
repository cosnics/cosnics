<?php
namespace Chamilo\Libraries\Architecture\Factory;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

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
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * ApplicationFactory constructor.
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(ChamiloRequest $request, StringUtilities $stringUtilities, Translator $translator)
    {
        $this->request = $request;
        $this->stringUtilities = $stringUtilities;
        $this->translator = $translator;
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
                throw new ClassNotExistException($className);
            }
        }

        return $className;
    }

    /**
     * @param string $context
     * @param ApplicationConfiguration $applicationConfiguration
     * @param string $fallBackAction
     *
     * @return mixed
     * @throws ClassNotExistException
     * @throws \Exception
     */
    protected function createApplication(
        string $context, ApplicationConfiguration $applicationConfiguration, string $fallBackAction = null
    )
    {
        $action = $this->getAction($context, $applicationConfiguration, $fallBackAction);
        $className = $this->getClassName($context, $applicationConfiguration, $action);

        /**
         * @var \Chamilo\Libraries\Architecture\Application\Application $application
         */
        $application = new $className($applicationConfiguration);

        $application->set_parameter($this->getActionParameter($context), $action);

        if (!$this->getParentApplication($applicationConfiguration) instanceof Application)
        {
            $application->set_parameter(Application::PARAM_CONTEXT, $context);
        }

        $parameters = $application->getAdditionalParameters();

        foreach ($parameters as $parameter)
        {
            $application->set_parameter($parameter, $this->getRequest()->get($parameter));
        }

        return $application;
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
     * @throws \Exception
     */
    protected function getActionParameter($context)
    {
        $managerClass = $this->getManagerClass($context);

        return $managerClass::PARAM_ACTION;
    }

    /**
     * @param string $context
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration $applicationConfiguration
     * @param string $fallBackAction When no action is available in request, use this if you want an action other then
     *     the default
     *
     * @return mixed
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     */
    public function getApplication(
        string $context, ApplicationConfiguration $applicationConfiguration, string $fallBackAction = null
    )
    {
        $application = $this->createApplication($context, $applicationConfiguration, $fallBackAction);
        $application->get_breadcrumb_generator()->generate_breadcrumbs();

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
                $this->getTranslator()->trans(
                    'InvalidApplication', array('CONTEXT' => $context), 'Chamilo\Libraries'
                )
            );
        }

        return $managerClass;
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
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslation(Translator $translator)
    {
        $this->translator = $translator;
    }
}
