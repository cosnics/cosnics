<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki\WikiPageMostActiveUsersBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki\WikiPageUsersContributionsBlock;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\Display\Manager;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.lib.weblcms.reporting.templates
 * @author Michael Kyndt
 */
class WikiPageTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);
        
        $complex_content_object_item_id = Request::get(
            Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($complex_content_object_item_id)
        {
            $this->set_parameter(
                Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                $complex_content_object_item_id);
        }
        
        $this->add_reporting_block(new WikiPageMostActiveUsersBlock($this));
        $this->add_reporting_block(new WikiPageUsersContributionsBlock($this));
    }
}
