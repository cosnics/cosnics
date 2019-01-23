<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EulaService
{
    /**
     * @var \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository
     */
    protected $turnitinRepository;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter\UserConverterInterface
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
     * TurnitinService constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter\UserConverterInterface $userConverter
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository,
        \Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter\UserConverterInterface $userConverter,
        LocalSetting $localSetting, ConfigurationConsulter $configurationConsulter
    )
    {
        $this->turnitinRepository = $turnitinRepository;
        $this->userConverter = $userConverter;
        $this->localSetting = $localSetting;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @param string $version
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getEULAPage(string $version = 'latest')
    {
        return $this->turnitinRepository->getEULAPage($version);
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

        $datePeriod = $this->getActiveEULADatePeriod();
        if (!$this->isDateTimeWithinPeriod($dateTime, $datePeriod))
        {
            throw new \InvalidArgumentException(
                'The given / current date time is not within the valid range of the EULA date period so the EULA could not be accepted'
            );
        }

        $userId = $this->userConverter->convertUserToId($user);
        $this->turnitinRepository->acceptEULAVersion($userId, $dateTime, 'en-US', 'latest');

        $this->localSetting->create(
            'turnitin_eula_accepted_date', $dateTime->format(\DateTimeInterface::ISO8601),
            'Chamilo\Application\Plagiarism', $user
        );
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

        $dateTimeFrom = \DateTime::createFromFormat(\DateTimeInterface::ISO8601, $validDateFrom);
        $dateTimeUntil = \DateTime::createFromFormat(\DateTimeInterface::ISO8601, $validUntil);

        if (!$dateTimeFrom instanceof \DateTime || !$dateTimeUntil instanceof \DateTime)
        {
            return $this->retrieveEULADatePeriodFromAPI();
        }

        $datePeriod = new \DatePeriod($dateTimeFrom, new \DateInterval('P1D'), $dateTimeUntil);

        if (!$this->isDateTimeWithinPeriod(new \DateTime(), $datePeriod))
        {
            return $this->retrieveEULADatePeriodFromAPI();
        }

        return $datePeriod;
    }

    /**
     * @return \DatePeriod
     * @throws \Exception
     */
    protected function retrieveEULADatePeriodFromAPI()
    {
        $versionInfo = $this->turnitinRepository->getEULAVersionInfo();

        $validDateFrom = $versionInfo['valid_from'];
        $validUntil = $versionInfo['valid_until'];

        $dateTimeFrom = \DateTime::createFromFormat(\DateTimeInterface::ISO8601, $validDateFrom);
        $dateTimeUntil = \DateTime::createFromFormat(\DateTimeInterface::ISO8601, $validUntil);

        if (!$dateTimeFrom instanceof \DateTime || !$dateTimeUntil instanceof \DateTime)
        {
            throw new \RuntimeException(
                sprintf(
                    'The given EULA date range from %s to %s could not be parsed as a valid date time object',
                    $validDateFrom, $validUntil
                )
            );
        }

        $this->configurationWriter->writeSetting(
            'Chamilo\Application\Plagiarism', 'turnitin_eula_valid_from_date', $validDateFrom
        );

        $this->configurationWriter->writeSetting(
            'Chamilo\Application\Plagiarism', 'turnitin_eula_valid_until_date', $validUntil
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

        $dateTime = \DateTime::createFromFormat(\DateTimeInterface::ISO8601, $acceptedDate);

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