<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
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
     * @var \Chamilo\Core\Metadata\Service\EntityService
     */
    private $entityService;

    /**
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    private $dataClassEntityFactory;

    /**
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntityFactory $dataClassEntityFactory
     */
    public function __construct(EntityService $entityService, DataClassEntityFactory $dataClassEntityFactory)
    {
        $this->entityService = $entityService;
        $this->dataClassEntityFactory = $dataClassEntityFactory;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function addElements(FormValidator $formValidator, DataClassEntity $entity)
    {
        $entity = $this->getDataClassEntityFactory()->getEntity($entity->getDataClassName());
        $availableSchemas = $this->getEntityService()->getAvailableSchemasForEntityType($entity);

        while ($availableSchema = $availableSchemas->next_result())
        {
            $formValidator->addElement(
                'checkbox', InstanceService::PROPERTY_METADATA_ADD_SCHEMA . '[' . $availableSchema->get_id() . ']',
                $availableSchema->get_name(), null, null, $availableSchema->get_id()
            );
        }
    }

    /**
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    public function getDataClassEntityFactory(): DataClassEntityFactory
    {
        return $this->dataClassEntityFactory;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntityFactory $dataClassEntityFactory
     */
    public function setDataClassEntityFactory(DataClassEntityFactory $dataClassEntityFactory): void
    {
        $this->dataClassEntityFactory = $dataClassEntityFactory;
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityService
     */
    public function getEntityService(): EntityService
    {
        return $this->entityService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     */
    public function setEntityService(EntityService $entityService): void
    {
        $this->entityService = $entityService;
    }
}
