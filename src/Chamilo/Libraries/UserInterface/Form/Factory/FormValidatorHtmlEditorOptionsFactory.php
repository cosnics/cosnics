<?php
namespace Chamilo\Libraries\UserInterface\Form\Factory;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidatorHtmlEditorOptions;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormValidatorHtmlEditorOptionsFactory
{
    protected ChamiloRequest $chamiloRequest;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        ChamiloRequest $chamiloRequest, SystemPathBuilder $systemPathBuilder, Translator $translator,
        WebPathBuilder $webPathBuilder
    )
    {
        $this->chamiloRequest = $chamiloRequest;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->translator = $translator;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * @param string[] $options
     */
    public function getDefaultFormValidatorHtmlEditorOptions(array $options = []): FormValidatorHtmlEditorOptions
    {
        $formValidatorHtmlEditorOptions = new FormValidatorHtmlEditorOptions($options);

        $webPath = $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) .
            'HtmlEditor/CkeditorInstanceConfig.js';

        $available_options = $formValidatorHtmlEditorOptions->get_option_names();

        foreach ($available_options as $available_option)
        {
            $value = $formValidatorHtmlEditorOptions->get_option($available_option);

            if (!isset($value))
            {
                switch ($available_option)
                {
                    case FormValidatorHtmlEditorOptions::OPTION_LANGUAGE :
                        $formValidatorHtmlEditorOptions->set_option(
                            $available_option, $this->getTranslator()->getLocale()
                        );
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_TOOLBAR :
                        $formValidatorHtmlEditorOptions->set_option($available_option, 'Basic');
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_FULL_PAGE:
                    case FormValidatorHtmlEditorOptions::OPTION_COLLAPSE_TOOLBAR :
                        $formValidatorHtmlEditorOptions->set_option($available_option, false);
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_WIDTH :
                        $formValidatorHtmlEditorOptions->set_option($available_option, '100%');
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_HEIGHT :
                        $formValidatorHtmlEditorOptions->set_option($available_option, 200);
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_RENDER_RESOURCE_INLINE :
                        $formValidatorHtmlEditorOptions->set_option($available_option, true);
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_SKIN :
                        $formValidatorHtmlEditorOptions->set_option($available_option, 'moono-lisa');
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_CONFIGURATION :
                        $formValidatorHtmlEditorOptions->set_option($available_option, $webPath);
                        break;
                }
            }
        }

        return $formValidatorHtmlEditorOptions;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->chamiloRequest;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }
}