<?php
namespace Chamilo\Core\Menu\UserInterface\Form;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Interface\ConfigurableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\TranslatableItemInterface;
use Chamilo\Core\Menu\Implementation\Menu\CategoryItemRenderer;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_category;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_checkbox;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_select;
use HTML_QuickForm_text;

/**
 * @package Chamilo\Core\Menu\UserInterface\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemForm extends FormValidator
{
    protected string $itemType;

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
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
     */
    protected function buildBasicForm(): void
    {
        $translator = $this->getTranslator();

        $this->addElement(HTML_QuickForm_category::class, $translator->trans('General', [], Manager::CONTEXT));

        if ($this->getItemType() === CategoryItemRenderer::class) {
            $options[0] = $this->getTranslator()->trans('Home', [], Manager::CONTEXT);
        }
        else {
            $options = $this->getMenuOptionsTreeRenderer()->getOptions();
        }

        $this->addElement(
            HTML_QuickForm_select::class, Item::PROPERTY_PARENT, $translator->trans('Parent', [], Manager::CONTEXT),
            $options, ['class' => 'form-control']
        );

        $this->addRule(
            Item::PROPERTY_PARENT, $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class
        );

        $this->addElement(
            HTML_QuickForm_checkbox::class, Item::PROPERTY_HIDDEN, $translator->trans('Hidden', [], Manager::CONTEXT)
        );
        $this->addElement(
            HTML_QuickForm_text::class, Item::PROPERTY_ICON_CLASS,
            $translator->trans('IconClass', [], Manager::CONTEXT), ['class' => 'form-control']
        );
    }

    /**
     * @throws \QuickformException
     */
    protected function buildFormTitles(): void
    {
        $translator = $this->getTranslator();

        $this->addElement(HTML_QuickForm_category::class, $translator->trans('Titles', [], Manager::CONTEXT));

        $activeLanguages = $this->getLanguageConsulter()->getLanguages();
        $platformLanguage =
            $this->getContainer()->getParameter('cosnics.libraries.userInterface.translation.language.default');

        foreach ($activeLanguages as $isocode => $language) {
            $this->addElement(
                HTML_QuickForm_text::class, Item::PROPERTY_TITLES . '[' . $isocode . ']', $language,
                ['class' => 'form-control']
            );

            if ($isocode == $platformLanguage) {
                $this->addRule(
                    Item::PROPERTY_TITLES . '[' . $isocode . ']',
                    $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
                    HTML_QuickForm_Rule_Required::class
                );
            }
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
     */
    protected function buildSettingsForm(): void
    {
        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($this->getItemType());

        if ($itemRenderer instanceof ConfigurableItemInterface &&
            count($itemRenderer->getConfigurationPropertyNames()) > 0) {
            $itemRenderer->addConfigurationToForm($this);
        }
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
     */
    protected function buildTitlesForm(): void
    {
        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($this->getItemType());

        if ($itemRenderer instanceof TranslatableItemInterface) {
            $this->buildFormTitles();
        }
    }

    public function getItemRendererFactory(): ItemRendererRegistry
    {
        return $this->getService(ItemRendererRegistry::class);
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
     * @param class-string<\Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer> $className
     */
    public function getMenuOptionsTreeRenderer(
        string $className = 'Chamilo\Core\Menu\UserInterface\Menu\ItemOptionsTreeRenderer'
    ): OptionsTreeRenderer
    {
        return $this->getService($className);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
     */
    protected function getParentItems(): array
    {
        $itemService = $this->getItemService();
        $itemRendererFactory = $this->getItemRendererFactory();

        $items = $itemService->findRootCategoryItems();

        $itemOptions = [];
        $itemOptions[0] = $this->getTranslator()->trans('Root', [], StringUtilities::LIBRARIES);

        foreach ($items as $item) {
            $itemRenderer = $itemRendererFactory->getItemRendererForItem($item);
            $itemOptions[$item->getId()] = '-- ' . $itemRenderer->renderTitleForCurrentLanguage($item);
        }

        return $itemOptions;
    }

    /**
     * @param string[] $defaults
     *
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
     */
    public function setItemDefaults(Item $item, array $defaults = []): void
    {
        $defaults[DataClass::PROPERTY_ID] = $item->getId();
        $defaults[Item::PROPERTY_PARENT] = $item->getParentId();
        $defaults[Item::PROPERTY_HIDDEN] = $item->getHidden();
        $defaults[Item::PROPERTY_TYPE] = $item->getType();
        $defaults[Item::PROPERTY_ICON_CLASS] = $item->getIconClass();

        $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($item);

        if ($itemRenderer instanceof TranslatableItemInterface) {
            $activeLanguages = $this->getLanguageConsulter()->getLanguages();

            foreach ($activeLanguages as $isoCode => $language) {
                $defaults[Item::PROPERTY_TITLES][$isoCode] = $itemRenderer->renderTitleForIsocode($item, $isoCode);
            }
        }

        if ($itemRenderer instanceof ConfigurableItemInterface) {
            foreach ($item->getConfiguration() as $setting => $settingValue) {
                $defaults[Item::PROPERTY_CONFIGURATION][$setting] = $settingValue;
            }
        }

        parent:: setDefaults($defaults);
    }
}
