<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Domain\TranslationContext;
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
     * @param \Chamilo\Core\Notification\Domain\TranslationContext $translationContext
     *
     * @return string
     */
    public function createNotificationTranslations(TranslationContext $translationContext)
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
                $parameters[$key] = $this->translateRecursively($translationContext, $locale);
            }
        }

        return $this->translator->trans(
            $translationContext->getTranslationVariable(), $parameters, $translationContext->getContext(), $locale
        );
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Notification $notification
     *
     * @return string
     */
    public function getTranslationFromNotification(Notification $notification)
    {
        $userLocale = $this->translator->getLocale();

        $translations = json_decode($notification->getDescriptionContext(), true);
        if (!array_key_exists($userLocale, $translations))
        {
            $userLocale = 'en';
        }

        if (!array_key_exists($userLocale, $translations))
        {
            throw new \InvalidArgumentException(
                'No valid translation has been found for the given locale nor for the english fallback'
            );
        }

        return $translations[$userLocale];
    }

}
