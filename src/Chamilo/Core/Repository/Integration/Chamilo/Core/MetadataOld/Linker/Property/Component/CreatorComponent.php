<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Form\ContentObjectPropertyRelMetadataElementFormBuilder;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to create a ContentObjectPropertyRelMetadataElement
 *
 * @author Sven Vanpoucke - Hogeschool Gent
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

        $form = new FormValidator('content_object_property_rel_metadata_element', 'post', $this->get_url());

        $form_builder = new ContentObjectPropertyRelMetadataElementFormBuilder($form);
        $form_builder->build_form();

        if ($form->validate())
        {
            $values = $form->exportValues();

            $content_object_property_rel_metadata_element = new ContentObjectPropertyRelMetadataElement();

            $this->fill_content_object_property_rel_metadata_element_from_values_array(
                $content_object_property_rel_metadata_element,
                $values);

            try
            {
                $success = $content_object_property_rel_metadata_element->create();

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

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
                $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE)),
                Translation :: get('BrowserComponent')));
    }
}