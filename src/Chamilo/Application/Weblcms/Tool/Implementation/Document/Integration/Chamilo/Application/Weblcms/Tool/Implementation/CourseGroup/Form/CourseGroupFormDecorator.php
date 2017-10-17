<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 * Decorates the CourseGroup form with additional items
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupFormDecorator implements CourseGroupFormDecoratorInterface
{

    /**
     * Decorates the course group form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $courseGroupForm
     */
    public function decorateCourseGroupForm(FormValidator $courseGroupForm)
    {
        // Creation form or editing form without linked document category
        $courseGroupForm->addElement(
            'checkbox', CourseGroup::PROPERTY_DOCUMENT_CATEGORY_ID, Translation::get('Document')
        );
    }
}