<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\Table\WikiPage\WikiPageTable;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WikiBrowserComponent extends Manager implements DelegateComponent, TableSupport
{

    private $owner;

    public function run()
    {
        $this->owner = $this->get_root_content_object()->get_id();

        if ($this->get_root_content_object() != null)
        {
            $html = array();

            $html[] = $this->render_header();
            $table = new WikiPageTable($this);
            $html[] = $table->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }

    public function get_condition()
    {
        // search condition
        $condition = $this->get_search_condition();

        // append with extra conditions
        $owner_condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->owner)
        );
        if ($condition)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = $owner_condition;
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = $owner_condition;
        }

        return $condition;
    }

    public function get_search_condition()
    {
        $query = $this->getButtonToolBarRenderer()->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE),
                '*' . $query . '*'
            );
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION),
                '*' . $query . '*'
            );

            return new OrCondition($conditions);
        }

        return null;
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
