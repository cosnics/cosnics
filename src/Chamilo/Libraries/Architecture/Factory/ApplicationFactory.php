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
 * @package Chamilo\Libraries\Architecture\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ApplicationFactory
{

    private ChamiloRequest $request;

    private StringUtilities $stringUtilities;

    private Translator $translator;

    public function __construct(ChamiloRequest $request, StringUtilities $stringUtilities, Translator $translator)
    {
        $this->request = $request;
        $this->stringUtilities = $stringUtilities;
        $this->translator = $translator;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     */
    private function buildClassName(string $context, string $action): string
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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function createApplication(
        string $context, ApplicationConfiguration $applicationConfiguration, ?string $fallBackAction = null
    ): Application
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
            $application->set_parameter($parameter, $this->getRequest()->getFromPostOrUrl($parameter));
        }

        return $application;
    }

    protected function determineLevel(?Application $application = null): int
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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getAction(
        string $context, ApplicationConfiguration $applicationConfiguration, ?string $fallBackAction = null
    ): string
    {
        $actionParameter = $this->getActionParameter($context);
        $managerClass = $this->getManagerClass($context);
        $level = $this->determineLevel($applicationConfiguration->getApplication());

        $actions = $this->getRequestedAction($actionParameter, $context, $fallBackAction);

        if (is_array($actions))
        {
            $action = $actions[$level] ?? $managerClass::DEFAULT_ACTION;
        }
        else
        {
            $action = $actions;
        }

        return $action;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getActionParameter(string $context): string
    {
        $managerClass = $this->getManagerClass($context);

        return $managerClass::PARAM_ACTION;
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getApplication(
        string $context, ApplicationConfiguration $applicationConfiguration, ?string $fallBackAction = null
    ): Application
    {
        $application = $this->createApplication($context, $applicationConfiguration, $fallBackAction);
        $application->get_breadcrumb_generator()->generateBreadcrumbs();

        return $application;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getClassName(
        string $context, ApplicationConfiguration $applicationConfiguration, ?string $action = null
    ): string
    {
        if (is_null($action))
        {
            $action = $this->getAction($context, $applicationConfiguration);
        }

        return $this->buildClassName($context, $action);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getDefaultAction(string $context): string
    {
        $managerClass = $this->getManagerClass($context);

        return $managerClass::DEFAULT_ACTION;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getManagerClass(string $context): string
    {
        $managerClass = $context . '\Manager';

        if (!class_exists($managerClass))
        {
            throw new UserException(
                $this->getTranslator()->trans(
                    'InvalidApplication', ['CONTEXT' => $context], 'Chamilo\Libraries'
                )
            );
        }

        return $managerClass;
    }

    protected function getParentApplication(ApplicationConfiguration $applicationConfiguration): ?Application
    {
        return $applicationConfiguration->getApplication();
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function setRequest(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return string|string[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getRequestedAction(string $actionParameter, string $context, ?string $fallBackAction = null)
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

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }
}
