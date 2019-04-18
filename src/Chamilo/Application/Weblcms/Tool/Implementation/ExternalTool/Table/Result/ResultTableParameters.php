<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultTableParameters
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService
     */
    protected $externalToolResultService;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * ResultTableParameters constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService $externalToolResultService
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService $externalToolResultService,
        \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
    )
    {
        $this->externalToolResultService = $externalToolResultService;
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService
     */
    public function getExternalToolResultService(
    ): \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService
    {
        return $this->externalToolResultService;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    public function getContentObjectPublication(
    ): \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
    {
        return $this->contentObjectPublication;
    }

}