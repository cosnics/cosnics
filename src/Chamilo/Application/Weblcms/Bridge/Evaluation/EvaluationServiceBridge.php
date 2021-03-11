<?php

namespace Chamilo\Application\Weblcms\Bridge\Evaluation;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EvaluationServiceBridge implements EvaluationServiceBridgeInterface
{
    /**
     * @var bool
     */
    protected $canEditEvaluation;

    /**
     *
     * @return boolean
     */
    public function canEditEvaluation()
    {
        return $this->canEditEvaluation;
    }

    /**
     * @param bool $canEditEvaluation
     */
    public function setCanEditEvaluation($canEditEvaluation = true)
    {
        $this->canEditEvaluation = $canEditEvaluation;
    }
}
