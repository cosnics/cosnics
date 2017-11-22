<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin\CoursesPerCategoryBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin\MostActiveInactiveLastDetailBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin\MostActiveInactiveLastPublicationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin\MostActiveInactiveLastVisitBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin\NoOfCoursesBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin\NoOfCoursesByLanguageBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin\NoOfUsersSubscribedCourseBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool\LastAccessToToolsPlatformBlock;
use Chamilo\Core\Reporting\ReportingTemplate;

/**
 *
 * @package application.lib.weblcms.reporting.templates
 */
class CourseDataTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->add_reporting_block(new NoOfCoursesBlock($this));
        $this->add_reporting_block(new NoOfCoursesByLanguageBlock($this));
        $this->add_reporting_block(new MostActiveInactiveLastVisitBlock($this));
        $this->add_reporting_block(new MostActiveInactiveLastPublicationBlock($this));
        $this->add_reporting_block(new MostActiveInactiveLastDetailBlock($this));
        // $this->add_reporting_block(new NoOfObjectsPerTypeBlock($this));
        // $this->add_reporting_block(new NoOfPublishedObjectsPerTypeBlock($this));
        $this->add_reporting_block(new CoursesPerCategoryBlock($this));
        $this->add_reporting_block(new LastAccessToToolsPlatformBlock($this));
        $this->add_reporting_block(new NoOfUsersSubscribedCourseBlock($this));
    }
}
