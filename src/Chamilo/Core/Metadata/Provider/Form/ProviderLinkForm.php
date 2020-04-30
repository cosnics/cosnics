<?php
namespace Chamilo\Core\Metadata\Provider\Form;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Service\ProviderFormService;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * Form for the element
 */
class ProviderLinkForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\DataClassEntity
     */
    private $entity;

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string $postUrl
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function __construct(DataClassEntity $entity, $postUrl)
    {
        parent::__construct('ProviderLink', self::FORM_METHOD_POST, $postUrl);

        $this->entity = $entity;

        $this->buildForm();
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function buildForm()
    {
        $providerFormService = $this->getProviderFormService();
        $providerFormService->addElements($this->entity, $this);
        $providerFormService->setDefaults($this->entity, $this);

        $this->addSaveResetButtons();
    }

    /**
     * @return \Chamilo\Core\Metadata\Provider\Service\ProviderFormService
     */
    public function getProviderFormService()
    {
        return $this->getService(ProviderFormService::class);
    }
}