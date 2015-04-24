<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;

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
    public function __construct(RelationInstance $relationInstance, $sourceEntities, $relations, $targetEntities,
        $postUrl)
    {
        parent :: __construct('relation', 'post', $postUrl);

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
        $relationsSelect = $this->addElement('select', 'relation', 'SelectRelations');

        $this->addSaveResetButtons();
    }
}