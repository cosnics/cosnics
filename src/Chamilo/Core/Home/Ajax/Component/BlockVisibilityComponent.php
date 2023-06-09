<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockVisibilityComponent extends Manager
{
    public const PARAM_BLOCK = 'block';
    public const PARAM_VISIBILITY = 'visibility';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();

            $isGeneralMode = $this->getSession()->get('Chamilo\Core\Home\General');
            $homepageUser = $this->getHomeService()->determineUser(
                $this->getUser(), $isGeneralMode
            );

            $blockId = $this->getPostDataValue(self::PARAM_BLOCK);

            $block = $this->getHomeService()->findElementByIdentifier($blockId);

            if ($block->getUserId() == $homepageUser->getId())
            {
                $block->setVisibility(!($this->getPostDataValue(self::PARAM_VISIBILITY) == 'false'));

                if ($this->getHomeService()->updateElement($block))
                {
                    JsonAjaxResult::success();
                }
                else
                {
                    JsonAjaxResult::error(409, $translator->trans('BlockNotUpdated', [], Manager::CONTEXT));
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
        $postParameters[] = self::PARAM_VISIBILITY;

        return parent::getRequiredPostParameters($postParameters);
    }
}
