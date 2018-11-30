<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Rights\Form\RightsForm;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsComponent extends Manager implements DelegateComponent
{
    /**
     * @var \Chamilo\Core\Menu\Storage\DataClass\Item
     */
    private $item;

    /**
     * @var integer
     */
    private $itemIdentifier;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $rightsService = $this->getRightsService();

        $rightsService->isUserAllowedToAccessComponent($this->getUser());

        //        $item_id = Request::get(self::PARAM_ITEM);
        //        $this->set_parameter(self::PARAM_ITEM, $item_id);
        //        if (!$item_id)
        //        {
        //            $location = array(Rights::getInstance()->get_root(self::package()));
        //        }
        //        else
        //        {
        //            $location = array(
        //                Rights::getInstance()->get_location_by_identifier(self::package(), Rights::TYPE_ITEM, $item_id)
        //            );
        //        }

        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();

        //        $application = $this->getApplicationFactory()->getApplication(
        //            \Chamilo\Core\Rights\Editor\Manager::context(),
        //            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        //        );
        //        $application->set_context(self::package());
        //        $application->set_locations($location);
        //        $application->set_entities($entities);
        //
        //        return $application->run();

        $itemIdentifier = $this->getItemIdentifier();

        $postBackUrl = new Redirect(
            array(
                self::PARAM_CONTEXT => self::package(), self::PARAM_ACTION => self::ACTION_RIGHTS,
                self::PARAM_ITEM => $itemIdentifier
            )
        );

        $rightsForm = new RightsForm(
            $postBackUrl->getUrl(), $this->getTranslator(), $itemIdentifier != 0, $rightsService->getAvailableRights(),
            $entities
        );

        $html = array();

        $html[] = $this->render_header();
        $html[] = $rightsForm->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return integer
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getItemIdentifier()
    {
        if (!isset($this->itemIdentifier))
        {
            $this->itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM, 0);

            if ($this->itemIdentifier != 0)
            {
                $this->getItem();
            }
        }

        return $this->itemIdentifier;
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getItem()
    {
        if (!isset($this->item))
        {
            $itemIdentifier = $this->getItemIdentifier();

            if ($itemIdentifier != 0)
            {
                $this->item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

                if (!$this->item instanceof Item)
                {
                    throw new ObjectNotExistException($this->getTranslator()->trans('MenuItem'), $itemIdentifier);
                }
            }
        }

        return $this->item;
    }
}
