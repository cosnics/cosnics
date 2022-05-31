<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

class CourseGroupUserFeedComponent extends Manager
{
    const PARAM_FILTER = 'filter';
    const PARAM_OFFSET = 'offset';
    const PARAM_SEARCH_QUERY = 'query';

    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    private $course;

    /**
     * @var integer[]
     */
    private $coursePlatformGroupUserIdentifiers;

    /**
     * @var integer[]
     */
    private $courseUserIdentifiers;

    /**
     * @var integer[]
     */
    private $userIdentifiers;

    /**
     * @throws \Exception
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->countUsers());

        $result->display();
    }

    /**
     * @return integer
     * @throws \Exception
     */
    protected function countUsers()
    {
        return \Chamilo\Core\User\Storage\DataManager::count(
            User::class, new DataClassCountParameters($this->getUserCondition())
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected function getCourse()
    {
        if (!isset($this->course))
        {

            $this->course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
                Course::class, $this->getCourseIdentifier()
            );
        }

        return $this->course;
    }

    protected function getCourseIdentifier()
    {
        return $this->getPostDataValue(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
    }

    /**
     * @return integer[]
     * @throws \Exception
     */
    protected function getCoursePlatformGroupUserIdentifiers()
    {
        if (!isset($this->coursePlatformGroupUserIdentifiers))
        {
            $this->coursePlatformGroupUserIdentifiers = [];

            $groupRelations = $this->getCourse()->get_subscribed_groups();

            if (count($groupRelations) > 0)
            {
                foreach ($groupRelations as $groupRelation)
                {
                    $group = DataManager::retrieve_by_id(Group::class, $groupRelation->getEntityId());

                    if ($group instanceof Group)
                    {
                        $groupUserIdentifiers =
                            $this->getGroupsTreeTraverser()->findUserIdentifiersForGroup($group, true, true);

                        $this->coursePlatformGroupUserIdentifiers =
                            array_merge($this->coursePlatformGroupUserIdentifiers, $groupUserIdentifiers);
                    }
                }
            }
        }

        return $this->coursePlatformGroupUserIdentifiers;
    }

    /**
     * @return integer[]
     */
    protected function getCourseUserIdentifiers()
    {
        if (!isset($this->courseUserIdentifiers))
        {
            $conditions = [];

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($this->getCourse()->getId())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)
            );

            $parameters = new DataClassDistinctParameters(
                new AndCondition($conditions), new RetrieveProperties(
                    array(
                        new PropertyConditionVariable(
                            CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                        )
                    )
                )
            );

            $this->courseUserIdentifiers = DataManager::distinct(CourseEntityRelation::class, $parameters);
        }

        return $this->courseUserIdentifiers;
    }

    /**
     * @return string[][]
     * @throws \Exception
     */
    protected function getElements()
    {
        $elements = new AdvancedElementFinderElements();

        $folderGlyph = new FontAwesomeGlyph('folder', [], null, 'fas');
        $courseUserGlyph = new FontAwesomeGlyph('user', [], null, 'fas');
        $platformGroupUserGlyph = new FontAwesomeGlyph('link', [], null, 'fas');

        $course = $this->getCourse();

        $parentElement = new AdvancedElementFinderElement(
            'users', $folderGlyph->getClassNamesString(), $course->get_title(), $course->get_title(),
            AdvancedElementFinderElement::TYPE_VISUAL
        );

        foreach ($this->retrieveUsers() as $user)
        {
            $userGlyph = $this->isPlatformGroupUser($user) ? $platformGroupUserGlyph : $courseUserGlyph;

            $parentElement->add_child(
                new AdvancedElementFinderElement(
                    'user_' . $user->getId(), $userGlyph->getClassNamesString(), $user->get_fullname(),
                    $user->get_username(), AdvancedElementFinderElement::TYPE_SELECTABLE
                )
            );
        }

        if ($parentElement->hasChildren())
        {
            $elements->add_element($parentElement);
        }

        return $elements->as_array();
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupsTreeTraverser
     */
    protected function getGroupsTreeTraverser()
    {
        return $this->getService(GroupsTreeTraverser::class);
    }

    /**
     * @return integer
     */
    protected function getOffset()
    {
        return $this->getRequest()->request->get(self::PARAM_OFFSET, 0);
    }

    public function getRequiredPostParameters()
    {
        return array(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
    }

    /**
     * @return string
     */
    protected function getSearchQuery()
    {
        return $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     * @throws \Exception
     */
    protected function getUserCondition()
    {
        $searchQuery = $this->getSearchQuery();

        $conditions = [];

        if (!empty($searchQuery))
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, array(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)
                )
            );
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ID), $this->getUserIdentifiers()
        );

        return new AndCondition($conditions);
    }

    /**
     * @return integer[]
     * @throws \Exception
     */
    protected function getUserIdentifiers()
    {
        if (!isset($this->userIdentifiers))
        {
            $courseUserIdentifiers = $this->getCourseUserIdentifiers();
            $coursePlatformGroupUserIdentifiers = $this->getCoursePlatformGroupUserIdentifiers();

            $this->userIdentifiers = array_merge($courseUserIdentifiers, $coursePlatformGroupUserIdentifiers);
        }

        return $this->userIdentifiers;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Exception
     */
    protected function isPlatformGroupUser(User $user)
    {
        return in_array($user->getId(), $this->getCoursePlatformGroupUserIdentifiers());
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     * @throws \Exception
     */
    protected function retrieveUsers()
    {
        $order = array(
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), SORT_ASC),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), SORT_ASC)
        );

        return \Chamilo\Core\User\Storage\DataManager::retrieves(
            User::class,
            new DataClassRetrievesParameters($this->getUserCondition(), 100, $this->getOffset(), new OrderBy($order))
        );
    }
}
