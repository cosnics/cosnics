<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Form\BuilderForm;
use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AddElementComponent extends Manager
{

    public function run()
    {
        $type = Request :: get(self :: PARAM_DYNAMIC_FORM_ELEMENT_TYPE);
        $parameters = array(self :: PARAM_DYNAMIC_FORM_ELEMENT_TYPE => $type);

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('AddElement')));
        $trail->add_help('dynamic form general');

        $element = new Element();
        $element->set_type($type);
        $element->set_dynamic_form_id($this->get_form()->get_id());

        $form = new BuilderForm(BuilderForm :: TYPE_CREATE, $element, $this->get_url($parameters), $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_dynamic_form_element();
            $this->redirect(
                Translation :: get($success ? 'DynamicFormElementAdded' : 'DynamicFormElementNotAdded'),
                ($success ? false : true),
                array(self :: PARAM_ACTION => self :: ACTION_BUILD_DYNAMIC_FORM));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
