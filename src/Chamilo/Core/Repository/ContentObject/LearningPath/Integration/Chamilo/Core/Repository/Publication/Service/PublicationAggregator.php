<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    protected PublicationService $publicationService;

    public function __construct(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    ): void
    {
    }

    /**
     * @param int[] $contentObjectIdentifiers
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers): bool
    {
        return $this->getPublicationService()->areContentObjectsPublished($contentObjectIdentifiers);
    }

    public function canContentObjectBeEdited(string $contentObjectIdentifier): bool
    {
        return true;
    }

    public function canContentObjectBeUnlinked(ContentObject $contentObject): bool
    {
        return true;
    }

    public function countPublicationAttributes(
        int $type, string $objectIdentifier, ?Condition $condition = null
    ): int
    {
        if ($type == self::ATTRIBUTES_TYPE_OBJECT)
        {
            return $this->getPublicationService()->countContentObjectPublicationAttributesForContentObject(
                $objectIdentifier
            );
        }
        else
        {
            return $this->getPublicationService()->countContentObjectPublicationAttributesForUser(
                $objectIdentifier
            );
        }
    }

    public function deleteContentObjectPublications(ContentObject $contentObject): bool
    {
        try
        {
            $this->getPublicationService()->deleteContentObjectPublicationsByObjectId($contentObject->getId());

            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }

    /**
     * @param int $type
     * @param string $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes>
     */
    public function getContentObjectPublicationsAttributes(
        int $type, string $objectIdentifier, ?Condition $condition = null, ?int $count = null, ?int $offset = null,
        OrderBy $orderBy = new OrderBy()
    ): ArrayCollection
    {
        if ($type == self::ATTRIBUTES_TYPE_OBJECT)
        {
            return $this->getPublicationService()->getContentObjectPublicationAttributesForContentObject(
                $objectIdentifier
            );
        }
        else
        {
            return $this->getPublicationService()->getContentObjectPublicationAttributesForUser(
                $objectIdentifier
            );
        }
    }

    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    public function isContentObjectPublished(string $contentObjectIdentifier): bool
    {
        return $this->getPublicationService()->areContentObjectsPublished([$contentObjectIdentifier]);
    }
}