<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Component;

use Chamilo\Core\Metadata\Relation\Instance\Manager;
use Chamilo\Core\Metadata\Relation\Instance\Table\Relation\RelationTable;
use Chamilo\Core\Metadata\Service\EntityConditionService;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $this->verifySetup();
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = array();
        
        $html[] = $this->buttonToolbarRenderer->render();
        
        $table = new RelationTable($this);
        $html[] = $table->as_html();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     * 
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            
            $commonActions->addButton(
                new Button(
                    Translation::get('Create', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Create'), 
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE))));
            
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the condition
     * 
     * @param string $table_class_name
     *
     * @return Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $entityConditionService = new EntityConditionService();
        $conditions = array();
        
        $relations = $this->getRelations();
        
        if (count($relations) > 0)
        {
            $relationIdentifiers = array();
            
            foreach ($relations as $relation)
            {
                $relationIdentifiers[] = $relation->get_id();
            }
            
            $conditions[] = new InCondition(
                new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_RELATION_ID), 
                $relationIdentifiers);
        }
        
        $sourceEntities = $this->getSourceEntities();
        
        if (count($sourceEntities) > 0)
        {
            $conditions[] = $entityConditionService->getEntitiesCondition(
                $sourceEntities, 
                RelationInstance::class_name(), 
                RelationInstance::PROPERTY_SOURCE_TYPE, 
                RelationInstance::PROPERTY_SOURCE_ID);
        }
        
        $targetEntities = $this->getTargetEntities();
        
        if (count($targetEntities) > 0)
        {
            $conditions[] = $entityConditionService->getEntitiesCondition(
                $targetEntities, 
                RelationInstance::class_name(), 
                RelationInstance::PROPERTY_TARGET_TYPE, 
                RelationInstance::PROPERTY_TARGET_ID);
        }
        
        return new AndCondition($conditions);
    }
}
