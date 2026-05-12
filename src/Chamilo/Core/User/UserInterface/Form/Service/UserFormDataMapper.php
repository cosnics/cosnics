<?php
namespace Chamilo\Core\User\UserInterface\Form\Service;

use ArrayIterator;
use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;
use Traversable;

/**
 * @package Chamilo\Core\User\UserInterface\Form\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserFormDataMapper implements DataMapperInterface
{
    private DataMapper $defaultMapper;

    public function __construct()
    {
        $this->defaultMapper = new DataMapper();
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        /** @var \Symfony\Component\Form\FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $defaultFields = [];

        foreach ($forms as $formName => $form) {
            if (!in_array($formName, [User::PROPERTY_ACTIVE, User::PROPERTY_PLATFORM_ADMINISTRATOR])) {
                $defaultFields[$formName] = $form;
            }
        }

        $this->defaultMapper->mapDataToForms($viewData, new ArrayIterator($defaultFields));

        $forms[User::PROPERTY_ACTIVE]->setData((bool) $viewData[User::PROPERTY_ACTIVE]);
        $forms[User::PROPERTY_PLATFORM_ADMINISTRATOR]->setData((bool) $viewData[User::PROPERTY_PLATFORM_ADMINISTRATOR]);
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var \Symfony\Component\Form\FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $activeForms = $forms[User::PROPERTY_ACTIVE];
        unset($forms[User::PROPERTY_ACTIVE]);
        $platformAdministratorForms = $forms[User::PROPERTY_PLATFORM_ADMINISTRATOR];
        unset($forms[User::PROPERTY_PLATFORM_ADMINISTRATOR]);

        $this->defaultMapper->mapFormsToData(new ArrayIterator($forms), $viewData);

        $viewData[User::PROPERTY_ACTIVE] = (int) $activeForms->getData();
        $viewData[User::PROPERTY_PLATFORM_ADMINISTRATOR] = (int) $platformAdministratorForms->getData();
    }
}