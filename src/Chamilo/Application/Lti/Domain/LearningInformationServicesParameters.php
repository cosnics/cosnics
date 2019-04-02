<?php

namespace Chamilo\Application\Lti\Domain;

/**
 * Class LearningInformationServicesParameters
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class LearningInformationServicesParameters
{
    /**
     * Recommended
     *
     * @var string
     */
    protected $personNameGiven;

    /**
     * Recommended
     *
     * @var string
     */
    protected $personNameFamily;

    /**
     * Recommended
     *
     * @var string
     */
    protected $personNameFull;

    /**
     * Recommended
     *
     * @var string
     */
    protected $personContactEmailPrimary;

    /**
     * This field contains the LIS identifier for the user account that is performing this launch
     *
     * @var string
     */
    protected $personSourcedId;


    /**
     * Use this in when you want to store the result of the LTI activity / tool in the LMS
     *
     * Recommended for result services
     *
     * @var string
     */
    protected $resultSourcedId;

    /**
     * Use this in when you want to store the result of the LTI activity / tool in the LMS
     *
     * Recommended for result services
     *
     * @var string
     */
    protected $outcomeServiceUrl;

    /**
     * @var string
     */
    protected $courseOfferingSourcedId;

    /**
     * @var string
     */
    protected $courseSectionSourcedId;

    /**
     * @param string $personNameGiven
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setPersonNameGiven(string $personNameGiven): LearningInformationServicesParameters
    {
        $this->personNameGiven = $personNameGiven;

        return $this;
    }

    /**
     * @param string $personNameFamily
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setPersonNameFamily(string $personNameFamily): LearningInformationServicesParameters
    {
        $this->personNameFamily = $personNameFamily;

        return $this;
    }

    /**
     * @param string $personNameFull
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setPersonNameFull(string $personNameFull): LearningInformationServicesParameters
    {
        $this->personNameFull = $personNameFull;

        return $this;
    }

    /**
     * @param string $personContactEmailPrimary
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setPersonContactEmailPrimary(string $personContactEmailPrimary): LearningInformationServicesParameters
    {
        $this->personContactEmailPrimary = $personContactEmailPrimary;

        return $this;
    }

    /**
     * @param string $personSourcedId
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setPersonSourceDid(string $personSourcedId): LearningInformationServicesParameters
    {
        $this->personSourcedId = $personSourcedId;

        return $this;
    }

    /**
     * @param string $resultSourcedId
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setResultSourcedId(string $resultSourcedId): LearningInformationServicesParameters
    {
        $this->resultSourcedId = $resultSourcedId;

        return $this;
    }

    /**
     * @param string $outcomeServiceUrl
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setOutcomeServiceUrl(string $outcomeServiceUrl): LearningInformationServicesParameters
    {
        $this->outcomeServiceUrl = $outcomeServiceUrl;

        return $this;
    }

    /**
     * @param string $courseOfferingSourcedId
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setCourseOfferingSourcedId(string $courseOfferingSourcedId): LearningInformationServicesParameters
    {
        $this->courseOfferingSourcedId = $courseOfferingSourcedId;

        return $this;
    }

    /**
     * @param string $courseSectionSourcedId
     *
     * @return \Chamilo\Application\Lti\Domain\LearningInformationServicesParameters
     */
    public function setCourseSectionSourcedId(string $courseSectionSourcedId): LearningInformationServicesParameters
    {
        $this->courseSectionSourcedId = $courseSectionSourcedId;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'lis_person_name_given' => $this->personNameGiven,
            'lis_person_name_family' => $this->personNameFamily,
            'lis_person_name_full' => $this->personNameFull,
            'lis_person_contact_email_primary' => $this->personContactEmailPrimary,
            'lis_result_sourcedid' => $this->resultSourcedId,
            'lis_outcome_service_url' => $this->outcomeServiceUrl,
            'lis_person_sourcedid' => $this->personSourcedId,
            'lis_course_offering_sourcedid' => $this->personSourcedId,
            'lis_course_section_sourcedid' => $this->personSourcedId

        ];
    }
}