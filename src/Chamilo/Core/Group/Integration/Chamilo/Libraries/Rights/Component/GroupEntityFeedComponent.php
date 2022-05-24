<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Component;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Manager;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Iterator\DataClassCollection;

/**
 *
 * @package Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupEntityFeedComponent extends Manager
{
    /**
     * The length for the filter prefix to remove
     */
    const FILTER_PREFIX_LENGTH = 2;

    const PARAM_FILTER = 'filter';

    const PARAM_GROUP = 'group';

    const PARAM_OFFSET = 'offset';

    const PARAM_SEARCH_QUERY = 'query';

    const PARAM_USER = 'user';

    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements());

        $userCount = $this->countUsers();

        if ($userCount > 0)
        {
            $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $userCount);
        }

        $result->display();
    }

    /**
     * @return integer
     * @throws \Exception
     */
    protected function countUsers()
    {
        $userIdentifiers = $this->getUserIdentifiers();

        if (count($userIdentifiers) == 0)
        {
            return 0;
        }

        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        return $this->getUserService()->countUsersForSearchQueryAndUserIdentifiers($searchQuery, $userIdentifiers);
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function findGroups()
    {
        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        return $this->getGroupService()->findGroupsForSearchQueryAndParentIdentifier($searchQuery, $this->getFilter());
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     * @throws \Exception
     */
    private function findUsers()
    {
        $userIdentifiers = $this->getUserIdentifiers();

        if (count($userIdentifiers) == 0)
        {
            return new DataClassCollection(User::class, []);
        }

        $searchQuery = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        return $this->getUserService()->findUsersForSearchQueryAndUserIdentifiers(
            $searchQuery, $userIdentifiers, $this->getOffset(), 100
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     * @throws \Exception
     */
    private function getElements()
    {
        $elements = new AdvancedElementFinderElements();
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add groups
        $groups = $this->findGroups();

        if ($groups->count() > 0)
        {
            $groupLabel = $this->getTranslator()->trans('Groups', [], 'Chamilo\Core\Group');
            $groupCategory = new AdvancedElementFinderElement('groups', $glyph->getClassNamesString(), $groupLabel, $groupLabel);
            $elements->add_element($groupCategory);

            foreach ($groups as $group)
            {
                $groupCategory->add_child($this->getGroupElement($group));
            }
        }

        // Add users
        $users = $this->findUsers();

        if ($users->count() > 0)
        {
            $userLabel = $this->getTranslator()->trans('Users', [], 'Chamilo\Core\User');
            $userCategory = new AdvancedElementFinderElement('users',  $glyph->getClassNamesString(), $userLabel, $userLabel);
            $elements->add_element($userCategory);

            foreach ($users as $user)
            {
                $userCategory->add_child($this->getUserElement($user));
            }
        }

        return $elements;
    }

    /**
     * @return string
     */
    protected function getFilter()
    {
        $filter = $this->getRequest()->request->get(self::PARAM_FILTER);

        return substr($filter, static::FILTER_PREFIX_LENGTH);
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    public function getGroupElement(Group $group)
    {
        $description = strip_tags($group->get_fully_qualified_name() . ' [' . $group->get_code() . ']');
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        return new AdvancedElementFinderElement(
            GroupEntityProvider::ENTITY_TYPE . '_' . $group->getId(), $glyph->getClassNamesString(), $group->get_name(),
            $description, AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    /**
     *
     * @return integer
     */
    protected function getOffset()
    {
        return $this->getRequest()->request->get(self::PARAM_OFFSET, 0);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    public function getUserElement(User $user)
    {
        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        return new AdvancedElementFinderElement(
            UserEntityProvider::ENTITY_TYPE . '_' . $user->getId(), $glyph->getClassNamesString(), $user->get_fullname(),
            $user->get_official_code()
        );
    }

    /**
     * @return integer[]
     * @throws \Exception
     */
    public function getUserIdentifiers()
    {
        $filterIdentifier = $this->getFilter();

        if (!$filterIdentifier)
        {
            return [];
        }

        return $this->getGroupMemberShipService()->findSubscribedUserIdentifiersForGroupIdentifier($filterIdentifier);
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupMembershipService
     */
    protected function getGroupMemberShipService()
    {
        return $this->getService(GroupMembershipService::class);
    }
}
