<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReportExporter
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer
     */
    protected $reportRenderer;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected $configurablePathBuilder;

    /**
     * @var \Chamilo\Libraries\Format\Theme
     */
    protected $themeUtilities;

    /**
     * @var \Twig_Environment
     */
    protected $twigRenderer;

    /**
     * ReportExporter constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer $reportRenderer
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Twig_Environment $twigRenderer
     */
    public function __construct(
        ReportRenderer $reportRenderer, ContentObjectRepository $contentObjectRepository,
        ConfigurablePathBuilder $configurablePathBuilder, Theme $themeUtilities, \Twig_Environment $twigRenderer
    )
    {
        $this->reportRenderer = $reportRenderer;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->themeUtilities = $themeUtilities;
        $this->twigRenderer = $twigRenderer;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $request
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function exportRequestReport(Request $request)
    {
        $content_object = $this->contentObjectRepository->findById($request->get_content_object_id());

        $parameters = [
            'CSS' => file_get_contents(
                $this->themeUtilities->getCssPath(
                    'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service', false
                ) . 'Report.css'
            ),
            'REPORT' => $this->reportRenderer->renderRequestReport($request)
        ];

        $exportHTML = $this->twigRenderer->render(
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus:EphorusReportExport.html.twig', $parameters
        );

        $unique_file_name = Filesystem::create_unique_name(
            $this->configurablePathBuilder->getTemporaryPath(),
            $content_object->get_title() . '.html'
        );

        $full_file_name = $this->configurablePathBuilder->getTemporaryPath() . $unique_file_name;
        Filesystem::create_dir(dirname($full_file_name));
        Filesystem::write_to_file($full_file_name, $exportHTML);
        Filesystem::file_send_for_download($full_file_name, true);
        Filesystem::remove($full_file_name);
    }
}