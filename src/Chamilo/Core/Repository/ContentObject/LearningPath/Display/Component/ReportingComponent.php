<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Integration\Chamilo\Core\Reporting\Template\ProgressDetailsTemplate;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Integration\Chamilo\Core\Reporting\Template\ProgressTemplate;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class ReportingComponent extends Manager
{

    public function run()
    {
        parent :: run();

        if ($this->is_current_step_set())
        {
            $template_type = ProgressDetailsTemplate :: class_name();
        }
        else
        {
            $template_type = ProgressTemplate :: class_name();
        }

        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Reporting\Viewer\Manager :: context(),
            $this->get_user(),
            $this);
        $component = $factory->getComponent();
        $component->set_template_by_name($template_type);
        return $component->run();
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\Manager::render_header()
     */
    public function render_header()
    {
        $tabs_renderer = $this->get_tabs_renderer();
        $html = array();

        $html[] = parent :: render_header();
        $html[] = $tabs_renderer->header();
        $html[] = $tabs_renderer :: body_header();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\Manager::render_footer()
     */
    public function render_footer()
    {
        $tabs_renderer = $this->get_tabs_renderer();
        $html = array();

        $html[] = $tabs_renderer :: body_footer();
        $html[] = $tabs_renderer->footer();
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }
}
