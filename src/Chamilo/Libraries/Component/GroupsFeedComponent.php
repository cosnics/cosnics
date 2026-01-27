<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Feed to return the course groups of this course
 *
 * @author  Sven Vanpoucke
 * @package Chamilo\Libraries\Ajax\Component
 */
abstract class GroupsFeedComponent extends Manager
{
    public const PARAM_FILTER = 'filter';
    public const PARAM_OFFSET = 'offset';
    public const PARAM_SEARCH_QUERY = 'query';

    public const PROPERTY_ELEMENTS = 'elements';
    public const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     * @var int
     */
    protected $user_count = 0;

    public function run(): Response
    {
        $result = new JsonAjaxResult();

        $elements = $this->get_elements();
        $elements = $elements->asArray();

        $result->setProperty(self::PROPERTY_ELEMENTS, $elements);

        if ($this->user_count > 0)
        {
            $result->setProperty(self::PROPERTY_TOTAL_ELEMENTS, $this->user_count);
        }

        return $result->getResponse();
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }

    /**
     * Returns all the elements for this feed
     *
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add groups
        $groups = $this->retrieve_groups();
        if ($groups && $groups->count() > 0)
        {
            $translator = $this->getTranslator();
            // Add group category
            $group_category = new AdvancedElementFinderElement(
                'groups', $glyph->getClassNamesString(), $translator->trans('Groups', [], StringUtilities::LIBRARIES),
                $translator->trans('Groups', [], StringUtilities::LIBRARIES)
            );
            $elements->addElement($group_category);

            foreach ($groups as $group)
            {
                $group_category->addChild($this->get_group_element($group));
            }
        }

        // Add users
        $users = $this->retrieve_users();
        if ($users && $users->count() > 0)
        {
            // Add user category
            $user_category = new AdvancedElementFinderElement('users', $glyph->getClassNamesString(), 'Users', 'Users');
            $elements->addElement($user_category);

            foreach ($users as $user)
            {
                $user_category->addChild($this->get_user_element($user));
            }
        }

        return $elements;
    }

    /**
     * @param \Chamilo\Core\Group\Storage\DataClass\Group $group
     *
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function get_group_element(Group $group): AdvancedElementFinderElement;

    /**
     * @return int
     */
    protected function get_offset()
    {
        $offset = $this->getRequest()->request->get(self::PARAM_OFFSET);
        if (!isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    abstract public function get_user_element(User $user): AdvancedElementFinderElement;

    /**
     * @return int[]
     */
    abstract public function get_user_ids();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     */
    abstract public function retrieve_groups();

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    private function retrieve_users()
    {
        $conditions = [];

        $user_ids = $this->get_user_ids();

        if (count($user_ids) == 0)
        {
            return new ArrayCollection();
        }

        $conditions[] = new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $user_ids);

        $search_query = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $search_query, [
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)
                ]
            );
        }

        $condition = new AndCondition($conditions);

        $this->user_count = $this->getUserService()->countUsers($condition);

        return $this->getUserService()->findUsers(
            $condition, $this->get_offset(), 100, new OrderBy([
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME))
            ])
        );
    }
}
