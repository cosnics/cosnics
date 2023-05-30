<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Application\Weblcms\Tool\Implementation\User;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Interfaces\UserListActionsExtenderInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extends actions for the weblcms user list
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserListActionsExtender implements UserListActionsExtenderInterface
{
    public function getActions(Toolbar $toolbar, string $currentUserId)
    {
        $parameters = [];

        $parameters[Application::PARAM_CONTEXT] = Manager::CONTEXT;
        $parameters[Application::PARAM_ACTION] = Manager::ACTION_HOME;
        $parameters[Manager::PARAM_USER_ID] = $currentUserId;

        $homeUrl = $this->getUrlGenerator()->fromParameters($parameters);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::getInstance()->getTranslation('ViewPortfolio', [], __NAMESPACE__),
                new NamespaceIdentGlyph('Chamilo\Application\Portfolio'), $homeUrl, ToolbarItem::DISPLAY_ICON, false,
                null, '_blank'
            )
        );
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }
}