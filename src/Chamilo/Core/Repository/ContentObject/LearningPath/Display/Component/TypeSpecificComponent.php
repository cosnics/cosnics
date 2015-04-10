<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TypeSpecificComponent extends Manager implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function run()
    {
        parent :: run();

        $object_namespace = $this->get_current_node()->get_content_object()->package();
        $integration_namespace = $object_namespace . '\Integration\\' . __NAMESPACE__;

        $factory = new ApplicationFactory($this->getRequest(), $integration_namespace, $this->get_user(), $this);
        return $factory->run();
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
