<?php
namespace Chamilo\Core\Metadata\Vocabulary\Service;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Metadata\Vocabulary\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VocabularyService
{

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getFallbackVocabulariesForUserEntitySchemaInstanceElement(User $user, DataClassEntity $entity,
        SchemaInstance $schemaInstance, Element $element)
    {
        if (! $element->usesVocabulary())
        {
            throw new \Exception(Translation :: get('ElementDoesNotUseVocabularies'));
        }

        $values = array();

        $providedVocabularies = $this->getProvidedVocabulariesForUserEntitySchemaInstanceElement(
            $user,
            $entity,
            $schemaInstance,
            $element);

        foreach ($providedVocabularies as $providedVocabulary)
        {
            $values[$providedVocabulary->get_id()] = $providedVocabulary;
        }

        $defaultVocabularies = $this->getDefaultVocabulariesForUserEntitySchemaInstanceElement(
            $user,
            $schemaInstance,
            $element);

        foreach ($defaultVocabularies as $defaultVocabulary)
        {
            if (! isset($values[$defaultVocabulary->get_id()]))
            {
                $values[$defaultVocabulary->get_id()] = $defaultVocabulary;
            }
        }

        return $values;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getFallbackValueForUserEntitySchemaInstanceElement(User $user, DataClassEntity $entity,
        SchemaInstance $schemaInstance, Element $element)
    {
        if ($element->usesVocabulary())
        {
            throw new \Exception(Translation :: get('ElementUsesVocabularies'));
        }

        return $this->getProvidedValueForUserEntitySchemaInstanceElement($user, $entity, $schemaInstance, $element);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getProvidedVocabulariesForUserEntitySchemaInstanceElement(User $user, DataClassEntity $entity,
        SchemaInstance $schemaInstance, Element $element)
    {
        if (! $element->usesVocabulary())
        {
            throw new \Exception(Translation :: get('ElementDoesNotUseVocabularies'));
        }

        $values = array();

        try
        {
            $propertyProviderService = new PropertyProviderService($entity, $schemaInstance);
            $providedPropertyValues = (array) $propertyProviderService->getPropertyValues($element);

            if (count($providedPropertyValues) > 0)
            {
                foreach ($providedPropertyValues as $providedPropertyValue)
                {
                    $vocabulary = $this->getVocabularyByElementUserValue($element, $user, $providedPropertyValue);

                    if (! $vocabulary instanceof Vocabulary)
                    {
                        try
                        {
                            $vocabulary = $this->createVocabularyByElementUserValue(
                                $element,
                                $user,
                                $providedPropertyValue);
                        }
                        catch (\Exception $exception)
                        {
                            return $values;
                        }
                    }

                    $values[] = $vocabulary;
                }
            }

            return $values;
        }
        catch (NoProviderAvailableException $exception)
        {
            return $values;
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getProvidedValueForUserEntitySchemaInstanceElement(User $user, DataClassEntity $entity,
        SchemaInstance $schemaInstance, Element $element)
    {
        if ($element->usesVocabulary())
        {
            throw new \Exception(Translation :: get('ElementUsesVocabularies'));
        }

        try
        {
            $propertyProviderService = new PropertyProviderService($entity, $schemaInstance);
            $providedPropertyValues = (array) $propertyProviderService->getPropertyValues($element);

            return implode(PHP_EOL, $providedPropertyValues);
        }
        catch (NoProviderAvailableException $exception)
        {
            return '';
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     */
    public function getDefaultVocabulariesForUserEntitySchemaInstanceElement(User $user, SchemaInstance $schemaInstance,
        Element $element)
    {
        if (! $element->usesVocabulary())
        {
            throw new \Exception(Translation :: get('ElementDoesNotUseVocabularies'));
        }

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->get_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_DEFAULT_VALUE),
            new StaticConditionVariable(1));

        if (($element->usesVocabulary() && $element->isVocabularyUserDefined()) || ! $element->usesVocabulary())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_USER_ID),
                new StaticConditionVariable($user->get_id()));
        }

        if ($element->usesVocabulary() && $element->isVocabularyPredefined())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_USER_ID),
                new StaticConditionVariable(0));
        }

        $condition = new AndCondition($conditions);

        return DataManager :: retrieves(Vocabulary :: class_name(), new DataClassRetrievesParameters($condition))->as_array();
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $providedPropertyValue
     */
    public function getVocabularyByElementUserValue(Element $element, User $user, $providedPropertyValue)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->get_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_VALUE),
            new StaticConditionVariable($providedPropertyValue));

        if (($element->usesVocabulary() && $element->isVocabularyUserDefined()) || ! $element->usesVocabulary())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_USER_ID),
                new StaticConditionVariable($user->get_id()));
        }

        if ($element->usesVocabulary() && $element->isVocabularyPredefined())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_USER_ID),
                new StaticConditionVariable(0));
        }

        $condition = new AndCondition($conditions);

        return DataManager :: retrieve(Vocabulary :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Element\Storage\DataClass\Element $element
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $providedPropertyValue
     */
    public function createVocabularyByElementUserValue(Element $element, User $user, $providedPropertyValue)
    {
        $vocabularyValues = array();

        $vocabularyValues[Vocabulary :: PROPERTY_ELEMENT_ID] = $element->get_id();
        $vocabularyValues[Vocabulary :: PROPERTY_VALUE] = $providedPropertyValue;
        $vocabularyValues[Vocabulary :: PROPERTY_DEFAULT_VALUE] = 0;

        if (! $element->usesVocabulary() || $element->isVocabularyUserDefined())
        {
            $vocabularyValues[Vocabulary :: PROPERTY_USER_ID] = $user->get_id();
        }
        else
        {
            throw new \Exception(Translation :: get('AddingPredefinedVocabularyViaProvidersNotAllowed'));
        }

        return $this->createVocabulary($vocabularyValues);
    }

    /**
     *
     * @param string[] $values
     * @throws \Exception
     * @return \Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary
     */
    public function createVocabulary($values)
    {
        $vocabulary = new Vocabulary();
        $vocabulary->set_element_id($values[Vocabulary :: PROPERTY_ELEMENT_ID]);
        $vocabulary->set_user_id($values[Vocabulary :: PROPERTY_USER_ID]);
        $vocabulary->set_value($values[Vocabulary :: PROPERTY_VALUE]);
        $vocabulary->set_default_value($values[Vocabulary :: PROPERTY_DEFAULT_VALUE]);

        if (! $vocabulary->create())
        {
            throw new \Exception(
                Translation :: get(
                    'ObjectCreationFailed',
                    array('OBJECT' => 'Vocabulary'),
                    Utilities :: COMMON_LIBRARIES));
        }

        return $vocabulary;
    }
}