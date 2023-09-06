<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\Metadata\Vocabulary\Table\User\UserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Vocabulary\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        if (! $this->getSelectedElementId())
        {
            throw new NoObjectSelectedException(Translation::get('Element', null, 'Chamilo\Core\Metadata\Element'));
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function as_html()
    {
        $table = new UserTable($this);
        
        $html = array();
        $html[] = $this->getButtonToolbarRenderer()->render();
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
            $buttonToolbar = new ButtonToolBar(
                $this->get_url(
                    array(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $this->getSelectedElementId())));
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the condition
     * 
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = array();
        
        $searchCondition = $this->buttonToolbarRenderer->getConditions(
            array(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), 
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME), 
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL), 
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE)));
        
        if ($searchCondition)
        {
            $conditions[] = $searchCondition;
        }
        
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Vocabulary::class_name(), Vocabulary::PROPERTY_ELEMENT_ID), 
            ComparisonCondition::EQUAL, 
            new StaticConditionVariable($this->getSelectedElementId()));
        
        return new AndCondition($conditions);
    }
}
