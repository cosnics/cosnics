<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 * Decorates the CourseGroup form with additional items
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupFormDecorator implements CourseGroupFormDecoratorInterface
{
    const PROPERTY_FORUM_CATEGORY_ID = 'forum_category_id';

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService
     */
    protected $courseGroupPublicationCategoryService;

    /**
     * CourseGroupServiceDecorator constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService $courseGroupPublicationCategoryService
     */
    public function __construct(
        CourseGroupPublicationCategoryService $courseGroupPublicationCategoryService
    )
    {
        $this->courseGroupPublicationCategoryService = $courseGroupPublicationCategoryService;
    }

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
            'checkbox', self::PROPERTY_FORUM_CATEGORY_ID . '[' . $id . ']',
            Translation::getInstance()->getTranslation('Forum')
        );

        $defaults = [
            self::PROPERTY_FORUM_CATEGORY_ID . '[' . $courseGroup->getId() . ']' =>
                $this->courseGroupPublicationCategoryService->courseGroupHasPublicationCategories($courseGroup, 'Forum')
        ];

        $courseGroupForm->setDefaults($defaults);
    }
}