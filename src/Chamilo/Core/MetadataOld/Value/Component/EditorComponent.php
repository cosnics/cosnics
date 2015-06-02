<?php
namespace Chamilo\Core\MetadataOld\Value\Component;

use Chamilo\Core\MetadataOld\Value\Form\Handler\ValueEditorFormHandler;
use Chamilo\Core\MetadataOld\Value\Form\ValueEditorFormBuilder;
use Chamilo\Core\MetadataOld\Value\Manager;
use Chamilo\Core\MetadataOld\Value\MetadataValueEditorComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * Displays and handles the metadata form
 *
 * @package core\metadata\value
 */
class EditorComponent extends Manager
{

    /**
     * Constructor
     *
     * @param MetadataValueEditorComponent $parent
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (! $applicationConfiguration->getApplication() instanceof MetadataValueEditorComponent)
        {
            throw new \InvalidArgumentException(
                'The parent component must be an instance of MetadataValueEditorComponent');
        }

        parent :: __construct($applicationConfiguration);
    }

    /**
     * Runs this component
     */
    public function run()
    {
        $form = new FormValidator('metadata_value_form', 'post', $this->get_url());

        $form_builder = new ValueEditorFormBuilder($form);
        $form_builder->build_form($this->get_parent()->get_element_values());
        $form_builder->add_submit_buttons();

        if ($form->validate())
        {
            try
            {
                $this->get_parent()->truncate_values();

                $handler = new ValueEditorFormHandler($this->get_parent()->get_value_creator());
                $handler->handle_form($form->exportValues());

                $success = true;
                $message = Translation :: get('MetadataValuesUpdated');
            }
            catch (\Exception $ex)
            {
                $message = $ex->getMessage();
                $success = false;
            }

            $this->get_parent()->redirect_after_update($success, $message);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_additional_information();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Displays the additional information (if any)
     */
    protected function display_additional_information()
    {
        if (method_exists($this->get_parent(), 'get_additional_information'))
        {
            return $this->get_parent()->get_additional_information();
        }
    }
}
