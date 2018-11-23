<?php
namespace Chamilo\Core\Menu\Form;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\LanguageConsulter;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Form
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemFormFactory
{

    /**
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

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
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Configuration\Service\LanguageConsulter $languageConsulter
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     */
    public function __construct(
        ClassnameUtilities $classnameUtilities, Translator $translator, ItemService $itemService,
        LanguageConsulter $languageConsulter, ConfigurationConsulter $configurationConsulter,
        RegistrationConsulter $registrationConsulter
    )
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->translator = $translator;
        $this->itemService = $itemService;
        $this->languageConsulter = $languageConsulter;
        $this->configurationConsulter = $configurationConsulter;
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities): void
    {
        $this->classnameUtilities = $classnameUtilities;
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
     * @param string $itemType
     * @param string $action
     *
     * @return \Chamilo\Core\Menu\Form\ItemForm
     * @throws \Exception
     */
    public function getItemForm(string $itemType, string $action)
    {
        $itemClass = $this->getClassnameUtilities()->getClassnameFromNamespace($itemType);
        $formClass = 'Chamilo\Core\Menu\Form\Item\\' . $itemClass . 'Form';

        if (!class_exists($formClass))
        {
            throw new \Exception(
                $this->getTranslator()->trans('FormTypeDoesNotExist', ['TYPE' => $itemType], 'Chamilo\Core\Menu')
            );
        }

        return new $formClass(
            $this->getTranslator(), $this->getItemService(), $this->getLanguageConsulter(),
            $this->getConfigurationConsulter(), $this->getRegistrationConsulter(), $action
        );
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
}
