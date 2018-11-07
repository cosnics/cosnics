<?php

namespace Chamilo\Core\Repository\Publication\Service;

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
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface PublicationAggregatorInterface
{
    const ATTRIBUTES_TYPE_OBJECT = 2;
    const ATTRIBUTES_TYPE_USER = 1;

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return boolean
     * @see PublicationInterface::any_content_object_is_published()
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers);

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     * @see PublicationInterface::is_content_object_editable()
     */
    public function canContentObjectBeEdited(int $contentObjectIdentifier);

    /**
     * Returns whether or not a content object can be unlinked
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     *
     */
    public function canContentObjectBeUnlinked(ContentObject $contentObject);

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     * @see PublicationInterface::count_publication_attributes()
     */
    public function countPublicationAttributes(int $type, int $objectIdentifier, Condition $condition = null);

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     * @see PublicationInterface::delete_content_object_publications()
     */
    public function deleteContentObjectPublications(ContentObject $contentObject);

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Repository\Publication\Location\Locations[]
     * @see PublicationInterface::get_content_object_publication_locations()
     */
    public function getContentObjectPublicationLocations(ContentObject $contentObject, User $user);

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes[]
     * @see PublicationInterface::get_content_object_publication_attributes()
     */
    public function getContentObjectPublicationsAttributes(
        int $type, int $objectIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        array $orderProperties = null
    );

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     * @see PublicationInterface::content_object_is_published()
     */
    public function isContentObjectPublished(int $contentObjectIdentifier);
}