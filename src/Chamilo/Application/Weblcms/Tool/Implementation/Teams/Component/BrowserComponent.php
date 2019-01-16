<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Class BrowserComponent
 */
class BrowserComponent extends Manager
{

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run():string
    {
        return $this->render();
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function render():string
    {
        return $this->getTwig()->render(
            'Chamilo\Application\Weblcms\Tool\Implementation\Teams:Browser.html.twig',
            [
                'HEADER' => $this->render_header(),
                'FOOTER' => $this->render_footer()
            ]
        );
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}