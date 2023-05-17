<?php
namespace Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Core\Menu\Renderer\ItemRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LanguageItemRenderer extends ItemRenderer
{

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\LanguageItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_QUICK_LANG,
                Manager::PARAM_CHOICE => $item->getIsocode(),
                Manager::PARAM_REFER => $item->getCurrentUrl()
            ]
        );

        $html = [];

        $html[] = '<li>';
        $html[] = '<a href="' . $redirect->getUrl() . '">';
        $html[] = '<div>';
        $html[] = $this->renderTitle($item);
        $html[] = '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function isItemVisibleForUser(User $user)
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\User', 'ChangeLanguage');
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\LanguageItem $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return $item->getLanguage();
    }
}