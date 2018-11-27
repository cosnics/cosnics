<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocation;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Core\Rights\Service\RightsService
{
    const TYPE_ITEM = 1;

    const VIEW_RIGHT = 1;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @param \Chamilo\Core\Rights\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator,
        ConfigurationConsulter $configurationConsulter
    )
    {
        parent::__construct($rightsRepository, $userService, $translator);

        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
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
     * @return boolean
     */
    public function createItemRightsLocationWithViewRightForEveryone(Item $item)
    {
        $rightsLocation = $this->createItemRightsLocation($item, true);

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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function moveItemRightsLocation(Item $item)
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);
        $itemLocation = $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);

        return $this->getRightsRepository()->moveRightsLocation($itemLocation, $parentLocation->getId());
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\RightsLocation $rightsLocation
     *
     * @return boolean
     */
    public function setRightsLocationViewRightForEveryone(RightsLocation $rightsLocation)
    {
        return $this->setRightsLocationEntityRight(self::VIEW_RIGHT, 0, 0, $rightsLocation->getId());
    }

    public function deleteItemRightsLocation(Item $item)
    {
        $rightsLocation = $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);

        return $this->deleteRightsLocation($rightsLocation);
    }

    /**
     * @return boolean
     */
    public function areRightsEnabled()
    {
        $setting = $this->getConfigurationConsulter()->getSetting(array('Chamilo\Core\Menu', 'enable_rights'));

        return $setting == 1;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function isUserAllowedToAccessComponent(User $user)
    {
        if (!$user->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $rightsAreEnabled = $this->areRightsEnabled();

        if (!$rightsAreEnabled)
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function canUserViewItem(User $user, Item $item)
    {
        if (!$item->isIdentified())
        {
            return true;
        }

        //TODO: Add the actual rights check here

        return true;
    }
}