<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Twig\Environment;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ReportExporter
{

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ContentObjectRepository $contentObjectRepository;

    protected \Symfony\Component\Filesystem\Filesystem $filesystem;

    protected FilesystemTools $filesystemTools;

    protected ReportRenderer $reportRenderer;

    protected ThemePathBuilder $themeWebPathBuilder;

    protected Environment $twigRenderer;

    public function __construct(
        ReportRenderer $reportRenderer, ContentObjectRepository $contentObjectRepository,
        ConfigurablePathBuilder $configurablePathBuilder, ThemePathBuilder $themePathBuilder, Environment $twigRenderer,
        \Symfony\Component\Filesystem\Filesystem $filesystem, FilesystemTools $filesystemTools
    )
    {
        $this->reportRenderer = $reportRenderer;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->themeWebPathBuilder = $themePathBuilder;
        $this->twigRenderer = $twigRenderer;
        $this->filesystem = $filesystem;
        $this->filesystemTools = $filesystemTools;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $request
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function exportRequestReport(Request $request)
    {
        $content_object = $this->contentObjectRepository->findById($request->get_content_object_id());

        $parameters = [
            'CSS' => file_get_contents(
                $this->themeWebPathBuilder->getCssPath(
                    'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service', false
                ) . 'Report.css'
            ),
            'REPORT' => $this->reportRenderer->renderRequestReport($request)
        ];

        $exportHTML = $this->twigRenderer->render(
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus:EphorusReportExport.html.twig', $parameters
        );

        $unique_file_name = $this->filesystemTools->createUniqueName(
            $this->configurablePathBuilder->getTemporaryPath(), $content_object->get_title() . '.html'
        );

        $full_file_name = $this->configurablePathBuilder->getTemporaryPath() . $unique_file_name;
        $this->filesystem->mkdir(dirname($full_file_name));
        $this->filesystem->dumpFile($full_file_name, $exportHTML);
        $this->filesystemTools->sendFileForDownload($full_file_name);
        $this->filesystem->remove($full_file_name);
    }
}