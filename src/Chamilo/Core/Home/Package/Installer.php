<?php
namespace Chamilo\Core\Home\Package;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Home\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    public function create_basic_home()
    {
        $tab = new Tab();
        $tab->setTitle(Translation::get('Home'));
        $tab->setUserId(0);

        if (!$tab->create())
        {
            return false;
        }

        $columnNews = new Column();
        $columnNews->setParentId($tab->get_id());
        $columnNews->setTitle(Translation::get('News'));
        $columnNews->setWidth(66);
        $columnNews->setUserId(0);

        if (!$columnNews->create())
        {
            return false;
        }

        $columnVarious = new Column();
        $columnVarious->setParentId($tab->get_id());
        $columnVarious->setTitle(Translation::get('Various'));
        $columnVarious->setWidth(33);
        $columnVarious->setUserId(0);

        if (!$columnVarious->create())
        {
            return false;
        }

        return true;
    }

    /**
     * Runs the install-script.
     *
     * @todo This function now uses the function of the RepositoryInstaller class. These shared functions should be
     *       available in a common base class.
     */
    public function extra(): bool
    {
        if (!$this->create_basic_home())
        {
            return false;
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('HomeCreated'));
        }

        return true;
    }
}
