<?php

namespace Chamilo\Application\Weblcms\Bridge\ExternalTool\Service;

use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\ExternalTool\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LTIIntegration implements IntegrationInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService
     */
    protected $externalToolResultService;

    /**
     * LTIIntegration constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService $externalToolResultService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService $externalToolResultService
    )
    {
        $this->externalToolResultService = $externalToolResultService;
    }

    /**
     * Replaces the result for the given identifier with the given score. Throw an LTIException on failure
     *
     * @param string $resultId
     * @param float $score
     */
    public function replaceResult(string $resultId, float $score)
    {
        $this->externalToolResultService->updateResultByIdAndLTIScore($resultId, $score);
    }

    /**
     * Returns the result as a floating number between 0.0 and 1.0. Throw an LTIException on failure
     *
     * @param string $resultId
     *
     * @return float
     */
    public function readResult(string $resultId)
    {
        $externalToolResult = $this->externalToolResultService->getResultById($resultId);
        return $externalToolResult->toLTIResult();
    }

    /**
     * Deletes the result for the given identifier. Throw an LTIException on failure
     *
     * @param string $resultId
     */
    public function deleteResult(string $resultId)
    {
        $this->externalToolResultService->deleteResultById($resultId);
    }
}