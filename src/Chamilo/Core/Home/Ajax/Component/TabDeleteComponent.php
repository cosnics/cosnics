<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabDeleteComponent extends Manager
{
    public const PARAM_TAB = 'tab';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get('Chamilo\Core\Home\General')
            );

            $tab = $this->getHomeService()->findElementByIdentifier($this->getPostDataValue(self::PARAM_TAB));

            if (!$tab instanceof Element || !$tab->isTab())
            {
                JsonAjaxResult::general_error($translator->trans('NoValidTabSelected', [], Manager::CONTEXT));
            }

            if ($tab->getUserId() == $homepageUserId && $this->getHomeService()->tabCanBeDeleted($tab))
            {
                if ($this->getHomeService()->deleteElement($tab))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::general_error($translator->trans('TabNotDeleted', [], Manager::CONTEXT));
                }
            }
            else
            {
                JsonAjaxResult::not_allowed();
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
        $postParameters[] = self::PARAM_TAB;

        return parent::getRequiredPostParameters($postParameters);
    }
}
