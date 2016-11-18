<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTranslationFormService
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface
     */
    private $entityTranslationImplementation;

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $formValidator;

    /**
     *
     * @param \Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface $entityTranslationImplementation
     */
    public function __construct(EntityTranslationInterface $entityTranslationImplementation)
    {
        $this->entityTranslationImplementation = $entityTranslationImplementation;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface
     */
    public function getEntityTranslationImplementation()
    {
        return $this->entityTranslationImplementation;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface $entity
     */
    public function setEntityTranslationImplementation(EntityTranslationInterface $entityTranslationImplementation)
    {
        $this->entityTranslationImplementation = $entityTranslationImplementation;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function getFormValidator()
    {
        return $this->formValidator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function setFormValidator($formValidator)
    {
        $this->formValidator = $formValidator;
    }

    public function addFieldsToForm()
    {
        if (! $this->getFormValidator() instanceof FormValidator)
        {
            throw new \Exception(Translation::get('NoFormValidatorSet'));
        }
        
        $this->getFormValidator()->addElement('category', Translation::get('Translations'));
        
        $languages = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieves(
            Language::class_name(), 
            new DataClassRetrievesParameters());
        $platformLanguage = Configuration::get('Chamilo\Core\Admin', 'platform_language');
        
        while ($language = $languages->next_result())
        {
            $fieldName = EntityTranslationService::PROPERTY_TRANSLATION . '[' . $language->get_isocode() . ']';
            $this->getFormValidator()->addElement('text', $fieldName, $language->get_original_name());
            
            if ($language->get_isocode() == $platformLanguage)
            {
                $this->getFormValidator()->addRule(
                    $fieldName, 
                    Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                    'required');
            }
        }
        
        $this->getFormValidator()->addElement('category');
    }

    public function setFormDefaults()
    {
        if (! $this->getFormValidator() instanceof FormValidator)
        {
            throw new \Exception(Translation::get('NoFormValidatorSet'));
        }
        
        $defaults = array();
        
        foreach ($this->getEntityTranslationImplementation()->getTranslations() as $isocode => $translation)
        {
            $defaults[EntityTranslationService::PROPERTY_TRANSLATION][$isocode] = $translation->get_value();
        }
        
        $this->getFormValidator()->setDefaults($defaults);
    }
}