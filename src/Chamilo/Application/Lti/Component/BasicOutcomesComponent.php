<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Domain\Application;
use Chamilo\Application\Lti\Domain\Outcome\ResultMessage;
use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\Security\OAuthDataStore;
use Chamilo\Application\Lti\Service\Security\OAuthSecurity;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use IMSGlobal\LTI\OAuth\OAuthConsumer;
use IMSGlobal\LTI\OAuth\OAuthRequest;
use IMSGlobal\LTI\OAuth\OAuthServer;
use IMSGlobal\LTI\OAuth\OAuthSignatureMethod_HMAC_SHA1;
use IMSGlobal\LTI\OAuth\OAuthUtil;

/**
 * Class BasicOutcomesComponent
 *
 * @package Chamilo\Application\Lti\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class BasicOutcomesComponent extends Manager implements NoAuthenticationSupport
{

    /**
     *
     * @return string
     * @throws \IMSGlobal\LTI\OAuth\OAuthException
     */
    function run()
    {
        $handle = fopen(__DIR__ . '/log.txt', 'a+');

        foreach (apache_request_headers() as $name => $value)
        {
            $message = 'HEADER ' . $name . ': ' . $value . PHP_EOL;
            fwrite($handle, $message);
        }

        fwrite($handle, file_get_contents('php://input'));
        fwrite($handle, PHP_EOL);
        fwrite($handle, PHP_EOL);
        fclose($handle);

        $body = <<< EOD
<?xml version = "1.0" encoding = "UTF-8"?>
<imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
  <imsx_POXHeader>
    <imsx_POXRequestHeaderInfo>
      <imsx_version>V1.0</imsx_version>
      <imsx_messageIdentifier>5ca35988892eb</imsx_messageIdentifier>
    </imsx_POXRequestHeaderInfo>
  </imsx_POXHeader>
  <imsx_POXBody>
    <replaceResultRequest>
      <resultRecord>
        <sourcedGUID>
          <sourcedId>5</sourcedId>
        </sourcedGUID>
        <result>
          <resultScore>
            <language>en-US</language>
            <textString>0.5</textString>
          </resultScore>
        </result>
      </resultRecord>
    </replaceResultRequest>
  </imsx_POXBody>
</imsx_POXEnvelopeRequest>
EOD;

        $ltiApplication = new Application(
            'http://dev.hogent.be/extra/lti_provider/src/connect.php', 'thisismychamilokey',
            '7Kts2OivnUnTZ6iCwdKgJSGJzYUqo3aD'
        );

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setContent($body);
        $this->getRequest()->headers->set(
            'Authorization',
            'OAuth oauth_version="1.0",oauth_nonce="b35834a8ed651f00ebbbe6cfcdc0d72c",oauth_timestamp="1554273282",oauth_consumer_key="thisismychamilokey",oauth_body_hash="E7/bLcJuvHXIDfCgRxcutZeiEkA=",oauth_signature_method="HMAC-SHA1",oauth_signature="JgfbTdH8uyczUbyL1TjRJjw/cJI="'
        );

        $oauthSecurity = new OAuthSecurity();
        $oauthSecurity->verifyRequest($ltiApplication, $this->getRequest());

        $domDocument = new \DOMDocument();
        if (!$domDocument->loadXML($body))
        {
            //TODO: fail
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
        $resultMessage = new ResultMessage($resultIdNode->textContent, $resultActionNode->nodeName, $score);
        var_dump($resultMessage);
    }
}