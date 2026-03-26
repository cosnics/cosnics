<?php
namespace Chamilo\Core\Admin\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Domain\AbstractActionProvider;
use Chamilo\Core\Admin\Architecture\Enum\ActionEnum;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Actions;

/**
 * @package Chamilo\Core\Admin\Implementation\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionProvider extends AbstractActionProvider implements ActionProviderInterface
{
    public function getActions(): Actions
    {
        $context = $this->getContext();

        $links = [];

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::DIAGNOSE->value
        ];

        $links[] = new Action(
            $this->translator->trans('DiagnoseDescription', [], $context),
            $this->translator->trans('Diagnose', [], $context),
            new FontAwesomeGlyph('stethoscope', ['fa-fw', 'fa-2x'], null, 'fas'),
            $this->urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::VIEW_LOGS->value
        ];

        $links[] = new Action(
            $this->translator->trans('LogsViewerDescription', [], $context),
            $this->translator->trans('LogsViewer', [], $context),
            new FontAwesomeGlyph('info-circle', ['fa-fw', 'fa-2x'], null, 'fas'),
            $this->urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::VIEW_ONLINE->value
        ];

        $links[] = new Action(
            $this->translator->trans('WhoisOnline', [], StringUtilities::LIBRARIES),
            $this->translator->trans('WhoisOnline', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('user', ['fa-fw', 'fa-2x'], null, 'fas'),
            $this->urlGenerator->fromParameters($parameters)
        );

        return new Actions($context, $links);
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }
}