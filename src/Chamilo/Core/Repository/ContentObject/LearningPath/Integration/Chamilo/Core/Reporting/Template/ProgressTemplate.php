<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Reporting\Block\ProgressBlock;

/**
 *
 * @package application\weblcms\integration\core\reporting
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProgressTemplate extends ReportingTemplate
{

    /**
     *
     * @param Application $parent
     */
    public function __construct($parent)
    {
        parent :: __construct($parent);
        $this->add_reporting_block(new ProgressBlock($this));
    }
}