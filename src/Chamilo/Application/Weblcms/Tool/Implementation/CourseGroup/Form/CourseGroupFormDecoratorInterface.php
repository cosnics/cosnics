<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * Decorates the CourseGroup form with additional items
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseGroupFormDecoratorInterface
{
    /**
     * Decorates the course group form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $courseGroupForm
     */
    public function decorateCourseGroupForm(FormValidator $courseGroupForm);
}