<?php

namespace Chamilo\Core\Notification\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\PriorityItem;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

/**
 * @package Chamilo\Core\Notification\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationWidgetItem extends PriorityItem
{
    function isItemSelected()
    {
        return false;
    }

    /**
     * @param bool $isSelected
     *
     * @param array $existingClasses
     *
     * @return array
     */
    protected function getClasses($isSelected = false, $existingClasses = [])
    {
        $existingClasses[] = 'chamilo-menu-item-priority';
        $existingClasses[] = 'dropdown';

        return parent::getClasses($isSelected, $existingClasses);
    }

    /**
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render()
    {
        if (!$this->canViewMenuItem($this->getMenuRenderer()->get_user()))
        {
            return '';
        }

        return $this->getTwig()->render(
            'Chamilo\Core\Notification\Integration\Chamilo\Core\Menu:NotificationWidgetItem.html.twig'
        );
    }

    /**
     * Returns whether or not the given user can view this menu item
     *
     * @param User $user
     *
     * @return bool
     */
    public function canViewMenuItem(User $user)
    {
        $authorizationChecker = $this->getAuthorizationChecker();

        return Application::is_active('Chamilo\Core\Notification') &&
            $authorizationChecker->isAuthorized($this->getMenuRenderer()->get_user(), 'Chamilo\Core\Notification');
    }
}