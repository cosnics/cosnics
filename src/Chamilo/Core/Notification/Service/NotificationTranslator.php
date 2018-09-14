<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Domain\ViewingContext;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Notification\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationTranslator
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * NotificationTranslator constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ViewingContext[] $viewingContexts
     *
     * @return string
     */
    public function createNotificationDescriptionContext($viewingContexts = [])
    {
        $descriptionContext = [];

        foreach ($viewingContexts as $viewingContext)
        {
            $viewingContextArray = [
                'path' => $viewingContext->getPath()
            ];

            foreach ($this->translator->getFallbackLocales() as $locale)
            {
                $viewingContextArray[$locale] =
                    $this->translateRecursively($viewingContext->getTranslationContext(), $locale);
            }

            $descriptionContext[] = $viewingContextArray;
        }

        return json_encode($descriptionContext);
    }

    /**
     * @param \Chamilo\Core\Notification\Domain\TranslationContext $translationContext
     *
     * @return string
     */
    public function translateToAllLanguagesAndEncode(TranslationContext $translationContext)
    {
        $translations = [];

        foreach ($this->translator->getFallbackLocales() as $locale)
        {
            $translations[$locale] = $this->translateRecursively($translationContext, $locale);
        }

        return json_encode($translations);
    }

    /**
     * @param TranslationContext $translationContext
     * @param string $locale
     *
     * @return string
     */
    protected function translateRecursively(TranslationContext $translationContext, $locale)
    {
        $parameters = $translationContext->getParameters();
        foreach ($parameters as $key => $parameter)
        {
            if ($parameter instanceof TranslationContext)
            {
                $parameters[$key] = $this->translateRecursively($parameter, $locale);
            }
        }

        return $this->translator->trans(
            $translationContext->getTranslationVariable(), $parameters, $translationContext->getContext(), $locale
        );
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Notification $notification
     * @param string $viewingContextPath
     *
     * @return string
     */
    public function getTranslationFromNotification(Notification $notification, string $viewingContextPath)
    {
        $userLocale = $this->translator->getLocale();
        $activeViewingContext = null;

        $viewingContexts = json_decode($notification->getDescriptionContext(), true);
        foreach ($viewingContexts as $viewingContext)
        {
            if ($viewingContext['path'] == $viewingContextPath)
            {
                $activeViewingContext = $viewingContext;
            }
        }

        if (!$activeViewingContext)
        {
            throw new \InvalidArgumentException(
                sprintf('The given viewing context with path %s could not be found', $viewingContextPath)
            );
        }

        if (!array_key_exists($userLocale, $activeViewingContext))
        {
            $userLocale = 'en';
        }

        if (!array_key_exists($userLocale, $activeViewingContext))
        {
            throw new \InvalidArgumentException(
                'No valid translation has been found for the given locale nor for the english fallback'
            );
        }

        return $activeViewingContext[$userLocale];
    }

}
