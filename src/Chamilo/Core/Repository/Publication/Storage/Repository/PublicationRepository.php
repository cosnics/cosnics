<?php

namespace Chamilo\Core\Repository\Publication\Storage\Repository;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\RecordResultSet;

/**
 * Repository to manage publications with their content objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationRepository
{
    /**
     * Retrieves publications with their content objects. The publication class should be an instance of
     * Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
     *
     * Optionally add the content object type class name to limit the retrieval of content objects to a specific type
     * and to join with the additional attributes of that type
     *
     * @param RecordRetrievesParameters $baseRecordRetrievesParameters
     * @param string $publicationClassName
     * @param string $contentObjectTypeClassName
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication[]
     */
    function getPublicationsWithContentObjects(
        RecordRetrievesParameters $baseRecordRetrievesParameters, $publicationClassName,
        $contentObjectTypeClassName = null
    )
    {
        $reflectionClass = new \ReflectionClass($publicationClassName);
        if (!$reflectionClass->isSubclassOf(Publication::class_name()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given publication class does not extend ' .
                    'Chamilo\Core\Repository\Publication\Storage\DataClass\Publication ' .
                    'and can therefor not be used in the function %s',
                    __FUNCTION__
                )
            );
        }

        $joins = new Joins();
        $joins->add($this->getPublicationToContentObjectJoin($publicationClassName));

        $propertiesArray = array();

        $propertiesArray[] = new PropertiesConditionVariable($publicationClassName);

        foreach (ContentObject::get_default_property_names() as $property_name)
        {
            if ($property_name != ContentObject::PROPERTY_ID)
            {
                $propertiesArray[] = new PropertyConditionVariable(ContentObject::class_name(), $property_name);
            }
        }

        if ($contentObjectTypeClassName)
        {
            foreach ($contentObjectTypeClassName::get_additional_property_names() as $property_name)
            {
                if ($property_name != DataClass::PROPERTY_ID)
                {
                    $propertiesArray[] = new PropertyConditionVariable($contentObjectTypeClassName, $property_name);
                }
            }

            $joins->add($this->getSpecificContentObjectJoin($contentObjectTypeClassName));
        }

        $properties = new DataClassProperties($propertiesArray);

        $properties->merge($baseRecordRetrievesParameters->get_properties());
        $joins->merge($baseRecordRetrievesParameters->get_joins());

        $recordRetrievesParameters = new RecordRetrievesParameters(
            $properties, $baseRecordRetrievesParameters->get_condition(),
            $baseRecordRetrievesParameters->get_count(), $baseRecordRetrievesParameters->get_offset(),
            $baseRecordRetrievesParameters->get_order_by(), $joins, $baseRecordRetrievesParameters->get_group_by()
        );

        $records = \Chamilo\Application\Weblcms\Storage\DataManager::records(
            $publicationClassName, $recordRetrievesParameters
        );

        return $this->hydratePublications($records, $publicationClassName, $contentObjectTypeClassName);
    }

    /**
     * @param string $publicationClassName
     *
     * @return Join
     */
    protected function getPublicationToContentObjectJoin($publicationClassName)
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(
                $publicationClassName,
                Publication::PROPERTY_CONTENT_OBJECT_ID
            ),
            new PropertyConditionVariable(
                ContentObject::class_name(),
                ContentObject::PROPERTY_ID
            )
        );

        return new Join(ContentObject::class_name(), $joinCondition);
    }

    /**
     * @param string $contentObjectTypeClassName
     *
     * @return Join
     */
    protected function getSpecificContentObjectJoin($contentObjectTypeClassName)
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObject::class_name(),
                ContentObject::PROPERTY_ID
            ),
            new PropertyConditionVariable(
                $contentObjectTypeClassName,
                DataClass::PROPERTY_ID
            )
        );

        return new Join($contentObjectTypeClassName, $joinCondition);
    }

    /**
     * @param array $record
     * @param string $publicationClassName
     * @param string $contentObjectTypeClassName
     *
     * @return Publication
     */
    protected function hydratePublication($record, $publicationClassName, $contentObjectTypeClassName = null)
    {
        /** @var Publication $publication */
        $publication = new $publicationClassName(
            array_intersect_key($record, array_flip($publicationClassName::get_default_property_names()))
        );

        if ($record[ContentObject::PROPERTY_TYPE] == $contentObjectTypeClassName)
        {
            $defaultProperties = array_intersect_key(
                $record, array_flip(ContentObject::get_default_property_names())
            );

            $defaultProperties[ContentObject::PROPERTY_ID] = $publication->get_content_object_id();

            $additionalProperties = array();

            if ($contentObjectTypeClassName)
            {
                $additionalProperties = array_intersect_key(
                    $record, array_flip($contentObjectTypeClassName::get_additional_property_names())
                );

                $additionalProperties[DataClass::PROPERTY_ID] = $publication->get_content_object_id();
            }
            else
            {
                $contentObjectTypeClassName = ContentObject::class_name();
            }

            $contentObject = new $contentObjectTypeClassName($defaultProperties, $additionalProperties);

            $publication->setContentObject($contentObject);
        }
        else
        {
            throw new \DomainException(
                sprintf(
                    'Invalid content object type. Expected %s got %s', $contentObjectTypeClassName,
                    $record[ContentObject::PROPERTY_TYPE]
                )
            );
        }

        return $publication;
    }

    /**
     * @param RecordResultSet $records
     *
     * @param string $publicationClassName
     * @param string $contentObjectTypeClassName
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication[]
     */
    protected function hydratePublications($records, $publicationClassName, $contentObjectTypeClassName = null)
    {
        $publications = array();

        while ($record = $records->next_result())
        {
            $publications[] = $this->hydratePublication($record, $publicationClassName, $contentObjectTypeClassName);
        }

        return $publications;
    }
}