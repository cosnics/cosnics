<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\FilesystemTools;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
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
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        protected readonly ConfigurablePathBuilder $configurablePathBuilder,
        protected readonly FilesystemTools $filesystemTools, protected readonly ResourceManager $resourceManager,
        protected readonly WebPathBuilder $webPathBuilder,
        protected readonly ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \TableException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->renderButtonToolBar();
        $html[] = $this->displayLogfileTable($this->getLogFile(), $this->getLineCount());
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
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
            $logFilePath = $this->configurablePathBuilder->getLogPath() . $logFile;

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

        foreach ($lines as $lineIndex => $lineContent) {
            $lineClass = null;

            if (str_contains($lineContent, 'error') || str_contains($lineContent, '[ERROR]') ||
                str_contains($lineContent, '[FATAL]')) {
                $lineClass = 'text-bg-danger';
            }
            elseif (str_contains($lineContent, 'warning') || str_contains($lineContent, '[WARNING]')) {
                $lineClass = 'text-bg-warning';
            }

            $table->setCellContents($lineIndex, 0, $lineContent);
            $table->setCellAttributes($lineIndex, 0, ['class' => $lineClass]);
        }

        return $table->toHtml();
    }

    protected function getLineCount(): int
    {
        return $this->request->query->get('line_count', 10);
    }

    protected function getLogFile(): string
    {
        $phpErrorLogPath = ini_get('error_log');

        return $this->request->query->get('log_file', basename($phpErrorLogPath));
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderButtonToolBar(): string
    {
        $buttonToolBar = new ButtonToolBar();

        $fileButton = new DropDownButtonCollection($this->translator->trans('LogFiles', [], Manager::CONTEXT),
            new FontAwesomeGlyph('file-alt', ['me-1'], null, 'fas'));

        $dir = $this->configurablePathBuilder->getLogPath();
        $content = $this->filesystemTools->getDirectoryContent($dir, FileTypeFilterIterator::ONLY_FILES, false);

        $phpErrorLogPath = ini_get('error_log');
        $phpErrorFileName = basename($phpErrorLogPath);

        $fileButton->addButton(
            new SubButton(
                label: $phpErrorFileName, action: $this->urlGenerator->fromRequest(
                ['log_file' => $phpErrorFileName]
            ), state: $this->getLogFile() == $phpErrorFileName
            )
        );

        foreach ($content->name('*.log') as $file) {
            $fileButton->addButton(
                new SubButton(
                    label: $file->getFilename(), action: $this->urlGenerator->fromRequest(
                    ['log_file' => $file->getFilename()]
                ), state: $this->getLogFile() == $file->getFilename()
                )
            );
        }

        $buttonToolBar->addButton($fileButton);

        $linesButton = new DropDownButtonCollection($this->translator->trans('Lines', [], Manager::CONTEXT),
            new FontAwesomeGlyph('hashtag', ['me-1'], null, 'fas'));

        $linesButton->addButton(
            new SubButton(
                label: '10 ' . $this->translator->trans('Lines', [], Manager::CONTEXT),
                action: $this->urlGenerator->fromRequest(['line_count' => 10]), state: $this->getLineCount() == 10
            )
        );
        $linesButton->addButton(
            new SubButton(
                label: '20 ' . $this->translator->trans('Lines', [], Manager::CONTEXT),
                action: $this->urlGenerator->fromRequest(['line_count' => 20]), state: $this->getLineCount() == 20
            )
        );
        $linesButton->addButton(
            new SubButton(
                label: '50 ' . $this->translator->trans('Lines', [], Manager::CONTEXT),
                action: $this->urlGenerator->fromRequest(['line_count' => 50]), state: $this->getLineCount() == 50
            )
        );
        $linesButton->addButton(
            new SubButton(label: $this->translator->trans('AllLines', [], Manager::CONTEXT),
                action: $this->urlGenerator->fromRequest(['line_count' => 0]), state: $this->getLineCount() == 0)
        );

        $buttonToolBar->addButton($linesButton);

        return $this->buttonToolBarRenderer->render($buttonToolBar);
    }
}
