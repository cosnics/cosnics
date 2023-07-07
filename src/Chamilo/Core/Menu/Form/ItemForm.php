<?php
namespace Chamilo\Core\Menu\Form;

use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Architecture\Interfaces\ConfigurableItemInterface;
use Chamilo\Core\Menu\Architecture\Interfaces\TranslatableItemInterface;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Menu\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemForm extends FormValidator
{
    protected string $itemType;

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function __construct(string $itemType, string $action)
    {
        $this->itemType = $itemType;

        parent::__construct('item-form', self::FORM_METHOD_POST, $action);

        $this->buildBasicForm();
        $this->buildSettingsForm();
        $this->buildTitlesForm();

        $this->addSaveResetButtons();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function buildBasicForm(): void
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('General', [], 'Chamilo\Core\Menu'));
        $this->addElement(
            'select', Item::PROPERTY_PARENT, $translator->trans('Parent', [], 'Chamilo\Core\Menu'),
            $this->getParentItems(), ['class' => 'form-control']
        );
        $this->addRule(
            Item::PROPERTY_PARENT, $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );

        $this->addElement('checkbox', Item::PROPERTY_HIDDEN, $translator->trans('Hidden', [], 'Chamilo\Core\Menu'));
        $this->addElement(
            'text', Item::PROPERTY_ICON_CLASS, $translator->trans('IconClass', [], 'Chamilo\Core\Menu'),
            ['class' => 'form-control']
        );
    }

    /**
     * @throws \QuickformException
     */
    protected function buildFormTitles(): void
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('Titles', [], 'Chamilo\Core\Menu'));

        $activeLanguages = $this->getLanguageConsulter()->getLanguages();
        $platformLanguage = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'platform_language']);

        foreach ($activeLanguages as $isocode => $language)
        {
            $this->addElement(
                'text', Item::PROPERTY_TITLES . '[' . $isocode . ']', $language, ['class' => 'form-control']
            );

            if ($isocode == $platformLanguage)
            {
                $this->addRule(
                    Item::PROPERTY_TITLES . '[' . $isocode . ']',
                    $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
                );
            }
        }
    }

    protected function buildSettingsForm(): void
    {
        $itemRenderer = $this->getItemRendererFactory()->getAvailableItemRenderer($this->getItemType());

        if ($itemRenderer instanceof ConfigurableItemInterface &&
            count($itemRenderer->getConfigurationPropertyNames()) > 0)
        {
            $itemRenderer->addConfigurationToForm($this);
        }
    }

    /**
     * @throws \QuickformException
     */
    protected function buildTitlesForm(): void
    {
        $itemRenderer = $this->getItemRendererFactory()->getAvailableItemRenderer($this->getItemType());

        if ($itemRenderer instanceof TranslatableItemInterface)
        {
            $this->buildFormTitles();
        }
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->getService(ItemRendererFactory::class);
    }

    public function getItemService(): ItemService
    {
        return $this->getService(ItemService::class);
    }

    public function getItemType(): string
    {
        return $this->itemType;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->getService(LanguageConsulter::class);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function getParentItems(): array
    {
        $itemService = $this->getItemService();
        $itemRendererFactory = $this->getItemRendererFactory();

        $items = $itemService->findRootCategoryItems();

        $itemOptions = [];
        $itemOptions[0] = $this->getTranslator()->trans('Root', [], StringUtilities::LIBRARIES);

        foreach ($items as $item)
        {
            $itemRenderer = $itemRendererFactory->getItemRenderer($item);
            $itemOptions[$item->getId()] = '-- ' . $itemRenderer->renderTitleForCurrentLanguage($item);
        }

        return $itemOptions;
    }

    /**
     * @param string[] $defaults
     *
     * @throws \QuickformException
     */
    public function setItemDefaults(Item $item, array $defaults = []): void
    {
        $defaults[DataClass::PROPERTY_ID] = $item->getId();
        $defaults[Item::PROPERTY_PARENT] = $item->getParentId();
        $defaults[Item::PROPERTY_HIDDEN] = $item->getHidden();
        $defaults[Item::PROPERTY_TYPE] = $item->getType();
        $defaults[Item::PROPERTY_ICON_CLASS] = $item->getIconClass();

        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($item);

        if ($itemRenderer instanceof TranslatableItemInterface)
        {
            $activeLanguages = $this->getLanguageConsulter()->getLanguages();

            foreach ($activeLanguages as $isoCode => $language)
            {
                $defaults[Item::PROPERTY_TITLES][$isoCode] = $itemRenderer->renderTitleForIsocode($item, $isoCode);
            }
        }

        if ($itemRenderer instanceof ConfigurableItemInterface)
        {
            foreach ($item->getConfiguration() as $setting => $settingValue)
            {
                $defaults[Item::PROPERTY_CONFIGURATION][$setting] = $settingValue;
            }
        }

        parent:: setDefaults($defaults);
    }

}
