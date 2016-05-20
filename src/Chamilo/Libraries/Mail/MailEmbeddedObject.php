<?php
namespace Chamilo\Libraries\Mail;

/**
 * Describes an object that can be embedded inline into an e-mail.
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MailEmbeddedObject
{

    /**
     * The filename of the embedded object
     * 
     * @var string
     */
    private $filename;

    /**
     * The path of the embedded object
     * 
     * @var string
     */
    private $path;

    /**
     * The mime type of the embedded object
     * 
     * @var string
     */
    private $mime_type;

    /**
     * Constructor
     * 
     * @param string $filename
     * @param string $mime_type
     * @param string $path
     */
    function __construct($filename, $mime_type, $path)
    {
        $this->filename = $filename;
        $this->mime_type = $mime_type;
        $this->path = $path;
    }

    /**
     *
     * @return string
     */
    public function get_filename()
    {
        return $this->filename;
    }

    /**
     *
     * @return string
     */
    public function get_mime_type()
    {
        return $this->mime_type;
    }

    /**
     *
     * @return string
     */
    public function get_path()
    {
        return $this->path;
    }
}