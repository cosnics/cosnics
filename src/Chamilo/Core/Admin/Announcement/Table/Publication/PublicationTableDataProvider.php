<?php
namespace Chamilo\Core\Admin\Announcement\Table\Publication;

use Chamilo\Core\Admin\Announcement\Component\BrowserComponent;
use Chamilo\Core\Admin\Announcement\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

class PublicationTableDataProvider extends RecordTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $type = $this->get_component()->get_type();
        switch ($type)
        {
            case BrowserComponent::TYPE_FROM_ME :
                return DataManager::retrieve_publications($condition);
                break;
            case BrowserComponent::TYPE_ALL :
                return DataManager::retrieve_publications($condition);
                break;
            default :
                return DataManager::retrieve_publications_for_me(
                    $condition, 
                    $order_property, 
                    $offset, 
                    $count, 
                    $this->get_component()->get_user()->get_id());
                break;
        }
    }

    public function count_data($condition)
    {
        $type = $this->get_component()->get_type();
        switch ($type)
        {
            case BrowserComponent::TYPE_FROM_ME :
                return DataManager::count_publications($condition);
                break;
            case BrowserComponent::TYPE_ALL :
                return DataManager::count_publications($condition);
                break;
            default :
                return DataManager::count_publications_for_me($condition, $this->get_component()->get_user()->get_id());
                break;
        }
    }
}
