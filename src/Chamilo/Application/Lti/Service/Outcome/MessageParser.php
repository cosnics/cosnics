<?php

namespace Chamilo\Application\Lti\Service\Outcome;

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

        $resultActionNode = null;

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

                $resultActionNode = $childNode;
                break;
            }
        }

        $resultIdNode =
            $domXPath->query('//ims:resultRecord/ims:sourcedGUID/ims:sourcedId', $resultActionNode)->item(0);
        $resultScoreNode =
            $domXPath->query('//ims:resultRecord/ims:result/ims:resultScore/ims:textString', $resultActionNode)->item(
                0
            );

        $score = empty($resultScoreNode) ? 0.0 : floatval($resultScoreNode->textContent);
        $action = $resultActionNode->nodeName;
        $resultId = $resultIdNode->textContent;

        if(empty($action))
        {
            throw new ParseMessageException('The message does not contain an action');
        }

        if(empty($resultId))
        {
            throw new ParseMessageException('The result sourcedId should not be empty');
        }

        return new OutcomeMessage($resultId, $action, $score);
    }
}