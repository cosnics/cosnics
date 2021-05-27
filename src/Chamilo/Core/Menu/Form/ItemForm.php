<?php
namespace Chamilo\Core\Menu\Form;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\LanguageConsulter;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\Menu\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemForm extends FormValidator
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Configuration\Service\LanguageConsulter
     */
    private $languageConsulter;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @var \Chamilo\Configuration\Service\RegistrationConsulter
     */
    private $registrationConsulter;

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Configuration\Service\LanguageConsulter $languageConsulter
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     * @param string $action
     *
     * @throws \Exception
     */
    public function __construct(
        Translator $translator, ItemService $itemService, LanguageConsulter $languageConsulter,
        ConfigurationConsulter $configurationConsulter, RegistrationConsulter $registrationConsulter, string $action
    )
    {
        parent::__construct('item-form', self::FORM_METHOD_POST, $action);

        $this->translator = $translator;
        $this->itemService = $itemService;
        $this->languageConsulter = $languageConsulter;
        $this->configurationConsulter = $configurationConsulter;
        $this->registrationConsulter = $registrationConsulter;

        $this->buildForm();
        $this->addSaveResetButtons();
        $this->setDefaults();
    }

    public function buildForm()
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('General', [], 'Chamilo\Core\Menu'));
        $this->addElement(
            'select', Item::PROPERTY_PARENT, $translator->trans('Parent', [], 'Chamilo\Core\Menu'),
            $this->getParentItems(), array('class' => 'form-control')
        );
        $this->addRule(
            Item::PROPERTY_PARENT, $translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement('checkbox', Item::PROPERTY_HIDDEN, $translator->trans('Hidden', [], 'Chamilo\Core\Menu'));
        $this->addElement(
            'text', Item::PROPERTY_ICON_CLASS, $translator->trans('IconClass', [], 'Chamilo\Core\Menu'),
            ['class' => 'form-control']
        );

        $this->buildFormTitles();
    }

    public function buildFormTitles()
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('Titles', [], 'Chamilo\Core\Menu'));

        $activeLanguages = $this->getLanguageConsulter()->getLanguages();
        $platformLanguage =
            $this->getConfigurationConsulter()->getSetting(array('Chamilo\Core\Admin', 'platform_language'));

        foreach ($activeLanguages as $isocode => $language)
        {
            $this->addElement(
                'text', ItemTitle::PROPERTY_TITLE . '[' . $isocode . ']', $language, array("class" => "form-control")
            );

            if ($isocode == $platformLanguage)
            {
                $this->addRule(
                    ItemTitle::PROPERTY_TITLE . '[' . $isocode . ']',
                    $translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES), 'required'
                );
            }
        }
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    /**
     * @return \Chamilo\Configuration\Service\LanguageConsulter
     */
    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\LanguageConsulter $languageConsulter
     */
    public function setLanguageConsulter(LanguageConsulter $languageConsulter): void
    {
        $this->languageConsulter = $languageConsulter;
    }

    /**
     * @return string[]
     */
    public function getParentItems()
    {
        $itemService = $this->getItemService();
        $items = $itemService->findRootCategoryItems();

        $itemOptions = $this->getRootParentItem();

        foreach ($items as $item)
        {
            $itemOptions[$item->getId()] = '-- ' . $itemService->getItemTitleForCurrentLanguage($item);
        }

        return $itemOptions;
    }

    /**
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     */
    public function setRegistrationConsulter(RegistrationConsulter $registrationConsulter): void
    {
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     * @return string[]
     */
    public function getRootParentItem()
    {
        return array(0 => $this->getTranslator()->trans('Root', [], Utilities::COMMON_LIBRARIES));
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    protected function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle[] $itemTitles
     * @param string[] $defaults
     *
     * @throws \Exception
     */
    public function setItemDefaults(Item $item, array $itemTitles, array $defaults = [])
    {
        $defaults[Item::PROPERTY_ID] = $item->getId();
        $defaults[Item::PROPERTY_PARENT] = $item->getParentId();
        $defaults[Item::PROPERTY_HIDDEN] = $item->getHidden();
        $defaults[Item::PROPERTY_TYPE] = $item->getType();
        $defaults[Item::PROPERTY_ICON_CLASS] = $item->getIconClass();

        $activeLanguages = $this->getLanguageConsulter()->getLanguages();

        foreach ($activeLanguages as $isoCode => $language)
        {
            if (key_exists($isoCode, $itemTitles))
            {
                $defaults[ItemTitle::PROPERTY_TITLE][$isoCode] = $itemTitles[$isoCode]->getTitle();
            }
        }

        parent:: setDefaults($defaults);
    }
}
