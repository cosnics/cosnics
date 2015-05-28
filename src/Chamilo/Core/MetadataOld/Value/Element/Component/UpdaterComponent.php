<?php
namespace Chamilo\Core\MetadataOld\Value\Element\Component;

use Chamilo\Core\MetadataOld\Value\Element\Form\ElementValueForm;
use Chamilo\Core\MetadataOld\Value\Element\Manager;
use Chamilo\Core\MetadataOld\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Core\MetadataOld\Value\Element\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update the controlled vocabulary
 *
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdaterComponent extends Manager
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

        $default_element_value_id = Request :: get(self :: PARAM_ELEMENT_VALUE_ID);
        $default_element_value = DataManager :: retrieve_by_id(
            DefaultElementValue :: class_name(),
            $default_element_value_id);

        $form = new ElementValueForm(
            $this->get_url(),
            $default_element_value,
            \Chamilo\Core\MetadataOld\Storage\DataManager :: retrieve_controlled_vocabulary_from_element(
                Request :: get(\Chamilo\Core\MetadataOld\Element\Manager :: PARAM_ELEMENT_ID))->as_array());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $default_element_value->set_value($values[DefaultElementValue :: PROPERTY_VALUE]);
                $default_element_value->set_element_vocabulary_id(
                    $values[DefaultElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID]);

                $success = $default_element_value->update();

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

            return implode(PHP_EOL, $html);
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
                $this->get_url(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE),
                    $this->get_additional_parameters()),
                Translation :: get('BrowserComponent')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ELEMENT_VALUE_ID);
    }
}