<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
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
    const PROPERTY_USE_GROUP = 'use_group';
    const PROPERTY_USE_GROUP_AND_TEAM = 'use_group_and_team';

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService
     */
    protected $courseGroupOffice365ReferenceService;

    /**
     * CourseGroupFormDecorator constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService
     */
    public function __construct(CourseGroupOffice365ReferenceService $courseGroupOffice365ReferenceService)
    {
        $this->courseGroupOffice365ReferenceService = $courseGroupOffice365ReferenceService;
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

        $courseGroupForm->addElement(
            'checkbox', self::PROPERTY_USE_GROUP . '[' . $id . ']',
            Translation::getInstance()->getTranslation('UseOffice365Group')
        );

        $courseGroupForm->addElement(
            'checkbox', self::PROPERTY_USE_GROUP_AND_TEAM . '[' . $id . ']',
            Translation::getInstance()->getTranslation('UseOffice365GroupAndTeam')
        );


        $defaults = [
            self::PROPERTY_USE_GROUP . '[' . $courseGroup->getId() . ']' =>
                $this->courseGroupOffice365ReferenceService->courseGroupHasLinkedReference($courseGroup)
        ];

        $courseGroupForm->setDefaults($defaults);
    }
}