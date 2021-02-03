<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Domain\Exception\LTIException;
use Chamilo\Application\Lti\Domain\Exception\ParseMessageException;
use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;

/**
 * Class MessageParser
 *
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class MessageParser
{
    /**
     * @var \Chamilo\Application\Lti\Service\Outcome\ResultIdEncoder
     */
    protected $resultIdEncoder;

    /**
     * MessageParser constructor.
     *
     * @param \Chamilo\Application\Lti\Service\Outcome\ResultIdEncoder $resultIdEncoder
     */
    public function __construct(\Chamilo\Application\Lti\Service\Outcome\ResultIdEncoder $resultIdEncoder)
    {
        $this->resultIdEncoder = $resultIdEncoder;
    }

    /**
     * @param string $message
     *
     * @return \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage
     */
    public function parseMessage(string $message)
    {
        $domDocument = new \DOMDocument();
        if (!$domDocument->loadXML($message))
        {
            throw new ParseMessageException('The message does not appear to be a valid XML message');
        }

        $domXPath = new \DOMXPath($domDocument);
        $domXPath->registerNamespace('ims', 'http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0');

        $messageIdNode =
            $domXPath->query('//ims:imsx_POXHeader/ims:imsx_POXRequestHeaderInfo/ims:imsx_messageIdentifier')->item(0);

        $operationNode = null;

        $domNodeList = $domXPath->query('//ims:imsx_POXBody');
        foreach ($domNodeList as $domNode)
        {
            /** @var \DOMElement $domNode */
            $childNodes = $domNode->childNodes;
            foreach ($childNodes as $childNode)
            {
                /** @var \DOMNode $childNode */
                if ($childNode->nodeName == '#text')
                {
                    continue;
                }

                $operationNode = $childNode;
                break;
            }
        }

        $resultIdNode =
            $domXPath->query('//ims:resultRecord/ims:sourcedGUID/ims:sourcedId', $operationNode)->item(0);
        $resultScoreNode =
            $domXPath->query('//ims:resultRecord/ims:result/ims:resultScore/ims:textString', $operationNode)->item(
                0
            );

        $messageId = empty($messageIdNode) ? null : $messageIdNode->textContent;
        $score = empty($resultScoreNode) ? 0.0 : floatval($resultScoreNode->textContent);
        $operation = empty($operationNode) ? null : $operationNode->nodeName;
        $result = empty($resultIdNode) ? null : $resultIdNode->textContent;

        if (empty($messageId))
        {
            throw new ParseMessageException('The message does not contain a valid messageIdentifier');
        }

        if (empty($operation))
        {
            throw new ParseMessageException('The message does not contain an operation');
        }

        if (empty($result))
        {
            throw new ParseMessageException('The result sourcedId should not be empty');
        }

        $operation = str_replace('Request', '', $operation);

        try
        {
            $resultArray = $this->resultIdEncoder->decodeResultId($result);
        }
        catch(LTIException $ex)
        {
            throw new ParseMessageException($ex->getMessage());
        }

        return new OutcomeMessage(
            $messageId, $resultArray['integrationClass'], $resultArray['resultId'], $operation, $score
        );
    }
}