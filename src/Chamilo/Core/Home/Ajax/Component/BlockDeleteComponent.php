<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BlockDeleteComponent extends Manager
{
    public const PARAM_BLOCK = 'block';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();

            $isGeneralMode = $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false);
            $homepageUser = $this->getHomeService()->determineUser(
                $this->getUser(), $isGeneralMode
            );

            $blockId = $this->getPostDataValue(self::PARAM_BLOCK);

            $block = $this->getHomeService()->findElementByIdentifier($blockId);

            if (!$block instanceof Element || !$block->isBlock())
            {
                throw new ObjectNotExistException($translator->trans('Block', [], Manager::CONTEXT), $blockId);
            }

            if ($block->getUserId() == $homepageUser->getId())
            {
                if ($this->getHomeService()->deleteElement($block))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::error(409, $translator->trans('BlockNotDeleted', [], Manager::CONTEXT));
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

        return parent::getRequiredPostParameters($postParameters);
    }
}
