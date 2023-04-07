<?php

namespace Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAggregator implements PublicationAggregatorInterface
{

    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    )
    {
    }

    /**
     * @param int[] $contentObjectIdentifiers
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers): bool
    {
        return Manager::areContentObjectsPublished($contentObjectIdentifiers);
    }

    public function canContentObjectBeEdited(int $contentObjectIdentifier): bool
    {
        return Manager::canContentObjectBeEdited($contentObjectIdentifier);
    }

    public function canContentObjectBeUnlinked(ContentObject $contentObject): bool
    {
        return true;
    }

    public function countPublicationAttributes(
        int $type, int $objectIdentifier, ?Condition $condition = null
    ): int
    {
        return Manager::countPublicationAttributes($type, $objectIdentifier, $condition);
    }

    public function deleteContentObjectPublications(ContentObject $contentObject): bool
    {
        return Manager::deleteContentObjectPublications($contentObject->getId());
    }

    /**
     * @param int $type
     * @param int $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes>
     */
    public function getContentObjectPublicationsAttributes(
        int $type, int $objectIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return Manager::getContentObjectPublicationsAttributes(
            $objectIdentifier, $type, $condition, $count, $offset, $orderBy
        );
    }
    
    public function isContentObjectPublished(int $contentObjectIdentifier): bool
    {
        return Manager::isContentObjectPublished($contentObjectIdentifier);
    }
}