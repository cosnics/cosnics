<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\ItemTitles;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $this->check_allowed();

        $itemType = $this->getRequest()->query->get(Manager::PARAM_TYPE);

        if ($itemType)
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    null,
                    Translation::get('Add' . ClassnameUtilities::getInstance()->getPackageNameFromNamespace($itemType))
                )
            );

            $item = new $type();
            $itemForm = ItemForm::factory(
                ItemForm::TYPE_CREATE, $item, $this->get_url(array(Manager::PARAM_TYPE => $type))
            );

            if ($itemForm->validate())
            {
                $success = $this->getItemService()->createItemWithTitlesForTypeFromValues(
                    $itemType, $itemForm->exportValues()
                );

                if ($success)
                {
                    $message = Translation::get(
                        'ObjectCreated', array('OBJECT' => Translation::get('ManagerItem')), Utilities::COMMON_LIBRARIES
                    );
                }
                else
                {
                    $message = Translation::get(
                        'ObjectNotCreated', array('OBJECT' => Translation::get('ManagerItem')),
                        Utilities::COMMON_LIBRARIES
                    );
                }

                $this->redirect(
                    $message, ($success ? false : true), array(
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_PARENT => $item->get_parent()
                    )
                );
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $itemForm->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }
}
