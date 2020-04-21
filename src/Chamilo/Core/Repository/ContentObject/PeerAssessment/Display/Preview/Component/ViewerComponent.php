<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Preview\Manager;
use Exception;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Preview\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewerComponent extends Manager
{

    public function run()
    {
        throw new Exception('PreviewNotAvailable');
    }
}
