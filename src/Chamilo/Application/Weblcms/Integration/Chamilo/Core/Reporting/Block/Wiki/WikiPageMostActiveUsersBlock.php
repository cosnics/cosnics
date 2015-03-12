<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class WikiPageMostActiveUsersBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(Translation :: get('MostActiveUser'), Translation :: get('NumberOfContributions')));
        
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item(
            Request :: get(
                \Chamilo\Core\Repository\Display\Action\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        
        $wiki_page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
            $complex_content_object_item->get_ref());
        $versions = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_versions($wiki_page);
        
        $users = array();
        while ($version = $versions->next_result())
        {
            $users[$version->get_owner_id()] = isset($users[$version->get_owner_id()]) ? $users[$version->get_owner_id()] ++ : 1;
        }
        arsort($users);
        $keys = array_keys($users);
        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(), 
            (int) $keys[0]);
        
        $reporting_data->add_category(0);
        $reporting_data->add_data_category_row(
            0, 
            Translation :: get('MostActiveUser'), 
            $user->get_fullname() . ' (' . $user->get_username() . ')');
        $reporting_data->add_data_category_row(0, Translation :: get('NumberOfContributions'), $users[$user->get_id()]);
        $reporting_data->hide_categories();
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }
}
