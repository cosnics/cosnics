<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationCategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Package
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Core\Menu\Action\Installer
{

    /**
     *
     * @param string[] $formValues
     */
    public function __construct($formValues)
    {
        parent :: __construct($formValues, Item :: DISPLAY_ICON, false);
    }

    public function extra()
    {
        if (! parent :: extra())
        {
            return false;
        }

        $repository_implementation = new RepositoryImplementationCategoryItem();
        $repository_implementation->set_display($this->getItemDisplay());

        if (! $repository_implementation->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation :: get('Instances'));
            $item_title->set_isocode(Translation :: getInstance()->getLanguageIsocode());
            $item_title->set_item_id($repository_implementation->get_id());
            if (! $item_title->create())
            {
                return false;
            }
        }

        $workspace = new RepositoryImplementationCategoryItem();
        $workspace->set_display($this->getItemDisplay());

        if (! $workspace->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation :: get('Workspaces'));
            $item_title->set_isocode(Translation :: getInstance()->getLanguageIsocode());
            $item_title->set_item_id($workspace->get_id());
            if (! $item_title->create())
            {
                return false;
            }
        }

        return true;
    }
}
