<?php
namespace Chamilo\Core\Home\Rights\Ajax\Component;

use Chamilo\Core\Home\Renderer\BlockRendererFactory;
use Chamilo\Core\Home\Rights\Ajax\Manager;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Exception;
use RuntimeException;
use Throwable;

/**
 * Ajax request to set the element target entities for a specific element instance
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SetElementTargetEntitiesComponent extends Manager
{
    public const PARAM_ELEMENT_ID = 'elementId';
    public const PARAM_TARGET_ENTITIES = 'targetEntities';
    public const RESULT_PROPERTY_BLOCK = 'block';

    public function run()
    {
        try
        {
            $isGeneralMode = $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false);
            $homepageUser = $this->getHomeService()->determineUser(
                $this->getUser(), $isGeneralMode
            );

            if (!is_null($homepageUser) || ($homepageUser instanceof User && !$homepageUser->isPlatformAdmin()))
            {
                JsonAjaxResult::not_allowed();
            }

            try
            {
                $block =
                    $this->getHomeService()->getElementByIdentifier($this->getPostDataValue(self::PARAM_ELEMENT_ID));

                if (!$block instanceof Element || !$block->isBlock())
                {
                    throw new RuntimeException('Only blocks are allowed at this time');
                }

                $this->getElementRightsService()->setTargetEntitiesForElement(
                    $block, $this->getTargetEntitiesFromRequest()
                );

                $blockRenderer = $this->getBlockRendererFactory()->getRenderer($block);

                $result = new JsonAjaxResult(200);
                $result->set_property(
                    self::RESULT_PROPERTY_BLOCK, $blockRenderer->render($block, $isGeneralMode, $homepageUser)
                );

                $result->display();
            }
            catch (Exception $ex)
            {
                JsonAjaxResult::bad_request($ex->getMessage());
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

    public function getElementRightsService(): ElementRightsService
    {
        return $this->getService(ElementRightsService::class);
    }

    public function getHomeService(): HomeService
    {
        return $this->getService(HomeService::class);
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        $postParameters[] = self::PARAM_ELEMENT_ID;

        return parent::getRequiredPostParameters($postParameters);
    }

    protected function getTargetEntitiesFromRequest(): array
    {
        $results = [];
        $values = json_decode($this->getRequest()->getFromRequestOrQuery(self::PARAM_TARGET_ENTITIES));

        foreach ($values as $value)
        {
            $split_by_underscores = explode('_', $value);

            $id = array_pop($split_by_underscores);
            $type = implode('_', $split_by_underscores);

            $results[$type][] = $id;
        }

        return $results;
    }
}
