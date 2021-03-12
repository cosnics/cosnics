<?php

namespace Chamilo\Application\Weblcms\Bridge\Evaluation;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Libraries\Architecture\ContextIdentifier;

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
     * @var integer
     */
    protected $currentEntityType;

    /**
     * @var ContextIdentifier
     */
    protected $contextIdentifier;

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

    /**
     * @param integer $currentEntityType
     */
    public function setCurrentEntityType(int $currentEntityType)
    {
        $this->currentEntityType = $currentEntityType;
    }

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType()
    {
        return $this->currentEntityType;
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     */
    public function setContextIdentifier(ContextIdentifier $contextIdentifier)
    {
        $this->contextIdentifier = $contextIdentifier;
    }

    /**
     *
     * @return ContextIdentifier
     */
    public function getContextIdentifier()
    {
        return $this->contextIdentifier;
    }

}
