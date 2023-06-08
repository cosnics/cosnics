<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package home.ajax
 * @author  Hans De Bisschop
 */
class TabDeleteComponent extends Manager
{
    public const PARAM_TAB = 'tab';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */

    public function run()
    {
        $userId = DataManager::determine_user_id();

        if ($userId === false)
        {
            JsonAjaxResult::not_allowed();
        }

        $tab = DataManager::retrieve_by_id(Tab::class, intval($this->getPostDataValue(self::PARAM_TAB)));

        if (!$tab instanceof Tab)
        {
            JsonAjaxResult::general_error(Translation::getInstance()->getTranslation('NoValidTabSelected'));
        }

        if ($tab->getUserId() == $userId && $this->getHomeService()->tabCanBeDeleted($tab))
        {
            if ($tab->delete())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::general_error(Translation::get('TabNotDeleted'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }

    public function getHomeService(): HomeService
    {
        return $this->getService(HomeService::class);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */

    public function getRequiredPostParameters(): array
    {
        return [self::PARAM_TAB];
    }
}
