<?php
namespace Chamilo\Core\Menu\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    private $item;

    private $form_type;

    public function __construct($form_type, $item, $action)
    {
        parent::__construct('item', 'post', $action);
        
        $this->item = $item;
        $this->form_type = $form_type;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        $this->add_footer();
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('category', Translation::get('General'));
        $this->addElement(
            'select', 
            Item::PROPERTY_PARENT, 
            Translation::get('Parent'), 
            $this->get_parents(), 
            array('class' => 'form-control'));
        $this->addRule(Item::PROPERTY_PARENT, Translation::get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('checkbox', Item::PROPERTY_HIDDEN, Translation::get('Hidden'));
        $this->addElement('category');
        
        $this->addElement('category', Translation::get('Titles'));
        $active_languages = \Chamilo\Configuration\Configuration::getInstance()->getLanguages();
        $platform_language = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'platform_language'));
        foreach ($active_languages as $isocode => $language)
        {
            $this->addElement(
                'text', 
                ItemTitle::PROPERTY_TITLE . '[' . $isocode . ']', 
                $language, 
                array("class" => "form-control"));
            
            if ($isocode == $platform_language)
            {
                $this->addRule(
                    ItemTitle::PROPERTY_TITLE . '[' . $isocode . ']', 
                    Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                    'required');
            }
        }
        $this->addElement('category');
        $this->addElement('hidden', Item::PROPERTY_TYPE);
    }

    public function add_footer()
    {
        switch ($this->form_type)
        {
            case self::TYPE_CREATE :
                $buttons[] = $this->createElement(
                    'style_submit_button', 
                    'submit_button', 
                    Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
                break;
            case self::TYPE_EDIT :
                $buttons[] = $this->createElement(
                    'style_submit_button', 
                    'submit_button', 
                    Translation::get('Update', null, Utilities::COMMON_LIBRARIES), 
                    null, 
                    null, 
                    'arrow-right');
                break;
        }
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', Item::PROPERTY_ID);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
    }

    public function get_parents()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT), 
            new StaticConditionVariable(0));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_TYPE), 
            new StaticConditionVariable(CategoryItem::class_name()));
        $condition = new AndCondition($conditions);
        $parameters = new DataClassRetrievesParameters(
            $condition, 
            null, 
            null, 
            new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_SORT)));
        $items = DataManager::retrieves(Item::class_name(), $parameters);
        
        $item_options = array();
        $item_options[0] = Translation::get('Root', null, Utilities::COMMON_LIBRARIES);
        
        while ($item = $items->next_result())
        {
            $item_options[$item->get_id()] = '-- ' . $item->get_titles()->get_current_translation();
        }
        
        return $item_options;
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     * 
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $item = $this->item;
        $active_languages = \Chamilo\Configuration\Configuration::getInstance()->getLanguages();
        $platform_language = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'platform_language'));
        foreach ($active_languages as $isocode => $language)
        {
            $defaults[ItemTitle::PROPERTY_TITLE][$isocode] = $item->get_titles()->get_translation($isocode, false);
        }
        $defaults[Item::PROPERTY_ID] = $item->get_id();
        $defaults[Item::PROPERTY_PARENT] = $item->get_parent();
        $defaults[Item::PROPERTY_HIDDEN] = $item->get_hidden();
        $defaults[Item::PROPERTY_TYPE] = $item->get_type();
        
        parent::setDefaults($defaults);
    }

    public function get_item()
    {
        return $this->item;
    }

    public static function factory($form_type, $item, $action = null)
    {
        $classNameUtilities = ClassnameUtilities::getInstance();
        $itemClass = $classNameUtilities->getClassnameFromObject($item);
        
        $formName = $itemClass . 'Form';
        $formClass = __NAMESPACE__ . '\\Item\\' . $formName;
        
        if (class_exists($formClass))
        {
            return new $formClass($form_type, $item, $action);
        }
        
        return new self($form_type, $item, $action);
    }
}
