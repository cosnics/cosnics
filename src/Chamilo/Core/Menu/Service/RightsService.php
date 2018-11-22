<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocation;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocationEntityRight;

/**
 * @package Chamilo\Core\Menu\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Core\Rights\Service\RightsService
{
    const VIEW_RIGHT = 1;
    const TYPE_ITEM = 1;

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\RightsLocationEntityRight
     */
    protected function getRightsLocationEntityRightInstance()
    {
        return new RightsLocationEntityRight();
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\RightsLocation
     */
    protected function getRightsLocationInstance()
    {
        return new RightsLocation();
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function createItemRightsLocationWithViewRightForEveryone(Item $item)
    {
        $rightsLocation = $this->createItemRightsLocation($item);

        if (!$rightsLocation instanceof RightsLocation)
        {
            return false;
        }

        if (!$this->setRightsLocationViewRightForEveryone($rightsLocation))
        {
            return false;
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\RightsLocation $rightsLocation
     *
     * @return boolean
     */
    public function setRightsLocationViewRightForEveryone(RightsLocation $rightsLocation)
    {
        return $this->setRightsLocationEntityRight(self::VIEW_RIGHT, 0, 0, $rightsLocation->getId());
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param bool $returnLocation
     *
     * @return boolean
     */
    public function createItemRightsLocation(Item $item, bool $returnLocation = false)
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);

        return $this->createRightsLocationFromParameters(
            self::TYPE_ITEM, $item->getId(), false, $parentLocation->getId(), false, 0, self::TREE_TYPE_ROOT,
            $returnLocation
        );
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool|\Chamilo\Core\Rights\Domain\RightsLocation
     */
    public function determineParentRightsLocationForItem(Item $item)
    {
        $parentIdentifier = $item->get_parent();

        if (!$parentIdentifier)
        {
            return $this->getRootLocation();
        }
        else
        {
            return $this->findRightsLocationByParameters($parentIdentifier, self::TYPE_ITEM);
        }
    }
}