<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Form;

use Chamilo\Core\Metadata\Relation\Instance\Service\RelationInstanceService;
use Chamilo\Core\Metadata\Service\EntityConditionService;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;

/**
 * Form for the element
 */
class RelationInstanceForm extends FormValidator
{

    /**
     *
     * @var RelationInstance
     */
    private $relationInstance;

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntity[]
     */
    private $sourceEntities;

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Relation[]
     */
    private $relations;

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntity[]
     */
    private $targetEntities;

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\RelationInstance $relationInstance
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $sourceEntities
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Relation[] $relations
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $targetEntities
     * @param string $postUrl
     */
    public function __construct(
        RelationInstance $relationInstance, $sourceEntities, $relations, $targetEntities, $postUrl
    )
    {
        parent::__construct('relation', self::FORM_METHOD_POST, $postUrl);

        $this->relationInstance = $relationInstance;
        $this->sourceEntities = $sourceEntities;
        $this->relations = $relations;
        $this->targetEntities = $targetEntities;

        $this->buildForm();
    }

    /**
     * Builds this form
     */
    protected function buildForm()
    {
        $this->addSourceEntities();
        $this->addRelations();
        $this->addTargetEntities();

        $this->addSaveResetButtons();
    }

    private function addSourceEntities()
    {
        $this->addSelect(
            RelationInstanceService::PROPERTY_SOURCE, Translation::get('SelectSourceEntities'),
            $this->getSourceEntityOptions()
        );
    }

    private function addTargetEntities()
    {
        $this->addSelect(
            RelationInstanceService::PROPERTY_TARGET, Translation::get('SelectTargetEntities'),
            $this->getTargetEntityOptions()
        );
    }

    private function addRelations()
    {
        $this->addSelect(
            RelationInstance::PROPERTY_RELATION_ID, Translation::get('SelectRelations'), $this->getRelationOptions()
        );
    }

    /**
     *
     * @return string[]
     */
    private function getRelationOptions()
    {
        $relationOptions = array();

        foreach ($this->relations as $relation)
        {
            $relationOptions[$relation->get_id()] = $relation->getTranslationByIsocode(
                Translation::getInstance()->getLanguageIsocode()
            );
        }

        return $relationOptions;
    }

    /**
     *
     * @return string[]
     */
    private function getSourceEntityOptions()
    {
        return $this->getEntityOptions($this->sourceEntities);
    }

    /**
     *
     * @return string[]
     */
    private function getTargetEntityOptions()
    {
        return $this->getEntityOptions($this->targetEntities);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $entities
     *
     * @return string[]
     */
    private function getEntityOptions($entities)
    {
        $expandedEntities = $this->getService(EntityConditionService::class)->expandEntities($entities);

        $options = array();

        foreach ($expandedEntities as $expandedEntity)
        {
            $options[$expandedEntity->getSerialization()] = $expandedEntity->getName();
        }

        return $options;
    }

    /**
     *
     * @param string $name
     * @param string $label
     * @param string[] $options
     * @param boolean $allowsMultiple
     */
    private function addSelect($name, $label, $options, $allowsMultiple = true)
    {
        $numberOfOptions = count($options);

        if ($numberOfOptions == 1)
        {
            $optionKeys = array_keys($options);
            $onlyOptionKey = array_pop($optionKeys);
            $onlyOptionValue = array_pop($options);

            $this->addElement('hidden', $name . '[]', $onlyOptionKey);
            $this->addElement('static', null, $label, $onlyOptionValue);
        }
        else
        {
            $relationSelect = $this->addElement('select', $name, $label, $options);
            $relationSelect->setMultiple($allowsMultiple);

            if ($numberOfOptions < 10)
            {
                $relationSelect->setSize($numberOfOptions);
            }
            else
            {
                $relationSelect->setSize(10);
            }
        }
    }
}