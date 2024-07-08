<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Repository\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Core\Metadata\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTranslationFormService
{
    protected ConfigurationConsulter $configurationConsulter;

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     *
     * @throws \Exception
     */
    public function addFieldsToForm(FormValidator $formValidator)
    {
        $formValidator->addElement('category', Translation::get('Translations'));

        $languages = DataManager::retrieves(Language::class);
        $platformLanguage = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'platform_language']);

        foreach ($languages as $language)
        {
            $fieldName = EntityTranslationService::PROPERTY_TRANSLATION . '[' . $language->get_isocode() . ']';
            $formValidator->addElement('text', $fieldName, $language->get_original_name());

            if ($language->get_isocode() == $platformLanguage)
            {
                $formValidator->addRule(
                    $fieldName, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
                );
            }
        }

        $formValidator->addElement('category');
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     * @param \Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface $entityTranslationImplementation
     *
     * @throws \Exception
     */
    public function setFormDefaults(
        FormValidator $formValidator, EntityTranslationInterface $entityTranslationImplementation
    )
    {
        if (!$formValidator instanceof FormValidator)
        {
            throw new Exception(Translation::get('NoFormValidatorSet'));
        }

        $defaults = [];

        foreach ($entityTranslationImplementation->getTranslations() as $isocode => $translation)
        {
            $defaults[EntityTranslationService::PROPERTY_TRANSLATION][$isocode] = $translation->get_value();
        }

        $formValidator->setDefaults($defaults);
    }
}