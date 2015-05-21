<?php
namespace Chamilo\Core\Repository\Common\Import\Webpage;

use Chamilo\Core\Repository\Common\Import\ImportParameters;

class WebpageImportParameters extends ImportParameters
{
    const CLASS_NAME = __CLASS__;

    /**
     *
     * @var int
     */
    private $document_type;

    /**
     *
     * @var \libraries\file\FileProperties
     */
    private $file;

    /**
     *
     * @var string
     */
    private $link;

    /**
     *
     * @param string $type
     * @param int $user
     * @param int $category
     * @param \libraries\file\FileProperties $file
     * @param multitype:mixed $values
     */
    public function __construct($type, $user, $category, $file, $values)
    {
        parent :: __construct($type, $user, $category);
        $this->document_type = $values[WebpageContentObjectImportForm :: PARAM_WEBPAGE_TYPE];
        $this->file = $file;
        $this->link = $values[WebpageContentObjectImportForm :: PROPERTY_LINK];
    }

    /**
     *
     * @return \libraries\file\FileProperties
     */
    public function get_file()
    {
        return $this->file;
    }

    /**
     *
     * @param \libraries\file\FileProperties $file
     */
    public function set_file($file)
    {
        $this->file = $file;
    }

    /**
     *
     * @return string
     */
    public function get_link()
    {
        return $this->link;
    }

    /**
     *
     * @param string $link
     */
    public function set_link($link)
    {
        $this->link = $link;
    }

    /**
     *
     * @return int
     */
    public function get_document_type()
    {
        return $this->document_type;
    }

    /**
     *
     * @param int $document_type
     */
    public function set_document_type($document_type)
    {
        $this->document_type = $document_type;
    }
}
