<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Form;

use Chamilo\Application\Lti\Service\ProviderService;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalToolCustomParameter;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\UUID;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolForm extends ContentObjectForm
{
    use DependencyInjectionContainerTrait;

    // Inherited
    public function create_content_object()
    {
        $externalTool = new ExternalTool();
        $this->set_content_object($externalTool);
        $this->setPropertiesForExternalTool($externalTool);

        return parent::create_content_object();
    }

    public function update_content_object()
    {
        /** @var ExternalTool $externalTool */
        $externalTool = $this->get_content_object();
        $this->setPropertiesForExternalTool($externalTool);

        return parent::update_content_object();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool $externalTool
     */
    protected function setPropertiesForExternalTool(ExternalTool $externalTool)
    {
        $values = $this->exportValues();
        $ltiProviderId = $values[ExternalTool::PROPERTY_LTI_PROVIDER_ID];
        if ($ltiProviderId > - 1)
        {
            $externalTool->setLtiProviderId($ltiProviderId);
            $externalTool->setLaunchUrl(null);
            $externalTool->setKey(null);
            $externalTool->setSecret(null);
        }
        else
        {
            $externalTool->setLtiProviderId(null);
            $externalTool->setLaunchUrl($values[ExternalTool::PROPERTY_LAUNCH_URL]);
            $externalTool->setKey($values[ExternalTool::PROPERTY_KEY]);
            $externalTool->setSecret($values[ExternalTool::PROPERTY_SECRET]);
        }

        if(empty($externalTool->getUuid()))
        {
            $externalTool->setUUID(UUID::v4());
        }

        $request = ChamiloRequest::createFromGlobals();
        $customParameterObjects = [];

        $customParameters = $request->getFromPost(ExternalTool::PROPERTY_CUSTOM_PARAMETERS);
        foreach ($customParameters as $customParameter)
        {
            $customParameterObjects[] =
                new ExternalToolCustomParameter($customParameter['name'], $customParameter['value']);
        }

        $externalTool->setCustomParameters($customParameterObjects);
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form();
        $this->addExternalToolForm();
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form();
        $this->addExternalToolForm();
    }

    /**
     * @param array $defaults
     */
    public function setDefaults($defaults = array(), $filter = null)
    {
        /** @var ExternalTool $externalTool */
        $externalTool = $this->get_content_object();

        if(!empty($externalTool->getLtiProviderId()))
        {
            $defaults[ExternalTool::PROPERTY_LTI_PROVIDER_ID] = $externalTool->getLtiProviderId();
        }

        $defaults[ExternalTool::PROPERTY_LAUNCH_URL] = $externalTool->getLaunchUrl();
        $defaults[ExternalTool::PROPERTY_KEY] = $externalTool->getKey();
        $defaults[ExternalTool::PROPERTY_SECRET] = $externalTool->getSecret();

        parent::setDefaults($defaults);
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function addExternalToolForm()
    {
        /** @var ExternalTool $externalTool */
        $externalTool = $this->get_content_object();

        $this->initializeContainer();

        $providersArray[- 1] = '-- ' . Translation::get('DefineYourOwnToolOrSelectExisting') . ' --';

        /** @var ProviderService $providerService */
        $providerService = $this->getService(ProviderService::class);
        $providers = $providerService->findProviders();
        foreach($providers as $provider)
        {
            $providersArray[$provider->getId()] = $provider->getName();
        }

        $this->addElement('category', Translation::get('SelectExistingProvider'));
        $this->addElement(
            'select', ExternalTool::PROPERTY_LTI_PROVIDER_ID, Translation::get('LtiProvider'),
            $providersArray, ['class' => 'form-control', 'id' => 'lti-provider-id']
        );

        $this->addElement('category', Translation::get('DefineNewProvider'));

        $this->addElement('html', '<div id="new-lti-provider">');
        $this->add_textfield(ExternalTool::PROPERTY_LAUNCH_URL, Translation::get('ExternalToolUrl'), false);
        $this->add_textfield(ExternalTool::PROPERTY_KEY, Translation::get('ConsumerKey'), false);
        $this->add_textfield(ExternalTool::PROPERTY_SECRET, Translation::get('ConsumerSecret'), false);
        $this->addElement('html', '</div>');

        $this->addElement('category', Translation::get('CustomParameters'));

        $this->addElement(
            'html', '<div class="col col-sm-8 col-md-9 col-lg-10 col-sm-push-4 col-md-push-3 col-lg-push-2">' .
            $this->getTwig()->render(
                'Chamilo\Application\Lti:Provider/CustomParameters.html.twig',
                [
                    'CUSTOM_PARAMETERS_ELEMENT_NAME' => ExternalTool::PROPERTY_CUSTOM_PARAMETERS,
                    'DEFAULT_CUSTOM_PARAMETERS_JSON' => $externalTool->getCustomParametersJSON()
                ]
            ) .
            '</div>'
        );

        $script = <<<EOD
<style>
    #new-lti-provider input[type="text"] {
        background-color: white;
    }        
    
    #new-lti-provider input[type="text"][disabled="disabled"] {
        background-color: #f5f5f5;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        let checkSelectedProvider = function() {
            let ltiProviderId = $('#lti-provider-id').val();
            if(ltiProviderId != -1) {
                $('input', $('#new-lti-provider')).attr('disabled', 'disabled');
                $('input', $('#new-lti-provider')).attr('readonly', 'true');
                $('input', $('#new-lti-provider')).val('');
            }
            else {
                $('input', $('#new-lti-provider')).removeAttr('disabled');   
                $('input', $('#new-lti-provider')).removeAttr('readonly');   
            }
        };
        
        checkSelectedProvider();
        
        $('#lti-provider-id').on('change', checkSelectedProvider);
    });
</script>
EOD;
        $this->addElement('html', $script);

    }
}
