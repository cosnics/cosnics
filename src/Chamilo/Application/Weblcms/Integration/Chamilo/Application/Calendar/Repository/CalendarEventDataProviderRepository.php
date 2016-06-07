<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\RecordResultSet;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to retrieve calendar events for the assignment tool based on the due date of assignments
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CalendarEventDataProviderRepository
{
    /**
     * Retrieves the valid publications for the user
     *
     * @param Course[]   $courses
     * @param int $fromDate
     * @param int $toDate
     *
     * @return ContentObjectPublication[]
     */
    function getPublications($fromDate, $toDate, $courses = array())
    {
        $contentObjectClassName = $this->getContentObjectClassName();

        $propertiesArray = array();

        $propertiesArray[] = new PropertiesConditionVariable(ContentObjectPublication::class_name());

        foreach (ContentObject::get_default_property_names() as $property_name)
        {
            if ($property_name != ContentObject::PROPERTY_ID)
            {
                $propertiesArray[] = new PropertyConditionVariable(ContentObject::class_name(), $property_name);
            }
        }

        foreach ($contentObjectClassName::get_additional_property_names() as $property_name)
        {
            if ($property_name != DataClass::PROPERTY_ID)
            {
                $propertiesArray[] = new PropertyConditionVariable($contentObjectClassName, $property_name);
            }
        }

        $properties = new DataClassProperties($propertiesArray);

        $joins = new Joins(
            array(
                $this->getContentObjectPublicationToContentObjectJoin(),
                $this->getSpecificContentObjectJoin()
            )
        );

        $condition = $this->getPublicationsCondition($fromDate, $toDate, $courses);

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins);

        $records = \Chamilo\Application\Weblcms\Storage\DataManager::records(
            ContentObjectPublication::class_name(), $parameters
        );

        return $this->hydrateContentObjectPublications($records, true);
    }

    /**
     * Retrieves the conditions to retrieve the publications
     *
     * @param Course[]   $courses
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Condition
     */
    protected function getPublicationsCondition($fromDate, $toDate, $courses = array())
    {
        $courseIds = array();
        foreach ($courses as $course)
        {
            $courseIds[] = $course->getId();
        }

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_COURSE_ID
            ),
            $courseIds
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_TOOL
            ),
            new StaticConditionVariable($this->getToolName())
        );

        $conditions[] = $this->getSpecificContentObjectConditions($fromDate, $toDate);

        return new AndCondition($conditions);
    }

    /**
     * @return Join
     */
    protected function getContentObjectPublicationToContentObjectJoin()
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
            ),
            new PropertyConditionVariable(
                ContentObject::class_name(),
                ContentObject::PROPERTY_ID
            )
        );

        return new Join(ContentObject::class_name(), $joinCondition);
    }

    /**
     * @return Join
     */
    protected function getSpecificContentObjectJoin()
    {
        $contentObjectClassName = $this->getContentObjectClassName();

        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObject::class_name(),
                ContentObject::PROPERTY_ID
            ),
            new PropertyConditionVariable(
                $contentObjectClassName,
                DataClass::PROPERTY_ID
            )
        );

        return new Join($contentObjectClassName, $joinCondition);
    }

    /**
     * @param array $publication_record
     * @param bool|false $hydrate_calendar_event_object
     *
     * @return ContentObjectPublication
     */
    protected function hydrateContentObjectPublication(
        $publication_record, $hydrate_calendar_event_object = false
    )
    {
        $contentObjectClassName = $this->getContentObjectClassName();

        $publication = new ContentObjectPublication(
            array_intersect_key($publication_record, array_flip(ContentObjectPublication::get_default_property_names()))
        );

        if ($hydrate_calendar_event_object)
        {
            if ($publication_record[ContentObject::PROPERTY_TYPE] == $contentObjectClassName)
            {
                $default_properties =
                    array_intersect_key($publication_record, array_flip(ContentObject::get_default_property_names()));
                $default_properties[ContentObject::PROPERTY_ID] = $publication->get_content_object_id();

                $additional_properties = array_intersect_key(
                    $publication_record, array_flip($contentObjectClassName::get_additional_property_names())
                );
                $additional_properties[DataClass::PROPERTY_ID] = $publication->get_content_object_id();
                $content_object = new $contentObjectClassName($default_properties, $additional_properties);
            }
            else
            {
                throw new \DomainException("unknown type:" . $publication_record[ContentObject::PROPERTY_TYPE]);
            }

            $publication->setContentObject($content_object);
        }

        return $publication;
    }

    /**
     * @param RecordResultSet $records
     * @param bool $hydrate_calendar_event_object
     *
     * @return ContentObjectPublication[]
     */
    protected function hydrateContentObjectPublications($records, $hydrate_calendar_event_object = false)
    {
        $publication_objects_array = array();

        while ($record = $records->next_result())
        {
            $publication_objects_array[] =
                $this->hydrateContentObjectPublication($record, $hydrate_calendar_event_object);
        }

        return $publication_objects_array;
    }

    /**
     * @return string
     */
    abstract protected function getToolName();

    /**
     * @return string
     */
    abstract protected function getContentObjectClassName();

    /**
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Condition
     */
    abstract protected function getSpecificContentObjectConditions($fromDate, $toDate);
}