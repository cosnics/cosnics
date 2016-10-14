<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home;

class Manager
{

    public function getBlockTypes()
    {
        return array(
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\AssignmentSubmissions',
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\CourseList',
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\CourseMenu',
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\EndingAssignments',
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\FilteredCourseList',
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\NewAnnouncements',
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\NewAssignments',
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\NewDocuments'
//            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\WeblcmsBookmarkDisplay'
        );
    }
}