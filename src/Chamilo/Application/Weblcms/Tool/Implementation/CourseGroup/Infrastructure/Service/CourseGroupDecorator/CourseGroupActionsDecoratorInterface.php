<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;

/**
 * Decorates the CourseGroup integrations toolbar with additional actions
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseGroupActionsDecoratorInterface
{
    /**
     * Adds actions to the toolbar of integration actions
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar $courseGroupActionsToolbar
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    public function addCourseGroupActions(ButtonToolBar $courseGroupActionsToolbar, CourseGroup $courseGroup);
}