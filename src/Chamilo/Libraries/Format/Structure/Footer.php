<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Footer
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var integer
     */
    private $viewMode;

    /**
     *
     * @param integer $viewMode
     */
    public function __construct($viewMode = Page :: VIEW_MODE_FULL)
    {
        $this->viewMode = $viewMode;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return integer
     */
    public function getViewMode()
    {
        return $this->viewMode;
    }

    /**
     *
     * @param integer $viewMode
     */
    public function setViewMode($viewMode)
    {
        $this->viewMode = $viewMode;
    }

    /**
     * Returns the HTML code for the footer
     */
    public function toHtml()
    {
        $output = array();

        $output[] = '</div> <!-- end of .container-fluid" -->';

        if ($this->getViewMode() != Page :: VIEW_MODE_HEADERLESS)
        {
            $output[] = '<div class="footer clearfix"> <!-- start of #footer section -->';
            $output[] = '<div class="pull-right">';
            $output[] = '<a href="http://www.chamilo.org"><img src="' .
                 Theme :: getInstance()->getCommonImagePath('LogoFooter') . '" alt="footer"/></a>';
            $output[] = '</div>';
            $output[] = '<div class="pull-left">';

            $links = array();
            $links[] = DatetimeUtilities :: format_locale_date(
                Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' .
                     Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES),
                    time());
            $links[] = '<a href="' . \Chamilo\Configuration\Configuration :: get(
                'Chamilo\Core\Admin',
                'institution_url') . '" target="about:blank">' . \Chamilo\Configuration\Configuration :: get(
                'Chamilo\Core\Admin',
                'institution') . '</a>';

            if (\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'show_administrator_data') == '1')
            {
                $admin_data = Translation :: get('Manager');
                $admin_data .= ':&nbsp;';

                $administrator_email = \Chamilo\Configuration\Configuration :: get(
                    'Chamilo\Core\Admin',
                    'administrator_email');
                $administrator_website = \Chamilo\Configuration\Configuration :: get(
                    'Chamilo\Core\Admin',
                    'administrator_website');

                if (! empty($administrator_email) && ! empty($administrator_website))
                {
                    $email = StringUtilities :: getInstance()->encryptMailLink(
                        \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'administrator_email'),
                        \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'administrator_surname') . ' ' . \Chamilo\Configuration\Configuration :: get(
                            'Chamilo\Core\Admin',
                            'administrator_firstname'));

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

                        $admin_data .= StringUtilities :: getInstance()->encryptMailLink(
                            \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'administrator_email'),
                            \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'administrator_surname') .
                                 ' ' . \Chamilo\Configuration\Configuration :: get(
                                    'Chamilo\Core\Admin',
                                    'administrator_firstname'));
                    }

                    if (! empty($administrator_website))
                    {
                        $admin_data = Translation :: get('Support');
                        $admin_data .= ':&nbsp;';

                        $admin_data .= '<a href="' . $administrator_website . '">' .
                             \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'administrator_surname') .
                             ' ' . \Chamilo\Configuration\Configuration :: get(
                                'Chamilo\Core\Admin',
                                'administrator_firstname') . '</a>';
                    }
                }

                $links[] = $admin_data;
            }

            if (\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'show_version_data') == '1')
            {
                $links[] = htmlspecialchars(Translation :: get('Version')) . ' ' .
                     \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'version');
            }

            $world = \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'whoisonlineaccess');

            if ($world == "1" || (key_exists('_uid', $_SESSION) && $world == "2"))
            {
                $redirect = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                        Application :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_WHOIS_ONLINE));

                $links[] = '<a href="' . htmlspecialchars($redirect->getUrl()) . '">' . Translation :: get(
                    'WhoisOnline') . '</a>';
            }

            $links[] = '&copy;&nbsp;' . date('Y');

            $output[] = implode('&nbsp;|&nbsp;', $links);

            $output[] = '<div class="clearfix"></div>';
            $output[] = '</div>';

            $output[] = '   </div> <!-- end of #footer -->';
        }

        $output[] = ' </body>';
        $output[] = '</html>';

        // hidden memory usage in source
        $output[] = '<!-- Memory Usage: ' . memory_get_peak_usage(1) . ' bytes -->';

        return implode(PHP_EOL, $output);
    }
}
