<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Configuration;
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
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $entity;

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $formValidator;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     */
    public function __construct(DataClass $entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
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
            throw new \Exception(Translation :: get('NoFormValidatorSet'));
        }
        
        $this->getFormValidator()->addElement('category', Translation :: get('Translations'));
        
        $languages = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieves(Language :: class_name());
        $platformLanguage = Configuration :: get('Chamilo\Core\Admin', 'platform_language');
        
        while ($language = $languages->next_result())
        {
            $fieldName = EntityTranslationService :: PROPERTY_TRANSLATION . '[' . $language->get_isocode() . ']';
            $this->getFormValidator()->addElement('text', $fieldName, $language->get_original_name());
            
            if ($language->get_isocode() == $platformLanguage)
            {
                $this->getFormValidator()->addRule(
                    $fieldName, 
                    Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                    'required');
            }
        }
        
        $this->getFormValidator()->addElement('category');
    }

    public function setFormDefaults()
    {
        if (! $this->getFormValidator() instanceof FormValidator)
        {
            throw new \Exception(Translation :: get('NoFormValidatorSet'));
        }
        
        $defaults = array();
        
        foreach ($this->getEntity()->getTranslations() as $isocode => $translation)
        {
            $defaults[EntityTranslationService :: PROPERTY_TRANSLATION][$isocode] = $translation->get_value();
        }
        
        $this->getFormValidator()->setDefaults($defaults);
    }
}