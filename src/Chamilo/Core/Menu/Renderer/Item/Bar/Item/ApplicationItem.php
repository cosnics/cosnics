<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationItem extends Bar
{
    /**
     * Returns whether or not this item is selected
     *
     * @return bool
     */
    public function isItemSelected()
    {
        $request = $this->getMenuRenderer()->getRequest();
        $currentContext = $request->get(Application::PARAM_CONTEXT);
        $currentAction = $request->get(Application::PARAM_ACTION);

        /** @var \Chamilo\Core\Menu\Storage\DataClass\ApplicationItem $item */
        $item = $this->getItem();

        if($currentContext != $item->get_application())
        {
            return false;
        }

        if($item->getComponent() && $currentAction != $item->getComponent())
        {
            return false;
        }

        return true;
    }

    /**
     * Returns the content
     *
     * @return string
     */
    public function getContent()
    {
        $application = $this->getItem()->get_application();

        if (! Application :: is_active($application))
        {
            return;
        }

        $url = $this->getApplicationItemURL();

        $html = array();

        if ($this->getItem()->get_use_translation())
        {
            $title = Translation :: get('TypeName', null, $this->getItem()->get_application());
        }
        else
        {
            $title = $this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode());
        }

        $html[] = '<a href="' . $url . '">';

        if ($this->getItem()->show_icon())
        {
            $integrationNamespace = $this->getItem()->get_application() . '\Integration\Chamilo\Core\Menu';
            $imagePath = Theme :: getInstance()->getImagePath(
                $integrationNamespace,
                'Menu' . ($this->isSelected() ? 'Selected' : ''));

            $html[] = '<img class="chamilo-menu-item-icon' .
                ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') .
                '" src="' . $imagePath . '" title="' . htmlentities($title) . '" alt="' .
                 $title . '" />';
        }

        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                 ($this->getItem()->show_icon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the url for the current application item
     *
     * @return string
     */
    protected function getApplicationItemURL()
    {
        /** @var \Chamilo\Core\Menu\Storage\DataClass\ApplicationItem $item */
        $item = $this->getItem();

        if ($item->get_application() == 'root')
        {
            return 'index.php';
        }

        $url = 'index.php?application=' . $item->get_application();

        if($item->getComponent())
        {
            $url .= '&go=' . $item->getComponent();
        }

        if($item->getExtraParameters())
        {
            $url .= '&' . $item->getExtraParameters();
        }

        return $url;
    }
}
