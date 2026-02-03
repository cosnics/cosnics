<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\UserInterface\Form\Factory\FormValidatorHtmlEditorOptionsFactory;
use HTML_QuickForm_html;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_textarea;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormValidatorHtmlEditorRenderer
{
    public const SETTING_COLLAPSE_TOOLBAR = 'collapse_toolbar';
    public const SETTING_CONFIGURATION = 'configuration';
    public const SETTING_ENTER_MODE = 'enter_mode';
    public const SETTING_FULL_PAGE = 'full_page';
    public const SETTING_HEIGHT = 'height';
    public const SETTING_LANGUAGE = 'language';
    public const SETTING_SHIFT_ENTER_MODE = 'shift_enter_mode';
    public const SETTING_TEMPLATES = 'templates';
    public const SETTING_THEME = 'theme';
    public const SETTING_TOOLBAR = 'toolbar';
    public const SETTING_WIDTH = 'width';

    protected FormValidatorHtmlEditorOptionsFactory $formValidatorHtmlEditorOptionsFactory;

    protected ResourceManager $resourceManager;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        ResourceManager $resourceManager, SystemPathBuilder $systemPathBuilder, Translator $translator,
        WebPathBuilder $webPathBuilder, FormValidatorHtmlEditorOptionsFactory $formValidatorHtmlEditorOptionsFactory
    )
    {
        $this->formValidatorHtmlEditorOptionsFactory = $formValidatorHtmlEditorOptionsFactory;
        $this->resourceManager = $resourceManager;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->translator = $translator;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * @throws \QuickformException
     */
    public function addHtmlEditor(
        FormValidator $formValidator, string $name, string $label, bool $required = true, array $options = [],
        array $attributes = []
    ): void
    {
        $formValidatorHtmlOptions =
            $this->getFormValidatorHtmlEditorOptionsFactory()->getDefaultFormValidatorHtmlEditorOptions($options);

        $element = $this->createHtmlEditor($formValidator, $name, $label, $formValidatorHtmlOptions, $attributes);

        $formValidator->addElement($element);
        $formValidator->applyFilter($name, 'trim');

        if ($required) {
            $formValidator->addRule(
                $name, $this->getTranslator()->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
                HTML_QuickForm_Rule_Required::class
            );
        }
    }

    /**
     * @throws \QuickformException
     */
    public function createHtmlEditor(
        FormValidator $formValidator, string $name, string $label,
        FormValidatorHtmlEditorOptions $formValidatorHtmlOptions, array $attributes = []
    ): HTML_QuickForm_textarea
    {
        $formValidator->addElement(HTML_QuickForm_html::class, implode(PHP_EOL, $this->getJavascriptForCreate()));

        $formValidator->addElement(
            HTML_QuickForm_html::class,
            implode(PHP_EOL, $this->getJavascriptForRender($name, $formValidatorHtmlOptions))
        );
        $formValidator->registerHtmlEditor($name);

        return $formValidator->createElement(HTML_QuickForm_textarea::class, $name, $label, $attributes);
    }

    protected function getFormValidatorHtmlEditorOptionsFactory(): FormValidatorHtmlEditorOptionsFactory
    {
        return $this->formValidatorHtmlEditorOptionsFactory;
    }

    /**
     * @return string[]
     */
    protected function getJavascriptForCreate(): array
    {
        $webPathBuilder = $this->getWebPathBuilder();
        $resourceManager = $this->getResourceManager();

        $configFile = $this->getSystemPathBuilder()->getBasePath() .
            '../web/Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorInstanceConfig.js';

        $timestamp = filemtime($configFile);

        $javascript = [];

        $javascript[] = '<script>';
        $javascript[] = 'window.CKEDITOR_BASEPATH = "' . $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) .
            '" + "HtmlEditor/Ckeditor/"';
        $javascript[] = '</script>';

        $javascript[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) . 'HtmlEditor/Ckeditor/ckeditor.js'
        );
        $javascript[] = '<script>';
        $javascript[] = 'CKEDITOR.timestamp = "' . $timestamp . '";';
        $javascript[] = 'var web_path = \'' . $webPathBuilder->getBasePath() . '\';';
        $javascript[] = '</script>';
        $javascript[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) . 'HtmlEditor/CkeditorGlobalConfig.js'
        );
        $javascript[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) . 'HtmlEditor/Ckeditor/adapters/jquery.js'
        );

        return $javascript;
    }

    /**
     * @return string[]
     */
    protected function getJavascriptForRender(string $name, FormValidatorHtmlEditorOptions $formValidatorHtmlOptions
    ): array
    {
        $javascript = [];

        $javascript[] = '<script>';
        $javascript[] = 'var web_path = \'' . $this->getWebPathBuilder()->getBasePath() . '\'';
        $javascript[] = '$(function ()';
        $javascript[] = '{';
        $javascript[] = '	$(document).ready(function ()';
        $javascript[] = '	{';
        $javascript[] = '         if(typeof $el == \'undefined\'){';
        $javascript[] = '           $el = new Array()';
        $javascript[] = '         }';
        $javascript[] = '	  $el.push($("textarea.html_editor[name=\'' . $name . '\']").ckeditor({';
        $javascript[] = $formValidatorHtmlOptions->renderOptions();
        $javascript[] = '		}, function(){ $(document).trigger(\'ckeditor_loaded\'); }));';
        $javascript[] = '	}); ';
        $javascript[] = '});';
        $javascript[] = '</script>';

        return $javascript;
    }

    protected function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    protected function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    protected function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * @throws \QuickformException
     */
    public function renderHtmlEditor(
        string $name, string $label, array $options = [], array $attributes = []
    ): string
    {
        if (!array_key_exists('class', $attributes)) {
            $attributes['class'] = 'html_editor';
        }

        $formValidator = new FormValidator('formValidatorHtmlEditorRenderer');
        $formValidatorHtmlOptions =
            $this->getFormValidatorHtmlEditorOptionsFactory()->getDefaultFormValidatorHtmlEditorOptions($options);

        $html = [];

        $html[] = $formValidator->createElement(HTML_QuickForm_textarea::class, $name, $label, $attributes)->toHtml();
        $html[] = implode(PHP_EOL, $this->getJavascriptForRender($name, $formValidatorHtmlOptions));

        return implode(PHP_EOL, $html);
    }
}
