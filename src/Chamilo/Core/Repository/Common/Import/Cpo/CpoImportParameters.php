<?php
namespace Chamilo\Core\Repository\Common\Import\Cpo;

use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;

class CpoImportParameters extends ImportParameters
{

    private $file;

    public function __construct($type, $user, WorkspaceInterface $workspace, $category, $file, $values)
    {
        parent::__construct($type, $user, $workspace, $category);
        $this->file = $file;
    }

    /**
     *
     * @return the $file
     */
    public function get_file()
    {
        return $this->file;
    }

    /**
     *
     * @param $file field_type
     */
    public function set_file($file)
    {
        $this->file = $file;
    }
}
