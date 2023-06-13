<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Renderer\BlockRendererFactory;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BlockAddComponent extends Manager
{
    public const PARAM_BLOCK = 'block';
    public const PARAM_COLUMN = 'column';
    public const PARAM_ORDER = 'order';

    public const PROPERTY_BLOCK = 'block';

    public function run()
    {
        try
        {
            $classnameUtilities = $this->getClassnameUtilities();

            $isGeneralMode = $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false);
            $homepageUser = $this->getHomeService()->determineUser(
                $this->getUser(), $isGeneralMode
            );

            $homepageUserId = $homepageUser instanceof User ? $homepageUser->getId() : 0;

            $columnId = $this->getPostDataValue(self::PARAM_COLUMN);
            $blockType = $this->getPostDataValue(self::PARAM_BLOCK);
            $blockName = $classnameUtilities->getClassnameFromNamespace($blockType);

            $block = new Element();
            $block->setType(Element::TYPE_BLOCK);
            $block->setParentId($columnId);
            $block->setTitle($this->getTranslator()->trans($blockName, [], $blockType::CONTEXT));
            $block->setContext($blockType::CONTEXT);
            $block->setBlockType($blockType);
            $block->setVisibility(true);
            $block->setUserId($homepageUserId);

            if ($this->getHomeService()->createElement($block))
            {
                if ($this->getHomeService()->updateElement($block))
                {
                    $blockRenderer = $this->getBlockRendererFactory()->getRenderer($block);

                    $result = new JsonAjaxResult(200);
                    $result->set_property(
                        self::PROPERTY_BLOCK, $blockRenderer->render($block, $isGeneralMode, $homepageUser)
                    );
                    $result->display();
                }
                else
                {
                    JsonAjaxResult::error(500);
                }
            }
            else
            {
                JsonAjaxResult::error(500);
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

    public function getBlockRendererFactory(): BlockRendererFactory
    {
        return $this->getService(BlockRendererFactory::class);
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        $postParameters[] = self::PARAM_BLOCK;
        $postParameters[] = self::PARAM_COLUMN;

        return parent::getRequiredPostParameters($postParameters);
    }
}
