<?php
namespace Chamilo\Core\Repository\Publication\Storage\Repository;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use InvalidArgumentException;

/**
 * Repository to manage publications with their content objects
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationRepository
{

    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Validates the publication class name to be a valid publication class
     */
    protected function checkPublicationClassName(string $publicationClassName): void
    {
        if (!is_subclass_of($publicationClassName, Publication::class))
        {
            throw new InvalidArgumentException(
                sprintf(
                    'The given publication class does not extend ' .
                    'Chamilo\Core\Repository\Publication\Storage\DataClass\Publication ' .
                    'and can therefor not be used in the function %s', __FUNCTION__
                )
            );
        }
    }

    /**
     * Counts publications with their content objects. The publication class should be an instance of
     * Chamilo\Core\Repository\Publication\Storage\DataClass\Publication.
     * Optionally add the content object type class  name to limit the retrieval of content objects to a specific type
     * and to join with the additional attributes of that type
     */
    public function countPublicationsWithContentObjects(
        DataClassCountParameters $baseCountParameters, string $publicationClassName,
        ?string $contentObjectTypeClassName = null
    ): int
    {
        $this->checkPublicationClassName($publicationClassName);

        $parameters = new DataClassCountParameters(
            $baseCountParameters->getCondition(), $this->getPublicationJoins(
            $publicationClassName, $baseCountParameters->getJoins(), $contentObjectTypeClassName
        ), $baseCountParameters->getRetrieveProperties()
        );

        return $this->getDataClassRepository()->count($publicationClassName, $parameters);
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * Gets the joins between the publication class and the content object (and optionally to the specific content
     * object)
     */
    protected function getPublicationJoins(
        string $publicationClassName, ?Joins $baseJoins = null, ?string $contentObjectTypeClassName = null
    ): Joins
    {
        $joins = new Joins();
        $joins->add($this->getPublicationToContentObjectJoin($publicationClassName));

        if ($contentObjectTypeClassName)
        {
            $joins->add($this->getSpecificContentObjectJoin($contentObjectTypeClassName));
        }

        if ($baseJoins instanceof Joins)
        {
            $joins->merge($baseJoins);
        }

        return $joins;
    }

    protected function getPublicationToContentObjectJoin(string $publicationClassName): Join
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable($publicationClassName, Publication::PROPERTY_CONTENT_OBJECT_ID),
            new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID)
        );

        return new Join(ContentObject::class, $joinCondition);
    }

    /**
     * Retrieves publications with their content objects.
     * The publication class should be an instance of
     * Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
     * Optionally add the content object type class name to limit the retrieval of content objects to a specific type
     * and to join with the additional attributes of that type
     *
     * @param ?class-string<\Chamilo\Core\Repository\Storage\DataClass\ContentObject> $contentObjectTypeClassName
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getPublicationsWithContentObjects(
        RecordRetrievesParameters $baseRecordRetrievesParameters, string $publicationClassName,
        ?string $contentObjectTypeClassName = null
    ): array
    {
        $this->checkPublicationClassName($publicationClassName);

        $propertiesArray = [];

        $propertiesArray[] = new PropertiesConditionVariable($publicationClassName);

        foreach (ContentObject::getDefaultPropertyNames() as $property_name)
        {
            if ($property_name != DataClass::PROPERTY_ID)
            {
                $propertiesArray[] = new PropertyConditionVariable(ContentObject::class, $property_name);
            }
        }

        if ($contentObjectTypeClassName)
        {
            foreach ($contentObjectTypeClassName::getAdditionalPropertyNames() as $property_name)
            {
                if ($property_name != DataClass::PROPERTY_ID)
                {
                    $propertiesArray[] = new PropertyConditionVariable($contentObjectTypeClassName, $property_name);
                }
            }
        }

        $properties = new RetrieveProperties($propertiesArray);

        $properties->merge($baseRecordRetrievesParameters->getRetrieveProperties());

        $recordRetrievesParameters = new RecordRetrievesParameters(
            $properties, $baseRecordRetrievesParameters->getCondition(), $baseRecordRetrievesParameters->getCount(),
            $baseRecordRetrievesParameters->getOffset(), $baseRecordRetrievesParameters->getOrderBy(),
            $this->getPublicationJoins(
                $publicationClassName, $baseRecordRetrievesParameters->getJoins(), $contentObjectTypeClassName
            ), $baseRecordRetrievesParameters->getGroupBy()
        );

        $records = $this->getDataClassRepository()->records(
            $publicationClassName, $recordRetrievesParameters
        );

        return $this->hydratePublications($records, $publicationClassName, $contentObjectTypeClassName);
    }

    protected function getSpecificContentObjectJoin(string $contentObjectTypeClassName): Join
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID),
            new PropertyConditionVariable($contentObjectTypeClassName, DataClass::PROPERTY_ID)
        );

        return new Join($contentObjectTypeClassName, $joinCondition);
    }

    /**
     * @param array $record
     * @param class-string<\Chamilo\Core\Repository\Publication\Storage\DataClass\Publication> $publicationClassName
     * @param ?class-string<\Chamilo\Core\Repository\Storage\DataClass\ContentObject> $contentObjectTypeClassName
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
     */
    protected function hydratePublication(
        array $record, string $publicationClassName, ?string $contentObjectTypeClassName = null
    ): Publication
    {
        $publication = new $publicationClassName(
            array_intersect_key($record, array_flip($publicationClassName::getDefaultPropertyNames()))
        );

        $defaultProperties = array_intersect_key($record, array_flip(ContentObject::getDefaultPropertyNames()));

        $defaultProperties[DataClass::PROPERTY_ID] = $publication->get_content_object_id();

        $additionalProperties = [];

        if ($contentObjectTypeClassName)
        {
            if ($record[ContentObject::PROPERTY_TYPE] == $contentObjectTypeClassName)
            {
                $additionalProperties = array_intersect_key(
                    $record, array_flip($contentObjectTypeClassName::getAdditionalPropertyNames())
                );

                $additionalProperties[DataClass::PROPERTY_ID] = $publication->get_content_object_id();
            }
            else
            {
                throw new DomainException(
                    sprintf(
                        'Invalid content object type. Expected %s got %s', $contentObjectTypeClassName,
                        $record[ContentObject::PROPERTY_TYPE]
                    )
                );
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

    protected function hydratePublications(
        ArrayCollection $records, string $publicationClassName, ?string $contentObjectTypeClassName = null
    ): array
    {
        $publications = [];

        foreach ($records as $record)
        {
            $publications[] = $this->hydratePublication($record, $publicationClassName, $contentObjectTypeClassName);
        }

        return $publications;
    }
}