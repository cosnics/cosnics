<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Factory\ItemFormFactory;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager implements DelegateComponent
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $itemType = $this->getRequest()->query->get(self::PARAM_TYPE);

        if (is_null($itemType))
        {
            throw new ParameterNotDefinedException(self::PARAM_TYPE);
        }

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, $this->getTranslator()->trans(
                'Add' . ClassnameUtilities::getInstance()->getPackageNameFromNamespace($itemType), [],
                Manager::package()
            )
            )
        );

        $itemForm = $this->getItemFormFactory()->getItemForm(
            $itemType, $this->get_url(array(self::PARAM_TYPE => $itemType))
        );

        if ($itemForm->validate())
        {
            $item = $this->getItemService()->createItemWithTitlesForTypeFromValues(
                $itemType, $itemForm->exportValues()
            );

            $success = $item instanceof Item;

            if ($success)
            {
                $message = $this->getTranslator()->trans(
                    'ObjectCreated',
                    array('OBJECT' => $this->getTranslator()->trans('ManagerItem', [], Manager::package())),
                    Utilities::COMMON_LIBRARIES
                );
            }
            else
            {
                $message = $this->getTranslator()->trans(
                    'ObjectNotCreated',
                    array('OBJECT' => $this->getTranslator()->trans('ManagerItem', [], Manager::package())),
                    Utilities::COMMON_LIBRARIES
                );
            }

            $this->redirect(
                $message, ($success ? false : true), array(
                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_PARENT => $item->getParentId()
                )
            );
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $itemForm->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemFormFactory
     */
    public function getItemFormFactory()
    {
        return $this->getService(ItemFormFactory::class);
    }
}
