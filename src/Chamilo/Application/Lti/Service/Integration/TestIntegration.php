<?php

namespace Chamilo\Application\Lti\Service\Integration;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class TestIntegration implements IntegrationInterface
{

    /**
     * Replaces the result for the given identifier with the given score. Throw an LTIException on failure
     *
     * @param string $resultId
     * @param float $score
     */
    public function replaceResult(string $resultId, float $score)
    {
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'result.txt', $score);
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
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'result.txt');
    }

    /**
     * Deletes the result for the given identifier. Throw an LTIException on failure
     *
     * @param string $resultId
     */
    public function deleteResult(string $resultId)
    {
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'result.txt', '');
    }
}