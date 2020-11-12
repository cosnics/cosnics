<?php

namespace Chamilo\Application\ExamAssignment\Service\Kernel;

use Chamilo\Application\ExamAssignment\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * Class RequestValidator
 * @package Chamilo\Application\ExamAssignment\Service\Kernel
 */
class RequestValidator
{
    /**
     * @var ChamiloRequest
     */
    protected $chamiloRequest;

    /**
     * @var RequestValidatorExtensionInterface[]
     */
    protected $requestValidatorExtensions;

    /**
     * RequestValidator constructor.
     *
     * @param ChamiloRequest $chamiloRequest
     */
    public function __construct(ChamiloRequest $chamiloRequest)
    {
        $this->chamiloRequest = $chamiloRequest;
        $this->requestValidatorExtensions = [];
    }

    /**
     * @param RequestValidatorExtensionInterface $requestValidatorExtension
     */
    public function addRequestValidatorExtension(RequestValidatorExtensionInterface $requestValidatorExtension)
    {
        $this->requestValidatorExtensions[] = $requestValidatorExtension;
    }

    /**
     * @throws NotAllowedException
     */
    public function validateRequest()
    {
        $context = strtolower($this->chamiloRequest->getFromUrl(Application::PARAM_CONTEXT));
        $action = strtolower($this->chamiloRequest->getFromUrl(Application::PARAM_ACTION));

        if(empty($context) || $context == strtolower(\Chamilo\Core\Home\Manager::context()))
        {
            $this->chamiloRequest->query->set(Application::PARAM_CONTEXT, Manager::context());
            $this->chamiloRequest->query->set(Application::PARAM_ACTION, Manager::ACTION_LOGIN);

            return;
        }

        if(!$this->isActionAllowed($context, $action))
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @param string $context
     * @param string|null $action
     *
     * @return bool
     */
    protected function isActionAllowed(string $context, string $action = null)
    {
        if($context == strtolower(Manager::context()))
        {
            return true;
        }

        foreach($this->requestValidatorExtensions as $requestValidatorExtension)
        {
            if($requestValidatorExtension->isActionAllowed($context, $action))
            {
                return true;
            }
        }

        return false;
    }


}
