<?php
namespace Chamilo\Libraries\Mail\ValueObject;

/**
 * Describes a file that can be embedded inline or attached to an e-mail.
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MailFile
{
    /**
     * The filename of the embedded object
     * 
     * @var string
     */
    private $filename;

    /**
     * The system path of the embedded object
     * 
     * @var string
     */
    private $path;

    /**
     * The mime type of the embedded object
     * 
     * @var string
     */
    private $mimeType;

    /**
     * Constructor
     * 
     * @param string $filename
     * @param string $mimeType
     * @param string $path
     */
    function __construct($filename, $path, $mimeType = null)
    {
        $this->filename = $filename;
        $this->path = $path;
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}