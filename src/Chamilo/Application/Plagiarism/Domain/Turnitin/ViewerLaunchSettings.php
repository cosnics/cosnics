<?php

namespace Chamilo\Application\Plagiarism\Domain\Turnitin;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewerLaunchSettings
{
    const DEFAULT_PERMISSION_SET_INSTRUCTOR = 'INSTRUCTOR';
    const DEFAULT_PERMISSION_SET_LEARNER = 'LEARNER';

    /**
     * @var string
     */
    protected $locale = 'en';

    /**
     * @var string
     */
    protected $viewerDefaultPermissionSet;

    /**
     * @var bool
     */
    protected $mayViewSubmissionFullSource;

    /**
     * @var bool
     */
    protected $matchOverviewMode;

    /**
     * @var bool
     */
    protected $allSourcesMode;

    /**
     * @var string
     */
    protected $overrideAuthorFamilyName;

    /**
     * @var string
     */
    protected $overrideAuthorGivenName;

    /**
     * @var string
     */
    protected $eulaVersion;

    /**
     * @var string
     */
    protected $eulaLanguage;

    /**
     * @var \DateTime
     */
    protected $eulaAcceptedTimestamp;

    /**
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param string $viewerDefaultPermissionSet
     */
    public function setViewerDefaultPermissionSet(string $viewerDefaultPermissionSet)
    {
        if (!in_array($viewerDefaultPermissionSet, $this->getAllowedDefaultPermissionSets()))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given permission set %s is not in the list of allowed permission sets (%s)',
                    $viewerDefaultPermissionSet, implode(', ', $this->getAllowedDefaultPermissionSets())
                )
            );
        }

        $this->viewerDefaultPermissionSet = $viewerDefaultPermissionSet;
    }

    /**
     * @param bool $mayViewSubmissionFullSource
     */
    public function setMayViewSubmissionFullSource(bool $mayViewSubmissionFullSource)
    {
        $this->mayViewSubmissionFullSource = $mayViewSubmissionFullSource;
    }

    /**
     * @param bool $matchOverviewMode
     */
    public function setMatchOverviewMode(bool $matchOverviewMode)
    {
        $this->matchOverviewMode = $matchOverviewMode;
    }

    /**
     * @param bool $allSourcesMode
     */
    public function setAllSourcesMode(bool $allSourcesMode)
    {
        $this->allSourcesMode = $allSourcesMode;
    }

    /**
     * @param string $overrideAuthorFamilyName
     */
    public function setOverrideAuthorFamilyName(string $overrideAuthorFamilyName)
    {
        $this->overrideAuthorFamilyName = $overrideAuthorFamilyName;
    }

    /**
     * @param string $overrideAuthorGivenName
     */
    public function setOverrideAuthorGivenName(string $overrideAuthorGivenName)
    {
        $this->overrideAuthorGivenName = $overrideAuthorGivenName;
    }

    /**
     * @param string $eulaVersion
     */
    public function setEulaVersion(string $eulaVersion)
    {
        $this->eulaVersion = $eulaVersion;
    }

    /**
     * @param string $eulaLanguage
     */
    public function setEulaLanguage(string $eulaLanguage)
    {
        $this->eulaLanguage = $eulaLanguage;
    }

    /**
     * @param \DateTime $eulaAcceptedTimestamp
     */
    public function setEulaAcceptedTimestamp(\DateTime $eulaAcceptedTimestamp)
    {
        $this->eulaAcceptedTimestamp = $eulaAcceptedTimestamp;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (empty($this->locale))
        {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'locale' => $this->locale,
            'viewer_default_permission_set' => $this->viewerDefaultPermissionSet,
            'viewer_permissions' => ['may_view_submission_full_source' => $this->mayViewSubmissionFullSource],
            'similarity' => [
                'modes' => ['match_overview' => $this->matchOverviewMode, 'all_sources' => $this->allSourcesMode]
            ],
            'author_metadata_override' => [
                'family_name' => $this->overrideAuthorFamilyName, 'given_name' => $this->overrideAuthorGivenName
            ],
            'eula' => [
                'version' => $this->eulaVersion,
                'accepted_timestamp' => $this->eulaAcceptedTimestamp->format(\DateTimeInterface::ISO8601),
                'language' => $this->eulaLanguage
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getAllowedDefaultPermissionSets()
    {
        return [self::DEFAULT_PERMISSION_SET_INSTRUCTOR, self::DEFAULT_PERMISSION_SET_LEARNER];
    }
}