<?php
namespace Chamilo\Core\Home\Rights\Component;

use Chamilo\Core\Home\Rights\Form\BlockTypeTargetEntitiesForm;
use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Exception;

/**
 * Sets the target entities for a block type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SetBlockTypeTargetEntitiesComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $blockType = $this->getBlockType();
        $blockTypeRightsService = $this->getBlockTypeRightsService();

        $form = new BlockTypeTargetEntitiesForm($this->get_url(), $blockType, $blockTypeRightsService);

        if ($form->validate())
        {
            try
            {
                $blockTypeRightsService->setTargetEntitiesForBlockType($blockType, $form->getTargetEntities());

                $message = 'BlockTypeTargetEntitiesSet';
                $success = true;
            }
            catch (Exception $ex)
            {
                $message = 'BlockTypeTargetEntitiesNotSet';
                $success = false;
            }

            $this->redirectWithMessage(
                $this->getTranslator()->trans($message, [], Manager::CONTEXT), !$success,
                [self::PARAM_ACTION => self::ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BLOCK_TYPE;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * Returns the block type from the request
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    protected function getBlockType()
    {
        $blockType = $this->getRequest()->getFromRequestOrQuery(self::PARAM_BLOCK_TYPE);

        if (!$blockType)
        {
            throw new NoObjectSelectedException(
                $this->getTranslator()->trans('BlockType', [], Manager::CONTEXT)
            );
        }

        return $blockType;
    }

    protected function getBlockTypeRightsService(): BlockTypeRightsService
    {
        return $this->getService(BlockTypeRightsService::class);
    }
}