<?php
namespace Chamilo\Core\Group\UserInterface\Form\Service;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;
use Traversable;

/**
 * @package Chamilo\Core\User\UserInterface\Form\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMoveFormDataMapper implements DataMapperInterface
{
    protected DataMapper $defaultMapper;

    public function __construct()
    {
        $this->defaultMapper = new DataMapper();
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        /** @var \Symfony\Component\Form\FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms[Group::PROPERTY_PARENT]->setData(
            new OptionsTreeChoice($viewData[Group::PROPERTY_PARENT], '')
        );
        $forms[Group::PROPERTY_NAME]->setData($viewData[Group::PROPERTY_NAME]);
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var \Symfony\Component\Form\FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $viewData[Group::PROPERTY_PARENT] = $forms[Group::PROPERTY_PARENT]->getData()->getValue();
        $viewData[Group::PROPERTY_NAME] = $forms[Group::PROPERTY_NAME]->getData();
    }
}