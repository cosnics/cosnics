<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: banner.class.php 179 2009-11-12 13:51:39Z vanpouckesven $
 *
 * @package common.html
 */

/**
 * Class to display the banner of a HTML-page
 */
class Banner
{

    public function render()
    {
        return $this->toHtml();
    }

    private static function get_languages()
    {
        return \Chamilo\Configuration\Storage\DataManager :: get_languages();
    }

    private static function is_allowed_quick_language()
    {
        return PlatformSetting :: get('allow_user_change_platform_language', \Chamilo\Core\User\Manager :: context()) ==
             1 && PlatformSetting :: get(
                'allow_user_quick_change_platform_language',
                \Chamilo\Core\User\Manager :: context()) == 1;
    }

    /**
     * Creates the HTML output for the banner.
     */
    public function toHtml()
    {
        $output = array();

        if (Authentication :: is_valid() && ! is_null(Session :: get_user_id()))
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                User :: class_name(),
                Session :: get_user_id());
        }
        else
        {
            $user = null;
        }

        if (! is_null(Session :: get('_as_admin')))
        {
            $link = Redirect :: get_link(

                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_ADMIN_USER),
                array(),
                false,
                Redirect :: TYPE_CORE);
            $output[] = '<div id="emulator">' .
                 Translation :: get('LoggedInAsUser', null, \Chamilo\Core\User\Manager :: context()) . ' ' .
                 $user->get_fullname() . ' <a href="' . $link . '">' .
                 Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES) . '</a></div>';
        }

        $output[] = '<a name="top"></a>';
        $output[] = '<div id="header">  <!-- header section start -->';
        $output[] = '<div id="header1"> <!-- top of banner with institution name/hompage link -->';

        // add quick language list in header: update your institution's CSS accordingly
        // if you want to use this feature
        // css id of the list: header_quick_language
        if (self :: is_allowed_quick_language())
        {
            $languages = self :: get_languages();

            if (count($languages) > 1)
            {
                $output[] = '<ul id="header_quick_language"> <!-- quick language buttons -->';

                $current_language = LocalSetting :: get('platform_language');
                $current_url = Redirect :: current_url();

                foreach ($languages as $isocode => $language)
                {
                    if ($isocode == $current_language)
                    {
                        $item = $isocode;
                    }
                    else
                    {
                        $href = Redirect :: get_link(
                            array(
                                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                                Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_QUICK_LANG,
                                \Chamilo\Core\User\Manager :: PARAM_CHOICE => $isocode,
                                \Chamilo\Core\User\Manager :: PARAM_REFER => $current_url));
                        $item = '<a href="' . $href . '">' . $isocode . '</a>';
                    }

                    $output[] = '<li>' . $item . '</li>';
                }

                $output[] = '</ul>';
            }
        }

        $output[] = '<div class="banner"><a href="' . Path :: getInstance()->getBasePath(true) .
             'index.php" target="_top"><span class="logo">' . PlatformSetting :: get('site_name', 'Chamilo\Core\Admin') .
             '</span></a></div>';

        if (Authentication :: is_valid())
        {
            $registration = \Chamilo\Configuration\Storage\DataManager :: get_registration('Chamilo\Core\Menu');
            if ($registration instanceof Registration && $registration->is_active())
            {
                $output[] = '<div class="applications">';
                $output[] = \Chamilo\Core\Menu\Renderer\Menu\Renderer :: as_html(
                    \Chamilo\Core\Menu\Renderer\Menu\Renderer :: TYPE_BAR,
                    $user);
                $output[] = '<div class="clear">&nbsp;</div>';
                $output[] = '</div>';
            }
            $output[] = '<div class="clear">&nbsp;</div>';
        }

        $output[] = '</div> <!-- end of #header1 -->';

        $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        if ($breadcrumbtrail->size() > 0)
        {
            $output[] = '<div id="trailbox">';

            $output[] = $breadcrumbtrail->render();

            $output[] = '<div class="clear">&nbsp;</div>';
            $output[] = '</div>';
        }

        $output[] = '<div class="clear">&nbsp;</div>';
        $output[] = '</div> <!-- end of the whole #header section -->';

        return implode(PHP_EOL, $output);
    }
}
