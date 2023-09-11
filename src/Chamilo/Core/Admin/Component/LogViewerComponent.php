<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_Table;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * @package Chamilo\Core\Admin\Component
 */
class LogViewerComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        $form = $this->buildForm();

        $html[] = $this->renderHeader();
        $html[] = $form->render() . '<br />';

        if ($form->validate())
        {
            $logFile = $form->exportValue('log_file');
            $lineCount = $form->exportValue('line_count');
        }
        else
        {
            $phpErrorLogPath = ini_get('error_log');
            $logFile = basename($phpErrorLogPath);
            $lineCount = '10';
        }

        $html[] = $this->displayLogfileTable($logFile, $lineCount);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): FormValidator
    {
        $form = new FormValidator('logviewer', FormValidator::FORM_METHOD_POST, $this->get_url());
        $translator = $this->getTranslator();

        $renderer = $form->defaultRenderer();
        $renderer->setElementTemplate(' {element} ');

        $lines = [
            '10' => '10 ' . $translator->trans('Lines', [], Manager::CONTEXT),
            '20' => '20 ' . $translator->trans('Lines', [], Manager::CONTEXT),
            '50' => '50 ' . $translator->trans('Lines', [], Manager::CONTEXT),
            'all' => $translator->trans('AllLines', [], Manager::CONTEXT)
        ];

        $dir = $this->getConfigurablePathBuilder()->getLogPath();
        $content = $this->getFilesystemTools()->getDirectoryContent($dir, FileTypeFilterIterator::ONLY_FILES, false);

        $phpErrorLogPath = ini_get('error_log');
        $phpErrorFileName = basename($phpErrorLogPath);

        $files = [$phpErrorFileName => $phpErrorFileName];

        foreach ($content->name('*.log') as $file)
        {
            $files[$file->getFilename()] = $file->getFilename();
        }

        $form->addElement('select', 'log_file', '', $files);
        $form->addElement('select', 'line_count', '', $lines);

        $form->addElement(
            'submit', 'submit', $translator->trans('Ok', [], StringUtilities::LIBRARIES), ['class' => 'positive finish']
        );
        $form->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Admin') . 'LogViewer.js'
        )
        );

        return $form;
    }

    /**
     * @throws \TableException
     */
    public function displayLogfileTable(string $logFile, int $lineCount): string
    {
        $translator = $this->getTranslator();
        $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-data']);

        $phpErrorLogPath = ini_get('error_log');
        $phpErrorFileName = basename($phpErrorLogPath);

        if ($logFile == $phpErrorFileName)
        {
            $logFilePath = $phpErrorLogPath;
        }
        else
        {
            $logFilePath = $this->getConfigurablePathBuilder()->getLogPath() . $logFile;

            if (!file_exists($logFilePath))
            {
                return '<div class="warning-message">' . $translator->trans('NoLogfilesFound', [], Manager::CONTEXT) .
                    '</div>';
            }
        }

        $string = trim(file_get_contents($logFilePath));

        $lines = explode(PHP_EOL, $string);
        $lines = array_reverse($lines);

        if ($lineCount != 'all' || count($lines) < $lineCount)
        {
            $lines = array_slice($lines, 0, $lineCount);
        }

        foreach ($lines as $i => $line)
        {
            $lineClass = null;

            if (str_contains($line, 'error') || str_contains($line, '[ERROR]') || str_contains($line, '[FATAL]'))
            {
                $lineClass = 'bg-danger';
            }
            elseif (str_contains($line, 'warning') || str_contains($line, '[WARNING]'))
            {
                $lineClass = 'bg-warning';
            }

            $table->setCellContents($i, 0, $line);
            $table->setCellAttributes($i, 0, ['class' => $lineClass]);
        }

        return $table->toHtml();
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
