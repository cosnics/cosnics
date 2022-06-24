<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Admin\Service\AbstractActionProvider;
use Chamilo\Core\Admin\Service\ActionProviderInterface;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\Actions;
use Chamilo\Libraries\Utilities\StringUtilities;

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
            Application::PARAM_ACTION => Manager::ACTION_BROWSE_GROUPS
        ];

        $links[] = new Action(
            $translator->trans('ListDescription', [], $context),
            $translator->trans('List', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_CREATE_GROUP,
            Manager::PARAM_GROUP_ID => 0
        ];

        $links[] = new Action(
            $translator->trans('CreateDescription', [], $context),
            $translator->trans('Create', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('plus', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_EXPORT
        ];

        $links[] = new Action(
            $translator->trans('ExportDescription', [], $context),
            $translator->trans('Export', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('download', ['fa-fw', 'fa-2x'], null, 'fas'),
            $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_IMPORT
        ];

        $links[] = new Action(
            $translator->trans('ImportDescription', [], $context),
            $translator->trans('Import', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('upload', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_IMPORT_GROUP_USERS
        ];

        $links[] = new Action(
            $translator->trans('ImportGroupUsersDescription', [], $context),
            $translator->trans('ImportGroupUsers', [], $context),
            new FontAwesomeGlyph('upload', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE_GROUPS
        ];

        return new Actions($context, $links, $urlGenerator->fromParameters($parameters));
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\Group';
    }
}