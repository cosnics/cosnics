<?php

namespace Chamilo\Core\Notification\Domain;

/**
 * @package Chamilo\Core\Notification\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewingContext
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var TranslationContext
     */
    protected $translationContext;

    /**
     * ViewingContext constructor.
     *
     * @param string $path
     * @param TranslationContext $translationContext
     */
    public function __construct(string $path, TranslationContext $translationContext)
    {
        $this->path = $path;
        $this->translationContext = $translationContext;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return TranslationContext
     */
    public function getTranslationContext(): TranslationContext
    {
        return $this->translationContext;
    }

}