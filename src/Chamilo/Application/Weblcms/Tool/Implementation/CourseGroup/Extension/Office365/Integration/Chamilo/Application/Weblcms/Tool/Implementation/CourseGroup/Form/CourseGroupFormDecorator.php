<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 * Decorates the CourseGroup form with additional items
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupFormDecorator implements CourseGroupFormDecoratorInterface
{
    const PROPERTY_USE_PLANNER = 'use_planner';

    /**
     * Decorates the course group form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $courseGroupForm
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    public function decorateCourseGroupForm(FormValidator $courseGroupForm, CourseGroup $courseGroup)
    {
        $id = $courseGroup->getId() ? $courseGroup->getId() : 0;

        // Creation form or editing form without linked forum category
        $courseGroupForm->addElement(
            'checkbox', self::PROPERTY_USE_PLANNER . '[' . $id . ']',
            Translation::getInstance()->getTranslation('UsePlanner')
        );

        $defaults = [
            self::PROPERTY_USE_PLANNER . '[' . $courseGroup->getId() . ']' => false
        ];

        $courseGroupForm->setDefaults($defaults);
    }
}