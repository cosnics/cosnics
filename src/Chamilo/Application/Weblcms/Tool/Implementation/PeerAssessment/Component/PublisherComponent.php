<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;
use Chamilo\Core\Repository\Viewer\Manager as RepoViewer;

class PublisherComponent extends Manager
{

    function get_additional_parameters()
    {
        return array(RepoViewer :: PARAM_ID, RepoViewer :: PARAM_ACTION);
    }
}