<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Component\TurnitinEulaComponent;
use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\ConfigurationWriter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * TODO: always retrieve version from database / api before getting the eula page
 */
class EulaService
{
    const ZULU_DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
    

    /**
     * @var \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository
     */
    protected $turnitinRepository;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface
     */
    protected $userConverter;

    /**
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected $localSetting;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationWriter
     */
    protected $configurationWriter;

    /**
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected $sessionUtilities;

    /**
     * TurnitinService constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository
     * @param \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface $userConverter
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Configuration\Service\ConfigurationWriter $configurationWriter
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository,
        \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface $userConverter,
        LocalSetting $localSetting, ConfigurationConsulter $configurationConsulter, ConfigurationWriter $configurationWriter,
        SessionUtilities $sessionUtilities
    )
    {
        $this->turnitinRepository = $turnitinRepository;
        $this->userConverter = $userConverter;
        $this->localSetting = $localSetting;
        $this->configurationConsulter = $configurationConsulter;
        $this->configurationWriter = $configurationWriter;
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * @param string $version
     * @param string $language
     *
     * @return string
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getEULAPage(string $version = 'latest', $language = 'en_US')
    {
        return $this->turnitinRepository->getEULAPage($version, $language);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function userHasAcceptedEULA(User $user)
    {
        $acceptedDate = $this->getAcceptedDateForUser($user);
        if (empty($acceptedDate))
        {
            return false;
        }

        $datePeriod = $this->getActiveEULADatePeriod();

        return $this->isDateTimeWithinPeriod($acceptedDate, $datePeriod);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \DateTime|null $dateTime
     *
     * @throws \Exception
     */
    public function acceptEULA(User $user, \DateTime $dateTime = null)
    {
        if (empty($dateTime))
        {
            $dateTime = new \DateTime();
        }

        $dateTime->setTimezone(new \DateTimeZone('UTC'));

        $datePeriod = $this->getActiveEULADatePeriod();
        if (!$this->isDateTimeWithinPeriod($dateTime, $datePeriod))
        {
            throw new \InvalidArgumentException(
                'The given / current date time is not within the valid range of the EULA date period so the EULA could not be accepted'
            );
        }

        $userId = $this->userConverter->convertUserToId($user);
        $this->turnitinRepository->acceptEULAVersion($userId, $dateTime, 'en-US', $this->getEULAVersion());

        $this->localSetting->create(
            'turnitin_eula_accepted_date', $dateTime->format(self::ZULU_DATE_FORMAT),
            'Chamilo\Application\Plagiarism', $user
        );
    }

    /**
     * @param string $redirectToURL
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getRedirectToEULAPageResponse(string $redirectToURL)
    {
        if(empty($redirectToURL))
        {
            throw new PlagiarismException('The given redirect URL can not be empty');
        }

        $redirect = new Redirect(
            [
                Manager::PARAM_CONTEXT => Manager::context(),
                Manager::PARAM_ACTION => Manager::ACTION_TURNITIN_EULA
            ]
        );

        $this->sessionUtilities->register(TurnitinEulaComponent::REDIRECT_URL, $redirectToURL);
        return new RedirectResponse($redirect->getUrl());
    }

    /**
     * @return \DatePeriod
     * @throws \Exception
     */
    protected function getActiveEULADatePeriod()
    {
        $validDateFrom = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Plagiarism', 'turnitin_eula_valid_from_date']
        );

        $validUntil = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Plagiarism', 'turnitin_eula_valid_until_date']
        );

        $dateTimeFrom = \DateTime::createFromFormat(self::ZULU_DATE_FORMAT, $validDateFrom);
        $dateTimeUntil = \DateTime::createFromFormat(self::ZULU_DATE_FORMAT, $validUntil);

        if (!$dateTimeFrom instanceof \DateTime || !$dateTimeUntil instanceof \DateTime)
        {
            return $this->retrieveEULADatePeriodFromAPI();
        }

        $datePeriod = new \DatePeriod($dateTimeFrom, new \DateInterval('P1D'), $dateTimeUntil);

        $currentDate = new \DateTime();
        $currentDate->setTimezone(new \DateTimeZone('UTC'));

        if (!$this->isDateTimeWithinPeriod($currentDate, $datePeriod))
        {
            return $this->retrieveEULADatePeriodFromAPI();
        }

        return $datePeriod;
    }

    /**
     * @return string
     */
    protected function getEULAVersion()
    {
        $eulaVersion = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Plagiarism', 'turnitin_eula_version']
        );

        return $eulaVersion;
    }


    /**
     * @return \DatePeriod
     * @throws \Exception
     */
    protected function retrieveEULADatePeriodFromAPI()
    {
        $versionInfo = $this->turnitinRepository->getEULAVersionInfo();

        $validFrom = $versionInfo['valid_from'];
        $dateTimeFrom = \DateTime::createFromFormat(self::ZULU_DATE_FORMAT, $validFrom);

        $validUntil = $versionInfo['valid_until'];
        if(empty($validUntil))
        {
            $dateTimeUntil = new \DateTime();
            $dateTimeUntil->add(new \DateInterval('P1D'));
            $dateTimeUntil->setTimezone(new \DateTimeZone('UTC'));

            $validUntil = $dateTimeUntil->format(self::ZULU_DATE_FORMAT);
        }
        else
        {
            $dateTimeUntil = \DateTime::createFromFormat(self::ZULU_DATE_FORMAT, $validUntil);
        }

        if (!$dateTimeFrom instanceof \DateTime || !$dateTimeUntil instanceof \DateTime)
        {
            throw new \RuntimeException(
                sprintf(
                    'The given EULA date range from %s to %s could not be parsed as a valid date time object',
                    $validFrom, $validUntil
                )
            );
        }

        $this->configurationWriter->writeSetting(
            'Chamilo\Application\Plagiarism', 'turnitin_eula_valid_from_date', $validFrom
        );

        $this->configurationWriter->writeSetting(
            'Chamilo\Application\Plagiarism', 'turnitin_eula_valid_until_date', $validUntil
        );

        $this->configurationWriter->writeSetting(
            'Chamilo\Application\Plagiarism', 'turnitin_eula_version', $versionInfo['version']
        );

        return new \DatePeriod($dateTimeFrom, new \DateInterval('P1D'), $dateTimeUntil);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \DateTime
     */
    protected function getAcceptedDateForUser(User $user)
    {
        $acceptedDate =
            $this->localSetting->get('turnitin_eula_accepted_date', 'Chamilo\Application\Plagiarism', $user);

        $dateTime = \DateTime::createFromFormat(self::ZULU_DATE_FORMAT, $acceptedDate);

        if (!$dateTime instanceof \DateTime)
        {
            return null;
        }

        return $dateTime;
    }

    /**
     * @param \DateTime $dateTime
     * @param \DatePeriod $datePeriod
     *
     * @return bool
     */
    protected function isDateTimeWithinPeriod(\DateTime $dateTime, \DatePeriod $datePeriod)
    {
        return $dateTime >= $datePeriod->getStartDate() && $dateTime <= $datePeriod->getEndDate();
    }
}