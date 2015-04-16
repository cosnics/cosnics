<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Core\Metadata\Vocabulary\Table\User\UserTable;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Metadata\Vocabulary\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserComponent extends Manager implements TableSupport
{

    private $action_bar;

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
            throw new NoObjectSelectedException(Translation :: get('Element', null, 'Chamilo\Core\Metadata\Element'));
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
        $html[] = $this->get_action_bar()->as_html();
        $html[] = $table->as_html();
        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     * 
     * @return ActionBarRenderer
     */
    protected function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $this->action_bar->set_search_url(
                $this->get_url(
                    array(\Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID => $this->getSelectedElementId())));
        }
        
        return $this->action_bar;
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
        
        $searchCondition = $this->get_action_bar()->get_conditions(
            array(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME), 
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME), 
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EMAIL), 
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE)));
        
        if ($searchCondition)
        {
            $conditions[] = $searchCondition;
        }
        
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_ELEMENT_ID), 
            ComparisonCondition :: EQUAL, 
            new StaticConditionVariable($this->getSelectedElementId()));
        
        return new AndCondition($conditions);
    }
}
