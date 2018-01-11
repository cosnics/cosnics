<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\CourseUserExerciseInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment\CourseUserAssignmentInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath\CourseUserLearningPathInformationBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool\LastAccessToToolsUserBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\User\UserInformationBlock;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.reporting.templates
 */

/**
 *
 * @author Michael Kyndt
 */
class CourseStudentTrackerDetailTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);

        $this->add_reporting_block(new UserInformationBlock($this));
        $this->add_reporting_block(new CourseUserAssignmentInformationBlock($this));
        $this->add_reporting_block(new CourseUserExerciseInformationBlock($this));
        $this->add_reporting_block(new CourseUserLearningPathInformationBlock($this));
        $this->add_reporting_block(new LastAccessToToolsUserBlock($this));

        $params = [];
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] =
            CourseStudentTrackerTemplate::class_name();
        $params[\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID] = 0;

        $breadcrumbTrail = BreadcrumbTrail::getInstance();

        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url($params), Translation::getInstance()->getTranslation('UsersTrackingBlock')
            )
        );

        $user_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS, $user_id);

        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            (int) $user_id
        );

        if ($user)
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url([], [\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID]), $user->get_fullname()
                )
            );
        }

        $this->addCurrentBlockBreadcrumb();
    }
}
