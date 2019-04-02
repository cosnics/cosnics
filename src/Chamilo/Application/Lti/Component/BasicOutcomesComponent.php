<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\OAuthDataStore;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use IMSGlobal\LTI\OAuth\OAuthConsumer;
use IMSGlobal\LTI\OAuth\OAuthRequest;
use IMSGlobal\LTI\OAuth\OAuthServer;
use IMSGlobal\LTI\OAuth\OAuthSignatureMethod_HMAC_SHA1;

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
     */
    function run()
    {
        $handle = fopen(__DIR__ . '/log.txt', 'a+');

        foreach (apache_request_headers() as $name => $value) {
            $message = 'HEADER ' . $name . ': ' . $value . PHP_EOL;
            fwrite($handle, $message);
        }

        fwrite($handle, file_get_contents('php://input'));
        fwrite($handle, PHP_EOL);
        fwrite($handle, PHP_EOL);
        fclose($handle);

        $authorization = [];
        $authorization['oauth_version'] = '1.0';
        $authorization['oauth_nonce'] = 'b35834a8ed651f00ebbbe6cfcdc0d72c';
        $authorization['oauth_timestamp'] = '1554209160';
        $authorization['oauth_consumer_key'] = 'thisismychamilokey';
        $authorization['oauth_body_hash'] = 'E7/bLcJuvHXIDfCgRxcutZeiEkA=';
        $authorization['oauth_signature_method'] = 'HMAC-SHA1';
        $authorization['oauth_signature'] = '57RKbLqsKAE5OXsX+ORH0EGCy10=';


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

        $store = new OAuthDataStore();
        $server = new OAuthServer($store);
        $method = new OAuthSignatureMethod_HMAC_SHA1();
        $server->add_signature_method($method);
        $request = OAuthRequest::from_request();

        foreach($authorization as $key => $value)
        {
            $request->set_parameter($key, $value);
        }
        $request->set_parameter('oauth_body_hash', base64_encode(sha1($body, true)), false);
        var_dump($request);
        var_dump($method->build_signature($request, new OAuthConsumer(
            'thisismychamilokey', '7Kts2OivnUnTZ6iCwdKgJSGJzYUqo3aD'
        ), ''));
//        $request = new OAuthRequest('http://cosnics.dev.hogent.be:80/index.php?application=Chamilo%5CApplication%5CLti&go=BasicOutcomes', 'POST', $authorization); var_dump($request);
        $res = $server->verify_request($request);
        var_dump($res);

    }
}