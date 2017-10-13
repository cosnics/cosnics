<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class RestData
{

    /**
     * @var string
     */
    private $data;

    const TYPE_FORM = 'form';
    const TYPE_URL = 'url';
    const TYPE_PLAIN = 'plain';

    /**
     * RestData constructor.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param string $content_type
     * @param string $data
     *
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData
     */
    public static function factory($content_type, $data)
    {
        $rest_data__class = __NAMESPACE__ . '\Data\\' .
             (string) StringUtilities::getInstance()->createString($content_type)->upperCamelize();
        return new $rest_data__class($data);
    }

    /**
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     *
     * @deprecated
     * @see getData
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     * @param string $data
     *
     * @deprecated
     * @see setData
     */
    public function set_data($data)
    {
        $this->setData($data);
    }

    abstract public function prepare();
}
