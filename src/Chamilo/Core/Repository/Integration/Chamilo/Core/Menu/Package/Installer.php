<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryApplicationItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationCategoryItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceCategoryItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Core\Menu\Action\Installer
{
    public const CONTEXT = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';

    /**
     * @param string[] $formValues
     */
    public function __construct($formValues)
    {
        parent::__construct($formValues, Item::DISPLAY_BOTH, false);
    }

    public function extra()
    {
        $item = new RepositoryApplicationItem();

        $context = ClassnameUtilities::getInstance()->getNamespaceParent(static::CONTEXT, 5);

        $item->set_application($context);
        $item->set_display($this->getItemDisplay());
        $item->set_use_translation(1);

        if (!$item->create())
        {
            return false;
        }

        $item_title = new ItemTitle();
        $item_title->set_title(Translation::get('TypeName', null, static::CONTEXT));
        $item_title->set_isocode(Translation::getInstance()->getLanguageIsocode());
        $item_title->setItemId($item->getId());

        if (!$item_title->create())
        {
            return false;
        }

        $repository_implementation = new RepositoryImplementationCategoryItem();
        $repository_implementation->set_display($this->getItemDisplay());

        if (!$repository_implementation->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation::get('Instances'));
            $item_title->set_isocode(Translation::getInstance()->getLanguageIsocode());
            $item_title->set_item_id($repository_implementation->get_id());
            if (!$item_title->create())
            {
                return false;
            }
        }

        $workspace = new WorkspaceCategoryItem();
        $workspace->set_display($this->getItemDisplay());

        if (!$workspace->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title(Translation::get('Workspaces'));
            $item_title->set_isocode(Translation::getInstance()->getLanguageIsocode());
            $item_title->set_item_id($workspace->get_id());
            if (!$item_title->create())
            {
                return false;
            }
        }

        return true;
    }
}
