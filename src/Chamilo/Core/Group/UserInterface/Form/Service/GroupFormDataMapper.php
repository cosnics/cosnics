<?php
namespace Chamilo\Core\Group\UserInterface\Form\Service;

use ArrayIterator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;
use Traversable;

/**
 * @package Chamilo\Core\User\UserInterface\Form\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupFormDataMapper implements DataMapperInterface
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

        $this->defaultMapper->mapDataToForms(
            $viewData, new ArrayIterator(
                [$forms[Group::PROPERTY_NAME], $forms[Group::PROPERTY_CODE], $forms[Group::PROPERTY_DESCRIPTION]]
            )
        );

        $forms[NestedSet::PROPERTY_PARENT_ID]->setData(
            new OptionsTreeChoice($viewData[NestedSet::PROPERTY_PARENT_ID], '')
        );
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var \Symfony\Component\Form\FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $this->defaultMapper->mapFormsToData(
            new ArrayIterator(
                [$forms[Group::PROPERTY_NAME], $forms[Group::PROPERTY_CODE], $forms[Group::PROPERTY_DESCRIPTION]]
            ), $viewData
        );

        $viewData[NestedSet::PROPERTY_PARENT_ID] = $forms[NestedSet::PROPERTY_PARENT_ID]->getData()->getValue();
    }
}