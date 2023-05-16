<?php
namespace Chamilo\Core\Notification\Domain;

/**
 * @package Chamilo\Core\Notification\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TranslationContext
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $translationVariable;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $locale;

    /**
     * TranslationContext constructor.
     *
     * @param string $context
     * @param string $translationVariable
     * @param array $parameters
     * @param string $locale
     */
    public function __construct(string $context, string $translationVariable, array $parameters = [], $locale = null)
    {
        $this->context = $context;
        $this->translationVariable = $translationVariable;
        $this->parameters = $parameters;
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getTranslationVariable(): string
    {
        return $this->translationVariable;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}