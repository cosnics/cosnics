<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\AssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\FeedbackService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryFeedbackAssignmentPublicationService extends AssignmentPublicationService
    implements AssignmentPublicationServiceInterface
{

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\FeedbackService
     */
    protected $feedbackService;

    /**
     * EntryAssignmentPublicationService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\FeedbackService $feedbackService
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService $treeNodeDataService
     * @param string $publicationContext
     */
    public function __construct(
        FeedbackService $feedbackService,
        AssignmentService $assignmentService, PublicationService $publicationService, CourseService $courseService,
        ContentObjectRepository $contentObjectRepository, TreeNodeDataService $treeNodeDataService,
        string $publicationContext
    )
    {
        $this->feedbackService = $feedbackService;

        parent::__construct(
            $assignmentService, $publicationService, $courseService, $contentObjectRepository, $treeNodeDataService,
            $publicationContext
        );
    }

    /**
     * Checks whether or not one of the given content objects are published
     *
     * @param array $contentObjectIds
     *
     * @return bool
     */
    public function areContentObjectsPublished($contentObjectIds = array())
    {
        return $this->feedbackService->countFeedbackByContentObjectIds($contentObjectIds) > 0;
    }

    /**
     * Deletes the publications by a given content object id
     *
     * @param int $contentObjectId
     */
    public function deleteContentObjectPublicationsByObjectId($contentObjectId)
    {
        // DO NOTHING BECAUSE THE FEEDBACK SHOULD NOT BE DELETED
    }

    /**
     * Deletes a specific publication by id
     *
     * @param int $publicationId
     */
    public function deleteContentObjectPublicationsByPublicationId($publicationId)
    {
        // DO NOTHING BECAUSE THE FEEDBACK SHOULD NOT BE DELETED
    }

    /**
     * Updates the content object id in the given publication
     *
     * @param int $publicationId
     * @param int $newContentObjectId
     */
    public function updateContentObjectId($publicationId, $newContentObjectId)
    {
        // DO NOTHING BECAUSE THE FEEDBACK SHOULD NOT BE UPDATED
    }

    /**
     * Returns the ContentObject publication attributes for a given publication
     *
     * @param int $publicationId
     *
     * @return Attributes
     */
    public function getContentObjectPublicationAttributes($publicationId)
    {
        return $this->getAttributesForEntryFeedback($this->feedbackService->findFeedbackByIdentifier($publicationId));
    }

    /**
     * Returns the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForContentObject($contentObjectId)
    {
        $contentObject = new ContentObject();
        $contentObject->setId($contentObjectId);

        return $this->getAttributesForMultipleEntryFeedback(
            $this->feedbackService->findFeedbackByContentObject($contentObject)
        );
    }

    /**
     * Returns the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForUser($userId)
    {
        $user = new User();
        $user->setId($userId);

        return $this->getAttributesForMultipleEntryFeedback($this->feedbackService->findFeedbackByUser($user));
    }

    /**
     * Counts the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForContentObject($contentObjectId)
    {
        return $this->feedbackService->countFeedbackByContentObjectIds([$contentObjectId]);
    }

    /**
     * Counts the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForUser($userId)
    {
        $user = new User();
        $user->setId($userId);

        return $this->feedbackService->countFeedbackByUser($user);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback[] $multipleEntryFeedback
     *
     * @return Attributes[]
     */
    protected function getAttributesForMultipleEntryFeedback($multipleEntryFeedback = [])
    {
        $attributes = [];

        foreach ($multipleEntryFeedback as $entryFeedback)
        {
            $attributes[] = $this->getAttributesForEntryFeedback($entryFeedback);
        }

        return $attributes;
    }

    /**
     * Builds the publication attributes for the given learning path child
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $entryFeedback
     *
     * @return Attributes
     */
    protected function getAttributesForEntryFeedback(Feedback $entryFeedback)
    {
        $contentObject = $this->contentObjectRepository->findById($entryFeedback->getFeedbackContentObjectId());
        $entry = $this->assignmentService->findEntryByIdentifier($entryFeedback->getEntryId());

        $attributes = new Attributes();
        $attributes->setId($entryFeedback->getId());
        $attributes->set_application('Chamilo\Application\Weblcms');
        $attributes->setPublicationContext(
            \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry::class
        );
        $attributes->set_publisher_id($entryFeedback->get_user_id());
        $attributes->set_date($entryFeedback->get_creation_date());
        $attributes->set_title($contentObject->get_title());
        $attributes->set_content_object_id($contentObject->getId());

        $this->addLocationForEntry(
            $entry, $attributes, Translation::getInstance()->getTranslation('EntryFeedback') . ': '
        );

        return $attributes;
    }
}