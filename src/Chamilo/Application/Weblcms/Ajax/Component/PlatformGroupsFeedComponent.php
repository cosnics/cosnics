<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Ajax\Component\GroupsFeedComponent;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Feed to return the platform groups of this course
 *
 * @author  Sven Vanpoucke
 * @package application.weblcms
 */
class PlatformGroupsFeedComponent extends GroupsFeedComponent
{
    public const PARAM_COURSE_ID = 'course_id';

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    /**
     * Returns the required parameters
     *
     * @return string[]
     */
    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_COURSE_ID];
    }

    /**
     * Returns the element for a specific group
     *
     * @return AdvancedElementFinderElement
     */
    public function get_group_element(Group $group): AdvancedElementFinderElement
    {
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        return new AdvancedElementFinderElement(
            CoursePlatformGroupEntity::ENTITY_TYPE . '_' . $group->get_id(), $glyph->getClassNamesString(),
            $group->get_name(), $group->get_code(), AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    /**
     * Returns the element for a specific user
     *
     * @return AdvancedElementFinderElement
     */
    public function get_user_element(User $user): AdvancedElementFinderElement
    {
        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

        return new AdvancedElementFinderElement(
            CourseUserEntity::ENTITY_TYPE . '_' . $user->get_id(), $glyph->getClassNamesString(), $user->get_fullname(),
            $user->get_official_code()
        );
    }

    /**
     * Retrieves all the users for the selected group
     */
    public function get_user_ids()
    {
        $filter = $this->getRequest()->request->get(self::PARAM_FILTER);
        $filter_id = substr($filter, 2);

        if (!$filter_id)
        {
            return [];
        }

        $relations = $this->getGroupMembershipService()->getGroupUserRelationsByGroupIdentifier($filter_id);

        $user_ids = [];

        foreach ($relations as $relation)
        {
            $user_ids[] = $relation->get_user_id();
        }

        return $user_ids;
    }

    /**
     * Returns all the groups for this feed
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieve_groups()
    {
        // Set the conditions for the search query
        $search_query = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
        if ($search_query && $search_query != '')
        {
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $search_query
            );
        }

        // Set the filter conditions
        $filter = $this->getRequest()->request->get(self::PARAM_FILTER);

        // Javascript filter
        if (!is_null($filter))
        {
            $filter_id = substr($filter, 2);
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                new StaticConditionVariable($filter_id)
            );
        }
        else
        {
            $course_id = $this->getRequest()->request->get(self::PARAM_COURSE_ID);

            $groupConditions = [];
            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($course_id)
            );
            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
            );

            $subscribed_group_ids = \Chamilo\Application\Weblcms\Course\Storage\DataManager::distinct(
                CourseEntityRelation::class, new DataClassDistinctParameters(
                    new AndCondition($groupConditions), new RetrieveProperties(
                        [
                            new PropertyConditionVariable(
                                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                            )
                        ]
                    )
                )
            );

            if (count($subscribed_group_ids) == 0)
            {
                return;
            }

            $conditions[] = new InCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $subscribed_group_ids
            );
        }

        // Combine the conditions
        $count = count($conditions);
        if ($count > 1)
        {
            $condition = new AndCondition($conditions);
        }

        if ($count == 1)
        {
            $condition = $conditions[0];
        }

        return $this->getGroupService()->findGroups(
            $condition, null, null,
            new OrderBy([new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME))])
        );
    }
}
