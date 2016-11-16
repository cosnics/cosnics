<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'calendar_personal_';

    /**
     *
     * @param Condition $condition
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_shared_personal_calendar_publications(Condition $condition)
    {
        $joins = array();
        $joins[] = new Join(
            PublicationUser::class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID), 
                new PropertyConditionVariable(PublicationUser::class_name(), PublicationUser::PROPERTY_PUBLICATION)), 
            Join::TYPE_LEFT);
        $joins[] = new Join(
            PublicationGroup::class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID), 
                new PropertyConditionVariable(PublicationGroup::class_name(), PublicationGroup::PROPERTY_PUBLICATION)), 
            Join::TYPE_LEFT);
        
        $parameters = new DataClassRetrievesParameters($condition, null, null, array(), new Joins($joins));
        
        return self::retrieves(Publication::class_name(), $parameters);
    }

    /**
     *
     * @param int $publication_id
     * @return boolean
     */
    public static function truncate_users_and_groups_for_publication($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class_name(), PublicationUser::PROPERTY_PUBLICATION), 
            new StaticConditionVariable($publication_id));
        
        if (! self::deletes(PublicationUser::class_name(), $condition))
        {
            return false;
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationGroup::class_name(), PublicationGroup::PROPERTY_PUBLICATION), 
            new StaticConditionVariable($publication_id));
        
        if (! self::deletes(PublicationGroup::class_name(), $condition))
        {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param Publication $publication
     * @return int[]
     */
    public static function retrieve_target_groups(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationGroup::class_name(), PublicationGroup::PROPERTY_PUBLICATION), 
            new StaticConditionVariable($publication->get_id()));
        
        $publication_groups = self::retrieves(
            PublicationGroup::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        $target_groups = array();
        
        while ($publication_group = $publication_groups->next_result())
        {
            $target_groups[] = $publication_group->get_group_id();
        }
        
        return $target_groups;
    }

    /**
     *
     * @param Publication $publication
     * @return int[]
     */
    public static function retrieve_target_users(Publication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser::class_name(), PublicationUser::PROPERTY_PUBLICATION), 
            new StaticConditionVariable($publication->get_id()));
        
        $publication_users = self::retrieves(
            PublicationUser::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        $target_users = array();
        
        while ($publication_user = $publication_users->next_result())
        {
            $target_users[] = $publication_user->get_user();
        }
        
        return $target_users;
    }
}
