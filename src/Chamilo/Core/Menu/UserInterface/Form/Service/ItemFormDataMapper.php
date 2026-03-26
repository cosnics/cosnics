<?php
namespace Chamilo\Core\Menu\UserInterface\Form\Service;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Interface\ConfigurableItemInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;
use Traversable;

/**
 * @package Chamilo\Core\User\UserInterface\Form\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemFormDataMapper implements DataMapperInterface
{
    protected DataMapper $defaultMapper;

    public function __construct(protected ItemRendererRegistry $itemRendererRegistry)
    {
        $this->defaultMapper = new DataMapper();
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        /** @var \Symfony\Component\Form\FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms[Item::PROPERTY_PARENT]->setData(new OptionsTreeChoice($viewData[Item::PROPERTY_PARENT], ''));
        $forms[Item::PROPERTY_HIDDEN]->setData((bool) $viewData[Item::PROPERTY_HIDDEN]);
        $forms[Item::PROPERTY_ICON_CLASS]->setData($viewData[Item::PROPERTY_ICON_CLASS]);
        $forms[Item::PROPERTY_TITLES]->setData($viewData[Item::PROPERTY_TITLES]);

        try {
            $itemRenderer = $this->itemRendererRegistry->getItemRenderer($viewData[Item::PROPERTY_TYPE]);

            if ($itemRenderer instanceof ConfigurableItemInterface) {
                $itemRenderer->mapDataToForms(
                    $viewData[Item::PROPERTY_CONFIGURATION], $forms[Item::PROPERTY_CONFIGURATION]
                );
            }
        }
        catch (NoSuchClassException) {
        }
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var \Symfony\Component\Form\FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $viewData[Item::PROPERTY_PARENT] = $forms[Item::PROPERTY_PARENT]->getData()->getValue();
        $viewData[Item::PROPERTY_HIDDEN] = $forms[Item::PROPERTY_HIDDEN]->getData() ? 1 : 0;
        $viewData[Item::PROPERTY_ICON_CLASS] = $forms[Item::PROPERTY_ICON_CLASS]->getData();
        $viewData[Item::PROPERTY_TITLES] = $forms[Item::PROPERTY_TITLES]->getData();

        try {
            $itemRenderer = $this->itemRendererRegistry->getItemRenderer($viewData[Item::PROPERTY_TYPE]);

            if ($itemRenderer instanceof ConfigurableItemInterface) {
                $itemRenderer->mapFormsToData(
                    $forms[Item::PROPERTY_CONFIGURATION], $viewData[Item::PROPERTY_CONFIGURATION]
                );
            }
        }
        catch (NoSuchClassException) {
        }
    }
}