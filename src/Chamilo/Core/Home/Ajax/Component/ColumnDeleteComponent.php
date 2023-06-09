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
class ColumnDeleteComponent extends Manager
{
    public const PARAM_COLUMN = 'column';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get('Chamilo\Core\Home\General')
            );

            $column = $this->getHomeService()->findElementByIdentifier($this->getPostDataValue(self::PARAM_COLUMN));

            if (!$column instanceof Element || !$column->isColumn())
            {
                JsonAjaxResult::general_error($translator->trans('NoValidColumnSelected', [], Manager::CONTEXT));
            }

            if ($column->getUserId() == $homepageUserId)
            {
                if ($this->getHomeService()->deleteElement($column))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::general_error($translator->trans('ColumnNotDeleted', [], Manager::CONTEXT));
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

        return parent::getRequiredPostParameters($postParameters);
    }
}
