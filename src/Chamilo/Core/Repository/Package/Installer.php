<?php
namespace Chamilo\Core\Repository\Package;

use Chamilo\Core\Repository\Quota\Rights\Rights;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;

/**
 * $Id: repository_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.install
 */
/**
 * This installer can be used to create the storage structure for the repository.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public function extra()
    {
        // Create a root rights location for the quota requests
        if (! Rights :: get_instance()->create_quota_root())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('QuotaLocationCreated'));
        }

        // Create a category item for the (external) repository implementations
        $category = new CategoryItem();

        if (! $category->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation :: get('ExternalRepositories'));
            $item_title->set_isocode(Translation :: getInstance()->getLanguageIsocode());
            $item_title->set_item_id($category->get_id());

            if (! $item_title->create())
            {
                return false;
            }
        }

        return true;
    }
}
