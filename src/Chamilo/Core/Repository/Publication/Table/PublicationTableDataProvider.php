<?php
namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class PublicationTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::get_content_object_publication_attributes(
            $this->get_component()->get_user_id(), 
            PublicationInterface::ATTRIBUTES_TYPE_USER, 
            $condition, 
            $count, 
            $offset, 
            $order_property);
    }

    public function count_data($condition)
    {
        return DataManager::count_publication_attributes(
            PublicationInterface::ATTRIBUTES_TYPE_USER, 
            $this->get_component()->get_user_id(), 
            $condition);
    }
}
