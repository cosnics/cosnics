<?php

namespace Chamilo\Application\Lti\Domain;

use Chamilo\Application\Lti\Domain\Role\Role;

/**
 * Class LaunchParameters
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class LaunchParameters
{
    const DOCUMENT_TARGET_WINDOW = 'window';
    const DOCUMENT_TARGET_IFRAME = 'iframe';
    const DOCUMENT_TARGET_FRAME = 'frame';

    /**
     * Required
     *
     * @var string
     */
    protected $ltiMessageType = 'basic-lti-launch-request';

    /**
     * Required
     *
     * @var string
     */
    protected $ltiVersion = 'LTI-1p0';

    /**
     * Required
     *
     * @var string
     */
    protected $resourceLinkId;

    /**
     * Recommended
     *
     * @var string
     */
    protected $resourceLinkTitle;

    /**
     * @var string
     */
    protected $resourceLinkDescription;

    /**
     * Best practice is that this field should be a TC-generated long-term “primary key” to the user record – not the “logical key"
     *
     * Recommended
     *
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $userImageUrl;

    /**
     * Recommended
     *
     * @var Role[]
     */
    protected $roles;

    /**
     * Recommended
     *
     * @var string
     */
    protected $lisPersonNameGiven;

    /**
     * Recommended
     *
     * @var string
     */
    protected $lisPersonNameFamily;

    /**
     * Recommended
     *
     * @var string
     */
    protected $lisPersonNameFull;

    /**
     * Recommended
     *
     * @var string
     */
    protected $listPersonContactEmailPrimary;

    /**
     * Recommended
     *
     * @var string
     */
    protected $contextId;

    /**
     * @var \Chamilo\Application\Lti\Domain\ContextType
     */
    protected $contextType;

    /**
     * Recommended
     *
     * @var string
     */
    protected $contextTitle;

    /**
     * Recommended
     *
     * @var string
     */
    protected $contextLabel;

    /**
     * @var string
     */
    protected $launchPresentationLocale = 'en-US';

    /**
     * The value should be either ‘frame’, ‘iframe’ or ‘window’.
     *
     * Recommended
     *
     * @var string
     */
    protected $launchPresentationDocumentTarget = 'iframe';

    /**
     * This is a URL to an LMS-specific CSS URL.  There are no standards that describe exactly what CSS classes, etc. should be in this CSS.  The TC could send its standard CSS URL that it would apply to its local tools.
     * The TC should include styling for HTML tags to set font, color, etc. and also include its proprietary tags used to style its internal tools.
     *
     * @var string
     */
    protected $launchPresentationCssUrl;

    /**
     * Recommended
     *
     * @var int
     */
    protected $launchPresentationWidth = 1280;

    /**
     * Recommended
     *
     * @var int
     */
    protected $launchPresentationHeight = 720;

    /**
     * Recommended
     *
     * @var string
     */
    protected $launchPresentationReturnUrl;

    /**
     * @var string
     */
    protected $toolConsumerInfoProductFamilyCode = 'Cosnics';

    /**
     * @var string
     */
    protected $toolConsumerInfoVersion = '1.0';

    /**
     * This is a unique identifier for the TC.  A common practice is to use the DNS of the organization or the DNS of the TC instance
     *
     * Recommended
     *
     * @var string
     */
    protected $toolConsumerInstanceGuid;

    /**
     * Recommended
     *
     * @var string
     */
    protected $toolConsumerInstanceName;

    /**
     * @var string
     */
    protected $toolConsumerInstanceDescription;

    /**
     * @var string
     */
    protected $toolConsumerInstanceUrl;

    /**
     * Recommended
     *
     * @var string
     */
    protected $toolConsumerInstanceContactEmail;

    /**
     * @var CustomLaunchParameter[]
     */
    protected $customLaunchParameters;

    /**
     * @param string $ltiMessageType
     *
     * @return LaunchParameters
     */
    public function setLtiMessageType(string $ltiMessageType): LaunchParameters
    {
        $this->ltiMessageType = $ltiMessageType;

        return $this;
    }

    /**
     * @param string $ltiVersion
     *
     * @return LaunchParameters
     */
    public function setLtiVersion(string $ltiVersion): LaunchParameters
    {
        $this->ltiVersion = $ltiVersion;

        return $this;
    }

    /**
     * @param string $resourceLinkId
     *
     * @return LaunchParameters
     */
    public function setResourceLinkId(string $resourceLinkId): LaunchParameters
    {
        $this->resourceLinkId = $resourceLinkId;

        return $this;
    }

    /**
     * @param string $resourceLinkTitle
     *
     * @return LaunchParameters
     */
    public function setResourceLinkTitle(string $resourceLinkTitle): LaunchParameters
    {
        $this->resourceLinkTitle = $resourceLinkTitle;

        return $this;
    }

    /**
     * @param string $resourceLinkDescription
     *
     * @return LaunchParameters
     */
    public function setResourceLinkDescription(string $resourceLinkDescription): LaunchParameters
    {
        $this->resourceLinkDescription = $resourceLinkDescription;

        return $this;
    }

    /**
     * @param string $userId
     *
     * @return LaunchParameters
     */
    public function setUserId(string $userId): LaunchParameters
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param string $userImageUrl
     *
     * @return LaunchParameters
     */
    public function setUserImageUrl(string $userImageUrl): LaunchParameters
    {
        $this->userImageUrl = $userImageUrl;

        return $this;
    }

    /**
     * @param string $lisPersonNameGiven
     *
     * @return LaunchParameters
     */
    public function setLisPersonNameGiven(string $lisPersonNameGiven): LaunchParameters
    {
        $this->lisPersonNameGiven = $lisPersonNameGiven;

        return $this;
    }

    /**
     * @param string $lisPersonNameFamily
     *
     * @return LaunchParameters
     */
    public function setLisPersonNameFamily(string $lisPersonNameFamily): LaunchParameters
    {
        $this->lisPersonNameFamily = $lisPersonNameFamily;

        return $this;
    }

    /**
     * @param string $lisPersonNameFull
     *
     * @return LaunchParameters
     */
    public function setLisPersonNameFull(string $lisPersonNameFull): LaunchParameters
    {
        $this->lisPersonNameFull = $lisPersonNameFull;

        return $this;
    }

    /**
     * @param string $listPersonContactEmailPrimary
     *
     * @return LaunchParameters
     */
    public function setListPersonContactEmailPrimary(string $listPersonContactEmailPrimary): LaunchParameters
    {
        $this->listPersonContactEmailPrimary = $listPersonContactEmailPrimary;

        return $this;
    }

    /**
     * @param string $contextId
     *
     * @return LaunchParameters
     */
    public function setContextId(string $contextId): LaunchParameters
    {
        $this->contextId = $contextId;

        return $this;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\ContextType $contextType
     *
     * @return LaunchParameters
     */
    public function setContextType(\Chamilo\Application\Lti\Domain\ContextType $contextType): LaunchParameters
    {
        $this->contextType = $contextType;

        return $this;
    }

    /**
     * @param string $contextTitle
     *
     * @return LaunchParameters
     */
    public function setContextTitle(string $contextTitle): LaunchParameters
    {
        $this->contextTitle = $contextTitle;

        return $this;
    }

    /**
     * @param string $contextLabel
     *
     * @return LaunchParameters
     */
    public function setContextLabel(string $contextLabel): LaunchParameters
    {
        $this->contextLabel = $contextLabel;

        return $this;
    }

    /**
     * @param string $launchPresentationLocale
     *
     * @return LaunchParameters
     */
    public function setLaunchPresentationLocale(string $launchPresentationLocale): LaunchParameters
    {
        $this->launchPresentationLocale = $launchPresentationLocale;

        return $this;
    }

    /**
     * @param string $launchPresentationDocumentTarget
     *
     * @return LaunchParameters
     */
    public function setLaunchPresentationDocumentTarget(string $launchPresentationDocumentTarget): LaunchParameters
    {
        $allowedTargets = [self::DOCUMENT_TARGET_WINDOW, self::DOCUMENT_TARGET_IFRAME, self::DOCUMENT_TARGET_FRAME];

        if (!in_array($launchPresentationDocumentTarget, $allowedTargets))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given launch presentation document target %s is invalid. Please use one of the following: %s',
                    $launchPresentationDocumentTarget, implode(', ', $allowedTargets)
                )
            );
        }

        $this->launchPresentationDocumentTarget = $launchPresentationDocumentTarget;

        return $this;
    }

    /**
     * @param string $launchPresentationCssUrl
     *
     * @return LaunchParameters
     */
    public function setLaunchPresentationCssUrl(string $launchPresentationCssUrl): LaunchParameters
    {
        $this->launchPresentationCssUrl = $launchPresentationCssUrl;

        return $this;
    }

    /**
     * @param int $launchPresentationWidth
     *
     * @return LaunchParameters
     */
    public function setLaunchPresentationWidth(int $launchPresentationWidth): LaunchParameters
    {
        $this->launchPresentationWidth = $launchPresentationWidth;

        return $this;
    }

    /**
     * @param int $launchPresentationHeight
     *
     * @return LaunchParameters
     */
    public function setLaunchPresentationHeight(int $launchPresentationHeight): LaunchParameters
    {
        $this->launchPresentationHeight = $launchPresentationHeight;

        return $this;
    }

    /**
     * @param string $launchPresentationReturnUrl
     *
     * @return LaunchParameters
     */
    public function setLaunchPresentationReturnUrl(string $launchPresentationReturnUrl): LaunchParameters
    {
        $this->launchPresentationReturnUrl = $launchPresentationReturnUrl;

        return $this;
    }

    /**
     * @param string $toolConsumerInfoProductFamilyCode
     *
     * @return LaunchParameters
     */
    public function setToolConsumerInfoProductFamilyCode(string $toolConsumerInfoProductFamilyCode): LaunchParameters
    {
        $this->toolConsumerInfoProductFamilyCode = $toolConsumerInfoProductFamilyCode;

        return $this;
    }

    /**
     * @param string $toolConsumerInfoVersion
     *
     * @return LaunchParameters
     */
    public function setToolConsumerInfoVersion(string $toolConsumerInfoVersion): LaunchParameters
    {
        $this->toolConsumerInfoVersion = $toolConsumerInfoVersion;

        return $this;
    }

    /**
     * @param string $toolConsumerInstanceGuid
     *
     * @return LaunchParameters
     */
    public function setToolConsumerInstanceGuid(string $toolConsumerInstanceGuid): LaunchParameters
    {
        $this->toolConsumerInstanceGuid = $toolConsumerInstanceGuid;

        return $this;
    }

    /**
     * @param string $toolConsumerInstanceName
     *
     * @return LaunchParameters
     */
    public function setToolConsumerInstanceName(string $toolConsumerInstanceName): LaunchParameters
    {
        $this->toolConsumerInstanceName = $toolConsumerInstanceName;

        return $this;
    }

    /**
     * @param string $toolConsumerInstanceDescription
     *
     * @return LaunchParameters
     */
    public function setToolConsumerInstanceDescription(string $toolConsumerInstanceDescription): LaunchParameters
    {
        $this->toolConsumerInstanceDescription = $toolConsumerInstanceDescription;

        return $this;
    }

    /**
     * @param string $toolConsumerInstanceUrl
     *
     * @return \Chamilo\Application\Lti\Domain\LaunchParameters
     */
    public function setToolConsumerInstanceUrl(string $toolConsumerInstanceUrl): LaunchParameters
    {
        $this->toolConsumerInstanceUrl = $toolConsumerInstanceUrl;

        return $this;
    }

    /**
     * @param string $toolConsumerInstanceContactEmail
     *
     * @return \Chamilo\Application\Lti\Domain\LaunchParameters
     */
    public function setToolConsumerInstanceContactEmail(string $toolConsumerInstanceContactEmail): LaunchParameters
    {
        $this->toolConsumerInstanceContactEmail = $toolConsumerInstanceContactEmail;

        return $this;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Role\Role $role
     *
     * @return \Chamilo\Application\Lti\Domain\LaunchParameters
     */
    public function addRole(Role $role): LaunchParameters
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\CustomLaunchParameter $customLaunchParameter
     *
     * @return \Chamilo\Application\Lti\Domain\LaunchParameters
     */
    public function addCustomLaunchParameters(CustomLaunchParameter $customLaunchParameter): LaunchParameters
    {
        $this->customLaunchParameters[] = $customLaunchParameter;

        return $this;
    }

    /**
     * @return bool
     */
    public function canShowInIFrame()
    {
        return $this->launchPresentationDocumentTarget == self::DOCUMENT_TARGET_IFRAME;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->validateRequiredValues();

        $basicLaunchParameters = [
            'lti_message_type' => $this->ltiMessageType,
            'lti_version' => $this->ltiVersion,
            'resource_link_id' => $this->resourceLinkId,
            'resource_link_title' => $this->resourceLinkTitle,
            'resource_link_description' => $this->resourceLinkDescription,
            'user_id' => $this->userId,
            'user_image' => $this->userImageUrl,
            'lis_person_name_given' => $this->lisPersonNameGiven,
            'lis_person_name_family' => $this->lisPersonNameFamily,
            'lis_person_name_full' => $this->lisPersonNameFull,
            'lis_person_contact_email_primary' => $this->listPersonContactEmailPrimary,
            'context_id' => $this->contextId,
            'context_type' => $this->contextType,
            'context_title' => $this->contextTitle,
            'context_label' => $this->contextLabel,
            'launch_presentation_locale' => $this->launchPresentationLocale,
            'launch_presentation_document_target' => $this->launchPresentationDocumentTarget,
            'launch_presentation_css_url' => $this->launchPresentationCssUrl,
            'launch_presentation_width' => $this->launchPresentationWidth,
            'launch_presentation_height' => $this->launchPresentationHeight,
            'launch_presentation_return_url' => $this->launchPresentationReturnUrl,
            'tool_consumer_info_product_family_code' => $this->toolConsumerInfoProductFamilyCode,
            'tool_consumer_info_version' => $this->toolConsumerInfoVersion,
            'tool_consumer_instance_guid' => $this->toolConsumerInstanceGuid,
            'tool_consumer_instance_name' => $this->toolConsumerInstanceName,
            'tool_consumer_instance_description' => $this->toolConsumerInstanceDescription,
            'tool_consumer_instance_url' => $this->toolConsumerInstanceUrl,
            'tool_consumer_instance_contact_email' => $this->toolConsumerInstanceContactEmail
        ];

        $rolesArray = [];
        foreach ($this->roles as $role)
        {
            $rolesArray[] = $role->getRole();
        }

        $basicLaunchParameters['roles'] = implode(',', $rolesArray);

        foreach ($this->customLaunchParameters as $customLaunchParameter)
        {
            $basicLaunchParameters['custom_' . $customLaunchParameter->getKeyName()] =
                $customLaunchParameter->getValue();
        }

        return $basicLaunchParameters;
    }

    protected function validateRequiredValues()
    {
        if (empty($this->ltiVersion) || empty($this->ltiMessageType) || empty($this->resourceLinkId))
        {
            throw new \RuntimeException(
                'One of the required parameters LTIVersion, LTIMessageType or ResourceLinkID is invalid'
            );
        }
    }

}