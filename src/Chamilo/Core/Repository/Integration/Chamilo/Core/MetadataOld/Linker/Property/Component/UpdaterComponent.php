<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Form\ContentObjectPropertyRelMetadataElementFormBuilder;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update a ContentObjectPropertyRelMetadataElement
 *
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

        $content_object_property_rel_metadata_element = $this->get_content_object_property_rel_metadata_element_from_request();

        $form = new FormValidator('content_object_property_rel_metadata_element', 'post', $this->get_url());

        $form_builder = new ContentObjectPropertyRelMetadataElementFormBuilder($form);
        $form_builder->build_form($content_object_property_rel_metadata_element);

        if ($form->validate())
        {
            $values = $form->exportValues();

            $this->fill_content_object_property_rel_metadata_element_from_values_array(
                $content_object_property_rel_metadata_element,
                $values);

            try
            {
                $success = $content_object_property_rel_metadata_element->update();

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('ContentObjectPropertyRelMetadataElement')),
                    Utilities :: COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect(
                $message,
                ! $success,
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE),
                $this->get_additional_parameters());
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
     * Returns the additional parameters for this component
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTENT_OBJECT_PROPERTY_REL_METADATA_ELEMENT_ID);
    }
}