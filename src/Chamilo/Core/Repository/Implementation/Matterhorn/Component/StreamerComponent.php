<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Component;

use Chamilo\Core\Repository\Implementation\Matterhorn\Manager;
use Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Stream;
use Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Type\PreviewStream;
use Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Type\TrackStream;
use Chamilo\Libraries\Platform\Session\Request;

class StreamerComponent extends Manager
{

    public function run()
    {
        $external_object_id = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_ID);
        $external_object = $this->retrieve_external_repository_object($external_object_id);
        $type = Request :: get(Stream :: PARAM_TYPE);
        
        switch ($type)
        {
            case Stream :: TYPE_PREVIEW :
                $stream = new PreviewStream($this, $external_object);
                break;
            case Stream :: TYPE_TRACK :
                $stream = new TrackStream($this, $external_object, Request :: get(TrackStream :: PARAM_TRACK_ID));
                break;
        }
        
        $stream->read();
    }
}