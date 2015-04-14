<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action;

class Installer extends \Chamilo\Core\Metadata\Action\Installer
{

    public function extra()
    {
        if (! parent :: extra())
        {
            return false;
        }

        // if(!$this->)
        return true;
    }
}