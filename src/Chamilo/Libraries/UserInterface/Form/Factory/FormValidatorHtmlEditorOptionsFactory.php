<?php
namespace Chamilo\Libraries\UserInterface\Form\Factory;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidatorHtmlEditorOptions;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Factory
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

        $availableOptions = $formValidatorHtmlEditorOptions->getOptionNames();

        foreach ($availableOptions as $availableOption) {
            $value = $formValidatorHtmlEditorOptions->getOption($availableOption);

            if (!isset($value)) {
                switch ($availableOption) {
                    case FormValidatorHtmlEditorOptions::OPTION_LANGUAGE :
                        $formValidatorHtmlEditorOptions->setOption(
                            $availableOption, $this->getTranslator()->getLocale()
                        );
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_TOOLBAR :
                        $formValidatorHtmlEditorOptions->setOption($availableOption, 'Basic');
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_FULL_PAGE:
                    case FormValidatorHtmlEditorOptions::OPTION_COLLAPSE_TOOLBAR :
                        $formValidatorHtmlEditorOptions->setOption($availableOption, false);
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_WIDTH :
                        $formValidatorHtmlEditorOptions->setOption($availableOption, '100%');
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_HEIGHT :
                        $formValidatorHtmlEditorOptions->setOption($availableOption, 200);
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_RENDER_RESOURCE_INLINE :
                        $formValidatorHtmlEditorOptions->setOption($availableOption, true);
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_SKIN :
                        $formValidatorHtmlEditorOptions->setOption($availableOption, 'moono-lisa');
                        break;
                    case FormValidatorHtmlEditorOptions::OPTION_CONFIGURATION :
                        $formValidatorHtmlEditorOptions->setOption($availableOption, $webPath);
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