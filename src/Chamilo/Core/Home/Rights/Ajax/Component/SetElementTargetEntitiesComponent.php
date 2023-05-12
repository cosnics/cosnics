<?php
namespace Chamilo\Core\Home\Rights\Ajax\Component;

use Chamilo\Core\Home\Renderer\Type\Basic\BlockRendererFactory;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Ajax\Manager;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;
use Exception;
use RuntimeException;

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

    /*
     * Returns the required post parameters
     * @return array
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_ELEMENT_ID);
    }

    /*
     * Executes this component and returns the json based result
     */
    public function run()
    {
        $userId = DataManager::determine_user_id();
        $generalMode = Session::retrieve('Chamilo\Core\Home\General');
        
        if ($userId === false || ! $generalMode || ! $this->getUser()->is_platform_admin() || $userId > 0)
        {
            JsonAjaxResult::not_allowed();
        }
        
        $elementId = intval($this->getPostDataValue(self::PARAM_ELEMENT_ID));
        $targetEntities = $this->getTargetEntitiesFromRequest();
        
        $elementRightsService = new ElementRightsService(new RightsRepository());
        $homeService = new HomeService(new HomeRepository(), $elementRightsService);
        $element = $homeService->getElementByIdentifier($elementId);
        
        try
        {
            $elementRightsService->setTargetEntitiesForElement($element, $targetEntities);
            
            if ($element->getType() != Block::class)
            {
                throw new RuntimeException('Only blocks are allowed at this time');
            }
            
            $blockRendererFactory = new BlockRendererFactory($this, $homeService, $element);
            $blockRenderer = $blockRendererFactory->getRenderer();
            
            $result = new JsonAjaxResult(200);
            $result->set_property(self::RESULT_PROPERTY_BLOCK, $blockRenderer->toHtml());
            $result->display();
        }
        catch (Exception $ex)
        {
            JsonAjaxResult::bad_request($ex->getMessage());
        }
    }

    /**
     * Retrieves and parses the target entities from the ajax request
     * 
     * @return array
     */
    protected function getTargetEntitiesFromRequest()
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
