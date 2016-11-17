<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Reporting\Viewer\NoBlockTabsAllowed;
use Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block\ProgressDetailsBlock;
use Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block\ProgressDetailsInformationBlock;

/**
 *
 * @package application\weblcms\integration\core\reporting
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProgressDetailsTemplate extends ReportingTemplate implements NoBlockTabsAllowed
{

    /**
     *
     * @param Application $parent
     */
    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->add_reporting_block(new ProgressDetailsInformationBlock($this));
        $this->add_reporting_block(new ProgressDetailsBlock($this));
    }
}