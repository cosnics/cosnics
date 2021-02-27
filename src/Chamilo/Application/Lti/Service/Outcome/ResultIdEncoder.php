<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Domain\Exception\LTIException;
use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;

/**
 * Encodes / Decodes the result ID from the LTI messages to encapsulate the integration class that handles the result
 *
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultIdEncoder
{
    /**
     * @param string $integrationClass
     * @param string $resultIdentifier
     *
     * @return string
     */
    public function encodeResultId(string $integrationClass, string $resultIdentifier)
    {
        if (!is_subclass_of($integrationClass, IntegrationInterface::class))
        {
            throw new LTIException(
                sprintf(
                    'The given integration class %s does not implement the IntegrationInterface class',
                    $integrationClass
                )
            );
        }

        $encodedResult = base64_encode(
            json_encode(['integrationClass' => $integrationClass, 'resultId' => $resultIdentifier])
        );

        if(empty($encodedResult))
        {
            throw new LTIException('The result id for the LTI message could not be encoded');
        }

        return $encodedResult;
    }

    /**
     * @param string $resultId
     *
     * @return string[]
     */
    public function decodeResultId(string $resultId)
    {
        $resultArray = json_decode(base64_decode($resultId), true);
        if (empty($resultArray))
        {
            throw new LTIException('The result sourcedID could not be parsed to a valid result');
        }

        if (empty($resultArray['integrationClass']))
        {
            throw new LTIException(
                'The integration handler could not be determined from the result sourcedID'
            );
        }

        if (empty($resultArray['resultId']))
        {
            throw new LTIException(
                'The result id could not be determined from the result sourcedID'
            );
        }

        return $resultArray;
    }
}
