<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
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
    const PROPERTY_USE_TEAM = 'use_team';

    const OPTION_NO_TEAM = 0;
    const OPTION_REGULAR_TEAM = 1;
    const OPTION_CLASS_TEAM = 2;

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
     *
     * @throws \Exception
     */
    public function decorateCourseGroupForm(FormValidator $courseGroupForm, CourseGroup $courseGroup)
    {
        $defaults = [];

        $id = $courseGroup->getId() ? $courseGroup->getId() : 0;
        $useTeamElementName = self::PROPERTY_USE_TEAM . '[' . $id . ']';

        $hasReference = $this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup);

        if ($hasReference)
        {
            $courseGroupForm->addElement(
                'checkbox', $useTeamElementName,
                Translation::getInstance()->getTranslation('UseOffice365Team')
            );

            $defaults[$useTeamElementName] = true;
        }
        else
        {
            $group = array();

            $group[] = &$courseGroupForm->createElement(
                'radio',
                $useTeamElementName,
                null,
                Translation::getInstance()->getTranslation('NoTeam'),
                self::OPTION_NO_TEAM
            );

            $group[] = &$courseGroupForm->createElement(
                'radio',
                $useTeamElementName,
                null,
                Translation::getInstance()->getTranslation('ClassTeam'),
                self::OPTION_CLASS_TEAM
            );

            $group[] = &$courseGroupForm->createElement(
                'radio',
                $useTeamElementName,
                null,
                Translation::getInstance()->getTranslation('RegularTeam'),
                self::OPTION_REGULAR_TEAM
            );

            $courseGroupForm->addGroup($group, null, Translation::getInstance()->getTranslation('UseOffice365Team'), '');

            $defaults[$useTeamElementName] = self::OPTION_NO_TEAM;
        }

        $courseGroupForm->setDefaults($defaults);
    }
}
