<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Home\Manager;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package Chamilo\Core\Repository\Home\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeService
{
    const PARAM_TAB_ID = 'tab';

    /**
     *
     * @var \Chamilo\Core\Home\Repository\HomeRepository
     */
    private $homeRepository;

    /**
     *
     * @param \Chamilo\Core\Home\Repository\HomeRepository $homeRepository
     */
    public function __construct(HomeRepository $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Repository\HomeRepository
     */
    public function getHomeRepository()
    {
        return $this->homeRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Home\Repository\HomeRepository $homeRepository
     */
    public function setHomeRepository($homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    /**
     *
     * @param integer $userIdentifier
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getElementsByUserIdentifier($userIdentifier)
    {
        return $this->getHomeRepository()->findElementsByUserIdentifier($userIdentifier);
    }

    /**
     *
     * @param integer $identifier
     * @return integer
     */
    public function countElementsByUserIdentifier($userIdentifier)
    {
        return $this->getHomeRepository()->countElementsByUserIdentifier($userIdentifier);
    }

    /**
     *
     * @param integer $userIdentifier
     */
    public function createDefaultHomeByUserIdentifier($userIdentifier)
    {
        $defaultElementResultSet = $this->getElementsByUserIdentifier(0);
        $defaultElements = array();

        $elementIdentifierMap = array();

        while ($defaultElement = $defaultElementResultSet->next_result())
        {
            $defaultElements[$defaultElement->get_type()][$defaultElement->getParentId()][] = $defaultElement;
        }

        // Process tabs
        $this->createDefaultElementsByUserIdentifier(
            Tab :: class_name(),
            $defaultElements,
            $elementIdentifierMap,
            $userIdentifier);

        // Process columns
        $this->createDefaultElementsByUserIdentifier(
            Column :: class_name(),
            $defaultElements,
            $elementIdentifierMap,
            $userIdentifier);

        // Process blocks
        $this->createDefaultElementsByUserIdentifier(
            Block :: class_name(),
            $defaultElements,
            $elementIdentifierMap,
            $userIdentifier);

        return true;
    }

    /**
     *
     * @param string $elementType
     * @param \Chamilo\Core\Home\Storage\DataClass\Element[] $defaultElements
     * @param integer[] $elementIdentifierMap
     * @param integer $userIdentifier
     */
    private function createDefaultElementsByUserIdentifier($elementType, $defaultElements, &$elementIdentifierMap,
        $userIdentifier)
    {
        foreach ($defaultElements[$elementType] as $typeParentId => $typeElements)
        {
            foreach ($typeElements as $typeElement)
            {
                if (! $this->createDefaultElementByUserIdentifier($elementIdentifierMap, $typeElement, $userIdentifier))
                {
                    return false;
                }
            }
        }

        return true;
    }

    private function createDefaultElementByUserIdentifier(&$elementIdentifierMap, Element $element, $userIdentifier)
    {
        $originalIdentifier = $element->getId();

        $element->setUserId($userIdentifier);

        if (! $element->isOnTopLevel())
        {
            $element->setParentId($elementIdentifierMap[$element->getParentId()]);
        }

        $result = $element->create();

        $elementIdentifierMap[$originalIdentifier] = $element->get_id();

        if (! $result)
        {
            throw new \Exception(Translation :: get('HomepageDefaultCreationFailed'));
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $type
     * @param integer $parentIdentifier
     */
    public function getElements(User $user = null, $type, $parentIdentifier = 0)
    {
        if (! isset($this->elements))
        {
            $homeUserIdentifier = $this->determineHomeUserIdentifier($user);
            $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());

            if ($userHomeAllowed && $user instanceof User)
            {
                if ($this->countElementsByUserIdentifier($homeUserIdentifier) == 0)
                {
                    $this->createDefaultHomeByUserIdentifier($homeUserIdentifier);
                }
            }

            $elementsResultSet = $this->getElementsByUserIdentifier($homeUserIdentifier);

            while ($element = $elementsResultSet->next_result())
            {
                $this->elements[$element->get_type()][$element->getParentId()][] = $element;
            }
        }

        if (isset($this->elements[$type]) && isset($this->elements[$type][$parentIdentifier]))
        {
            return $this->elements[$type][$parentIdentifier];
        }
        else
        {
            return array();
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function determineHomeUserIdentifier(User $user = null)
    {
        if (! isset($this->homeUserIdentifier))
        {
            $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());
            $generalMode = \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');

            // Get user id
            if ($user instanceof User && $generalMode && $user->is_platform_admin())
            {
                $this->homeUserIdentifier = 0;
            }
            elseif ($userHomeAllowed && $user instanceof User)
            {
                $this->homeUserIdentifier = $user->get_id();
            }
            else
            {
                $this->homeUserIdentifier = 0;
            }
        }

        return $this->homeUserIdentifier;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return integer
     */
    public function getCurrentTabIdentifier(Request $request)
    {
        return $request->query->get(self :: PARAM_TAB_ID);
    }

    /**
     *
     * @return boolean
     */
    public function isUserHomeAllowed()
    {
        return (boolean) PlatformSetting :: get('allow_user_home', Manager :: context());
    }

    /**
     *
     * @return boolean
     */
    public function isInGeneralMode()
    {
        return (boolean) \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
     */
    public function userHasMultipleTabs(User $user = null)
    {
        $tabs = $this->getElements($user, Tab :: class_name());
        return count($tabs) > 1;
    }

    public function tabByUserAndIdentifierHasMultipleColumns(User $user = null, $tabIdentifier)
    {
        $columns = $this->getElements($user, Column :: class_name(), $tabIdentifier);
        return count($columns) > 1;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tabKey
     * @param \Chamilo\Core\Home\Storage\DataClass\Tab $tab
     * @return boolean
     */
    public function isActiveTab(Request $request, $tabKey, Tab $tab)
    {
        $currentTabIdentifier = $this->getCurrentTabIdentifier($request);
        return ($currentTabIdentifier == $tab->getId() || (! isset($currentTabIdentifier) && $tabKey == 0));
    }
}