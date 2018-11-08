<?php

namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Manages the communication between the repository and the publications of content objects. This service is used
 * to determine whether or not a content object can be deleted, can be edited, ...
 *
 * @package Chamilo\Core\Repository\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    /**
     * @var \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface[]
     */
    protected $publicationAggregators;

    /**
     * PublicationAggregator constructor.
     */
    public function __construct()
    {
        $this->publicationAggregators = [];
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface $publicationAggregator
     */
    public function addPublicationAggregator(
        PublicationAggregatorInterface $publicationAggregator
    )
    {
        $this->publicationAggregators[] = $publicationAggregator;
    }

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return boolean
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers)
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

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function canContentObjectBeEdited(int $contentObjectIdentifier)
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

    /**
     * Returns whether or not a content object can be unlinked
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function canContentObjectBeUnlinked(ContentObject $contentObject)
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

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countPublicationAttributes(int $type, int $objectIdentifier, Condition $condition = null)
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

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function deleteContentObjectPublications(ContentObject $contentObject)
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
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Repository\Publication\Location\Locations[]
     */
    public function getContentObjectPublicationLocations(ContentObject $contentObject, User $user)
    {
        $locations = array();

        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            $contextLocations = $publicationAggregator->getContentObjectPublicationLocations(
                $contentObject, $user
            );

            if (!is_null($contextLocations) && count($contextLocations) > 0)
            {
                $locations = array_merge($locations, $contextLocations);
            }
        }

        return $locations;
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes[]
     */
    public function getContentObjectPublicationsAttributes(
        int $type, int $objectIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        array $orderProperties = null
    )
    {
        $publicationAttributes = array();

        foreach ($this->publicationAggregators as $publicationAggregator)
        {
            $applicationAttributes = $publicationAggregator->getContentObjectPublicationsAttributes(
                $type, $objectIdentifier, $condition, $count, $offset
            );

            if (!is_null($applicationAttributes) && count($applicationAttributes) > 0)
            {
                $publicationAttributes = array_merge($publicationAttributes, $applicationAttributes);
            }
        }

        // Sort the publication attributes
        if (count($orderProperties) > 0)
        {
            $orderProperty = $orderProperties[0];

            usort(
                $publicationAttributes,
                function (Attributes $publicationAttributeLeft, Attributes $publicationAttributeRight) use (
                    $orderProperty
                ) {
                    return strcasecmp(
                        $publicationAttributeLeft->get_default_property(
                            $orderProperty->getConditionVariable()->get_property()
                        ), $publicationAttributeRight->get_default_property(
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

        return $publicationAttributes;
    }

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function isContentObjectPublished(int $contentObjectIdentifier)
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