<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Manages the communication between the repository and the publications of content objects. This service is used
 * to determine whether or not a content object can be deleted, can be edited, ...
 *
 * @package Chamilo\Core\Repository\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    /**
     * @var \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface[]
     */
    protected array $publicationAggregators;

    public function __construct()
    {
        $this->publicationAggregators = [];
    }

    public function addPublicationAggregator(PublicationAggregatorInterface $publicationAggregator): void
    {
        $this->publicationAggregators[] = $publicationAggregator;
    }

    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    ): void
    {
        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            $publicationAggregator->addPublicationTargetsToFormForContentObjectAndUser($form, $contentObject, $user);
        }
    }

    /**
     * @param string[] $contentObjectIdentifiers
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers): bool
    {
        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            if ($publicationAggregator->areContentObjectsPublished($contentObjectIdentifiers))
            {
                return true;
            }
        }

        return false;
    }

    public function canContentObjectBeEdited(string $contentObjectIdentifier): bool
    {
        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            if (!$publicationAggregator->canContentObjectBeEdited($contentObjectIdentifier))
            {
                return false;
            }
        }

        return true;
    }

    public function canContentObjectBeUnlinked(ContentObject $contentObject): bool
    {
        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            if (!$publicationAggregator->canContentObjectBeUnlinked($contentObject))
            {
                return false;
            }
        }

        return true;
    }

    public function countPublicationAttributes(int $type, string $objectIdentifier, ?Condition $condition = null): int
    {
        $count = 0;

        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            $count += $publicationAggregator->countPublicationAttributes(
                $type, $objectIdentifier, $condition
            );
        }

        return $count;
    }

    public function deleteContentObjectPublications(ContentObject $contentObject): bool
    {
        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            if (!$publicationAggregator->deleteContentObjectPublications($contentObject))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $type
     * @param string $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes>
     */
    public function getContentObjectPublicationsAttributes(
        int $type, string $objectIdentifier, ?Condition $condition = null, ?int $count = null, ?int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $publicationAttributes = [];

        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            $applicationAttributes = $publicationAggregator->getContentObjectPublicationsAttributes(
                $type, $objectIdentifier, $condition, $count, $offset
            );

            if (!is_null($applicationAttributes) && count($applicationAttributes) > 0)
            {
                $publicationAttributes = array_merge($publicationAttributes, $applicationAttributes->toArray());
            }
        }

        // Sort the publication attributes
        if (count($orderBy) > 0)
        {
            $orderProperty = $orderBy[0];

            usort(
                $publicationAttributes,
                function (Attributes $publicationAttributeLeft, Attributes $publicationAttributeRight) use (
                    $orderProperty
                ) {
                    return strcasecmp(
                        $publicationAttributeLeft->getDefaultProperty(
                            $orderProperty->getConditionVariable()->get_property()
                        ), $publicationAttributeRight->getDefaultProperty(
                        $orderProperty->getConditionVariable()->get_property()
                    )
                    );
                }
            );

            if ($orderProperty->getDirection() == SORT_DESC)
            {
                $publicationAttributes = array_reverse($publicationAttributes);
            }
        }

        if (isset($offset))
        {
            if (isset($count))
            {
                $publicationAttributes = array_splice($publicationAttributes, $offset, $count);
            }
            else
            {
                $publicationAttributes = array_splice($publicationAttributes, $offset);
            }
        }

        return new ArrayCollection($publicationAttributes);
    }

    public function isContentObjectPublished(string $contentObjectIdentifier): bool
    {
        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            if ($publicationAggregator->isContentObjectPublished($contentObjectIdentifier))
            {
                return true;
            }
        }

        return false;
    }
}