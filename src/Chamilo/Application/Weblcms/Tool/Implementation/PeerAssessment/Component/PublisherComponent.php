<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Core\Repository\Viewer\Manager as RepoViewer;
use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;

class PublisherComponent extends Manager
{

    function get_additional_parameters()
    {
        return array(RepoViewer :: PARAM_ID, RepoViewer :: PARAM_ACTION);
    }
}