<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ApplicationRegistry;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\Architecture\Exception\UserException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Service\Bootstrap
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class ApplicationFactory
{
    protected ApplicationRegistry $applicationRegistry;

    protected ChamiloRequest $request;

    protected Translator $translator;

    public function __construct(
        ChamiloRequest $request, Translator $translator, ApplicationRegistry $applicationRegistry
    )
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->applicationRegistry = $applicationRegistry;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    private function buildClassName(string $context, string $action): string
    {
        $className = $context . '\Component\\' . $action . 'Component';

        if (!class_exists($className)) {
            throw new ClassNotExistException($className);
        }

        return $className;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    protected function getAction(string $context): string
    {
        $request = $this->getRequest();

        $getAction = $request->query->get(Application::PARAM_ACTION);

        if ($getAction) {
            return $getAction;
        }

        $postAction = $request->request->get(Application::PARAM_ACTION);

        if ($postAction) {
            return $postAction;
        }

        return $this->getDefaultAction($context);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    public function getApplication(string $context): Application
    {
        $action = $this->getAction($context);
        $className = $this->getClassName($context, $action);

        return $this->getApplicationRegistry()->getApplication($className);
    }

    public function getApplicationRegistry(): ApplicationRegistry
    {
        return $this->applicationRegistry;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    public function getClassName(
        string $context, ?string $action = null
    ): string
    {
        if (is_null($action)) {
            $action = $this->getAction($context);
        }

        return $this->buildClassName($context, $action);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    protected function getDefaultAction(string $context): string
    {
        $managerClass = $this->getManagerClass($context);

        return $managerClass::DEFAULT_ACTION;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    protected function getManagerClass(string $context): string
    {
        $managerClass = $context . '\Manager';

        if (!class_exists($managerClass)) {
            throw new UserException(
                $this->getTranslator()->trans(
                    'InvalidApplication', ['%Context%' => $context], StringUtilities::LIBRARIES
                )
            );
        }

        return $managerClass;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
