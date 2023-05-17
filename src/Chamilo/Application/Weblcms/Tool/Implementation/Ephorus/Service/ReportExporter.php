<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
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

    protected ReportRenderer $reportRenderer;

    protected ThemePathBuilder $themeWebPathBuilder;

    protected Environment $twigRenderer;

    public function __construct(
        ReportRenderer $reportRenderer, ContentObjectRepository $contentObjectRepository,
        ConfigurablePathBuilder $configurablePathBuilder, ThemePathBuilder $themePathBuilder, Environment $twigRenderer
    )
    {
        $this->reportRenderer = $reportRenderer;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->themeWebPathBuilder = $themePathBuilder;
        $this->twigRenderer = $twigRenderer;
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

        $unique_file_name = Filesystem::create_unique_name(
            $this->configurablePathBuilder->getTemporaryPath(), $content_object->get_title() . '.html'
        );

        $full_file_name = $this->configurablePathBuilder->getTemporaryPath() . $unique_file_name;
        Filesystem::create_dir(dirname($full_file_name));
        Filesystem::write_to_file($full_file_name, $exportHTML);
        Filesystem::file_send_for_download($full_file_name, true);
        Filesystem::remove($full_file_name);
    }
}