<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class WikiPageMostActiveUsersBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(Translation::get('MostActiveUser'), Translation::get('NumberOfContributions')));
        
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class,
            Request::get(Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        
        $wiki_page = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class,
            $complex_content_object_item->get_ref());
        $versions = \Chamilo\Core\Repository\Storage\DataManager::retrieve_content_object_versions($wiki_page);
        
        $users = [];
        foreach($versions as $version)
        {
            $users[$version->get_owner_id()] = isset($users[$version->get_owner_id()]) ? $users[$version->get_owner_id()] ++ : 1;
        }
        arsort($users);
        $keys = array_keys($users);
        $user = DataManager::retrieve_by_id(
            User::class,
            (int) $keys[0]);
        
        $reporting_data->add_category(0);
        $reporting_data->add_data_category_row(
            0, 
            Translation::get('MostActiveUser'), 
            $user->get_fullname() . ' (' . $user->get_username() . ')');
        $reporting_data->add_data_category_row(0, Translation::get('NumberOfContributions'), $users[$user->get_id()]);
        $reporting_data->hide_categories();
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }
}
