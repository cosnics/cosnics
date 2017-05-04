<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Component;

use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Manager;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\DataTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\LoginTemplate;
use Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Template\UserTemplate;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Platform\Translation;

class BrowserComponent extends Manager
{

    function run()
    {
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->get_table();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    function get_table()
    {
        $table = new SortableTableFromArray($this->get_table_data());
        
        $html[] = $table->toHtml();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode(PHP_EOL, $html);
    }

    private function get_table_data()
    {
        $data = array();
        $data[] = $this->get_data(LoginTemplate::class_name());
        $data[] = $this->get_data(DataTemplate::class_name());
        $data[] = $this->get_data(UserTemplate::class_name());
        return $data;
    }

    private function get_data($class_name)
    {
        $title = Translation::get($class_name::PROPERTY_NAME);
        $title_url = '<a href="' . htmlentities($this->get_viewer_url($class_name::TEMPLATE_ID)) . '" title="' . $title .
             '">' . $title . '</a>';
        $description = Translation::get($class_name::PROPERTY_DESCRIPTION);
        return array($title_url, $description);
    }
}
?>