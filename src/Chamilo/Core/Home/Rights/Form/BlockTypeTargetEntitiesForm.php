<?php
namespace Chamilo\Core\Home\Rights\Form;

use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Libraries\Translation\Translation;

/**
 * Target Entities Form
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeTargetEntitiesForm extends TargetEntitiesForm
{
    const PROPERTY_BLOCK_TYPE = 'block_type';

    /**
     *
     * @var BlockTypeRightsService
     */
    protected $blockTypeRightsService;

    /**
     * The current element
     * 
     * @var string
     */
    protected $blockType;

    /**
     * Constructor
     * TargetEntitiesForm constructor.
     * 
     * @param string $action
     * @param string $blockType
     * @param BlockTypeRightsService $blockTypeRightsService
     */
    public function __construct($action, $blockType, BlockTypeRightsService $blockTypeRightsService)
    {
        $this->blockType = $blockType;
        $this->blockTypeRightsService = $blockTypeRightsService;
        
        parent::__construct('block_type_target_entities_form', $action);
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $this->addElement(
            'static', 
            self::PROPERTY_BLOCK_TYPE, 
            Translation::getInstance()->getTranslation('BlockType', null, Manager::context()), 
            $this->blockType);
        
        parent::buildForm();
    }

    /**
     * Returns the selected entities
     * 
     * @return HomeTargetEntity[]
     */
    protected function getSelectedEntities()
    {
        return $this->blockTypeRightsService->getTargetEntitiesForBlockType($this->blockType);
    }
}