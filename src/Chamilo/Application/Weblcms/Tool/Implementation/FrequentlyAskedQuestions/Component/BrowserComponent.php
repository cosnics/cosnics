<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * $Id: forum_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.forum.component
 */
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
