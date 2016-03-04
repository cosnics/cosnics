<?php
namespace Chamilo\Core\Home\Renderer\Type;

use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Home\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Basic extends Renderer
{

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Element[]
     */
    private $elements;

    /**
     *
     * @var integer
     */
    private $homeUserIdentifier;

    /**
     *
     * @var \Chamilo\Core\Home\Service\HomeService
     */
    private $homeService;

    /**
     *
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    private function getHomeService()
    {
        if (! isset($this->homeService))
        {
            $this->homeService = new HomeService(new HomeRepository());
        }

        return $this->homeService;
    }

    /**
     *
     * @param string $type
     * @param integer $parentId
     */
    private function getElements($type, $parentIdentifier = 0)
    {
        if (! isset($this->elements))
        {
            $homeUserIdentifier = $this->determineHomeUserIdentifier();
            $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());

            if ($userHomeAllowed && $this->get_user() instanceof User)
            {
                if ($this->getHomeService()->countElementsByUserIdentifier($homeUserIdentifier) == 0)
                {
                    $this->getHomeService()->createDefaultHomeByUserIdentifier($homeUserIdentifier);
                }
            }

            $elementsResultSet = $this->getHomeService()->getElementsByUserIdentifier($homeUserIdentifier);

            while ($element = $elementsResultSet->next_result())
            {
                $this->elements[$element->get_type()][$element->getParentId()][] = $element;
            }
        }

        if (isset($this->elements[$type]) && isset($this->elements[$type][$parentIdentifier]))
        {
            return $this->elements[$type][$parentIdentifier];
        }
        else
        {
            return array();
        }
    }

    private function determineHomeUserIdentifier()
    {
        if (! isset($this->homeUserIdentifier))
        {
            $user = $this->get_user();
            $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());
            $generalMode = \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');

            // Get user id
            if ($user instanceof User && $generalMode && $user->is_platform_admin())
            {
                $this->homeUserIdentifier = 0;
            }
            elseif ($userHomeAllowed && $user instanceof User)
            {
                $this->homeUserIdentifier = $user->get_id();
            }
            else
            {
                $this->homeUserIdentifier = 0;
            }
        }

        return $this->homeUserIdentifier;
    }

    public function render()
    {
        $currentTabIdentifier = $this->getCurrentTabIdentifier();
        $homeUserIdentifier = $this->determineHomeUserIdentifier();
        $user = $this->get_user();

        $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());
        $generalMode = \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');

        if (($generalMode && $user instanceof User && $user->is_platform_admin()))
        {
            $html[] = '<div class="general_mode">' . Translation :: get('HomepageInGeneralMode') . '</div>';
        }

        $tabs = $this->getElements(Tab :: class_name());

        $html[] = '<ul class="nav nav-tabs portal-nav-tabs">';
        foreach ($tabs as $tabKey => $tab)
        {
            $tab_id = $tab->get_id();

            if (($tab_id == $currentTabIdentifier) || (count($tabs) == 1) ||
                 (! isset($currentTabIdentifier) && $tabKey == 0))
            {
                $class = 'active';
            }
            else
            {
                $class = '';
            }

            $html[] = '<li class="' . $class . '" id="tab_select_' . $tab->get_id() . '"><a class="tabTitle" href="' .
                 htmlspecialchars($this->get_home_tab_viewing_url($tab)) . '">' . htmlspecialchars($tab->getTitle()) .
                 '</a>';

            $isUser = $this->get_user() instanceof User;
            $homeAllowed = $isUser && ($userHomeAllowed || ($this->get_user()->is_platform_admin()) && $generalMode);
            $isAnonymous = $isUser && $this->get_user()->is_anonymous_user();

            if ($isUser && $homeAllowed && ! $isAnonymous)
            {
                $html[] = '<a class="deleteTab"><img src="' . htmlspecialchars(
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/DeleteTab')) . '" /></a>';
            }

            $html[] = '</li>';
        }
        $html[] = '</ul>';

//         if ($user instanceof User && ($userHomeAllowed || $user->is_platform_admin()))
//         {
//             $style = (! $userHomeAllowed && ! $generalMode && $user->is_platform_admin()) ? ' style="display:block;"' : '';

//             $html[] = '<div id="tab_actions" ' . $style . '>';

//             if ($userHomeAllowed || $generalMode)
//             {
//                 $html[] = '<a class="addTab" href="#"><img src="' . htmlspecialchars(
//                     Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/AddTab')) . '" />&nbsp;' .
//                      htmlspecialchars(Translation :: get('NewTab')) . '</a>';
//                 $html[] = '<a class="addColumn" href="#"><img src="' . htmlspecialchars(
//                     Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/AddColumn')) . '" />&nbsp;' .
//                      htmlspecialchars(Translation :: get('NewColumn')) . '</a>';
//                 $html[] = '<a class="addEl" href="#"><img src="' . htmlspecialchars(
//                     Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/AddBlock')) . '" />&nbsp;' .
//                      htmlspecialchars(Translation :: get('NewBlock')) . '</a>';

//                 $redirect = new Redirect(array(Manager :: PARAM_ACTION => Manager :: ACTION_TRUNCATE));

//                 if ($homeUserIdentifier != '0')
//                 {
//                     $html[] = '<a onclick="return confirm(\'' .
//                          Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES) . '\');" href="' .
//                          $redirect->getUrl() . '"><img src="' . htmlspecialchars(
//                             Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/Reset')) . '" />&nbsp;' .
//                          htmlspecialchars(Translation :: get('ResetHomepage')) . '</a>';
//                 }
//             }

//             if (! $generalMode && $user->is_platform_admin())
//             {
//                 $redirect = new Redirect(array(Manager :: PARAM_ACTION => Manager :: ACTION_MANAGE_HOME));

//                 $html[] = '<a href="' . $redirect->getUrl() . '"><img src="' . htmlspecialchars(
//                     Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/Configure')) . '" />&nbsp;' .
//                      htmlspecialchars(Translation :: get('ConfigureDefault')) . '</a>';
//             }
//             elseif ($generalMode && $user->is_platform_admin())
//             {
//                 $redirect = new Redirect(array(Manager :: PARAM_ACTION => Manager :: ACTION_PERSONAL));

//                 $title = $userHomeAllowed ? 'BackToPersonal' : 'ViewDefault';

//                 $html[] = '<a href="' . $redirect->getUrl() . '"><img src="' . htmlspecialchars(
//                     Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/Home')) . '" />&nbsp;' .
//                      htmlspecialchars(Translation :: get($title)) . '</a>';
//             }

//             $html[] = '</div>';
//         }

        foreach ($tabs as $tabKey => $tab)
        {
            $html[] = '<div class="row portal-tab" data-element-id="' . $tab->get_id() . '" style="display: ' . (((! isset(
                $currentTabIdentifier) && ($tabKey == 0 || count($tabs) == 1)) || $currentTabIdentifier == $tab->get_id()) ? 'block' : 'none') .
                 ';">';

            $columns = $this->getElements(Column :: class_name(), $tab->get_id());

            foreach ($columns as $columnKey => $column)
            {
                $html[] = '<div class="col-xs-12 col-md-' . $column->getWidth() . ' portal-block" data-tab-id="' .
                     $tab->get_id() . '" data-element-id="' . $column->get_id() . '">';

                $blocks = $this->getElements(Block :: class_name(), $column->get_id());

                foreach ($blocks as $block)
                {
                    $blockRendition = BlockRendition :: factory($this, $block);

                    if ($blockRendition->isVisible())
                    {
                        $html[] = $blockRendition->toHtml();
                    }
                }

                $footer_style = (count($blocks) > 0) ? 'style="display:none;"' : '';
                $html[] = '<div class="empty_portal_column" ' . $footer_style . '>';
                $html[] = htmlspecialchars(Translation :: get('EmptyColumnText'));
                $html[] = '<div class="deleteColumn"><a href="#"><img src="' .
                     htmlspecialchars(Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/RemoveColumn')) .
                     '" /></a></div>';
                // $html[] = '<div class="clearfix"></div>';
                $html[] = '</div>';

                $html[] = '</div>';
            }

            $html[] = '</div>';
            // $html[] = '<div class="clearfix"></div>';
        }

        // $html[] = '<div class="clearfix"></div>';

        if ($user instanceof User && ($userHomeAllowed || ($user->is_platform_admin() && $generalMode)))
        {
            $html[] = '<script type="text/javascript" src="' .
                 Path :: getInstance()->getJavascriptPath('Chamilo\Core\Home', true) . 'HomeAjax.js' . '"></script>';
        }
        else
        {
            $html[] = '<script type="text/javascript" src="' .
                 Path :: getInstance()->getJavascriptPath('Chamilo\Core\Home', true) . 'HomeView.js' . '"></script>';
        }

        return implode(PHP_EOL, $html);
    }
}
