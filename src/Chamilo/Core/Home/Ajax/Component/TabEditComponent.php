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
class TabEditComponent extends Manager
{
    public const PARAM_TAB = 'tab';
    public const PARAM_TITLE = 'title';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get('Chamilo\Core\Home\General')
            );

            $title = $this->getPostDataValue(self::PARAM_TITLE);

            $tab = $this->getHomeService()->findElementByIdentifier($this->getPostDataValue(self::PARAM_TAB));

            if ($tab->getUserId() == $homepageUserId)
            {
                $tab->setTitle($title);

                if ($this->getHomeService()->updateElement($tab))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::general_error($translator->trans('TabNotUpdated', [], Manager::CONTEXT));
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
        $postParameters[] = self::PARAM_TITLE;

        return parent::getRequiredPostParameters($postParameters);
    }
}
