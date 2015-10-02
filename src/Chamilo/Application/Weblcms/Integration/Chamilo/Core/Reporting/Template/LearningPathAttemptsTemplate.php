<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath\LearningPathAttemptsBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: learning_path_attempts_reporting_template.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.reporting.templates
 */
/**
 *
 * @author Michael Kyndt
 */
class LearningPathAttemptsTemplate extends ReportingTemplate
{

    private $publication_id;

    public function __construct($parent)
    {
        parent :: __construct($parent);
        
        $this->publication_id = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION);
        $this->tool = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL);
        $this->add_reporting_block(new LearningPathAttemptsBlock($this));
        
        $lp = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(), 
            $this->publication_id)->get_content_object();
        
        $custom_breadcrumbs = array();
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), $lp->get_title());
        $this->set_custom_breadcrumb_trail($custom_breadcrumbs);
    }

    public function get_publication_id()
    {
        return $this->publication_id;
    }
}
