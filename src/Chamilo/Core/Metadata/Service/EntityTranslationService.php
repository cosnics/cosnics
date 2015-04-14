<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTranslationService
{
    const PROPERTY_TRANSLATION = 'translation';

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $entity;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     */
    public function __construct(DataClass $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation[]
     */
    public function getEntityTranslationsIndexedByIsocode()
    {
        $conditions = array();
        $translationsIndexedByIsocode = array();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(EntityTranslation :: class_name(), EntityTranslation :: PROPERTY_ENTITY_TYPE),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($this->getEntity()->class_name()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(EntityTranslation :: class_name(), EntityTranslation :: PROPERTY_ENTITY_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($this->getEntity()->get_id()));

        $translations = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieves(
            EntityTranslation :: class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));

        while ($translation = $translations->next_result())
        {
            $translationsIndexedByIsocode[$translation->get_isocode()] = $translation;
        }

        return $translationsIndexedByIsocode;
    }

    /**
     *
     * @param string[] $entityTranslations
     * @return boolean
     */
    public function createEntityTranslations($entityTranslations)
    {
        foreach ($entityTranslations[self :: PROPERTY_TRANSLATION] as $isocode => $value)
        {
            $translation = new EntityTranslation();
            $translation->set_entity_type($this->getEntity()->class_name());
            $translation->set_entity_id($this->getEntity()->get_id());
            $translation->set_isocode($isocode);
            $translation->set_value($value);

            if (! $translation->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param string[] $entityTranslations
     * @return boolean
     */
    public function updateEntityTranslations($entityTranslations)
    {
        $translations = $this->getEntity()->getTranslations();

        foreach ($entityTranslations as $isocode => $value)
        {
            if ($translations[$isocode] instanceof EntityTranslation)
            {
                $translation = $translations[$isocode];
                $translation->set_value($value);

                if (! $translation->update())
                {
                    return false;
                }
            }
            else
            {
                $translation = new EntityTranslation();
                $translation->set_entity_type($this->getEntity()->class_name());
                $translation->set_entity_id($this->getEntity()->get_id());
                $translation->set_isocode($isocode);
                $translation->set_value($value);

                if (! $translation->create())
                {
                    return false;
                }
            }
        }

        return true;
    }
}
