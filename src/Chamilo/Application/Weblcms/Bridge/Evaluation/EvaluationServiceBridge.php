<?php

namespace Chamilo\Application\Weblcms\Bridge\Evaluation;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass\Publication as EvaluationPublication;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceManager;
use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationServiceBridge implements EvaluationServiceBridgeInterface
{
    /**
     * @var PublicationEntityServiceManager
     */
    protected $publicationEntityServiceManager;

    /**
     * @var EvaluationEntryService
     */
    protected $evaluationEntryService;

    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var EvaluationPublication
     */
    protected $evaluationPublication;

    /**
     * @var bool
     */
    protected $canEditEvaluation;

    /**
     * @param PublicationEntityServiceManager $publicationEntityServiceManager
     * @param EvaluationEntryService $evaluationEntryService
     */
    public function __construct(PublicationEntityServiceManager $publicationEntityServiceManager, EvaluationEntryService $evaluationEntryService, PublicationRepository $publicationRepository)
    {
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->evaluationEntryService = $evaluationEntryService;
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param EvaluationPublication $evaluationPublication
     */
    public function setEvaluationPublication(EvaluationPublication $evaluationPublication)
    {
        $this->evaluationPublication = $evaluationPublication;
    }

    /**
     * @param User $currentUser
     *
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser): ?int
    {
        return $this->getPublicationEntityService()->getCurrentEntityIdentifier($currentUser);
    }

    /**
     * @return integer
     */
    public function getCurrentEntityType(): int
    {
        return $this->evaluationPublication->getEntityType();
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        return new ContextIdentifier(get_class($this->evaluationPublication), $this->contentObjectPublication->getId());
    }

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool
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
     * @return boolean
     */
    public function getOpenForStudents(): bool
    {
        return $this->evaluationPublication->getOpenForStudents();
    }

    /**
     * @param bool $openForStudents
     * @throws \Exception
     */
    public function setOpenForStudents(bool $openForStudents)
    {
        $this->publicationRepository->setPublicationOpenForStudents($this->evaluationPublication, $openForStudents);
    }

    /**
     * @return boolean
     */
    public function getSelfEvaluationAllowed(): bool
    {
        return $this->evaluationPublication->getSelfEvaluationAllowed();
    }

    /**
     * @param bool $selfEvaluationAllowed
     * @throws \Exception
     */
    public function setSelfEvaluationAllowed(bool $selfEvaluationAllowed)
    {
        $this->publicationRepository->setSelfEvaluationAllowed($this->evaluationPublication, $selfEvaluationAllowed);
    }

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        return $this->getPublicationEntityService()->getTargetEntityIds();
    }

    /**
     * @return PublicationEntityServiceInterface
     */
    private function getPublicationEntityService(): PublicationEntityServiceInterface
    {
        return $this->publicationEntityServiceManager->getEntityServiceByType($this->getCurrentEntityType());
    }

    /**
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityId): array
    {
        return $this->getPublicationEntityService()->getUsersForEntity($entityId);
    }

    /**
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityId): string
    {
        return $this->getPublicationEntityService()->getEntityDisplayName($entityId);
    }

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool
    {
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        return $publicationEntityService->isUserPartOfEntity($user, $entityId);
    }
}
