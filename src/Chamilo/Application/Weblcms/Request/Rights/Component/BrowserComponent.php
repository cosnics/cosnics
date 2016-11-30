<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Component;

use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Table\Entity\EntityTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

class BrowserComponent extends Manager implements TableSupport
{

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $table = new EntityTable($this);
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->get_tabs(self::ACTION_BROWSE, $table->as_html())->render();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /*
     * (non-PHPdoc) @see common\libraries.NewObjectTableSupport::get_object_table_condition()
     */
    public function get_table_condition($object_table_class_name)
    {
        // TODO Auto-generated method stub
    }
}
?>