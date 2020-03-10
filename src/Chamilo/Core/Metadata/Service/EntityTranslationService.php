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
     *
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntity
     */
    private $entity;

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     */
    public function __construct(DataClassEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     */
    public function setEntity(DataClassEntity $entity)
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
            new PropertyConditionVariable(EntityTranslation::class_name(), EntityTranslation::PROPERTY_ENTITY_TYPE), 
            ComparisonCondition::EQUAL, 
            new StaticConditionVariable($this->getEntity()->getDataClassName()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(EntityTranslation::class_name(), EntityTranslation::PROPERTY_ENTITY_ID), 
            ComparisonCondition::EQUAL, 
            new StaticConditionVariable($this->getEntity()->getDataClassIdentifier()));
        
        $translations = DataManager::retrieves(
            EntityTranslation::class_name(), 
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
        foreach ($entityTranslations[self::PROPERTY_TRANSLATION] as $isocode => $value)
        {
            $translation = new EntityTranslation();
            $translation->set_entity_type($this->getEntity()->getDataClassName());
            $translation->set_entity_id($this->getEntity()->getDataClassIdentifier());
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
        $translations = $this->getEntity()->getDataClass()->getTranslations();
        
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
                $translation->set_entity_type($this->getEntity()->getDataClassName());
                $translation->set_entity_id($this->getEntity()->getDataClassIdentifier());
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
