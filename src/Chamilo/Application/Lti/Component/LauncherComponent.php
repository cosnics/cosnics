<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Manager;
use IMSGlobal\LTI\OAuth\OAuthConsumer;
use IMSGlobal\LTI\OAuth\OAuthRequest;
use IMSGlobal\LTI\OAuth\OAuthSignatureMethod_HMAC_SHA1;

/**
 * Class LauncherComponent
 *
 * @package Chamilo\Application\Lti
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class LauncherComponent extends Manager
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
        $parameters = [
            'context_id' => '9d5d6098a0763716622ebb48921d548713d1bae8',
            'context_label' => 'ALGCUR001',
            'context_title' => 'Demo Cursus',
            'ext_roles' => 'urn:lti:instrole:ims/lis/Instructor,urn:lti:role:ims/lis/Instructor,urn:lti:sysrole:ims/lis/User',
            'launch_presentation_document_target' => 'iframe',
            'launch_presentation_height' => '400',
            'launch_presentation_locale' => 'nl',
            'launch_presentation_return_url' => 'http://cosnics.dev.hogent.be/index.php?application=Chamilo\Application\Lti',
            'launch_presentation_width' => '800',
            'lis_person_contact_email_primary' => 'sven.vanpoucke@hogent.be',
            'lis_person_name_family' => 'Vanpoucke',
            'lis_person_name_full' => 'Sven Vanpoucke',
            'lis_person_name_given' => 'Sven',
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            'oauth_callback' => 'about:blank',
            'resource_link_id' => '9d5d6098a0763716622ebb48921d548713d1bae8',
            'resource_link_title' => 'Buddycheck',
            'roles' => 'Instructor',
            'tool_consumer_info_product_family_code' => 'chamilo',
            'tool_consumer_info_version' => 'cloud',
            'tool_consumer_instance_contact_email' => '',
            'tool_consumer_instance_guid' => 'chamilo:hogeschool_gent',
            'tool_consumer_instance_name' => 'Hogeschool Gent',
            'user_id' => 'e1ff2c39669d723e46209e974b58b160e3d56805',
            'user_image' => '',
        ];

        $hmacMethod = new OAuthSignatureMethod_HMAC_SHA1();
        $consumer = new OAuthConsumer('thisismychamilokey', '7Kts2OivnUnTZ6iCwdKgJSGJzYUqo3aD', null);
        $request = OAuthRequest::from_consumer_and_token($consumer, null, 'POST', 'http://dev.hogent.be/extra/lti_provider/src/connect.php', $parameters);
        $request->sign_request($hmacMethod, $consumer, null);

        $oauthParameters = $request->get_parameters();

        $parameters = array_merge($parameters, $oauthParameters);
        var_dump($parameters);

        return $this->getTwig()->render('Chamilo\Application\Lti:Launcher.html.twig', ['LTI_PARAMETERS' => $parameters]);
    }
}