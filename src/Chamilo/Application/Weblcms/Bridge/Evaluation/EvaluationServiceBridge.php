<?php

namespace Chamilo\Application\Weblcms\Bridge\Evaluation;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EntityService;
use Chamilo\Core\User\Storage\DataClass\User;
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
     * @var integer
     */
    protected $publicationId;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EntityService $entityService
     */
    public function __construct(
        EntityService $entityService
    )
    {
        $this->entityService = $entityService;
    }

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

    /**
     * @param int $publicationId
     */
    public function setPublicationId(int $publicationId)
    {
        $this->publicationId = $publicationId;
    }

    /**
     *
     * @return int[]
     */
    public function getTargetEntityIds()
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::getPublicationTargetUserIds($this->publicationId, null);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId)
    {
        // Todo: actually search by entity type
        return [$this->entityService->getUserForEntity($entityId)];
        /*$entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->getUsersForEntity($entityId);*/
    }
}
