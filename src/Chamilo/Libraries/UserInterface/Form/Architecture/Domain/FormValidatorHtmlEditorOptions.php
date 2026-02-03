<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

/**
 * The combination of options available for the FormValidatorHtmlEditor Should be implemented for each specific editor
 * to translate the generic option values
 *
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author  Scaramanga
 */
class FormValidatorHtmlEditorOptions
{
    /**
     * Whether the toolbar should be collapse by default
     */
    public const OPTION_COLLAPSE_TOOLBAR = 'toolbarStartupExpanded';
    /**
     * Path to the editors configuration file
     */
    public const OPTION_CONFIGURATION = 'customConfig';
    /**
     * Whether the content of the editor should be treated as a standalone page
     */
    public const OPTION_FULL_PAGE = 'fullPage';
    /**
     * The height of the editor in pixels
     */
    public const OPTION_HEIGHT = 'height';
    /**
     * Name of the language to be used for the editor
     */
    public const OPTION_LANGUAGE = 'language';
    public const OPTION_RENDER_RESOURCE_INLINE = 'render_resource_inline';
    public const OPTION_SKIN = 'skin';
    /**
     * Path to available templates for the editor
     */
    public const OPTION_TEMPLATES = 'templates_files';
    /**
     * The name of the toolbar set e.g.
     * Basic, Wiki, Assessment
     */
    public const OPTION_TOOLBAR = 'toolbar';
    /**
     * The width of the editor in pixels or per cent
     */
    public const OPTION_WIDTH = 'width';

    /**
     * @var string[]
     */
    private array $options;

    /**
     * @param string[] $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function formatForJavascript(int|string|array|bool $value): int|string
    {
        if (is_bool($value)) {
            if ($value === true) {
                return 'true';
            }
            else {
                return 'false';
            }
        }
        elseif (is_int($value)) {
            return $value;
        }
        elseif (is_array($value)) {
            $elements = [];

            foreach ($value as $element) {
                $elements[] = $this->formatForJavascript($element);
            }

            return '[' . implode(',', $elements) . ']';
        }
        else {
            return '\'' . $value . '\'';
        }
    }

    /**
     * @return string[]
     */
    public function getMapping(): array
    {
        return array_combine($this->getOptionNames(), $this->getOptionNames());
    }

    public function getOption(string $variable): ?string
    {
        return $this->options[$variable] ?? null;
    }

    /**
     * @return string[]
     */
    public function getOptionNames(): array
    {
        return [
            self::OPTION_COLLAPSE_TOOLBAR,
            self::OPTION_CONFIGURATION,
            self::OPTION_FULL_PAGE,
            self::OPTION_LANGUAGE,
            self::OPTION_TEMPLATES,
            self::OPTION_TOOLBAR,
            self::OPTION_SKIN,
            self::OPTION_HEIGHT,
            self::OPTION_WIDTH,
            self::OPTION_RENDER_RESOURCE_INLINE
        ];
    }

    /**
     * @return string[] The options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function renderOptions(): string
    {
        $javascript = [];
        $availableOptions = $this->getOptionNames();
        $mapping = $this->getMapping();

        foreach ($availableOptions as $availableOption) {
            if (key_exists($availableOption, $mapping)) {
                $value = $this->getOption($availableOption);

                if (isset($value)) {
                    $processingFunction = 'process_' . $availableOption;
                    if (method_exists($this, $processingFunction)) {
                        $value = call_user_func([$this, $processingFunction], $value);
                    }

                    $javascript[] =
                        '			' . $mapping[$availableOption] . ' : ' . $this->formatForJavascript($value);
                }
            }
        }

        return implode(",\n", $javascript);
    }

    public function setOption(string $variable, mixed $value): void
    {
        $this->options[$variable] = $value;
    }
}
