<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_button_submit;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use HTML_QuickForm_html;
use HTML_Table;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewLogsComponent extends Manager
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ResourceManager $resourceManager;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, ResourceManager $resourceManager,
        ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator);

        $this->resourceManager = $resourceManager;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $form = $this->buildForm();

        $html[] = $this->renderHeader($currentUser);
        $html[] = $form->render();

        if ($form->validate()) {
            $logFile = $form->exportValue('log_file');
            $lineCount = $form->exportValue('line_count');
        }
        else {
            $phpErrorLogPath = ini_get('error_log');
            $logFile = basename($phpErrorLogPath);
            $lineCount = 10;
        }

        $html[] = $this->displayLogfileTable($logFile, $lineCount);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): FormValidator
    {
        $form =
            new FormValidator('logviewer', FormValidator::FORM_METHOD_POST, $this->getUrlGenerator()->fromRequest());
        $translator = $this->getTranslator();

        $lines = [
            10 => '10 ' . $translator->trans('Lines', [], Manager::CONTEXT),
            20 => '20 ' . $translator->trans('Lines', [], Manager::CONTEXT),
            50 => '50 ' . $translator->trans('Lines', [], Manager::CONTEXT),
            0 => $translator->trans('AllLines', [], Manager::CONTEXT)
        ];

        $dir = $this->getConfigurablePathBuilder()->getLogPath();
        $content = $this->getFilesystemTools()->getDirectoryContent($dir, FileTypeFilterIterator::ONLY_FILES, false);

        $phpErrorLogPath = ini_get('error_log');
        $phpErrorFileName = basename($phpErrorLogPath);

        $files = [$phpErrorFileName => $phpErrorFileName];

        foreach ($content->name('*.log') as $file) {
            $files[$file->getFilename()] = $file->getFilename();
        }

        $form->addElement(HTML_QuickForm_html::class, '<div class="row">');

        $form->addElement(HTML_QuickForm_html::class, '<div class="col-auto">');
        $form->addSelect('log_file', $translator->trans('LogFile', [], Manager::CONTEXT), $files, false);
        $form->getRenderer()->setElementTemplate($this->getSelectTemplate(), 'log_file');
        $form->addElement(HTML_QuickForm_html::class, '</div>');

        $form->addElement(HTML_QuickForm_html::class, '<div class="col-auto">');
        $form->addSelect('line_count', $translator->trans('Linecount', [], Manager::CONTEXT), $lines, false);
        $form->getRenderer()->setElementTemplate($this->getSelectTemplate(), 'line_count');
        $form->addElement(HTML_QuickForm_html::class, '</div>');

        $form->addElement(HTML_QuickForm_html::class, '<div class="col-auto">');
        $form->addElement(
            HTML_QuickForm_button_submit::class, 'submit', $translator->trans('Ok', [], StringUtilities::LIBRARIES),
            ['class' => 'positive finish']
        );
        $form->addElement(HTML_QuickForm_html::class, '</div>');
        $form->addElement(HTML_QuickForm_html::class, '</div>');

        $form->addElement(
            HTML_QuickForm_html::class, $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath() . 'LogViewer.js'
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
        $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover']);

        $phpErrorLogPath = ini_get('error_log');
        $phpErrorFileName = basename($phpErrorLogPath);

        if ($logFile == $phpErrorFileName) {
            $logFilePath = $phpErrorLogPath;
        }
        else {
            $logFilePath = $this->getConfigurablePathBuilder()->getLogPath() . $logFile;

            if (!file_exists($logFilePath)) {
                return '<div class="alert alert-warning">' .
                    $translator->trans('NoLogfilesFound', [], Manager::CONTEXT) . '</div>';
            }
        }

        $string = trim(file_get_contents($logFilePath));

        $lines = preg_split('[\n]', $string);
        $lines = array_reverse($lines);

        if ($lineCount !== 0 || count($lines) < $lineCount) {
            $lines = array_slice($lines, 0, $lineCount);
        }

        foreach ($lines as $i => $line) {
            $lineClass = null;

            if (str_contains($line, 'error') || str_contains($line, '[ERROR]') || str_contains($line, '[FATAL]')) {
                $lineClass = 'text-bg-danger';
            }
            elseif (str_contains($line, 'warning') || str_contains($line, '[WARNING]')) {
                $lineClass = 'text-bg-warning';
            }

            $table->setCellContents($i, 0, $line);
            $table->setCellAttributes($i, 0, ['class' => $lineClass]);
        }

        return $table->toHtml();
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getSelectTemplate(): string
    {
        $html = [];
        $glyph = new FontAwesomeGlyph('asterisk', ['text-danger', 'fa-2xs'], null, 'fas');

        $html[] = '<div class="mb-3 clearfix">';
        $html[] = '    {element}';
        $html[] = '    <label class="visually-hidden">';
        $html[] = '        {label}';
        $html[] = '        <!-- BEGIN required -->';
        $html[] = '        <span class="text-danger ms-1">' . $glyph->render() . '</span>';
        $html[] = '        <!-- END required -->';
        $html[] = '    </label>';
        $html[] = '    <!-- BEGIN error -->';
        $html[] = '    <div class="invalid-feedback">{error}</div>';
        $html[] = '    <!-- END error -->';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
