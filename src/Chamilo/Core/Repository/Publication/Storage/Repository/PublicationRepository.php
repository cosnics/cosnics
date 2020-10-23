<?php
namespace Chamilo\Core\Repository\Publication\Storage\Repository;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use DomainException;
use InvalidArgumentException;
use ReflectionClass;

/**
 * Repository to manage publications with their content objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationRepository
{

    /**
     * Retrieves publications with their content objects.
     * The publication class should be an instance of
     * Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
     * Optionally add the content object type class name to limit the retrieval of content objects to a specific type
     * and to join with the additional attributes of that type
     *
     * @param RecordRetrievesParameters $baseRecordRetrievesParameters
     * @param string $publicationClassName
     * @param string $contentObjectTypeClassName
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication[]
     */
    function getPublicationsWithContentObjects(RecordRetrievesParameters $baseRecordRetrievesParameters,
        $publicationClassName, $contentObjectTypeClassName = null)
    {
        $this->checkPublicationClassName($publicationClassName);

        $propertiesArray = array();

        $propertiesArray[] = new PropertiesConditionVariable($publicationClassName);

        foreach (ContentObject::get_default_property_names() as $property_name)
        {
            if ($property_name != ContentObject::PROPERTY_ID)
            {
                $propertiesArray[] = new PropertyConditionVariable(ContentObject::class, $property_name);
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
        }

        $properties = new DataClassProperties($propertiesArray);

        $properties->merge($baseRecordRetrievesParameters->get_properties());

        $recordRetrievesParameters = new RecordRetrievesParameters(
            $properties,
            $baseRecordRetrievesParameters->get_condition(),
            $baseRecordRetrievesParameters->get_count(),
            $baseRecordRetrievesParameters->get_offset(),
            $baseRecordRetrievesParameters->get_order_by(),
            $this->getPublicationJoins(
                $baseRecordRetrievesParameters->get_joins(),
                $publicationClassName,
                $contentObjectTypeClassName),
            $baseRecordRetrievesParameters->get_group_by());

        $records = DataManager::records(
            $publicationClassName,
            $recordRetrievesParameters);

        return $this->hydratePublications($records, $publicationClassName, $contentObjectTypeClassName);
    }

    /**
     * Counts publications with their content objects.
     * The publication class should be an instance of
     * Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
     * Optionally add the content object type class name to limit the retrieval of content objects to a specific type
     * and to join with the additional attributes of that type
     *
     * @param DataClassCountParameters $baseCountParameters
     * @param string $publicationClassName
     * @param string $contentObjectTypeClassName
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication[]
     */
    public function countPublicationsWithContentObjects(DataClassCountParameters $baseCountParameters,
        $publicationClassName, $contentObjectTypeClassName = null)
    {
        $this->checkPublicationClassName($publicationClassName);

        $parameters = new DataClassCountParameters(
            $baseCountParameters->getCondition(),
            $this->getPublicationJoins(
                $baseCountParameters->getJoins(),
                $publicationClassName,
                $contentObjectTypeClassName),
            $baseCountParameters->getDataClassProperties());

        return DataManager::count($publicationClassName, $parameters);
    }

    /**
     * Validates the publication class name to be a valid publication class
     *
     * @param string $publicationClassName
     */
    protected function checkPublicationClassName($publicationClassName)
    {
        $reflectionClass = new ReflectionClass($publicationClassName);
        if (! $reflectionClass->isSubclassOf(Publication::class))
        {
            throw new InvalidArgumentException(
                sprintf(
                    'The given publication class does not extend ' .
                         'Chamilo\Core\Repository\Publication\Storage\DataClass\Publication ' .
                         'and can therefor not be used in the function %s',
                        __FUNCTION__));
        }
    }

    /**
     * Gets the joins between the publication class and the content object (and optionally to the specific content
     * object)
     *
     * @param Joins $baseJoins
     * @param string $publicationClassName
     * @param string $contentObjectTypeClassName
     *
     * @return Joins
     */
    protected function getPublicationJoins(Joins $baseJoins = null, $publicationClassName,
        $contentObjectTypeClassName = null)
    {
        $joins = new Joins();
        $joins->add($this->getPublicationToContentObjectJoin($publicationClassName));

        if ($contentObjectTypeClassName)
        {
            $joins->add($this->getSpecificContentObjectJoin($contentObjectTypeClassName));
        }

        $joins->merge($baseJoins);

        return $joins;
    }

    /**
     *
     * @param string $publicationClassName
     *
     * @return Join
     */
    protected function getPublicationToContentObjectJoin($publicationClassName)
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable($publicationClassName, Publication::PROPERTY_CONTENT_OBJECT_ID),
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID));

        return new Join(ContentObject::class, $joinCondition);
    }

    /**
     *
     * @param string $contentObjectTypeClassName
     *
     * @return Join
     */
    protected function getSpecificContentObjectJoin($contentObjectTypeClassName)
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
            new PropertyConditionVariable($contentObjectTypeClassName, DataClass::PROPERTY_ID));

        return new Join($contentObjectTypeClassName, $joinCondition);
    }

    /**
     *
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
            array_intersect_key($record, array_flip($publicationClassName::get_default_property_names())));

        $defaultProperties = array_intersect_key($record, array_flip(ContentObject::get_default_property_names()));

        $defaultProperties[ContentObject::PROPERTY_ID] = $publication->get_content_object_id();

        $additionalProperties = array();

        if ($contentObjectTypeClassName)
        {
            if ($record[ContentObject::PROPERTY_TYPE] == $contentObjectTypeClassName)
            {
                $additionalProperties = array_intersect_key(
                    $record,
                    array_flip($contentObjectTypeClassName::get_additional_property_names()));

                $additionalProperties[DataClass::PROPERTY_ID] = $publication->get_content_object_id();
            }
            else
            {
                throw new DomainException(
                    sprintf(
                        'Invalid content object type. Expected %s got %s',
                        $contentObjectTypeClassName,
                        $record[ContentObject::PROPERTY_TYPE]));
            }
        }
        else
        {
            $contentObjectTypeClassName = ContentObject::class;
        }

        $contentObject = new $contentObjectTypeClassName($defaultProperties, $additionalProperties);

        $publication->setContentObject($contentObject);

        return $publication;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Iterator\DataClassIterator $records
     *
     * @param string $publicationClassName
     * @param string $contentObjectTypeClassName
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication[]
     */
    protected function hydratePublications($records, $publicationClassName, $contentObjectTypeClassName = null)
    {
        $publications = array();

        foreach($records as $record)
        {
            $publications[] = $this->hydratePublication($record, $publicationClassName, $contentObjectTypeClassName);
        }

        return $publications;
    }
}