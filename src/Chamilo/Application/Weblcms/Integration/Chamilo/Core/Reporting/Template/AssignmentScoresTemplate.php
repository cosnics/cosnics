<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentCourseGroupScoresBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentPlatformGroupScoresBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\AssignmentUserScoresBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of scores of each assignment
 *          per user, course group and platform group
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssignmentScoresTemplate extends ReportingTemplate
{
    // const LEGEND_TYPE_GROUP = 'group';
    public function __construct($parent)
    {
        parent::__construct($parent);
        
        $custom_breadcrumbs = array();
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), Translation::get('AssignmentScores'));
        $this->set_custom_breadcrumb_trail($custom_breadcrumbs);
        
        $this->add_reporting_block(new AssignmentUserScoresBlock($this, true));
        $this->add_reporting_block(new AssignmentCourseGroupScoresBlock($this, true));
        $this->add_reporting_block(new AssignmentPlatformGroupScoresBlock($this, true));
    }
}
