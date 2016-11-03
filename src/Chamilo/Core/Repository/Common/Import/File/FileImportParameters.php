<?php
namespace Chamilo\Core\Repository\Common\Import\File;

use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;

class FileImportParameters extends ImportParameters
{

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
    public function __construct($type, $user, WorkspaceInterface $workspace, $category, $file, $values)
    {
        parent :: __construct($type, $user, $workspace, $category);
        $this->document_type = $values[FileContentObjectImportForm :: PARAM_DOCUMENT_TYPE];
        $this->file = $file;
        $this->link = $values[FileContentObjectImportForm :: PROPERTY_LINK];
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
