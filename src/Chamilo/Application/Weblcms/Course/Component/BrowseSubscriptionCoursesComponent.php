<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Manager;

/**
 * Abstract class that extends the browse component and is used for the subcribed / unsubscribed browser
 *
 * @package \application\weblcms\course
 * @author  Yannick & Tristan
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
abstract class BrowseSubscriptionCoursesComponent extends BrowseComponent
{

    protected function checkComponentAuthorization()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManagePersonalCourses');
    }
}
