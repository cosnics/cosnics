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

    private $data;
    const TYPE_FORM = 'form';
    const TYPE_URL = 'url';
    const TYPE_PLAIN = 'plain';

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function factory($content_type, $data)
    {
        $rest_data__class = __NAMESPACE__ . '\Data\\' .
             (string) StringUtilities::getInstance()->createString($content_type)->upperCamelize();
        return new $rest_data__class($data);
    }

    /**
     *
     * @return the $data
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     *
     * @param $data the $data to set
     */
    public function set_data($data)
    {
        $this->data = $data;
    }

    abstract public function prepare();
}
