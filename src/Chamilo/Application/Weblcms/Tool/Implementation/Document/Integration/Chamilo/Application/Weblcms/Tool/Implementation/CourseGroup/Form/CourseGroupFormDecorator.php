<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface;
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
    const PROPERTY_DOCUMENT_CATEGORY_ID = 'document_category_id';

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
        \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService $courseGroupPublicationCategoryService
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

        // Creation form or editing form without linked document category
        $courseGroupForm->addElement(
            'checkbox', CourseGroup::PROPERTY_DOCUMENT_CATEGORY_ID . '[' . $id . ']',
            Translation::getInstance()->getTranslation('Document')
        );

        $defaults = [
            CourseGroup::PROPERTY_DOCUMENT_CATEGORY_ID . '[' . $id . ']' =>
                $this->courseGroupPublicationCategoryService->courseGroupHasPublicationCategories(
                    $courseGroup, 'Document'
                )
        ];

        $courseGroupForm->setDefaults($defaults);
    }
}