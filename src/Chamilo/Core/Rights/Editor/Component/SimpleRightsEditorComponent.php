<?php
namespace Chamilo\Core\Rights\Editor\Component;

use Chamilo\Core\Rights\Editor\Form\SimpleRightsEditorForm;
use Chamilo\Core\Rights\Editor\Manager;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Translation\Translation;

/**
 * Simple interface to edit rights
 *
 * @author Sven Vanpoucke
 * @package application.common.rights_editor_manager.component
 * @deprecated Should not be needed anymore
 */
class SimpleRightsEditorComponent extends Manager implements BreadcrumbLessComponentInterface
{
    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);

        $additional_information = $this->get_additional_information();

        if ($additional_information)
        {
            $html[] = '<div style="background-color: #E5EDF9; border: 1px solid #B9D0EF; color: #272761; margin-top: 5px;
                margin-bottom: 5px; padding: 7px;">';
            $html[] = $additional_information;
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function run()
    {
        $form = new SimpleRightsEditorForm(
            $this->get_url(), $this->get_context(), $this->get_locations(), $this->get_available_rights(),
            $this->get_entities()
        );

        if ($form->validate())
        {
            $succes = $form->handle_form_submit();

            $message = Translation::get($succes ? 'RightsChanged' : 'RightsNotChanged');
            $this->redirectWithMessage($message, !$succes);
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
