<?php
namespace Chamilo\Application\Weblcms\Admin\Storage;

use Chamilo\Application\Weblcms\Admin\Entity\UserEntity;
use Chamilo\Application\Weblcms\Admin\Manager;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Application\Weblcms\Admin\Storage\DataClass\Admin;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Application\Weblcms\Admin\Entity\Helper\UserEntityHelper;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

/**
 *
 * @package Chamilo\Application\Weblcms\Admin\Storage
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'weblcms_';

    /**
     * Gets the type of DataManager to be instantiated
     *
     * @return string
     */
    public static function get_type()
    {
        return 'doctrine';
    }

    public static function user_is_admin($user)
    {
        return self :: entity_is_admin(UserEntity :: ENTITY_TYPE, $user->get_id());
    }

    public static function entity_is_admin($entity_type, $entity_id)
    {
        // Process entity
        $helper_class = Manager :: get_selected_class($entity_type, true);
        $expanded_entities = $helper_class :: expand($entity_id);

        $conditions = array();

        if (count($expanded_entities) > 0)
        {
            $expanded_entities_conditions = array();

            foreach ($expanded_entities as $expanded_entity_type => $expanded_entity_ids)
            {
                foreach ($expanded_entity_ids as $expanded_entity_id)
                {
                    $expanded_entity_conditions = array();

                    $expanded_entity_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($expanded_entity_type));
                    $expanded_entity_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_ID),
                        new StaticConditionVariable($expanded_entity_id));

                    $expanded_entities_conditions[] = new AndCondition($expanded_entity_conditions);
                }
            }

            $condition = new OrCondition($expanded_entities_conditions);

            return DataManager :: count(Admin :: class_name(), new DataClassCountParameters($condition)) > 0;
        }
        else
        {
            return false;
        }
    }

    public static function entity_is_admin_for_target($entity_type, $entity_id, $target_type, $target_id)
    {
        // Process entity
        $helper_class = Manager :: get_selected_class($entity_type, true);
        $expanded_entities = $helper_class :: expand($entity_id);

        $conditions = array();

        if (count($expanded_entities) > 0)
        {
            $expanded_entities_conditions = array();

            foreach ($expanded_entities as $expanded_entity_type => $expanded_entity_ids)
            {
                foreach ($expanded_entity_ids as $expanded_entity_id)
                {
                    $expanded_entity_conditions = array();

                    $expanded_entity_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($expanded_entity_type));
                    $expanded_entity_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_ID),
                        new StaticConditionVariable($expanded_entity_id));

                    $expanded_entities_conditions[] = new AndCondition($expanded_entity_conditions);
                }
            }

            $conditions[] = new OrCondition($expanded_entities_conditions);
        }
        else
        {
            return false;
        }

        // Process target
        $helper_class = Manager :: get_selected_class($target_type, true);
        $expanded_targets = $helper_class :: expand($target_id);

        if (count($expanded_targets) > 0)
        {
            $expanded_targets_conditions = array();

            foreach ($expanded_targets as $expanded_target_type => $expanded_target_ids)
            {
                foreach ($expanded_target_ids as $expanded_target_id)
                {
                    $expanded_target_conditions = array();

                    $expanded_target_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_TARGET_TYPE),
                        new StaticConditionVariable($expanded_target_type));
                    $expanded_target_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_TARGET_ID),
                        new StaticConditionVariable($expanded_target_id));

                    $expanded_targets_conditions[] = new AndCondition($expanded_target_conditions);
                }
            }

            $conditions[] = new OrCondition($expanded_targets_conditions);
        }
        else
        {
            return false;
        }

        $condition = new AndCondition($conditions);

        return DataManager :: count(Admin :: class_name(), new DataClassCountParameters($condition)) > 0;
    }

    public static function retrieve_courses($user)
    {
        $expanded_entities = UserEntityHelper :: expand($user->get_id());

        if (count($expanded_entities) > 0)
        {
            $expanded_entities_conditions = array();

            foreach ($expanded_entities as $expanded_entity_type => $expanded_entity_ids)
            {
                foreach ($expanded_entity_ids as $expanded_entity_id)
                {
                    $expanded_entity_conditions = array();

                    $expanded_entity_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($expanded_entity_type));
                    $expanded_entity_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_ID),
                        new StaticConditionVariable($expanded_entity_id));

                    $expanded_entities_conditions[] = new AndCondition($expanded_entity_conditions);
                }
            }

            $condition = new OrCondition($expanded_entities_conditions);

            $admins = DataManager :: retrieves(Admin :: class_name(), new DataClassRetrievesParameters($condition));
            $course_ids = array();

            while ($admin = $admins->next_result())
            {
                $helper_class = Manager :: get_selected_class($admin->get_target_type(), true);
                $entity_course_ids = $helper_class :: get_course_ids($admin->get_target_id());

                foreach ($entity_course_ids as $entity_course_id)
                {
                    $course_ids[] = $entity_course_id;
                }
            }

            $properties = new DataClassProperties(
                array(
                    new PropertiesConditionVariable(
                        \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: class_name())));

            $parameters = new RecordRetrievesParameters(
                $properties,
                new InCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: class_name(),
                        \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: PROPERTY_ID),
                    $course_ids),
                null,
                null,
                array(
                    new OrderBy(
                        new PropertyConditionVariable(
                            \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: class_name(),
                            \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: PROPERTY_TITLE))));

            return DataManager :: records(
                \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: class_name(),
                $parameters);
        }
        else
        {
            return new ArrayResultSet(array());
        }
    }
}
