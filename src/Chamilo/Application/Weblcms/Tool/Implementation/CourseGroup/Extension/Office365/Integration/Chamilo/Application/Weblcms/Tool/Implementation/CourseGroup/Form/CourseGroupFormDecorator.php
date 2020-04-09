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
     *
     * @throws \Exception
     */
    public function decorateCourseGroupForm(FormValidator $courseGroupForm, CourseGroup $courseGroup)
    {
        $id = $courseGroup->getId() ? $courseGroup->getId() : 0;

        $courseGroupForm->addElement(
            'checkbox', self::PROPERTY_USE_GROUP . '[' . $id . ']',
            Translation::getInstance()->getTranslation('UseOffice365Group')
        );

        /*$courseGroupForm->addElement(
            'checkbox', self::PROPERTY_USE_GROUP_AND_TEAM . '[' . $id . ']',
            Translation::getInstance()->getTranslation('UseOffice365GroupAndTeam')
        );

        $courseGroupForm->addElement(
            'html',
            ResourceManager::getInstance()->getResourceHtml(
                Path::getInstance()->getJavascriptPath('Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup', true) .
                'TeamAndGroupFormSelection.js'));*/

        $defaults = [];
        $office365Reference = $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup);
        if($office365Reference){
            if($office365Reference->isLinked()) {
                $defaults[self::PROPERTY_USE_GROUP . '[' . $courseGroup->getId() . ']'] = true;
            }

            if($office365Reference->hasTeam()) {
              $defaults[self::PROPERTY_USE_GROUP_AND_TEAM . '[' . $courseGroup->getId() . ']' ] = true;
            }
        }

        $courseGroupForm->setDefaults($defaults);
    }
}