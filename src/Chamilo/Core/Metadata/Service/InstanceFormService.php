<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Entity\EntityInterface;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class InstanceFormService
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    private $entity;

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $formValidator;

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\EntityInterface $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function __construct(EntityInterface $entity, FormValidator $formValidator)
    {
        $this->entity = $entity;
        $this->formValidator = $formValidator;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\EntityInterface $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function getFormValidator()
    {
        return $this->formValidator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function setFormValidator($formValidator)
    {
        $this->formValidator = $formValidator;
    }

    public function addElements(EntityService $entityService, RelationService $relationService)
    {
        $entityFactory = DataClassEntityFactory :: getInstance();
        $entity = $entityFactory->getEntity($this->getEntity()->getDataClassName());
        $availableSchemas = $entityService->getAvailableSchemasForEntityType($relationService, $entity);

        while ($availableSchema = $availableSchemas->next_result())
        {
            $this->formValidator->addElement(
                'checkbox',
                InstanceService :: PROPERTY_METADATA_ADD_SCHEMA . '[' . $availableSchema->get_id() . ']',
                $availableSchema->get_name(),
                null,
                null,
                $availableSchema->get_id());
        }
    }

    public function setDefaults()
    {
        $defaults = array();

        $this->formValidator->setDefaults($defaults);
    }
}
