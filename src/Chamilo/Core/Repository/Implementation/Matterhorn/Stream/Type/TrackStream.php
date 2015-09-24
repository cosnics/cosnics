<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Type;

use Chamilo\Core\Repository\Implementation\Matterhorn\ExternalObject;
use Chamilo\Core\Repository\Implementation\Matterhorn\Stream\Stream;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package core\repository\implementation\matterhorn
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TrackStream extends Stream
{
    // Parameters
    const PARAM_TRACK_ID = 'track_id';

    /**
     *
     * @var string
     */
    private $track_id;

    /**
     *
     * @var \core\repository\implementation\matterhorn\Track
     */
    private $track;

    /**
     *
     * @param \libraries\architecture\application\Application $application
     * @param ExternalObject $external_object
     */
    public function __construct(Application $application, ExternalObject $external_object, $track_id)
    {
        $this->track_id = $track_id;
        parent :: __construct($application, $external_object);
    }

    /**
     *
     * @return \core\repository\implementation\matterhorn\Track
     */
    public function get_track()
    {
        if (! isset($preview))
        {
            $external_object = $this->get_external_object();
            $tracks = $external_object->get_tracks();
            
            foreach ($tracks as $track)
            {
                if ($track->get_id() == $this->get_track_id())
                {
                    $this->track = $track;
                    break;
                }
            }
        }
        
        return $this->track;
    }

    /**
     *
     * @return string
     */
    public function get_track_id()
    {
        return $this->track_id;
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->get_track()->get_url();
    }

    /**
     *
     * @return string
     */
    public function get_mimetype()
    {
        return $this->get_track()->get_mimetype();
    }
}