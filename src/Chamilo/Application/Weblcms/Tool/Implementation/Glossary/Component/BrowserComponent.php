<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class BrowserComponent extends Manager
{

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

    }
}
