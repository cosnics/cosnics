<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Preview;

use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package core\repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PreviewStorage
{

    /**
     *
     * @var \core\repository\content_object\portfolio\display\PreviewStorage
     */
    private static $instance;

    /**
     *
     * @return \core\repository\content_object\portfolio\display\PreviewStorage
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new PreviewStorage();
        }
        return self :: $instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $storage = $this->get_storage();

        if (! isset($storage))
        {
            $this->set_storage(array());
        }
    }

    /**
     * Empty the storage
     *
     * @return boolean
     */
    public function reset()
    {
        return $this->set_storage(array());
    }

    /**
     *
     * @return mixed
     */
    public function get_storage()
    {
        return unserialize(Session :: retrieve(__NAMESPACE__));
    }

    /**
     *
     * @param mixed $data
     * @return boolean
     */
    public function set_storage($data)
    {
        Session :: register(__NAMESPACE__, serialize($data));
        return true;
    }

    /**
     *
     * @param string $property
     * @param mixed $value
     */
    public function set_property($property, $value)
    {
        $data = $this->get_storage();
        $data[$property] = $value;
        return $this->set_storage($data);
    }

    /**
     *
     * @param string $property
     * @return mixed
     */
    public function get_property($property)
    {
        $data = $this->get_storage();
        return $data[$property];
    }
}