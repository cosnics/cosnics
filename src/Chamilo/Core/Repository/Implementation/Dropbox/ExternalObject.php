<?php
namespace Chamilo\Core\Repository\Implementation\Dropbox;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'dropbox';

    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    public function get_content_data($external_object)
    {
        $external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_by_id(
            Instance :: class_name(), 
            $this->get_external_repository_id());
        return DataConnector :: get_instance($external_repository)->download_external_repository_object(
            $external_object);
    }
}
