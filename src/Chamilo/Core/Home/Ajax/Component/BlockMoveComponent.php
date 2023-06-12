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
class BlockMoveComponent extends Manager
{
    public const PARAM_BLOCK = 'block';
    public const PARAM_COLUMN = 'column';

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
                $block->setParentId($this->getPostDataValue(self::PARAM_COLUMN));

                if ($this->getHomeService()->updateElement($block))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::general_error($translator->trans('BlockNotMovedToTab', [], Manager::CONTEXT));
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
        $postParameters[] = self::PARAM_COLUMN;
        $postParameters[] = self::PARAM_BLOCK;

        return parent::getRequiredPostParameters($postParameters);
    }
}
