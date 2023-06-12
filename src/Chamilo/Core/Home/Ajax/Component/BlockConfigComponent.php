<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Renderer\BlockRendererFactory;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Throwable;

/**
 * @package home.ajax
 * @author  Hans De Bisschop
 */
class BlockConfigComponent extends Manager
{
    public const PARAM_BLOCK = 'block';
    public const PARAM_DATA = 'data';
    public const PROPERTY_BLOCK = 'block';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();

            $isGeneralMode = $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false);
            $homepageUser = $this->getHomeService()->determineUser(
                $this->getUser(), $isGeneralMode
            );
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $isGeneralMode
            );

            $block = $this->getHomeService()->findElementByIdentifier($this->getPostDataValue(self::PARAM_BLOCK));

            if (!$block instanceof Element || !$block->isBlock())
            {
                JsonAjaxResult::general_error($translator->trans('NoValidBlockSelected', [], Manager::CONTEXT));
            }

            if ($block->getUserId() == $homepageUserId)
            {
                $postedValues = $this->getPostDataValue(self::PARAM_DATA);

                $blockRenderer = $this->getBlockRendererFactory()->getRenderer($block);

                if ($blockRenderer instanceof ConfigurableBlockInterface ||
                    $blockRenderer instanceof ContentObjectPublicationBlockInterface)
                {
                    if ($blockRenderer instanceof ConfigurableBlockInterface)
                    {
                        foreach ($blockRenderer->getConfigurationVariables() as $configurationVariable)
                        {
                            $block->setSetting($configurationVariable, $postedValues[$configurationVariable]);
                        }
                    }

                    if ($blockRenderer instanceof ContentObjectPublicationBlockInterface)
                    {
                        foreach ($blockRenderer->getContentObjectConfigurationVariables() as $configurationVariable)
                        {
                            $this->getContentObjectPublicationService()->setOnlyContentObjectForElement(
                                $block, $postedValues[$configurationVariable]
                            );
                        }
                    }

                    if (isset($postedValues[Element::PROPERTY_TITLE]))
                    {
                        $block->setTitle($postedValues[Element::PROPERTY_TITLE]);
                    }

                    if (!$this->getHomeService()->updateElement($block))
                    {
                        JsonAjaxResult::general_error();
                    }
                    else
                    {

                        $result = new JsonAjaxResult(200);
                        $result->set_property(
                            self::PROPERTY_BLOCK, $blockRenderer->render($block, $isGeneralMode, $homepageUser)
                        );
                        $result->display();
                    }
                }
                else
                {
                    JsonAjaxResult::bad_request();
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

    public function getBlockRendererFactory(): BlockRendererFactory
    {
        return $this->getService(BlockRendererFactory::class);
    }

    public function getContentObjectPublicationService(): ContentObjectPublicationService
    {
        return $this->getService(ContentObjectPublicationService::class);
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        $postParameters[] = self::PARAM_BLOCK;
        $postParameters[] = self::PARAM_DATA;

        return parent::getRequiredPostParameters($postParameters);
    }
}
