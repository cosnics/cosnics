<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LanguageCategoryItem extends CategoryItem
{

    private function isQuickLanguageChangeAllowed()
    {
        return true;

        return PlatformSetting:: get('allow_user_change_platform_language', \Chamilo\Core\User\Manager:: context()) ==
        1 && PlatformSetting:: get(
            'allow_user_quick_change_platform_language',
            \Chamilo\Core\User\Manager:: context()
        ) == 1;
    }

    public function render()
    {
        if (!$this->canViewMenuItem($this->getMenuRenderer()->get_user()))
        {
            return '';
        }

        $html = array();

        $sub_html = array();

        $languages = \Chamilo\Configuration\Configuration:: get_instance()->getLanguages();

        if (count($languages) > 1)
        {
            $redirect = new Redirect();
            $currentUrl = $redirect->getCurrentUrl();

            $sub_html[] = '<ul class="dropdown-menu language-selector">';

            $currentLanguage = LocalSetting:: getInstance()->get('platform_language');

            foreach ($languages as $isocode => $language)
            {
                $redirect = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager:: context(),
                        Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_QUICK_LANG,
                        \Chamilo\Core\User\Manager :: PARAM_CHOICE => $isocode,
                        \Chamilo\Core\User\Manager :: PARAM_REFER => $currentUrl
                    )
                );

                $languageItem = new \Chamilo\Core\Menu\Storage\DataClass\LanguageItem();
                $languageItem->set_language($isocode);
                $languageItem->setCurrentUrl($currentUrl);
                $languageItem->set_parent($this->getItem()->get_id());

                if ($currentLanguage != $isocode)
                {
                    $sub_html[] = Renderer:: toHtml($this->getMenuRenderer(), $languageItem, $this);
                }
            }

            $sub_html[] = '</ul>';
            $sub_html[] = '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';
        }

        $html[] = '<li class="dropdown">';

        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
        $html[] = '<div class="chamilo-menu-item-label">';
        $html[] = $currentLanguage;
        $html[] = '<span class="caret"></span>';
        $html[] = '</div>';
        $html[] = '</a>';

        $html[] = implode(PHP_EOL, $sub_html);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
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

        return $this->isQuickLanguageChangeAllowed() && $authorizationChecker->isAuthorized(
            $this->getMenuRenderer()->get_user(), 'Chamilo\Core\User', 'ChangeLanguage'
        );
    }
}
