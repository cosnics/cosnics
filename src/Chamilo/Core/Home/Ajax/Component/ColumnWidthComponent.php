<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ColumnWidthComponent extends Manager
{
    public const PARAM_COLUMN = 'column';
    public const PARAM_WIDTH = 'width';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get('Chamilo\Core\Home\General')
            );

            $columnId = $this->getPostDataValue(self::PARAM_COLUMN);
            $columnWidth = $this->getPostDataValue(self::PARAM_WIDTH);

            $column = $this->getHomeService()->findElementByIdentifier($columnId);

            if ($column->getUserId() == $homepageUserId)
            {
                $column->setWidth((int) $columnWidth);

                if ($this->getHomeService()->updateElement($column))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::error(409, $translator->trans('ColumnNotUpdated', [], Manager::CONTEXT));
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
        $postParameters[] = self::PARAM_WIDTH;

        return parent::getRequiredPostParameters($postParameters);
    }
}
