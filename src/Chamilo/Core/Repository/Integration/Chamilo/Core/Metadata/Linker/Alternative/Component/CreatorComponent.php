<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Form\ContentObjectAlternativeFormBuilder;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataClass\ContentObjectAlternative;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to create a ContentObjectAlternative
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

        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            return $this->display_repository_viewer();
        }
        else
        {
            $selected_content_object_ids = \Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects();

            if (! is_array($selected_content_object_ids))
            {
                $selected_content_object_ids = array($selected_content_object_ids);
            }

            $this->display_content_object_alternative_form($selected_content_object_ids);
        }
    }

    /**
     * Override the render_header functionality to display additional information before the repo viewer / form
     */
    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();
        $html[] = $this->display_content_object();

        return implode("\n", $html);
    }

    /**
     * Displays the repository viewer
     */
    protected function display_repository_viewer()
    {
        $this->set_parameter(
            \Chamilo\Core\Repository\Viewer\Component\BrowserComponent :: SHARED_BROWSER_ALLOWED,
            false);

        $excluded_content_object_ids = DataManager :: retrieve_linked_content_object_ids(
            $this->get_selected_content_object_id());

        $excluded_content_object_ids[] = $this->get_selected_content_object_id();

        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Viewer\Manager :: context(),
            $this->get_user(),
            $this);
        $component = $factory->getComponent();
        $component->set_excluded_objects($excluded_content_object_ids);
        return $component->run();
    }

    /**
     * Displays the content object alternative form
     *
     * @param array $selected_content_object_ids
     */
    protected function display_content_object_alternative_form($selected_content_object_ids = array())
    {
        $form = new FormValidator('content_object_alternative', 'post', $this->get_url());

        $metadata_elements = $this->get_allowed_metadata_elements($selected_content_object_ids);

        $form_builder = new ContentObjectAlternativeFormBuilder($form);
        $form_builder->build_form(null, $metadata_elements);

        if (count($metadata_elements) == 1 || $form->validate())
        {
            try
            {
                if ($form->validate())
                {
                    $values = $form->exportValues();
                    $metadata_element_id = $values[ContentObjectAlternative :: PROPERTY_METADATA_ELEMENT_ID];
                }
                else
                {
                    $metadata_element_id = $metadata_elements[0]->get_id();
                }

                $success = DataManager :: create_content_object_alternatives(
                    $this->get_selected_content_object_id(),
                    $selected_content_object_ids,
                    $metadata_element_id);

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

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
            $html[] = $this->display_selected_content_objects($selected_content_object_ids);
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }

    /**
     * Displays the selected content objects
     *
     * @param int[] $selected_content_object_ids
     */
    protected function display_selected_content_objects($selected_content_object_ids = array())
    {
        $html = array();

        $html[] = '<br /><br />';
        $html[] = '<div class="content_object padding_10">';
        $html[] = '<div class="title">';
        $html[] = Translation :: get('SelectedContentObjects', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul class="attachments_list">';

        $content_objects = $this->retrieve_content_objects_by_id($selected_content_object_ids);

        while ($content_object = $content_objects->next_result())
        {
            $html[] = '<li><img src="' . $content_object->get_icon_path(Theme :: ICON_MINI) . '" alt="' .
                 htmlentities(Translation :: get('TypeName', null, $content_object->get_type())) . '"/> ' .
                 $content_object->get_title() . '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</div></div>';
        $html[] = '<br /><br />';

        return implode("\n", $html);
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

    /**
     * Returns the allowed content object types
     *
     * @return array
     */
    public function get_allowed_content_object_types()
    {
        return array(
            \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File :: class_name(),
            \Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube :: class_name(),
            \Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link :: class_name(),
            $this->get_selected_content_object()->get_type());
    }

    /**
     * Returns the additional parameters that need to be registered for this component
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION,
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID);
    }
}