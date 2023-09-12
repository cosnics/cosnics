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
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
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
            throw new ClassNotExistException($className);
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
        $action = $this->getAction($context, $fallBackAction);
        $className = $this->getClassName($context, $action);

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
            $application->set_parameter($parameter, $this->getRequest()->getFromRequestOrQuery($parameter));
        }

        return $application;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getAction(
        string $context, ?string $fallBackAction = null
    ): string
    {
        $actionParameter = $this->getActionParameter($context);

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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getActionParameter(string $context): string
    {
        $managerClass = $this->getManagerClass($context);

        return $managerClass::PARAM_ACTION;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getApplication(
        string $context, ApplicationConfiguration $applicationConfiguration, ?string $fallBackAction = null
    ): Application
    {
        return $this->createApplication($context, $applicationConfiguration, $fallBackAction);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getClassName(
        string $context, ?string $action = null
    ): string
    {
        if (is_null($action))
        {
            $action = $this->getAction($context);
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
                    'InvalidApplication', ['CONTEXT' => $context], StringUtilities::LIBRARIES
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

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
