<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Admin\ImportActionProviderInterface;
use Chamilo\Core\Admin\Service\AbstractActionProvider;
use Chamilo\Core\Admin\Service\ActionProviderInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\Actions;

class AdminActionProvider extends AbstractActionProvider implements ActionProviderInterface
{

    public function getActions(): Actions
    {
        $translator = $this->getTranslator();
        $context = $this->getContext();
        $urlGenerator = $this->getUrlGenerator();

        $links = [];

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_COURSE_TYPE_MANAGER
        ];

        $links[] = new Action(
            $translator->trans('CourseTypeListDescription', [], $context),
            $translator->trans('CourseTypeList', [], $context),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_ADMIN_COURSE_MANAGER
        ];

        $links[] = new Action(
            $translator->trans('ListDescription', [], $context), $translator->trans('CourseList', [], $context),
            new FontAwesomeGlyph('chalkboard', ['fa-fw', 'fa-2x'], null, 'fas'),
            $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_ADMIN_COURSE_MANAGER,
            \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_CREATE
        ];

        $links[] = new Action(
            $translator->trans('CreateDescription', [], $context), $translator->trans('CreateCourse', [], $context),
            new FontAwesomeGlyph('plus', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_IMPORT_COURSES
        ];

        $links[] = new Action(
            $translator->trans('ImportDescription', [], $context), $translator->trans('Import', [], $context),
            new FontAwesomeGlyph('upload', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_ADMIN_REQUEST_BROWSER
        ];

        $links[] = new Action(
            $translator->trans('RequestDescription', [], $context), $translator->trans('RequestList', [], $context),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_COURSE_CATEGORY_MANAGER
        ];

        $links[] = new Action(
            $translator->trans('CourseCategoryManagementDescription', [], $context),
            $translator->trans('CourseCategoryManagement', [], $context),
            new FontAwesomeGlyph('folder', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_IMPORT_COURSE_USERS
        ];

        $links[] = new Action(
            $translator->trans('UserImportDescription', [], $context), $translator->trans('UserImport', [], $context),
            new FontAwesomeGlyph('upload', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_ADMIN_COURSE_MANAGER
        ];

        return new Actions($context, $links, $urlGenerator->fromParameters($parameters));
    }

    public function getContext(): string
    {
        return 'Chamilo\Application\Weblcms';
    }
}