<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_Table;

/**
 * @package admin.lib.admin_manager.component
 */

/**
 * Admin component
 */
class LogViewerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        $form = $this->build_form();

        $html[] = $this->render_header();
        $html[] = $form->toHtml() . '<br />';

        if ($form->validate())
        {
            $type = $form->exportValue('type');
            $chamilo_type = $form->exportValue('chamilo_type');
            $server_type = $form->exportValue('server_type');
            $lines = $form->exportValue('lines');
        }
        else
        {
            $type = 'chamilo';

            $dir = $this->getConfigurablePathBuilder()->getLogPath();
            $content = Filesystem::get_directory_content($dir, Filesystem::LIST_FILES, false);

            $chamilo_type = $content[0];
            $lines = '10';
        }

        $html[] = $this->display_logfile_table($type, $chamilo_type, $server_type, $lines);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function build_form()
    {
        $form = new FormValidator('logviewer', FormValidator::FORM_METHOD_POST, $this->get_url());
        $renderer = &$form->defaultRenderer();
        $renderer->setElementTemplate(' {element} ');

        $types = ['server' => Translation::get('ServerLogs')];

        $file = $this->getConfigurablePathBuilder()->getLogPath();
        $scan_list = scandir($file);

        foreach ($scan_list as $i => $item)
        {
            if (substr($item, 0, 1) == '.')
            {
                unset($scan_list[$i]);
            }
        }

        if (count($scan_list) > 0)
        {
            $types['chamilo'] = Translation::get('ChamiloLogs');
        }

        $lines = [
            '10' => '10 ' . Translation::get('Lines'),
            '20' => '20 ' . Translation::get('Lines'),
            '50' => '50 ' . Translation::get('Lines'),
            'all' => Translation::get('AllLines')
        ];

        $dir = $this->getConfigurablePathBuilder()->getLogPath();
        $content = Filesystem::get_directory_content($dir, Filesystem::LIST_FILES, false);
        foreach ($content as $file)
        {
            if (substr($file, 0, 1) == '.')
            {
                continue;
            }

            $files[$file] = $file;
        }

        $server_types = [
            'php' => Translation::get('PHPErrorLog'),
            'httpd' => Translation::get('HTTPDErrorLog'),
            'mysql' => Translation::get('MYSQLErrorLog')
        ];

        $form->addElement('select', 'type', '', $types, ['id' => 'type']);
        $form->addElement('select', 'chamilo_type', '', $files, ['id' => 'chamilo_type']);

        $form->addElement('select', 'server_type', '', $server_types, ['id' => 'server_type']);
        $form->addElement('select', 'lines', '', $lines);

        $form->addElement(
            'submit', 'submit', Translation::get('Ok', [], StringUtilities::LIBRARIES), ['class' => 'positive finish']
        );
        $form->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Admin') . 'LogViewer.js'
        )
        );

        return $form;
    }

    public function display_logfile_table($type, $chamilo_type, $server_type, $count)
    {
        if ($type == 'chamilo')
        {
            $file = $this->getConfigurablePathBuilder()->getLogPath() . $chamilo_type;
            $message = Translation::get('NoLogfilesFound');
        }
        else
        {
            $file = Configuration::getInstance()->get_setting(
                ['Chamilo\Core\Admin', $server_type . '_error_location']
            );
            $message = Translation::get('ServerLogfileLocationNotDefined');
        }

        if (!file_exists($file) || is_dir($file))
        {
            return '<div class="warning-message">' . $message . '</div>';
        }

        $table = new HTML_Table(['style' => 'background-color: lightblue; width: 100%;', 'cellspacing' => 0]);
        $this->read_file($file, $table, $count);

        return $table->toHtml();
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    public function read_file($file, $table, $count)
    {
        $fh = fopen($file, 'r');
        $string = file_get_contents($file);
        $lines = explode(PHP_EOL, $string);
        $lines = array_reverse($lines);

        if ($count == 'all' || count($lines) < $count)
        {
            $count = count($lines) - 1;
        }

        $row = 0;
        foreach ($lines as $line)
        {
            if ($row >= $count)
            {
                break;
            }

            if ($line == '')
            {
                continue;
            }

            $border = ($row < $count - 1) ? 'border-bottom: 1px solid black;' : '';
            // $color = ($row % 2 == 0) ? 'background-color: yellow;' : '';

            if (stripos($line, 'error') !== false)
            {
                $color = 'background-color: red;';
            }
            elseif (stripos($line, 'warning') !== false)
            {
                $color = 'background-color: pink;';
            }
            else
            {
                $color = null;
            }

            $table->setCellContents($row, 0, $line);
            $table->setCellAttributes($row, 0, ['style' => "$border $color padding: 5px;"]);
            $row ++;
        }

        fclose($fh);
    }
}
