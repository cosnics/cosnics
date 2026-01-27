<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

/**
 * The combination of options available for the FormValidatorHtmlEditor Should be implemented for each specific editor
 * to translate the generic option values
 *
 * @package Chamilo\Libraries\Format\Form
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
     * The width of the editor in pixels or percent
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
        if (is_bool($value))
        {
            if ($value === true)
            {
                return 'true';
            }
            else
            {
                return 'false';
            }
        }
        elseif (is_int($value))
        {
            return $value;
        }
        elseif (is_array($value))
        {
            $elements = [];

            foreach ($value as $element)
            {
                $elements[] = $this->formatForJavascript($element);
            }

            return '[' . implode(',', $elements) . ']';
        }
        else
        {
            return '\'' . $value . '\'';
        }
    }

    /**
     * @return string[]
     */
    public function get_mapping(): array
    {
        return array_combine($this->get_option_names(), $this->get_option_names());
    }

    public function get_option(string $variable): ?string
    {
        return $this->options[$variable] ?? null;
    }

    /**
     * @return string[]
     */
    public function get_option_names(): array
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
    public function get_options(): array
    {
        return $this->options;
    }

    /**
     * @param string[] $options
     */
    public function set_options(array $options): void
    {
        $this->options = $options;
    }

    public function renderOptions(): string
    {
        $javascript = [];
        $available_options = $this->get_option_names();
        $mapping = $this->get_mapping();

        foreach ($available_options as $available_option)
        {
            if (key_exists($available_option, $mapping))
            {
                $value = $this->get_option($available_option);

                if (isset($value))
                {
                    $processing_function = 'process_' . $available_option;
                    if (method_exists($this, $processing_function))
                    {
                        $value = call_user_func([$this, $processing_function], $value);
                    }

                    $javascript[] =
                        '			' . $mapping[$available_option] . ' : ' . $this->formatForJavascript($value);
                }
            }
        }

        return implode(",\n", $javascript);
    }

    public function set_option(string $variable, mixed $value): void
    {
        $this->options[$variable] = $value;
    }
}
