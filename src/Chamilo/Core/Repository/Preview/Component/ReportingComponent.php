<?php
namespace Chamilo\Core\Repository\Preview\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Reporting\Preview\PreviewSupport;
use Chamilo\Core\Repository\Preview\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ReportingComponent extends Manager implements PreviewSupport
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->has_reporting())
        {
            $this->redirect(
                Translation::get('NoReportingPreview'),
                true,
                array(self::PARAM_ACTION => self::ACTION_DISPLAY));
        }

        return $this->getApplicationFactory()->getApplication(
            $this->get_preview_context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    public function render_header()
    {
        $html = array();

        $html[] = parent::render_header();

        $preview_context = $this->get_preview_context();
        $preview_class_name = $preview_context . '\Manager';
        $available_actions = $preview_class_name::get_available_actions();

        if (count($available_actions) > 1)
        {
            $html[] = '<div class="reporting-container">';
            $html[] = '<div class="reporting-body">';
            $html[] = '<div class="reporting-container">';
            $html[] = '<main class="reporting-content">';
        }

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $preview_context = $this->get_preview_context();
        $preview_class_name = $preview_context . '\Manager';
        $available_actions = $preview_class_name::get_available_actions();

        $html = array();

        if (count($available_actions) > 1)
        {
            $toolbar = new Toolbar(Toolbar::TYPE_VERTICAL);

            foreach ($available_actions as $available_action)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get(
                            (string) StringUtilities::getInstance()->createString($available_action)->upperCamelize() .
                                 'Component',
                                null,
                                $preview_context),
                        Theme::getInstance()->getImagePath($preview_context, 'Type/' . $available_action),
                        $this->get_url(
                            array(
                                \Chamilo\Core\Repository\Integration\Chamilo\Core\Reporting\Preview\Manager::PARAM_ACTION => $available_action))));
            }

            $html[] = '</main>';
            $html[] = '</div>';

            $html[] = '<aside class="reporting-left">';
            $html[] = '<div class="reporting-left-title"><div class="bevel">' . Translation::get('AvailableTemplates') .
                 '</div></div>';
            $html[] = $toolbar->as_html();
            $html[] = '</aside>';

            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_preview_context()
    {
        $package = $this->get_content_object()->package();
        return $package . '\Integration\Chamilo\Core\Reporting\Preview';
    }
}
