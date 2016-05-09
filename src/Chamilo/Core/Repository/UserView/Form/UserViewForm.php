<?php
namespace Chamilo\Core\Repository\UserView\Form;

use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserViewRelContentObject;
use Chamilo\Core\Repository\UserView\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserViewForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    /**
     *
     * @var \core\repository\user_view\UserView
     */
    private $user_view;

    /**
     *
     * @var int
     */
    private $form_type;

    /**
     *
     * @param int $form_type
     * @param \core\repository\user_view\UserView $user_view
     * @param string $action
     */
    public function __construct($form_type, $user_view, $action)
    {
        parent :: __construct('user_views_settings', 'post', $action);

        $this->user_view = $user_view;

        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement(
            'text',
            UserView :: PROPERTY_NAME,
            Translation :: get('Name', null, Utilities :: COMMON_LIBRARIES),
            array("size" => "50"));
        $this->addRule(
            UserView :: PROPERTY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');
        $this->add_html_editor(
            UserView :: PROPERTY_DESCRIPTION,
            Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES),
            false);

        $registrations = \Chamilo\Core\Repository\Storage\DataManager :: get_registered_types();
        $hidden_types = \Chamilo\Core\Repository\Storage\DataManager :: get_active_helper_types();

        $typeSelectorFactory = new TypeSelectorFactory(
            \Chamilo\Core\Repository\Storage\DataManager :: get_registered_types());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        foreach ($type_selector->get_categories() as $category)
        {
            foreach ($category->get_options() as $option)
            {
                $content_object_template_ids[$option->get_template_registration_id()] = $option->get_label();
            }
        }

        if ($this->form_type == self :: TYPE_EDIT)
        {

            $relations = DataManager :: retrieves(
                UserViewRelContentObject :: class_name(),
                new DataClassRetrievesParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(
                            UserViewRelContentObject :: class_name(),
                            UserViewRelContentObject :: PROPERTY_USER_VIEW_ID),
                        new StaticConditionVariable($this->get_user_view()->get_id()))));

            while ($relation = $relations->next_result())
            {
                $defaults[] = $relation->get_content_object_template_id();
            }
        }

        $elem = &$this->addElement(
            'select',
            'types',
            Translation :: get('SelectTypesToShow'),
            $content_object_template_ids,
            array(
                'multiple' => 'true',
                'size' => (count($content_object_template_ids) > 10 ? 10 : count($content_object_template_ids))));

        $this->setDefaults(array('types' => $defaults));
    }

    public function build_editing_form()
    {
        $user_view = $this->user_view;
        $this->build_basic_form();

        $this->addElement('hidden', UserView :: PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     *
     * @return boolean
     */
    public function update_user_view()
    {
        $user_view = $this->user_view;
        $values = $this->exportValues();

        $user_view->set_name($values[UserView :: PROPERTY_NAME]);
        $user_view->set_description($values[UserView :: PROPERTY_DESCRIPTION]);

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                UserViewRelContentObject :: class_name(),
                UserViewRelContentObject :: PROPERTY_USER_VIEW_ID),
            new StaticConditionVariable($user_view->get_id()));

        $types = DataManager :: retrieves(
            UserViewRelContentObject :: class_name(),
            new DataClassRetrievesParameters($condition));
        $existing_types = array();
        while ($type = $types->next_result())
        {
            $existing_types[] = $type->get_content_object_template_id();
        }

        $new_types = $values['types'];
        $to_add = array_diff($new_types, $existing_types);
        $to_delete = array_diff($existing_types, $new_types);

        foreach ($to_add as $type_to_add)
        {
            $user_view_type = new UserViewRelContentObject();
            $user_view_type->set_user_view_id($user_view->get_id());
            $user_view_type->set_content_object_template_id($type_to_add);
            $user_view_type->create();
        }

        $types->reset();
        while ($type = $types->next_result())
        {
            if (in_array($type->get_content_object_template_id(), $to_delete))
            {
                $type->delete();
            }
        }

        return $user_view->update();
    }

    /**
     *
     * @return boolean
     */
    public function create_user_view()
    {
        $values = $this->exportValues();

        $this->user_view->set_name($values[UserView :: PROPERTY_NAME]);
        $this->user_view->set_description($values[UserView :: PROPERTY_DESCRIPTION]);

        if ($this->user_view->create())
        {
            foreach ($values['types'] as $template_id)
            {
                $relation = new UserViewRelContentObject();
                $relation->set_user_view_id($this->user_view->get_id());
                $relation->set_content_object_template_id($template_id);

                if (! $relation->create())
                {
                    return false;
                }
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @see HTML_QuickForm::setDefaults()
     */
    public function setDefaults($defaults = array ())
    {
        $defaults[UserView :: PROPERTY_ID] = $this->user_view->get_id();
        $defaults[UserView :: PROPERTY_NAME] = $this->user_view->get_name();
        $defaults[UserView :: PROPERTY_DESCRIPTION] = $this->user_view->get_description();
        parent :: setDefaults($defaults);
    }

    /**
     *
     * @return \core\repository\user_view\UserView
     */
    public function get_user_view()
    {
        return $this->user_view;
    }
}
