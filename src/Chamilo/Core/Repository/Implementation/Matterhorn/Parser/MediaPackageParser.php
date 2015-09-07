<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Parser;

use Chamilo\Libraries\Utilities\StringUtilities;
use DOMDocument;
use DOMElement;
use stdClass;

abstract class MediaPackageParser
{
    const TYPE_XML = 'xml';
    const TYPE_JSON = 'json';

    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     *
     * @return the $response
     */
    public function get_response()
    {
        return $this->response;
    }

    /**
     *
     * @param $response the $response to set
     */
    public function set_response($response)
    {
        $this->response = $response;
    }

    public static function get($response)
    {
        if ($response instanceof stdClass)
        {
            $type = 'object';
        }
        elseif ($response instanceof DOMElement || $response instanceof DOMDocument)
        {
            $type = 'dom';
        }
        else
        {
            return false;
        }
        $class_file = __DIR__ . '/media_package/' . $type . '.class.php';
        $class = __NAMESPACE__ . '\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() .
             'MediaPackageParser';
        return new $class($response);
    }

    public static function process_get($response)
    {
        $parser = self :: get($response);
        return $parser->process();
    }

    abstract public function process();
}
