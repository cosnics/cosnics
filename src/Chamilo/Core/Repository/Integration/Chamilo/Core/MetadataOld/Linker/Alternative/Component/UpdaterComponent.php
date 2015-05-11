<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Form\ContentObjectAlternativeFormBuilder;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Storage\DataClass\ContentObjectAlternative;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Controller to update a ContentObjectAlternative
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

        $content_object_alternative = $this->get_content_object_alternative_from_request();

        $form = new FormValidator('content_object_alternative', 'post', $this->get_url());

        $this->add_content_object_information_to_form($content_object_alternative, $form);

        $form_builder = new ContentObjectAlternativeFormBuilder($form);

        $form_builder->build_form(
            $content_object_alternative,
            $this->get_allowed_metadata_elements(array($content_object_alternative->get_content_object_id())));

        if ($form->validate())
        {
            $values = $form->exportValues();

            try
            {
                $success = DataManager :: update_content_object_alternative_to_new_metadata_element(
                    $content_object_alternative,
                    $this->get_selected_content_object_id(),
                    $values[ContentObjectAlternative :: PROPERTY_METADATA_ELEMENT_ID]);

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation :: get(
                    $translation,
                    array('OBJECT' => Translation :: get('ContentObjectAlternative')),
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
     * Adds the content object information to the form
     *
     * @param ContentObjectAlternative $content_object_alternative
     * @param FormValidator $form
     */
    public function add_content_object_information_to_form($content_object_alternative, $form)
    {
        $form->addElement('category', Translation :: get('BaseContentObject'));
        $form->addElement('html', $this->display_content_object());
        $form->addElement('category');

        $alternative_content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $content_object_alternative->get_content_object_id());

        $form->addElement('category', Translation :: get('AlternativeContentObject'));
        $form->addElement('html', $this->display_content_object($alternative_content_object));
        $form->addElement('category');
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
        return array(self :: PARAM_CONTENT_OBJECT_ALTERNATIVE_ID);
    }
}