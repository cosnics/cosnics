<?php
namespace Chamilo\Core\Repository\ContentObject\File\Browser;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;

class RepositoryTable extends \Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTable
{

    public function __construct($component)
    {
        parent::__construct($component);
        $this->setType(File::class);
    }
}
