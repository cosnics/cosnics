<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;


/**
 * Class ApplicationFactory
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ApplicationFactory extends \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface
     */
    protected $evaluationServiceBridge;

    protected int $viewEntityId;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface $evaluationServiceBridge
     */
    public function setEvaluationServiceBridge(EvaluationServiceBridgeInterface $evaluationServiceBridge)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
    }

    public function getDefaultAction($context)
    {
        if(!empty($this->viewEntityId))
        {
            $this->getRequest()->query->set(Manager::PARAM_ENTITY_ID, $this->viewEntityId);

            return Manager::ACTION_ENTRY;
        }

        if ($this->evaluationServiceBridge->canEditEvaluation()) {
            return Manager::ACTION_BROWSE;
        }

        return Manager::ACTION_ENTRY;
    }

    public function setViewEntity(int $entityId)
    {
        $this->viewEntityId = $entityId;
    }

}
