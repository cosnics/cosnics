<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabSortComponent extends Manager
{
    public const PARAM_ORDER = 'order';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false)
            );

            parse_str($this->getPostDataValue(self::PARAM_ORDER), $tabs);

            $errors = 0;

            foreach ($tabs[self::PARAM_ORDER] as $sortOrder => $tabId)
            {
                $tab = $this->getHomeService()->findElementByIdentifier($tabId);

                if ($tab->getUserId() == $homepageUserId)
                {
                    $tab->setSort($sortOrder + 1);

                    if (!$this->getHomeService()->updateElement($tab))
                    {
                        $errors ++;
                    }
                }
                else
                {
                    $errors ++;
                }
            }

            if ($errors > 0)
            {
                JsonAjaxResult::error(409, $translator->trans('OneOrMoreTabsNotUpdated', [], Manager::CONTEXT));
            }
            else
            {
                JsonAjaxResult::success();
            }
        }
        catch (NotAllowedException $exception)
        {
            JsonAjaxResult::not_allowed($exception->getMessage());
        }
        catch (Throwable $throwable)
        {
            JsonAjaxResult::error(500, $throwable->getMessage());
        }
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        $postParameters[] = self::PARAM_ORDER;

        return parent::getRequiredPostParameters($postParameters);
    }
}
