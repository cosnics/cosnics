<?php
namespace Chamilo\Libraries\UserInterface\Form\Factory;

use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlEditorOptionsFactory
{
    /**
     * Whether the toolbar should be collapse by default
     */
    public const string OPTION_COLLAPSE_TOOLBAR = 'toolbarStartupExpanded';
    /**
     * Path to the editors configuration file
     */
    public const string OPTION_CONFIGURATION = 'customConfig';
    /**
     * Whether the content of the editor should be treated as a standalone page
     */
    public const string OPTION_FULL_PAGE = 'fullPage';
    /**
     * The height of the editor in pixels
     */
    public const string OPTION_HEIGHT = 'height';
    /**
     * Name of the language to be used for the editor
     */
    public const string OPTION_LANGUAGE = 'language';
    public const string OPTION_SKIN = 'skin';
    /**
     * The name of the toolbar set e.g.
     * Basic, Wiki, Assessment
     */
    public const string OPTION_TOOLBAR = 'toolbar';
    /**
     * The width of the editor in pixels or per cent
     */
    public const string OPTION_WIDTH = 'width';

    protected Translator $translator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        Translator $translator, WebPathBuilder $webPathBuilder
    )
    {
        $this->translator = $translator;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * @param string[] $options
     */
    public function getDefaultFormValidatorHtmlEditorOptions(array $options = []): ArrayCollection
    {
        return new ArrayCollection(array_merge($this->getDefaultOptions(), $options));
    }

    protected function getDefaultOptions(): array
    {
        $webPath = $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) .
            'HtmlEditor/CkeditorInstanceConfig.js';

        return [
            self::OPTION_LANGUAGE => $this->getTranslator()->getLocale(),
            self::OPTION_TOOLBAR => 'Basic',
            self::OPTION_FULL_PAGE => 'false',
            self::OPTION_COLLAPSE_TOOLBAR => 'false',
            self::OPTION_WIDTH => '100%',
            self::OPTION_HEIGHT => 200,
            self::OPTION_SKIN => 'moono-lisa',
            self::OPTION_CONFIGURATION => $webPath

        ];
    }

    protected function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }
}