<?php
namespace Chamilo\Core\Metadata\Value\Element\Component;

use Chamilo\Core\Metadata\Value\Element\Form\ElementValueForm;
use Chamilo\Core\Metadata\Value\Element\Manager;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to create the schema
 */
class CreatorComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $form = new ElementValueForm(
            $this->get_url(),
            null,
            \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_controlled_vocabulary_from_element(
                Request :: get(\Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID))->as_array());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $default_element_value = new DefaultElementValue();

                $default_element_value->set_value($values[DefaultElementValue :: PROPERTY_VALUE]);
                $default_element_value->set_element_vocabulary_id(
                    $values[DefaultElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID]);

                $element_id = Request :: get(\Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID);
                $default_element_value->set_element_id($element_id);

                $success = $default_element_value->create();

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('DefaultElementValue')),
                    Utilities :: COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }

    /**
     * Adds additional breadcrumbs
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumb_trail
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE)),
                Translation :: get('BrowserComponent')));
    }
}