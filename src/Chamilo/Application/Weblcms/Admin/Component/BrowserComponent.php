<?php
namespace Chamilo\Application\Weblcms\Admin\Component;

use Chamilo\Application\Weblcms\Admin\Manager;
use Chamilo\Application\Weblcms\Admin\Renderer;

class BrowserComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $renderer = new Renderer($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $renderer->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}