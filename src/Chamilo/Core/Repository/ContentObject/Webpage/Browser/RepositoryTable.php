<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Browser;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;

class RepositoryTable extends \Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTable
{

    public function __construct($component)
    {
        parent::__construct($component);
        $this->set_type(Webpage::class);
    }
}
