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
class BlockSortComponent extends Manager
{
    public const PARAM_COLUMN = 'column';
    public const PARAM_ORDER = 'order';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get('Chamilo\Core\Home\General')
            );

            parse_str($this->getPostDataValue(self::PARAM_ORDER), $blocks);

            $column = $this->getHomeService()->findElementByIdentifier($this->getPostDataValue(self::PARAM_COLUMN));

            if (!$column instanceof Element || !$column->isColumn())
            {
                JsonAjaxResult::general_error($translator->trans('NoValidBlockSelected', [], Manager::CONTEXT));
            }

            if ($column->getUserId() == $homepageUserId)
            {
                $errors = 0;

                foreach ($blocks[self::PARAM_ORDER] as $sortOrder => $blockId)
                {
                    $block = $this->getHomeService()->findElementByIdentifier($blockId);

                    if ($block instanceof Element && $block->isBlock() && $block->getUserId() == $homepageUserId)
                    {
                        $block->setParentId($column->getId());
                        $block->setSort($sortOrder + 1);

                        if (!$this->getHomeService()->updateElement($block))
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
                    JsonAjaxResult::error(409, $translator->trans('OneOrMoreBlocksNotUpdated'));
                }
                else
                {
                    JsonAjaxResult::success();
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
        $postParameters[] = self::PARAM_ORDER;

        return parent::getRequiredPostParameters($postParameters);
    }
}
