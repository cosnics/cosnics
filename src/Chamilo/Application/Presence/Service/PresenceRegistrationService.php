<?php

namespace Chamilo\Application\Presence\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Service\PublicationService;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\PresenceResultEntryService;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\PresenceResultPeriodService;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\VerificationIconService;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultEntry;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Application\Presence\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class PresenceRegistrationService
{
    protected PresenceResultEntryService $resultEntryService;
    protected PresenceResultPeriodService $resultPeriodService;
    protected PublicationService $publicationService;
    protected \Chamilo\Application\Weblcms\Service\PublicationService $contentObjectPublicationService;
    protected ContentObjectService $contentObjectService;
    protected TreeNodeDataService $treeNodeDataService;
    protected LearningPathStepContextService $stepContextService;
    protected VerificationIconService $verificationIconService;

    public function __construct(
        PresenceResultEntryService $resultEntryService, PresenceResultPeriodService $resultPeriodService,
        PublicationService $publicationService,
        \Chamilo\Application\Weblcms\Service\PublicationService $contentObjectPublicationService,
        ContentObjectService $contentObjectService, TreeNodeDataService $treeNodeDataService,
        LearningPathStepContextService $stepContextService,
        VerificationIconService $verificationIconService
    )
    {
        $this->resultEntryService = $resultEntryService;
        $this->resultPeriodService = $resultPeriodService;
        $this->publicationService = $publicationService;
        $this->contentObjectPublicationService = $contentObjectPublicationService;
        $this->contentObjectService = $contentObjectService;
        $this->treeNodeDataService = $treeNodeDataService;
        $this->stepContextService = $stepContextService;
        $this->verificationIconService = $verificationIconService;
    }

    /**
     * @param User $user
     * @param int $publicationId
     * @param int|null $treeNodeId
     * @param string $securityKey
     *
     * @return array
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws NotAllowedException
     *
     * @TODO create a system to validate the security of the parameters so the presence list is protected for unauthorized access?
     */
    public function registerUserInPresence(
        User $user, int $publicationId, int $treeNodeId = null, string $securityKey = ''
    )
    {
        $publication = $this->getPublicationById($publicationId);

        if (!empty($treeNodeId))
        {
            $treeNode = $this->getTreeNodeById($treeNodeId, $publication);

            $presenceId = $treeNode->getContentObjectId();

            $stepContext = $this->stepContextService->getOrCreateLearningPathStepContext(
                $treeNodeId, ContentObjectPublication::class, $publicationId
            );

            $contextIdentifier = new ContextIdentifier(LearningPathStepContext::class, $stepContext->getId());
        }
        else
        {
            $presenceId = $publication->get_content_object_id();
            $contextIdentifier = new ContextIdentifier(Publication::class, $publicationId);
        }

        $presence = $this->getPresenceById($presenceId, $publicationId, $treeNodeId);

        $calculatedSecurityKey = $this->calculateSecurityKey($presence, $publicationId, $treeNodeId);
        if($calculatedSecurityKey != $securityKey)
        {
            throw new NotAllowedException();
        }

        $periods = $this->resultPeriodService->getResultPeriodsForPresence($presence, $contextIdentifier, true);
        $lastPeriod = array_pop($periods);

        $result = $this->resultEntryService->getPresenceResultEntry($lastPeriod['id'], $user->getId());
        if (!$result instanceof PresenceResultEntry)
        {
            $result = $this->resultEntryService->createOrUpdatePresenceResultEntry(
                $presence, $lastPeriod['id'], $user->getId(), Presence::STATUS_PRESENT
            );
        }

        return array($result, $presence);
    }

    public function isUserInPresenceList(User $user, int $publicationId): bool
    {
        $publication = $this->getPublicationById($publicationId);

        return $this->publicationService->isUserSubscribedToPublication($user, $publication);
    }

    /**
     * @param int $publicationId
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected function getPublicationById(int $publicationId): ContentObjectPublication
    {
        $publication = $this->contentObjectPublicationService->getPublication($publicationId);
        if (!$publication)
        {
            throw new \InvalidArgumentException(
                sprintf('The given publication with id %s could not be found', $publicationId)
            );
        }

        return $publication;
    }

    /**
     * @param int $treeNodeId
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $publication
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    protected function getTreeNodeById(
        int $treeNodeId, \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $publication
    ): TreeNodeData
    {
        $treeNode = $this->treeNodeDataService->getTreeNodeDataById($treeNodeId);
        if ($treeNode->getLearningPathId() != $publication->get_content_object_id())
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given treenode with id %s does not belong to publication %s', $treeNodeId,
                    $publication->getId()
                )
            );
        }

        return $treeNode;
    }

    /**
     * @param int $presenceId
     * @param int $publicationId
     * @param int|null $treeNodeId
     *
     * @return Presence
     */
    protected function getPresenceById(int $presenceId, int $publicationId, ?int $treeNodeId): Presence
    {
        $presence = $this->contentObjectService->findById($presenceId);
        if (!$presence instanceof Presence)
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given publication (%s) and / or treenode (%s) do not resolve to a valid presence content object',
                    $publicationId, $treeNodeId
                )
            );
        }

        return $presence;
    }

    /**
     * @param Presence $presence
     * @return string
     */
    public function getPresenceVerificationIcon(Presence $presence): string
    {
        return $this->verificationIconService->renderVerificationIconForPresence($presence);
    }

    public function getPresenceRegistrationUrl(Presence $presence, int $publicationId, int $treeNodeId = null): string
    {
        $securityKey = $this->calculateSecurityKey($presence, $publicationId, $treeNodeId);

        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => \Chamilo\Application\Presence\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Presence\Manager::ACTION_PRESENCE_REGISTRATION,
                \Chamilo\Application\Presence\Manager::PARAM_PUBLICATION_ID => $publicationId,
                \Chamilo\Application\Presence\Manager::PARAM_TREE_NODE_ID => $treeNodeId,
                \Chamilo\Application\Presence\Manager::PARAM_SECURITY_KEY => $securityKey
            ]
        );

        return $redirect->getUrl();
    }

    /**
     * @param Presence $presence
     * @param int $publicationId
     * @param int|null $treeNodeId
     *
     * @return string
     */
    protected function calculateSecurityKey(Presence $presence, int $publicationId, int $treeNodeId = null): string
    {
        return md5($publicationId . ':' . $treeNodeId . ':' . $presence->get_object_number());
    }

}
