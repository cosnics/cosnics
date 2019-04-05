<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\Outcome\OutcomeWebservice;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 * Class BasicOutcomesComponent
 *
 * @package Chamilo\Application\Lti\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class BasicOutcomesComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
          <sourcedId>eyJpbnRlZ3JhdGlvbkNsYXNzIjoiQ2hhbWlsb1xcQXBwbGljYXRpb25cXEx0aVxcU2VydmljZVxcSW50ZWdyYXRpb25cXFRlc3RJbnRlZ3JhdGlvbiIsInJlc3VsdElkIjo1fQ==</sourcedId>
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

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setContent($body);
        $this->getRequest()->headers->set(
            'Authorization',
            'OAuth oauth_version="1.0",oauth_nonce="b35834a8ed651f00ebbbe6cfcdc0d72c",oauth_timestamp="1554446537",oauth_consumer_key="thisismychamilokey",oauth_body_hash="E7/bLcJuvHXIDfCgRxcutZeiEkA=",oauth_signature_method="HMAC-SHA1",oauth_signature="w65hyioHvdaW8mMDNVT6TBv0agg="'
        );

        $this->getRequest()->query->set(self::PARAM_UUID, '951b6ec2-e454-4e1c-9abf-05f562bface9');

        $outcomeWebservice = $this->getService(OutcomeWebservice::class);

        echo '<pre>';
        echo htmlentities($outcomeWebservice->handleRequest($this->getRequest()));
    }
}