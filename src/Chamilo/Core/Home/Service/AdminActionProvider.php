<?php
namespace Chamilo\Core\Home\Service;

use Chamilo\Core\Admin\Service\AbstractActionProvider;
use Chamilo\Core\Admin\Service\ActionProviderInterface;
use Chamilo\Core\Home\Rights\Manager;
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

        $rightsContext = 'Chamilo\Core\Home\Rights';

        $parameters = [
            Application::PARAM_CONTEXT => $rightsContext,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES
        ];

        $links[] = new Action(

            $translator->trans('BrowseBlockTypeTargetEntitiesComponentDescription', [], $rightsContext),
            $translator->trans('BrowseBlockTypeTargetEntitiesComponent', [], $rightsContext),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        return new Actions($context, $links);
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\Home';
    }
}