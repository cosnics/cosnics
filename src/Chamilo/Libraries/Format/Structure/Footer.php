<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Class to display the footer of a HTML-page
 */
class Footer
{

    /**
     * Create a new Footer
     */
    public function __construct()
    {
    }

    public function get_setting($variable, $application)
    {
        return \Chamilo\Configuration\Configuration :: get($application, $variable);
    }

    /**
     * Returns the HTML code for the footer
     */
    public function toHtml()
    {
        $output = array();

        $output[] = '<div class="clear">&nbsp;</div> <!-- "clearing" div to make sure that footer stays below the main and right column sections -->';
        $output[] = '</div> <!-- end of #main" started at the end of banner.inc.php -->';

        $registration = \Chamilo\Configuration\Storage\DataManager :: get_registration('core\menu');
        if ($registration instanceof Registration && $registration->is_active())
        {
            $show_sitemap = $this->get_setting('show_sitemap', 'core\menu');

            if (Authentication :: is_valid() && $show_sitemap == '1')
            {
                $output[] = '<div id="sitemap">';
                $output[] = '<div class="categories">';
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    (int) Session :: get_user_id());

                $output[] = \Chamilo\Core\Menu\Renderer\Menu\Renderer :: as_html(
                    \Chamilo\Core\Menu\Renderer\Menu\Renderer :: TYPE_SITE_MAP,
                    $user);
                $output[] = '<div class="clear"></div>';
                $output[] = '</div>';
                $output[] = '<div class="clear"></div>';
                $output[] = '</div>';
            }
        }

        $output[] = '<div id="footer"> <!-- start of #footer section -->';
        $output[] = '<div id="copyright">';
        $output[] = '<div class="logo">';
        $output[] = '<a href="http://www.chamilo.org"><img src="' .
             Theme :: getInstance()->getCommonImagePath('LogoFooter') . '" alt="footer"/></a>';
        $output[] = '</div>';
        $output[] = '<div class="links">';

        $links = array();
        $links[] = DatetimeUtilities :: format_locale_date(
            Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' .
                 Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES),
                time());
        $links[] = '<a href="' . $this->get_setting('institution_url', 'Chamilo\Core\Admin') . '" target="about:blank">' .
             $this->get_setting('institution', 'Chamilo\Core\Admin') . '</a>';

        if ($this->get_setting('show_administrator_data', 'Chamilo\Core\Admin') == '1')
        {
            $admin_data = Translation :: get('Manager');
            $admin_data .= ':&nbsp;';

            $administrator_email = $this->get_setting('administrator_email', 'Chamilo\Core\Admin');
            $administrator_website = $this->get_setting('administrator_website', 'Chamilo\Core\Admin');

            if (! empty($administrator_email) && ! empty($administrator_website))
            {
                $email = Display :: encrypted_mailto_link(
                    $this->get_setting('administrator_email', 'Chamilo\Core\Admin'),
                    $this->get_setting('administrator_surname', 'Chamilo\Core\Admin') . ' ' .
                         $this->get_setting('administrator_firstname', 'Chamilo\Core\Admin'));

                $admin_data = Translation :: get(
                    'ManagerContactWebsite',
                    array('EMAIL' => $email, 'WEBSITE' => $administrator_website));
            }
            else
            {
                if (! empty($administrator_email))
                {
                    $admin_data = Translation :: get('Manager');
                    $admin_data .= ':&nbsp;';

                    $admin_data .= Display :: encrypted_mailto_link(
                        $this->get_setting('administrator_email', 'Chamilo\Core\Admin'),
                        $this->get_setting('administrator_surname', 'Chamilo\Core\Admin') . ' ' .
                             $this->get_setting('administrator_firstname', 'Chamilo\Core\Admin'));
                }

                if (! empty($administrator_website))
                {
                    $admin_data = Translation :: get('Support');
                    $admin_data .= ':&nbsp;';

                    $admin_data .= '<a href="' . $administrator_website . '">' .
                         $this->get_setting('administrator_surname', 'Chamilo\Core\Admin') . ' ' .
                         $this->get_setting('administrator_firstname', 'Chamilo\Core\Admin') . '</a>';
                }
            }

            $links[] = $admin_data;
        }

        if ($this->get_setting('show_version_data', 'Chamilo\Core\Admin') == '1')
        {
            $links[] = htmlspecialchars(Translation :: get('Version')) . ' ' .
                 $this->get_setting('version', 'Chamilo\Core\Admin');
        }

        $world = $this->get_setting('whoisonlineaccess', 'Chamilo\Core\Admin');

        if ($world == "1" || (key_exists('_uid', $_SESSION) && $world == "2"))
        {
            $links[] = '<a href="' . htmlspecialchars(
                Path :: getInstance()->getBasePath(true) . 'index.php?go=whois_online&application=admin') . '">' .
                 Translation :: get('WhoisOnline') . '</a>';
        }

        $links[] = '&copy;&nbsp;' . date('Y');

        $output[] = implode('&nbsp;|&nbsp;', $links);

        $output[] = '</div>';
        $output[] = '<div class="clear"></div>';
        $output[] = '</div>';

        $output[] = '   </div> <!-- end of #footer -->';
        $output[] = '  </div> <!-- end of #outerframe opened in header -->';
        $output[] = ' </body>';
        $output[] = '</html>';

        // hidden memory usage in source
        $output[] = '<!-- Memory Usage: ' . memory_get_peak_usage(1) . ' bytes -->';

        return implode(PHP_EOL, $output);
    }
}
