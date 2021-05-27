<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Form\BuilderForm;
use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdateElementComponent extends Manager
{

    public function run()
    {
        $element_id = Request::get(self::PARAM_DYNAMIC_FORM_ELEMENT_ID);
        $parameters = array(self::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element_id);
        
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation::get('UpdateElement')));
        $trail->add_help('dynamic form general');
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_ID),
            new StaticConditionVariable($element_id));
        $element = DataManager::retrieve_dynamic_form_elements($condition)->current();
        
        $form = new BuilderForm(BuilderForm::TYPE_EDIT, $element, $this->get_url($parameters), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_dynamic_form_element();
            $this->redirect(
                Translation::get($success ? 'DynamicFormElementUpdated' : 'DynamicFormElementNotUpdated'), 
                ($success ? false : true), 
                array(self::PARAM_ACTION => self::ACTION_BUILD_DYNAMIC_FORM));
        }
        else
        {
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}
