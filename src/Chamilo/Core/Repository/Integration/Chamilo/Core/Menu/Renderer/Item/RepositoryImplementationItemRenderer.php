<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\External\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryImplementationItemRenderer extends ItemRenderer
{

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $selected = $this->isSelected($item);

        if ($selected)
        {
            $class = 'class="chamilo-menu-item-current" ';
        }
        else
        {
            $class = '';
        }

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => $item->get_implementation(),
                Manager::PARAM_EXTERNAL_REPOSITORY => $item->get_instance_id()
            )
        );

        $html = array();

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a ' . $class . 'href="' . $redirect->getUrl() . '">';
        $title = htmlentities($item->get_name());

        if ($item->showIcon())
        {
            $imagePath = $this->getThemeUtilities()->getImagePath($item->get_implementation(), 'Menu');

            $html[] = '<img class="chamilo-menu-item-icon' .
                ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationItem $item
     *
     * @return bool
     */
    public function isSelected(Item $item)
    {
        $request = $this->getRequest();
        $currentContext = $request->query->get(Application::PARAM_CONTEXT);
        $currentInstance = $request->query->get(Manager::PARAM_EXTERNAL_REPOSITORY);

        return ($currentContext == $item->get_implementation() && $currentInstance == $item->get_instance_id());
    }
}