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
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $sourceEntities
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Relation[] $relations
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $targetEntities
     * @param string $postUrl
     *
     * @throws \Exception
     */
    public function __construct($sourceEntities, $relations, $targetEntities, $postUrl)
    {
        parent::__construct('relation', self::FORM_METHOD_POST, $postUrl);

        $this->sourceEntities = $sourceEntities;
        $this->relations = $relations;
        $this->targetEntities = $targetEntities;

        $this->buildForm();
    }

    /**
     * @throws \Exception
     */
    private function addRelations()
    {
        $this->addSelect(
            RelationInstance::PROPERTY_RELATION_ID, Translation::get('SelectRelations'), $this->getRelationOptions()
        );
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

    /**
     * @throws \Exception
     */
    private function addSourceEntities()
    {
        $this->addSelect(
            RelationInstanceService::PROPERTY_SOURCE, Translation::get('SelectSourceEntities'),
            $this->getSourceEntityOptions()
        );
    }

    /**
     * @throws \Exception
     */
    private function addTargetEntities()
    {
        $this->addSelect(
            RelationInstanceService::PROPERTY_TARGET, Translation::get('SelectTargetEntities'),
            $this->getTargetEntityOptions()
        );
    }

    /**
     * Builds this form
     * @throws \Exception
     */
    protected function buildForm()
    {
        $this->addSourceEntities();
        $this->addRelations();
        $this->addTargetEntities();

        $this->addSaveResetButtons();
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityConditionService
     */
    private function getEntityConditionService()
    {
        return $this->getService(EntityConditionService::class);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $entities
     *
     * @return string[]
     * @throws \Exception
     */
    private function getEntityOptions($entities)
    {
        $expandedEntities = $this->getEntityConditionService()->expandEntities($entities);

        $options = [];

        foreach ($expandedEntities as $expandedEntity)
        {
            $options[$expandedEntity->getSerialization()] = $expandedEntity->getName();
        }

        return $options;
    }

    /**
     *
     * @return string[]
     * @throws \Exception
     */
    private function getRelationOptions()
    {
        $relationOptions = [];

        foreach ($this->relations as $relation)
        {
            $relationOptions[$relation->getId()] = $relation->getTranslationByIsocode(
                Translation::getInstance()->getLanguageIsocode()
            );
        }

        return $relationOptions;
    }

    /**
     *
     * @return string[]
     * @throws \Exception
     */
    private function getSourceEntityOptions()
    {
        return $this->getEntityOptions($this->sourceEntities);
    }

    /**
     *
     * @return string[]
     * @throws \Exception
     */
    private function getTargetEntityOptions()
    {
        return $this->getEntityOptions($this->targetEntities);
    }
}