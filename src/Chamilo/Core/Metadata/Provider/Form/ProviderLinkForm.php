<?php
namespace Chamilo\Core\Metadata\Provider\Form;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Service\ProviderFormService;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * Form for the element
 */
class ProviderLinkForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Service\EntityService
     */
    private $entityService;

    /**
     *
     * @var \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    private $elementService;

    /**
     *
     * @var \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    private $relationService;

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntity
     */
    private $entity;

    /**
     *
     * @param \Chamilo\Core\Metadata\Service\EntityService $entityService
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string $postUrl
     */
    public function __construct(EntityService $entityService, ElementService $elementService,
        RelationService $relationService, DataClassEntity $entity, $postUrl)
    {
        parent :: __construct('ProviderLink', 'post', $postUrl);

        $this->entityService = $entityService;
        $this->elementService = $elementService;
        $this->relationService = $relationService;
        $this->entity = $entity;

        $this->buildForm();
    }

    /**
     * Builds this form
     */
    protected function buildForm()
    {
        $providerFormService = new ProviderFormService(
            $this->entityService,
            $this->elementService,
            $this->relationService,
            $this->entity,
            $this);
        $providerFormService->addElements();
        $providerFormService->setDefaults();

        $this->addSaveResetButtons();
    }
}