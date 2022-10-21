<?php

namespace Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * @package Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    )
    {
    }

    /**
     * @param int $contentObjectIdentifiers
     *
     * @return bool
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers)
    {
        return Manager::areContentObjectsPublished($contentObjectIdentifiers);
    }

    /**
     * @param int $contentObjectIdentifier
     *
     * @return bool
     */
    public function canContentObjectBeEdited(int $contentObjectIdentifier)
    {
        return Manager::canContentObjectBeEdited($contentObjectIdentifier);
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
        return true;
    }

    /**
     * @param int $type
     * @param int $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countPublicationAttributes(
        int $type, int $objectIdentifier, Condition $condition = null
    )
    {
        return Manager::countPublicationAttributes($type, $objectIdentifier, $condition);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function deleteContentObjectPublications(ContentObject $contentObject)
    {
        return Manager::deleteContentObjectPublications($contentObject->getId());
    }

    /**
     * @param int $type
     * @param int $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes[]
     */
    public function getContentObjectPublicationsAttributes(
        int $type, int $objectIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        ?OrderBy $orderBy = null
    )
    {
        return Manager::getContentObjectPublicationsAttributes(
            $objectIdentifier, $type, $condition, $count, $offset, $orderBy
        );
    }

    /**
     * @param int $contentObjectIdentifier
     *
     * @return bool
     */
    public function isContentObjectPublished(int $contentObjectIdentifier)
    {
        return Manager::isContentObjectPublished($contentObjectIdentifier);
    }
}