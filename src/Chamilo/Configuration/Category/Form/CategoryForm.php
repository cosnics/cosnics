<?php
namespace Chamilo\Configuration\Category\Form;

use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package application.common.category_manager
 */
class CategoryForm extends FormValidator
{
    public const RESULT_ERROR = 'CategoryUpdateFailed';

    public const RESULT_SUCCESS = 'CategoryUpdated';

    public const TYPE_CREATE = 1;

    public const TYPE_EDIT = 2;

    private $category;

    private $form_type;

    private $manager;

    private $user;

    /**
     * Creates a new LanguageForm
     */
    public function __construct($form_type, $action, $category, $user, $manager)
    {
        parent::__construct('category_form', self::FORM_METHOD_POST, $action);

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

    public function add_name_field($number = null)
    {
        $element = $this->createElement(
            'text', PlatformCategory::PROPERTY_NAME . $number, Translation::get('Name'), ['size' => '50']
        );
        // $this->addRule(PlatformCategory::PROPERTY_NAME . $number,
        // Translation::get('ThisFieldIsRequired'), 'required');
        return $element;
    }

    /**
     * Creates a new basic form
     */
    public function build_creation_form()
    {
        $context = ClassnameUtilities::getInstance()->getNamespaceFromObject($this->category);
        if (!$this->isSubmitted())
        {
            unset($_SESSION[$context]['number_of_categories']);
            unset($_SESSION[$context]['skipped_categories']);
        }

        if (!isset($_SESSION[$context]['number_of_categories']))
        {
            $_SESSION[$context]['number_of_categories'] = 1;
        }

        if (!isset($_SESSION[$context]['skipped_categories']))
        {
            $_SESSION[$context]['skipped_categories'] = [];
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
            if (!in_array($category_number, $_SESSION[$context]['skipped_categories']))
            {
                $group = [];
                $group[] = $this->add_name_field($category_number);
                if ($number_of_categories - count($_SESSION[$context]['skipped_categories']) > 1)
                {
                    $group[] = $this->createElement(
                        'style_button', 'remove[' . $category_number . ']', null, [], null,
                        new FontAwesomeGlyph('times', [], null, 'fas')
                    );
                }
                $this->addGroup(
                    $group, PlatformCategory::PROPERTY_NAME . $category_number, Translation::get('CategoryName'), '',
                    false
                );
                $this->addRule(
                    PlatformCategory::PROPERTY_NAME . $category_number,
                    Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
                );
            }
        }

        $this->addElement(
            'style_button', 'add[]', null, [], null, new FontAwesomeGlyph('plus', [], null, 'fas')
        );

        $this->build_footer('Create');
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

    public function build_footer($action_name)
    {
        $this->addElement('html', '</fieldset>');

        // Submit button
        // $this->addElement('submit', 'submit', 'OK');

        $buttons[] = $this->createElement('style_submit_button', 'create', Translation::get($action_name));
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'CategoryForm.js'
        )
        );
    }

    public function build_header()
    {
        $this->addElement('html', '<fieldset>');
        $this->addElement(
            'html', '<legend>' . Translation::get('Required', null, StringUtilities::LIBRARIES) . '</legend>'
        );
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
                $category = $this->manager->get_parent()->getCategory();
                $category->set_name($value);
                $category->set_parent($this->category->get_parent());
                $category->set_display_order(
                    $this->manager->get_parent()->get_next_category_display_order($this->category->get_parent())
                );

                $conditions = [];
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(get_class($category), $category::PROPERTY_NAME),
                    new StaticConditionVariable($category->get_name())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(get_class($category), $category::PROPERTY_PARENT),
                    new StaticConditionVariable($category->get_parent())
                );
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

    /**
     * Sets default values.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        $category = $this->category;
        $defaults[PlatformCategory::PROPERTY_ID] = $category->get_id();
        $defaults[PlatformCategory::PROPERTY_NAME] = $category->get_name();
        parent::setDefaults($defaults);
    }

    public function update_category()
    {
        $category = $this->category;
        $category->set_name($this->exportValue(PlatformCategory::PROPERTY_NAME));

        $conditions = [];
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(get_class($category), $category::PROPERTY_ID),
                new StaticConditionVariable($category->get_id())
            )
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(get_class($category), $category::PROPERTY_NAME),
            new StaticConditionVariable($category->get_name())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(get_class($category), $category::PROPERTY_PARENT),
            new StaticConditionVariable($category->get_parent())
        );
        $condition = new AndCondition($conditions);
        $cats = $this->manager->get_parent()->count_categories($condition);

        if ($cats > 0)
        {
            return false;
        }

        return $category->update();
    }

    public function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }

        return parent::validate();
    }
}
