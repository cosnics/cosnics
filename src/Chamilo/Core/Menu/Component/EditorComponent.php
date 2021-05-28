<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Factory\ItemFormFactory;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
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
    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Exception
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

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
            $item->getType(), $this->get_url(array(self::PARAM_ITEM => $item->getId()))
        );
        $itemForm->setItemDefaults($item, $itemTitles);

        if ($itemForm->validate())
        {
            $success = $this->getItemService()->saveItemWithTitlesFromValues($item, $itemForm->exportValues());

            $message = $this->getTranslator()->trans(
                $success ? 'ObjectCreated' : 'ObjectNotCreated',
                array('OBJECT' => $this->getTranslator()->trans('ManagerItem', [], 'Chamilo\Core\Menu')),
                Utilities::COMMON_LIBRARIES
            );

            $this->redirect(
                $message, !$success,
                array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_ITEM => $item->getParentId())
            );
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $itemForm->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
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

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemFormFactory
     */
    public function getItemFormFactory()
    {
        return $this->getService(ItemFormFactory::class);
    }
}
