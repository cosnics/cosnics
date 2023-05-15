<?php
namespace Chamilo\Configuration\Form;

use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package configuration\form
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_ADD_FORM_ELEMENT = 'AddElement';
    public const ACTION_BUILD_DYNAMIC_FORM = 'Builder';
    public const ACTION_DELETE_FORM_ELEMENT = 'DeleteElement';
    public const ACTION_UPDATE_FORM_ELEMENT = 'UpdateElement';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BUILD_DYNAMIC_FORM;

    public const PARAM_ACTION = 'form_action';
    public const PARAM_DELETE_FORM_ELEMENETS = 'delete_elements';
    public const PARAM_DYNAMIC_FORM_ELEMENT_ID = 'dynfo_el_id';
    public const PARAM_DYNAMIC_FORM_ELEMENT_TYPE = 'dynfo_el_type';
    public const PARAM_DYNAMIC_FORM_ID = 'dynfo_id';

    public const TYPE_BUILDER = 0;
    public const TYPE_EXECUTER = 2;
    public const TYPE_VIEWER = 1;

    private $form;

    private $target_user_id;

    public function get_add_element_url()
    {
        return $this->get_url([self::PARAM_ACTION => self::ACTION_ADD_FORM_ELEMENT]);
    }

    public function get_delete_element_url($element)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_DELETE_FORM_ELEMENT,
                self::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->get_id()
            ]
        );
    }

    public function get_dynamic_form_title()
    {
        return $this->get_parent()->get_dynamic_form_title();
    }

    public function get_form()
    {
        return $this->form;
    }

    public function get_target_user_id($target_user_id)
    {
        return $this->target_user_id;
    }

    public function get_update_element_url($element)
    {
        return $this->get_url(
            [
                self::PARAM_ACTION => self::ACTION_UPDATE_FORM_ELEMENT,
                self::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->get_id()
            ]
        );
    }

    private function retrieve_form($application, $name)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_APPLICATION),
            new StaticConditionVariable($application)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_NAME), new StaticConditionVariable($name)
        );
        $condition = new AndCondition($conditions);
        $form = DataManager::retrieve_dynamic_forms($condition)->current();

        if (!$form)
        {
            $form = new Instance();
            $form->set_application($application);
            $form->set_name($name);
            $form->create();
        }

        return $form;
    }

    public function set_form($form)
    {
        $this->form = $form;
    }

    public function set_form_by_name($name)
    {
        $this->set_form($this->retrieve_form($this->get_application()::CONTEXT, $name));
    }

    public function set_target_user_id($target_user_id)
    {
        $this->target_user_id = $target_user_id;
    }
}
