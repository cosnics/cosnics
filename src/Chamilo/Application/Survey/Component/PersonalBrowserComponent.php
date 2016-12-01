<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Table\Publication\Personal\PersonalPublicationTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PersonalBrowserComponent extends TabComponent implements TableSupport
{

    public function build()
    {
        $table = new PersonalPublicationTable($this);
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
    }
}