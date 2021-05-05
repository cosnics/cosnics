<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display;
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

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface $evaluationServiceBridge
     */
    public function setEvaluationServiceBridge(EvaluationServiceBridgeInterface $evaluationServiceBridge)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
    }

    public function getDefaultAction($context)
    {
        if ($this->evaluationServiceBridge->canEditEvaluation()) {
            return 'Browser';
        }
        return Manager::ACTION_ENTRY;
    }

}