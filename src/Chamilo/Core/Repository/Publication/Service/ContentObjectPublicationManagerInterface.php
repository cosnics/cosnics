<?php

namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Manages the communication between the repository and the publications of content objects. This service is used
 * to determine whether or not a content object can be deleted, can be edited, ...
 *
 * @package Chamilo\Core\Repository\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ContentObjectPublicationManagerInterface
{
    /**
     * Returns whether or not a content object can be unlinked
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function canContentObjectBeUnlinked(ContentObject $contentObject);

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countPublicationAttributes(
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier,
        Condition $condition = null
    );

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
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier, Condition $condition = null,
        int $count = null, int $offset = null, array $orderProperties = null
    );

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function deleteContentObjectPublications(ContentObject $contentObject);

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function isContentObjectPublished(int $contentObjectIdentifier);

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return boolean
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers);

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function canContentObjectBeEdited(int $contentObjectIdentifier);
}