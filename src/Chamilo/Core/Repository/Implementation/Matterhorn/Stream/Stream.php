<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Stream;

use Chamilo\Core\Repository\Implementation\Matterhorn\ExternalObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Properties\FileProperties;

/**
 *
 * @package core\repository\implementation\matterhorn
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Stream
{
    // Parameters
    const PARAM_TYPE = 'stream_type';
    
    // Types
    const TYPE_PREVIEW = 'Preview';
    const TYPE_TRACK = 'Track';

    /**
     *
     * @var \libraries\architecture\application\Application
     */
    private $application;

    /**
     *
     * @var ExternalObject
     */
    private $external_object;

    /**
     *
     * @param \libraries\architecture\application\Application $application
     * @param ExternalObject $external_object
     */
    public function __construct(Application $application, ExternalObject $external_object)
    {
        $this->application = $application;
        $this->external_object = $external_object;
    }

    /**
     *
     * @return \libraries\architecture\application\Application
     */
    public function get_application()
    {
        return $this->application;
    }

    /**
     *
     * @param \libraries\architecture\application\Application $application
     */
    public function set_application($application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \core\repository\implementation\matterhorn\ExternalObject
     */
    public function get_external_object()
    {
        return $this->external_object;
    }

    /**
     *
     * @param \core\repository\implementation\matterhorn\ExternalObject $external_object
     */
    public function set_external_object($external_object)
    {
        $this->external_object = $external_object;
    }

    /**
     *
     * @return string
     */
    abstract public function get_url();

    /**
     *
     * @return string
     */
    abstract public function get_mimetype();

    /**
     *
     * @return int
     */
    public function get_size()
    {
        $properties = FileProperties :: from_url($this->get_url());
        return $properties->get_size();
    }

    /**
     *
     * @return string
     */
    public function get_filename()
    {
        return basename($this->get_url());
    }

    public function read()
    {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $this->get_mimetype());
        header('Content-Disposition: inline; filename=' . $this->get_filename());
        header('Content-Length: ' . $this->get_size());
        readfile($this->get_url());
        exit();
    }

    /**
     *
     * @param string $type
     * @param Application $application
     * @param ExternalObject $external_object
     * @return Stream
     */
    public static function factory($type, Application $application, ExternalObject $external_object)
    {
        $class_name = __NAMESPACE__ . '\\' . $type . 'Stream';
        return new $class_name($application, $external_object);
    }
}