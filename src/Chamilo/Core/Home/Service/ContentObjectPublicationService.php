<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Home\Storage\DataClass\Element;
use RuntimeException;

/**
 * Service to manage content object publications for this application
 *
 * @package Chamilo\Core\Home\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectPublicationService
{

    protected ContentObjectPublicationRepository $contentObjectPublicationRepository;

    public function __construct(ContentObjectPublicationRepository $contentObjectPublicationRepository)
    {
        $this->contentObjectPublicationRepository = $contentObjectPublicationRepository;
    }

    public function countContentObjectPublicationsByContentObjectId($contentObjectId): int
    {
        return $this->getContentObjectPublicationRepository()->countContentObjectPublicationsByContentObjectId(
            $contentObjectId
        );
    }

    /**
     * @param string[] $contentObjectIds
     */
    public function countContentObjectPublicationsByContentObjectIds(array $contentObjectIds = []): int
    {
        return $this->getContentObjectPublicationRepository()->countContentObjectPublicationsByContentObjectIds(
            $contentObjectIds
        );
    }

    public function countContentObjectPublicationsByContentObjectOwnerId(string $ownerId): int
    {
        return $this->getContentObjectPublicationRepository()->countContentObjectPublicationsByContentObjectOwnerId(
            $ownerId
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function deleteContentObjectPublicationById(string $publicationId): bool
    {
        $contentObjectPublication = $this->getContentObjectPublicationById($publicationId);

        if (!$contentObjectPublication)
        {
            return false;
        }

        return $contentObjectPublication->delete();
    }

    public function deleteContentObjectPublicationsByContentObjectId(string $contentObjectId): void
    {
        $this->getContentObjectPublicationRepository()->deleteContentObjectPublicationsByContentObjectId(
            $contentObjectId
        );
    }

    public function getContentObjectPublicationById(string $publicationId): ?ContentObjectPublication
    {
        return $this->getContentObjectPublicationRepository()->findContentObjectPublicationById($publicationId);
    }

    public function getContentObjectPublicationRepository(): ContentObjectPublicationRepository
    {
        return $this->contentObjectPublicationRepository;
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    public function getContentObjectPublicationsByContentObjectId(string $contentObjectId): array
    {
        return $this->getContentObjectPublicationRepository()->findContentObjectPublicationsByContentObjectId(
            $contentObjectId
        );
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication[]
     */
    public function getContentObjectPublicationsByContentObjectOwnerId(string $ownerId): array
    {
        return $this->getContentObjectPublicationRepository()->findContentObjectPublicationsByContentObjectOwnerId(
            $ownerId
        );
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication[]
     */
    public function getContentObjectPublicationsForElement(Element $element): array
    {
        return $this->getContentObjectPublicationRepository()->findContentObjectPublicationsByElementId(
            $element->getId()
        );
    }

    public function getFirstContentObjectPublicationForElement(Element $element): ?ContentObjectPublication
    {
        return $this->getContentObjectPublicationRepository()->findFirstContentObjectPublicationByElementId(
            $element->getId()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function publishContentObject(Element $element, string $contentObjectId): void
    {
        if ($contentObjectId == 0)
        {
            return;
        }

        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->set_element_id($element->getId());
        $contentObjectPublication->set_content_object_id((int) $contentObjectId);

        if (!$this->getContentObjectPublicationRepository()->createContentObjectPublication($contentObjectPublication))
        {
            throw new RuntimeException(
                sprintf('Could not publish the content object %s in element %s', $contentObjectId, $element->getId())
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function setOnlyContentObjectForElement(Element $element, string $contentObjectId): void
    {
        if (!$this->getContentObjectPublicationRepository()->deleteContentObjectPublicationsForElement(
            $element->getId()
        ))
        {
            throw new RuntimeException('Could not clear the publications for element ' . $element->getId());
        }

        $this->publishContentObject($element, $contentObjectId);
    }
}
