<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BlockEditComponent extends Manager
{
    public const PARAM_BLOCK = 'block';
    public const PARAM_TITLE = 'title';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false)
            );

            $block = $this->getHomeService()->findElementByIdentifier($this->getPostDataValue(self::PARAM_BLOCK));

            if (!$block instanceof Element || !$block->isBlock())
            {
                JsonAjaxResult::general_error($translator->trans('NoValidBlockSelected', [], Manager::CONTEXT));
            }

            if ($block->getUserId() == $homepageUserId)
            {
                $block->setTitle($this->getPostDataValue(self::PARAM_TITLE));

                if ($this->getHomeService()->updateElement($block))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::general_error($translator->trans('BlockNotUpdated', [], Manager::CONTEXT));
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
        $postParameters[] = self::PARAM_BLOCK;
        $postParameters[] = self::PARAM_TITLE;

        return parent::getRequiredPostParameters($postParameters);
    }
}
