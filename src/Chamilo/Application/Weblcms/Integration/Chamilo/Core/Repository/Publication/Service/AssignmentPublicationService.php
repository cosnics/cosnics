<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\AssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentPublicationService
{
    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var string
     */
    protected $publicationContext;

    /**
     * AssignmentPublicationService constructor.
     *
     * @param AssignmentService $assignmentService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param string $publicationContext
     */
    public function __construct(
        AssignmentService $assignmentService, ContentObjectRepository $contentObjectRepository, $publicationContext
    )
    {
        $this->assignmentService = $assignmentService;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->publicationContext = $publicationContext;
    }

    /**
     * @return string
     */
    public function getPublicationContext(): string
    {
        return $this->publicationContext;
    }

    /**
     * Checks whether or not one of the given content objects are used as attachment in entries
     *
     * @param array $contentObjectIds
     *
     * @return bool
     */
    public function areContentObjectsPublished($contentObjectIds = [])
    {
        return $this->assignmentService->countEntryAttachmentsByAttachmentIds($contentObjectIds) > 0;
    }

    /**
     * Deletes the attachments for entries by a given content object id
     *
     * @param int $contentObjectId
     */
    public function deleteContentObjectPublicationsByObjectId($contentObjectId)
    {
        $this->assignmentService->deleteEntryAttachmentsByAttachmentId($contentObjectId);
    }

    /**
     * Deletes a specific entry attachment
     *
     * @param int $publicationId
     */
    public function deleteContentObjectPublicationsByPublicationId($publicationId)
    {
        $entryAttachment = $this->assignmentService->findEntryAttachmentById($publicationId);
        if ($entryAttachment instanceof EntryAttachment)
        {
            $this->assignmentService->deleteEntryAttachment($entryAttachment);
        }
    }

    /**
     * Updates the content object id in the given entry attachment
     *
     * @param int $publicationId
     * @param int $newContentObjectId
     */
    public function updateContentObjectId($publicationId, $newContentObjectId)
    {
        $entryAttachment = $this->assignmentService->findEntryAttachmentById($publicationId);
        if ($entryAttachment instanceof EntryAttachment)
        {
            $entryAttachment->setAttachmentId($newContentObjectId);
            $this->assignmentService->updateEntryAttachment($entryAttachment);
        }
    }

    /**
     * Returns the ContentObject publication attributes for a given entry attachment id
     *
     * @param int $publicationId
     *
     * @return Attributes
     */
    public function getContentObjectPublicationAttributes($publicationId)
    {
        $entryAttachment = $this->assignmentService->findEntryAttachmentById($publicationId);
        if (!$entryAttachment instanceof EntryAttachment)
        {
            return null;
        }

        return $this->getAttributesForEntryAttachment($entryAttachment);
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
        return $this->getAttributesForEntryAttachments(
            $this->assignmentService->findEntryAttachmentsByAttachmentId($contentObjectId)
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
        return $this->getAttributesForEntryAttachments(
            $this->assignmentService->findEntryAttachmentsByUserId($userId)
        );
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
        return $this->assignmentService->countEntryAttachmentsByAttachmentIds([$contentObjectId]);
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
        return $this->assignmentService->countEntryAttachmentsByUserId($userId);
    }

    /**
     * @param EntryAttachment[] $entryAttachments
     *
     * @return Attributes[]
     */
    protected function getAttributesForEntryAttachments($entryAttachments = [])
    {
        $attributes = [];

        foreach ($entryAttachments as $entryAttachment)
        {
            $attributes[] = $this->getAttributesForEntryAttachment($entryAttachment);
        }

        return $attributes;
    }

    /**
     * Builds the publication attributes for the given learning path child
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     *
     * @return Attributes
     */
    protected function getAttributesForEntryAttachment(EntryAttachment $entryAttachment)
    {
        $entry = $this->assignmentService->findEntryByIdentifier($entryAttachment->getEntryId());
        $contentObject = $this->contentObjectRepository->findById($entryAttachment->getAttachmentId());

        $attributes = new Attributes();
        $attributes->setId($entryAttachment->getId());
        $attributes->set_application('Chamilo\Application\Weblcms');
        $attributes->setPublicationContext($this->publicationContext);
        $attributes->set_publisher_id($contentObject->get_owner_id());
        $attributes->set_date($contentObject->get_creation_date());
        $attributes->set_location(Translation::getInstance()->getTranslation('EntryAttachment') . ': ' . $entry->getContentObject()->get_title());
        $attributes->set_url(null);
        $attributes->set_title($contentObject->get_title());
        $attributes->set_content_object_id($entryAttachment->getAttachmentId());
        // TODO: These should be split off to their own service and the property set accordingly
        $attributes->setModifierServiceIdentifier(PublicationModifier::class);

        return $attributes;
    }

}