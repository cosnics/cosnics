<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupActionsDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupActionsDecorator implements CourseGroupActionsDecoratorInterface
{
    /**
     * @var \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService
     */
    protected $courseGroupPublicationCategoryService;

    /**
     * CourseGroupActionsDecorator constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator $urlGenerator
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService $courseGroupPublicationCategoryService
     */
    public function __construct(
        UrlGenerator $urlGenerator, Translator $translator,
        CourseGroupPublicationCategoryService $courseGroupPublicationCategoryService
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->courseGroupPublicationCategoryService = $courseGroupPublicationCategoryService;
    }

    /**
     * Adds actions to the toolbar of integration actions
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar $courseGroupActionsToolbar
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param bool $isCourseTeacher
     */
    public function addCourseGroupActions(
        ButtonToolBar $courseGroupActionsToolbar, CourseGroup $courseGroup, User $user, $isCourseTeacher = false
    )
    {
        if(!$this->courseGroupPublicationCategoryService->courseGroupHasPublicationCategories($courseGroup, 'Document'))
        {
            return;
        }

        $publicationCategories = $this->courseGroupPublicationCategoryService->getPublicationCategoriesForCourseGroup(
            $courseGroup, 'Document'
        );

        $visitDocumentUrl = $this->urlGenerator->fromRequest(
            [
                Manager::PARAM_TOOL => 'Document',
                Manager::PARAM_TOOL_ACTION =>
                    \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager::ACTION_BROWSE,
                Manager::PARAM_CATEGORY => $publicationCategories[0]->getId()
            ]
        );

        $visitDocumentLabel = $this->translator->trans(
            'VisitDocumentCategory', [], 'Chamilo\Application\Weblcms\Tool\Implementation\Document'
        );

        $visitDocumentButton = new Button(
            $visitDocumentLabel, null, $visitDocumentUrl,
            Button::DISPLAY_ICON_AND_LABEL, null, [], '_blank'
        );

        $courseGroupActionsToolbar->addItem($visitDocumentButton);
    }
}
