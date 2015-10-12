<?php
namespace Chamilo\Core\Home\Package;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: home_installer.class.php 227 2009-11-13 14:45:05Z kariboe $
 *
 * @package home.install
 */
/**
 * This installer can be used to create the storage structure for the home application.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Runs the install-script.
     *
     * @todo This function now uses the function of the RepositoryInstaller class. These shared functions should be
     *       available in a common base class.
     */
    public function extra()
    {
        if (! $this->create_basic_home())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('HomeCreated'));
        }

        return true;
    }

    public function create_basic_home()
    {
        $tab = new Tab();
        $tab->setTitle(Translation :: get('Home'));
        $tab->setUserId(0);

        if (! $tab->create())
        {
            return false;
        }

        $columnNews = new Column();
        $columnNews->setParentId($tab->get_id());
        $columnNews->setTitle(Translation :: get('News'));
        $columnNews->setWidth(66);
        $columnNews->setUserId(0);

        if (! $columnNews->create())
        {
            return false;
        }

        $columnVarious = new Column();
        $columnVarious->setParentId($tab->get_id());
        $columnVarious->setTitle(Translation :: get('Various'));
        $columnVarious->setWidth(33);
        $columnVarious->setUserId(0);

        if (! $columnVarious->create())
        {
            return false;
        }

        return true;
    }
}
