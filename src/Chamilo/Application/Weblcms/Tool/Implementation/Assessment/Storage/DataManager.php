<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class represents the data manager for this package
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package application.weblcms.tool.assignment
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'weblcms_assessment_';

    /**
     * **************************************************************************************************************
     * Assessments functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Retrieves the basic data for a raw export of the results in the assessment tool.
     * This method uses dynamic
     * variables and dynamic query construction. For each new table added, care must be taken. Example: Add new table
     * 'x' for class 'X'. The following constants must also be declared. $x_ref = 'x'; $x_table =
     * {DataManager}::getInstance()->escape_table_name(X::get_table_name()); //DataManager must be the appropriate data
     * manager. $x_alias = {DataManager}::getInstance()->get_alias(X::get_table_name()); //DataManager must be the
     * appropriate data manager. Failure to declare them will break the code using dynamic variables. New columns to be
     * added to $select. New tables to be added to $table_aliases using $x_ref. New joins to be added to
     * $join_declarations. Subselects are not catered for.
     * 
     * @param $condition type conditions to be applied to the query.
     * @param $offset type the required offset.
     * @param $max_objects type the required number ob rows.
     * @param $order_by type the order in which the rows are to be returned.
     * @return \libraries\storage\RecordResultSet
     */
    public static function retrieve_assessment_raw_export_data($condition = null, $offset = null, $max_objects = null, 
        $order_by = null)
    {
        $properties = new DataClassProperties();
        
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        
        $properties->add(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ASSESSMENT_ID));
        
        $properties->add(new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        
        $properties->add(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_START_TIME));
        
        $properties->add(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_TOTAL_TIME));
        
        $properties->add(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_TOTAL_SCORE));
        
        $properties->add(
            new PropertyConditionVariable(QuestionAttempt::class_name(), QuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID));
        
        $properties->add(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER));
        
        $properties->add(
            new PropertyConditionVariable(QuestionAttempt::class_name(), QuestionAttempt::PROPERTY_ANSWER));
        
        $properties->add(
            new PropertyConditionVariable(QuestionAttempt::class_name(), QuestionAttempt::PROPERTY_SCORE));
        
        $joins = new Joins();
        
        $joins->add(
            new Join(
                AssessmentAttempt::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        QuestionAttempt::class_name(), 
                        QuestionAttempt::PROPERTY_ASSESSMENT_ATTEMPT_ID), 
                    new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ID))));
        
        $joins->add(
            new Join(
                User::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_USER_ID), 
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID))));
        
        $joins->add(
            new Join(
                ContentObjectPublication::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        AssessmentAttempt::class_name(), 
                        AssessmentAttempt::PROPERTY_ASSESSMENT_ID), 
                    new PropertyConditionVariable(
                        ContentObjectPublication::class_name(), 
                        ContentObjectPublication::PROPERTY_ID))));
        
        $joins->add(
            new Join(
                ContentObject::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class_name(), 
                        ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID), 
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID))));
        
        $joins->add(
            new Join(
                ComplexContentObjectItem::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        QuestionAttempt::class_name(), 
                        QuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID), 
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class_name(), 
                        ComplexContentObjectItem::PROPERTY_ID))));
        
        $parameters = new RecordRetrievesParameters($properties, $condition, $max_objects, $offset, $order_by, $joins);
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager::records(
            QuestionAttempt::class_name(), 
            $parameters);
    }
}
