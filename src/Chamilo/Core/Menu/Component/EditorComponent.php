<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\Form\ItemFormFactory;
use Chamilo\Core\Menu\ItemTitles;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
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
class EditorComponent extends Manager implements DelegateComponent
{
    protected function getItem()
    {
        $itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifier))
        {
            throw new ParameterNotDefinedException(self::PARAM_ITEM);
        }

        $item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

        if (!$item instanceof Item)
        {
            throw new ObjectNotExistException($this->getTranslator()->trans('MenuItem'), $itemIdentifier);
        }

        return $item;
    }

    public function run()
    {
        $this->check_allowed();

        $item = $this->getItem();
        $itemTitles = $this->getItemService()->findItemTitlesByItemIdentifierIndexedByIsoCode($item->getId());

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, $this->getTranslator()->trans(
                'MenuManagerEditor', array(
                '{NAME}' => $this->getItemService()->determineItemTitleForCurrentLanguage(
                    $itemTitles
                )
            ), 'Chamilo\Core\Menu'
            )
            )
        );

        $itemForm = $this->getItemFormFactory()->getItemForm(
            $item->get_type(), $this->get_url(array(self::PARAM_ITEM => $item->getId()))
        );
        $itemForm->setItemDefaults($item, $itemTitles);

        if ($itemForm->validate())
        {
            $success = $this->getItemService()->saveItemTitlesForItemFromValues($item, $itemForm->exportValues());

            if ($success)
            {
                $message = Translation::get(
                    'ObjectCreated', array('OBJECT' => Translation::get('ManagerItem')), Utilities::COMMON_LIBRARIES
                );
            }
            else
            {
                $message = Translation::get(
                    'ObjectNotCreated', array('OBJECT' => Translation::get('ManagerItem')), Utilities::COMMON_LIBRARIES
                );
            }

            $this->redirect(
                $message, ($success ? false : true),
                array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_ITEM => $item->get_parent())
            );
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $itemForm->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * @return \Chamilo\Core\Menu\Form\ItemFormFactory
     */
    public function getItemFormFactory()
    {
        return $this->getService(ItemFormFactory::class);
    }
}
