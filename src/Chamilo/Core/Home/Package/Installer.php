<?php
namespace Chamilo\Core\Home\Package;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Row;
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
        $tab->set_title(Translation :: get('Home'));
        $tab->set_user('0');
        if (! $tab->create())
        {
            return false;
        }
        
        $row = new Row();
        $row->set_title(Translation :: get('Site'));
        $row->set_tab($tab->get_id());
        $row->set_user('0');
        if (! $row->create())
        {
            return false;
        }
        
        $column_news = new Column();
        $column_news->set_row($row->get_id());
        $column_news->set_title(Translation :: get('News'));
        $column_news->set_sort('1');
        $column_news->set_width('66');
        $column_news->set_user('0');
        if (! $column_news->create())
        {
            return false;
        }
        
        $column_varia = new Column();
        $column_varia->set_row($row->get_id());
        $column_varia->set_title(Translation :: get('Various'));
        $column_varia->set_sort('2');
        $column_varia->set_width('33');
        $column_varia->set_user('0');
        if (! $column_varia->create())
        {
            return false;
        }
        
        return true;
    }
}
