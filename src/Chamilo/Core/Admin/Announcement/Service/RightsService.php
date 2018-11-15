<?php
namespace Chamilo\Core\Admin\Announcement\Service;

use Chamilo\Core\Admin\Announcement\Rights;

/**
 * @package Chamilo\Core\Admin\Announcement\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService
{
    /**
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $userIdentifier
     *
     * @return integer[]
     */
    public function findPublicationIdentifiersWithViewRightForEntitiesAndUserIdentifier(
        $entities, $userIdentifier
    )
    {
        return $this->findPublicationIdentifiersWithRightForEntitiesAndUserIdentifier(
            Rights::VIEW_RIGHT, $entities, $userIdentifier
        );
    }

    /**
     * @param integer $right
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $userIdentifier
     *
     * @return integer[]
     */
    public function findPublicationIdentifiersWithRightForEntitiesAndUserIdentifier(
        int $right, array $entities, int $userIdentifier
    )
    {
        return Rights::getInstance()->get_identifiers_with_right_granted(
            $right, \Chamilo\Core\Admin\Announcement\Manager::context(),
            Rights::getInstance()->get_root(\Chamilo\Core\Admin\Announcement\Manager::context()),
            Rights::TYPE_PUBLICATION, $userIdentifier, $entities
        );
    }
}