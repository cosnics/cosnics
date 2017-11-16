<?php
namespace Chamilo\Configuration\Category\Form;

use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.common.category_manager
 */
class CategoryForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'CategoryUpdated';
    const RESULT_ERROR = 'CategoryUpdateFailed';

    private $category;

    private $user;

    private $form_type;

    private $manager;

    /**
     * Creates a new LanguageForm
     */
    public function __construct($form_type, $action, $category, $user, $manager)
    {
        parent::__construct('category_form', 'post', $action);

        $this->category = $category;
        $this->user = $user;
        $this->form_type = $form_type;
        $this->manager = $manager;

        $this->build_header();

        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        else
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    public function build_header()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement(
            'html',
            '<span class="category">' . Translation::get('Required', null, Utilities::COMMON_LIBRARIES) . '</span>');
    }

    public function build_footer($action_name)
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');

        // Submit button
        // $this->addElement('submit', 'submit', 'OK');

        $buttons[] = $this->createElement('style_submit_button', 'create', Translation::get($action_name));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'CategoryForm.js'));
    }

    public function add_name_field($number = null)
    {
        $element = $this->createElement(
            'text',
            PlatformCategory::PROPERTY_NAME . $number,
            Translation::get('Name'),
            array("size" => "50"));
        // $this->addRule(PlatformCategory :: PROPERTY_NAME . $number,
        // Translation :: get('ThisFieldIsRequired'), 'required');
        return $element;
    }

    /**
     * Creates a new basic form
     */
    public function build_creation_form()
    {
        $context = ClassnameUtilities::getInstance()->getNamespaceFromObject($this->category);
        if (! $this->isSubmitted())
        {
            unset($_SESSION[$context]['number_of_categories']);
            unset($_SESSION[$context]['skipped_categories']);
        }

        if (! isset($_SESSION[$context]['number_of_categories']))
        {
            $_SESSION[$context]['number_of_categories'] = 1;
        }

        if (! isset($_SESSION[$context]['skipped_categories']))
        {
            $_SESSION[$context]['skipped_categories'] = array();
        }

        if (isset($_POST['add']))
        {
            $_SESSION[$context]['number_of_categories'] = $_SESSION[$context]['number_of_categories'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION[$context]['skipped_categories'][] = $indexes[0];
        }

        $number_of_categories = intval($_SESSION[$context]['number_of_categories']);

        for ($category_number = 0; $category_number < $number_of_categories; $category_number ++)
        {
            if (! in_array($category_number, $_SESSION[$context]['skipped_categories']))
            {
                $group = array();
                $group[] = $this->add_name_field($category_number);
                if ($number_of_categories - count($_SESSION[$context]['skipped_categories']) > 1)
                {
                    $group[] = $this->createElement(
                        'image',
                        'remove[' . $category_number . ']',
                        Theme::getInstance()->getCommonImagePath('Action/ListRemove'),
                        array('style' => 'border: 0px;'));
                }
                $this->addGroup(
                    $group,
                    PlatformCategory::PROPERTY_NAME . $category_number,
                    Translation::get('CategoryName'),
                    '',
                    false);
                $this->addRule(
                    PlatformCategory::PROPERTY_NAME . $category_number,
                    Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                    'required');
            }
        }

        $this->addElement(
            'image',
            'add[]',
            Theme::getInstance()->getCommonImagePath('Action/ListAdd'),
            array('style' => 'border: 0px;'));
        $this->build_footer('Create');
    }

    public function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }
        return parent::validate();
    }

    /**
     * Builds an editing form
     */
    public function build_editing_form()
    {
        $this->addElement($this->add_name_field());
        $this->addElement('hidden', PlatformCategory::PROPERTY_ID);
        $this->build_footer('Update');
    }

    public function create_category()
    {
        $values = $this->exportValues();
        // dump($values);

        $result = true;

        foreach ($values as $key => $value)
        {
            if (strpos($key, 'name') !== false)
            {
                $category = $this->manager->get_parent()->get_category();
                $category->set_name($value);
                $category->set_parent($this->category->get_parent());
                $category->set_display_order(
                    $this->manager->get_parent()->get_next_category_display_order($this->category->get_parent()));

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable($category::class_name(), $category::PROPERTY_NAME),
                    new StaticConditionVariable($category->get_name()));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable($category::class_name(), $category::PROPERTY_PARENT),
                    new StaticConditionVariable($category->get_parent()));
                $condition = new AndCondition($conditions);
                $cats = $this->manager->get_parent()->count_categories($condition);

                if ($cats > 0)
                {
                    $result = false;
                }
                else
                {
                    $result &= $category->create();
                }
            }
        }
        return $result;
    }

    public function update_category()
    {
        $category = $this->category;
        $category->set_name($this->exportValue(PlatformCategory::PROPERTY_NAME));

        $conditions = array();
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable($category::class_name(), $category::PROPERTY_ID),
                new StaticConditionVariable($category->get_id())));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($category::class_name(), $category::PROPERTY_NAME),
            new StaticConditionVariable($category->get_name()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($category::class_name(), $category::PROPERTY_PARENT),
            new StaticConditionVariable($category->get_parent()));
        $condition = new AndCondition($conditions);
        $cats = $this->manager->get_parent()->count_categories($condition);

        if ($cats > 0)
        {
            return false;
        }

        return $category->update();
    }

    /**
     * Sets default values.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $category = $this->category;
        $defaults[PlatformCategory::PROPERTY_ID] = $category->get_id();
        $defaults[PlatformCategory::PROPERTY_NAME] = $category->get_name();
        parent::setDefaults($defaults);
    }
}
