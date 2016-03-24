<?php
namespace Chamilo\Application\Survey\Rights\Component;

use Chamilo\Application\Survey\Rights\Table\EntityRelation\EntityRelationTable;
use Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends TabComponent implements TableSupport
{

    public function build()
    {
        $table = new EntityRelationTable($this);
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                PublicationEntityRelation :: class_name(), 
                PublicationEntityRelation :: PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->getCurrentPublication()->getId()));
    }
}