<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Chat\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Chat\Manager;
use Chamilo\Libraries\Format\Response\Response;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class ViewerComponent extends Manager
{

    public function run()
    {

        $html = [];

        $html[] = $this->render_header();
        $html[] = '<div class="alert alert-warning">' .
            $this->getTranslator()->trans('NoLongerSupported', [], Manager::context()) . '</div>';
        $html[] = $this->render_footer();

        return new Response(implode(PHP_EOL, $html));
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_tool_chat_viewer');
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

        return $additionalParameters;
    }
}
