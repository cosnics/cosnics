<?php

namespace Chamilo\Core\Home\Rights\Component;

use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Form\BlockTypeTargetEntitiesForm;
use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;

/**
 * Sets the target entities for a block type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SetBlockTypeTargetEntitiesComponent extends Manager
{
    /**
     * Executes this component and renders it's output
     */
    public function run()
    {
        if (! $this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $blockType = $this->getBlockType();
        $blockTypeRightsService = new BlockTypeRightsService(new RightsRepository(), new HomeRepository());

        $form = new BlockTypeTargetEntitiesForm($this->get_url(), $blockType, $blockTypeRightsService);

        if ($form->validate())
        {
            try
            {
                $blockTypeRightsService->setTargetEntitiesForBlockType(
                    $blockType, $form->getTargetEntities()
                );

                $message = 'BlockTypeTargetEntitiesSet';
                $success = true;

            }
            catch (\Exception $ex)
            {
                $message = 'BlockTypeTargetEntitiesNotSet';
                $success = false;
            }

            $this->redirect(
                Translation::getInstance()->getTranslation($message, null, Manager::context()), !$success,
                array(self::PARAM_ACTION => self::ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES)
            );
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Returns the block type from the request
     */
    protected function getBlockType()
    {
        $blockType = $this->getRequest()->get(self::PARAM_BLOCK_TYPE);

        if (!$blockType)
        {
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation(
                    'BlockType', null, Manager::context()
                )
            );
        }

        return $blockType;
    }

    /**
     * Registers these parameters from the request
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_BLOCK_TYPE);
    }
}