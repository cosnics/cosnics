<?php
namespace Chamilo\Core\Install;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Filesystem;

/**
 * $Id: install_manager.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager
 */
/**
 * An install manager provides some functionalities to the end user to install his Chamilo platform
 *
 * @author Hans De Bisschop
 */
abstract class Manager extends Application implements NoContextComponent
{
    const DEFAULT_ACTION = self :: ACTION_INTRODUCTION;

    /**
     * Constant defining an action of the repository manager.
     */
    const ACTION_INSTALL_PLATFORM = 'installer';
    const ACTION_INTRODUCTION = 'introduction';
    const ACTION_REQUIREMENTS = 'requirements';
    const ACTION_LICENSE = 'license';
    const ACTION_SETTINGS = 'settings';
    const ACTION_OVERVIEW = 'overview';

    // Parameters
    const PARAM_LANGUAGE = 'language';

    /**
     * Property of this repository manager.
     */
    private $breadcrumbs;

    /**
     * Constructor
     *
     * @param $user_id int The user id of current user
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "7200");
        parent :: __construct($applicationConfiguration);

        $language = $this->getRequest()->query->get(self :: PARAM_LANGUAGE);

        if ($language)
        {
            Translation :: getInstance()->setLanguageIsocode($language);
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header()
    {
        $page = Page :: getInstance();
        $page->setApplication($this);

        $html = array();

        $html[] = $page->getHeader()->toHtml();
        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-sm-2"></div>';
        $html[] = '<div class="col-xs-12 col-sm-8">';

        $html[] = '<div class="alert alert-info">';
        $html[] = $this->getInfo();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getInfo()
    {
        return Translation :: get(ClassnameUtilities :: getInstance()->getClassnameFromObject($this) . 'Information');
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_footer()
     */
    public function render_footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '<div class="col-xs-12 col-sm-2"></div>';
        $html[] = '</div>';
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getLanguages()
    {
        $language_path = Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/I18n/';
        $language_files = Filesystem :: get_directory_content($language_path, Filesystem :: LIST_FILES, false);

        $language_list = array();
        foreach ($language_files as $language_file)
        {
            $file_info = pathinfo($language_file);
            $language_info_file = $language_path . $file_info['filename'] . '.info';

            if (file_exists($language_info_file))
            {
                $dom_document = new \DOMDocument('1.0', 'UTF-8');
                $dom_document->load($language_info_file);
                $dom_xpath = new \DOMXPath($dom_document);

                $language_node = $dom_xpath->query('/packages/package')->item(0);

                $language_list[$dom_xpath->query('extra/isocode', $language_node)->item(0)->nodeValue] = $dom_xpath->query(
                    'name',
                    $language_node)->item(0)->nodeValue;
            }
        }

        return $language_list;
    }
}
