<?php
namespace Chamilo\Core\User\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
use Chamilo\Core\User\Architecture\Domain\UserPictureProviderRegistry;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 *
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnector implements SettingsConnectorInterface
{
    protected DatetimeUtilities $datetimeUtilities;

    protected Translator $translator;

    protected UserPictureProviderRegistry $userPictureProviderFactory;

    public function __construct(
        UserPictureProviderRegistry $userPictureProviderFactory, DatetimeUtilities $datetimeUtilities,
        Translator $translator
    )
    {
        $this->userPictureProviderFactory = $userPictureProviderFactory;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->translator = $translator;
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserPictureProviderFactory(): UserPictureProviderRegistry
    {
        return $this->userPictureProviderFactory;
    }

    /**
     * @return string[]
     */
    public function getUserPictureProviders(): array
    {
        return $this->getUserPictureProviderFactory()->getAvailablePictureProviderTypes();
    }
}
