<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Component;

use Chamilo\Core\Metadata\Value\Element\Form\ElementValueEditorFormBuilder;
use Chamilo\Core\Metadata\Value\Element\Form\Handler\ElementValueEditorFormHandler;
use Chamilo\Core\Metadata\Value\Form\Helper\ValueEditorFormExportValuesCleaner;
use Chamilo\Core\Repository\Form\TagsFormBuilder;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\ContentObjectMetadataValueCreator;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to edit the metadata in batch for several content objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataBatchEditorComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $selected_content_objects = $this->get_content_objects_from_request();

        $selected_elements = \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Storage\DataManager :: get_common_metadata_elements(
            $selected_content_objects);

        $form = $this->create_form($selected_elements);

        if ($form->validate())
        {
            $values = $form->exportValues();

            try
            {
                $this->clean_element_values($selected_elements, $values, $selected_content_objects);

                foreach ($selected_content_objects as $content_object)
                {
                    $metadata_form_handler = new ElementValueEditorFormHandler(
                        new ContentObjectMetadataValueCreator($content_object));
                    $metadata_form_handler->handle_form($values);
                }

                \Chamilo\Core\Repository\Storage\DataManager :: add_tags_to_content_objects(
                    explode(',', $values[TagsFormBuilder :: PROPERTY_TAGS]),
                    $this->get_selected_content_object_ids(),
                    $this->get_user_id());

                $success = true;

                $message = Translation :: get('BatchMetadataSet');
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->get_parent()->redirect_from_batch_editor($success, $message);
        }
        else
        {
            $this->display_page($form, $selected_content_objects);
        }
    }

    /**
     * Builds the form
     *
     * @param Element[] $selected_elements
     *
     * @return \libraries\format\FormValidator
     */
    protected function create_form(array $selected_elements)
    {
        $form = new FormValidator('metadata_batch_editor', 'post', $this->get_url());

        $tags_form_builder = new TagsFormBuilder($form);
        $tags_form_builder->build_form(
            \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_tags_for_user($this->get_user_id()));

        $form_builder = new ElementValueEditorFormBuilder($form);
        $form_builder->build_form($selected_elements, null, false);

        $buttons = array();

        $buttons[] = $form->createElement(
            'style_submit_button',
            'submit_button',
            Translation :: get('SetMetadata'),
            array('class' => 'positive'));

        $buttons[] = $form->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        return $form;
    }

    /**
     * Display's the page
     *
     * @param FormValidator $form
     * @param ContentObject[] $selected_content_objects
     */
    protected function display_page(FormValidator $form, array $selected_content_objects)
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = '<div class="content_object padding_10">';
        $html[] = '<div class="title">';
        $html[] = Translation :: get('SelectedContentObjects', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul class="attachments_list">';

        foreach ($selected_content_objects as $content_object)
        {
            $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                ContentObject :: get_content_object_type_namespace($content_object->get_type()));

            $html[] = '<li><img src="' . $content_object->get_icon_path(Theme :: ICON_MINI) . '" alt="' .
                 htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' . $content_object->get_title() .
                 '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Cleans the element values for the selected elements that are filled in with a valid value for the given content
     * objects
     *
     * @param Element[] $selected_elements
     * @param string[] $form_values
     * @param ContentObject[] $content_objects
     */
    protected function clean_element_values($selected_elements, $form_values, $content_objects)
    {
        $value_cleaner = new ValueEditorFormExportValuesCleaner();
        $form_values = $value_cleaner->clean_export_values($form_values);
        $metadata_form_values = $form_values[ElementValueEditorFormBuilder :: FORM_ELEMENT_METADATA_PREFIX];

        $clean_elements = array();

        foreach ($selected_elements as $selected_element)
        {
            $value = $metadata_form_values[$selected_element->render_name()];
            if (\Chamilo\Core\Metadata\Element\Storage\DataManager :: element_has_controlled_vocabulary(
                $selected_element->get_id()))
            {
                if (is_numeric($value) && $value != 0)
                {
                    $clean_elements[] = $selected_element;
                }
            }
            else
            {
                if (is_numeric($value) || ! empty($value))
                {
                    $clean_elements[] = $selected_element;
                }
            }
        }

        DataManager :: clean_element_values_for_elements_and_content_objects($clean_elements, $content_objects);
    }
}