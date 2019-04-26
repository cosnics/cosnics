<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Printer\PrintableFormatRenderer;

/**
 * Class PrintableViewerComponent
 *
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class PrintableViewerComponent extends Manager
{
    /**
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $viewUrl = $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                self::PARAM_CHILD_ID => '__TREE_NODE_ID__'
            )
        );

        $printableFormatRenderer = $this->getPrintableFormatRenderer();

        return $printableFormatRenderer->render(
            $this->learningPath, $this->getUser(), $this->getTree(), $viewUrl, $this->getTrackingService(),
            $this->canAuditLearningPath(),
            $this->get_application()->getContextTitleForPrint()
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Printer\PrintableFormatRenderer
     */
    protected function getPrintableFormatRenderer()
    {
        return $this->getService(PrintableFormatRenderer::class);
    }
}