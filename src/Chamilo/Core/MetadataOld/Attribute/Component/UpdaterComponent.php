<?php
namespace Chamilo\Core\MetadataOld\Attribute\Component;

use Chamilo\Core\MetadataOld\Attribute\Form\AttributeForm;
use Chamilo\Core\MetadataOld\Attribute\Manager;
use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Attribute\Storage\DataManager;
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

        $attribute_id = Request :: get(self :: PARAM_ATTRIBUTE_ID);
        $attribute = DataManager :: retrieve_by_id(Attribute :: class_name(), $attribute_id);

        $form = new AttributeForm($this->get_url(), $attribute);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $attribute->set_schema_id($values[Attribute :: PROPERTY_SCHEMA_ID]);
                $attribute->set_name($values[Attribute :: PROPERTY_NAME]);
                $attribute->set_display_name($values[Attribute :: PROPERTY_DISPLAY_NAME]);
                $success = $attribute->update();

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('Attribute')),
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
                    array(self :: PARAM_ATTRIBUTE_ID)),
                Translation :: get('BrowserComponent')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ATTRIBUTE_ID);
    }
}