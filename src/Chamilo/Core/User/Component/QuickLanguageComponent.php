<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package user.lib.user_manager.component
 */
class QuickLanguageComponent extends Manager
{

    private function isAllowedToChangeLanguage()
    {
        return PlatformSetting :: get('allow_user_change_platform_language', self :: package()) == 1 &&
             PlatformSetting :: get('allow_user_quick_change_platform_language', self :: package()) == 1;
    }

    private function getLanguages()
    {
        return \Chamilo\Configuration\Configuration :: get_instance()->getLanguages();
    }

    /**
     * Runs this component and redirect to the original location afterwards.
     * If the allow_user_quick_lang platform
     * setting is set, overwrite the language setting (depending on the request parameters).
     */
    public function run()
    {
        if ($this->isAllowedToChangeLanguage())
        {
            $choice = Request :: get(self :: PARAM_CHOICE);
            $languages = array_keys($this->getLanguages());

            if ($choice && in_array($choice, $languages))
            {
                LocalSetting :: getInstance()->create('platform_language', $choice);
            }
        }

        $response = new RedirectResponse(Request :: get(self :: PARAM_REFER));
        $response->send();
    }
}
