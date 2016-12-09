<?php
namespace Chamilo\Core\Repository\Table\Doubles;

use Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTable;

class DoublesTable extends RepositoryTable
{

    private $is_detail;

    public function __construct($component, $is_detail = false)
    {
        parent :: __construct($component);
        $this->is_detail = $is_detail;
    }

    public function is_detail()
    {
        return $this->is_detail;
    }
}
