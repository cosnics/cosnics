<?php
namespace Chamilo\Core\Metadata\Vocabulary\Service;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

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
     * @var \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    private $propertyProviderService;

    /**
     * @param \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService $propertyProviderService
     */
    public function __construct(PropertyProviderService $propertyProviderService)
    {
        $this->propertyProviderService = $propertyProviderService;
    }

    /**
     *
     * @param string[] $values
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary
     * @throws \Exception
     */
    public function createVocabulary($values)
    {
        $vocabulary = new Vocabulary();
        $vocabulary->set_element_id($values[Vocabulary::PROPERTY_ELEMENT_ID]);
        $vocabulary->set_user_id($values[Vocabulary::PROPERTY_USER_ID]);
        $vocabulary->set_value($values[Vocabulary::PROPERTY_VALUE]);
        $vocabulary->set_default_value($values[Vocabulary::PROPERTY_DEFAULT_VALUE]);

        if (!$vocabulary->create())
        {
            throw new Exception(
                Translation::get('ObjectCreationFailed', array('OBJECT' => 'Vocabulary'), StringUtilities::LIBRARIES)
            );
        }

        return $vocabulary;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $providedPropertyValue
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary
     * @throws \Exception
     */
    public function createVocabularyByElementUserValue(Element $element, User $user, $providedPropertyValue)
    {
        $vocabularyValues = [];

        $vocabularyValues[Vocabulary::PROPERTY_ELEMENT_ID] = $element->getId();
        $vocabularyValues[Vocabulary::PROPERTY_VALUE] = $providedPropertyValue;
        $vocabularyValues[Vocabulary::PROPERTY_DEFAULT_VALUE] = 0;

        if (!$element->usesVocabulary() || $element->isVocabularyUserDefined())
        {
            $vocabularyValues[Vocabulary::PROPERTY_USER_ID] = $user->getId();
        }
        else
        {
            throw new Exception(Translation::get('AddingPredefinedVocabularyViaProvidersNotAllowed'));
        }

        return $this->createVocabulary($vocabularyValues);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary[]
     * @throws \ReflectionException
     */
    public function getDefaultVocabulariesForUserEntitySchemaInstanceElement(
        User $user, SchemaInstance $schemaInstance, Element $element
    )
    {
        if (!$element->usesVocabulary())
        {
            throw new Exception(Translation::get('ElementDoesNotUseVocabularies'));
        }

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_DEFAULT_VALUE),
            new StaticConditionVariable(1)
        );

        if (($element->usesVocabulary() && $element->isVocabularyUserDefined()) || !$element->usesVocabulary())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID),
                new StaticConditionVariable($user->getId())
            );
        }

        if ($element->usesVocabulary() && $element->isVocabularyPredefined())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID),
                new StaticConditionVariable(0)
            );
        }

        $condition = new AndCondition($conditions);

        return DataManager::retrieves(Vocabulary::class, new DataClassRetrievesParameters($condition));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return string
     * @throws \Exception
     */
    public function getFallbackValueForUserEntitySchemaInstanceElement(
        User $user, DataClassEntity $entity, SchemaInstance $schemaInstance, Element $element
    )
    {
        if ($element->usesVocabulary())
        {
            throw new Exception(Translation::get('ElementUsesVocabularies'));
        }

        return $this->getProvidedValueForUserEntitySchemaInstanceElement($user, $entity, $schemaInstance, $element);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return string[]
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getFallbackVocabulariesForUserEntitySchemaInstanceElement(
        User $user, DataClassEntity $entity, SchemaInstance $schemaInstance, Element $element
    )
    {
        if (!$element->usesVocabulary())
        {
            throw new Exception(Translation::get('ElementDoesNotUseVocabularies'));
        }

        $values = [];

        $providedVocabularies = $this->getProvidedVocabulariesForUserEntitySchemaInstanceElement(
            $user, $entity, $schemaInstance, $element
        );

        foreach ($providedVocabularies as $providedVocabulary)
        {
            $values[$providedVocabulary->get_id()] = $providedVocabulary;
        }

        $defaultVocabularies = $this->getDefaultVocabulariesForUserEntitySchemaInstanceElement(
            $user, $schemaInstance, $element
        );

        foreach ($defaultVocabularies as $defaultVocabulary)
        {
            if (!isset($values[$defaultVocabulary->getId()]))
            {
                $values[$defaultVocabulary->getId()] = $defaultVocabulary;
            }
        }

        return $values;
    }

    /**
     * @return \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    public function getPropertyProviderService(): PropertyProviderService
    {
        return $this->propertyProviderService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService $propertyProviderService
     */
    public function setPropertyProviderService(PropertyProviderService $propertyProviderService): void
    {
        $this->propertyProviderService = $propertyProviderService;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getProvidedValueForUserEntitySchemaInstanceElement(
        User $user, DataClassEntity $entity, SchemaInstance $schemaInstance, Element $element
    )
    {
        if ($element->usesVocabulary())
        {
            throw new Exception(Translation::get('ElementUsesVocabularies'));
        }

        try
        {
            $providedPropertyValues = (array) $this->getPropertyProviderService()->getPropertyValues($entity, $element);

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
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getProvidedVocabulariesForUserEntitySchemaInstanceElement(
        User $user, DataClassEntity $entity, SchemaInstance $schemaInstance, Element $element
    )
    {
        if (!$element->usesVocabulary())
        {
            throw new Exception(Translation::get('ElementDoesNotUseVocabularies'));
        }

        $values = [];

        try
        {
            $providedPropertyValues = (array) $this->getPropertyProviderService()->getPropertyValues($entity, $element);

            if (count($providedPropertyValues) > 0)
            {
                foreach ($providedPropertyValues as $providedPropertyValue)
                {
                    $vocabulary = $this->getVocabularyByElementUserValue($element, $user, $providedPropertyValue);

                    if (!$vocabulary instanceof Vocabulary)
                    {
                        try
                        {
                            $vocabulary = $this->createVocabularyByElementUserValue(
                                $element, $user, $providedPropertyValue
                            );
                        }
                        catch (Exception $exception)
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
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $providedPropertyValue
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getVocabularyByElementUserValue(Element $element, User $user, $providedPropertyValue)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_VALUE),
            new StaticConditionVariable($providedPropertyValue)
        );

        if (($element->usesVocabulary() && $element->isVocabularyUserDefined()) || !$element->usesVocabulary())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID),
                new StaticConditionVariable($user->get_id())
            );
        }

        if ($element->usesVocabulary() && $element->isVocabularyPredefined())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID),
                new StaticConditionVariable(0)
            );
        }

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(Vocabulary::class, new DataClassRetrieveParameters($condition));
    }
}