<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

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
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string[] $entityTranslations
     *
     * @return boolean
     * @throws \Exception
     */
    public function createEntityTranslations(DataClassEntity $entity, $entityTranslations)
    {
        foreach ($entityTranslations[self::PROPERTY_TRANSLATION] as $isocode => $value)
        {
            $translation = new EntityTranslation();
            $translation->set_entity_type($entity->getDataClassName());
            $translation->set_entity_id($entity->getDataClassIdentifier());
            $translation->set_isocode($isocode);
            $translation->set_value($value);

            if (!$translation->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation[]
     * @throws \Exception
     */
    public function getEntityTranslationsIndexedByIsocode(DataClassEntity $entity)
    {
        $conditions = [];
        $translationsIndexedByIsocode = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(EntityTranslation::class, EntityTranslation::PROPERTY_ENTITY_TYPE),
            ComparisonCondition::EQUAL, new StaticConditionVariable($entity->getDataClassName())
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(EntityTranslation::class, EntityTranslation::PROPERTY_ENTITY_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($entity->getDataClassIdentifier())
        );

        $translations = DataManager::retrieves(
            EntityTranslation::class, new DataClassRetrievesParameters(new AndCondition($conditions))
        );

        foreach($translations as $translation)
        {
            $translationsIndexedByIsocode[$translation->get_isocode()] = $translation;
        }

        return $translationsIndexedByIsocode;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string[] $entityTranslations
     *
     * @return boolean
     * @throws \Exception
     */
    public function updateEntityTranslations(DataClassEntity $entity, $entityTranslations)
    {
        $translations = $entity->getDataClass()->getTranslations();

        foreach ($entityTranslations as $isocode => $value)
        {
            if ($translations[$isocode] instanceof EntityTranslation)
            {
                $translation = $translations[$isocode];
                $translation->set_value($value);

                if (!$translation->update())
                {
                    return false;
                }
            }
            else
            {
                $translation = new EntityTranslation();
                $translation->set_entity_type($entity->getDataClassName());
                $translation->set_entity_id($entity->getDataClassIdentifier());
                $translation->set_isocode($isocode);
                $translation->set_value($value);

                if (!$translation->create())
                {
                    return false;
                }
            }
        }

        return true;
    }
}
