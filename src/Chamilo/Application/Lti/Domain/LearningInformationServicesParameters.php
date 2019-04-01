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
     */
    public function setPersonNameGiven(string $personNameGiven): void
    {
        $this->personNameGiven = $personNameGiven;
    }

    /**
     * @param string $personNameFamily
     */
    public function setPersonNameFamily(string $personNameFamily): void
    {
        $this->personNameFamily = $personNameFamily;
    }

    /**
     * @param string $personNameFull
     */
    public function setPersonNameFull(string $personNameFull): void
    {
        $this->personNameFull = $personNameFull;
    }

    /**
     * @param string $personContactEmailPrimary
     */
    public function setPersonContactEmailPrimary(string $personContactEmailPrimary): void
    {
        $this->personContactEmailPrimary = $personContactEmailPrimary;
    }

    /**
     * @param string $personSourcedId
     */
    public function setPersonSourceDid(string $personSourcedId): void
    {
        $this->personSourcedId = $personSourcedId;
    }

    /**
     * @param string $resultSourcedId
     */
    public function setResultSourcedId(string $resultSourcedId): void
    {
        $this->resultSourcedId = $resultSourcedId;
    }

    /**
     * @param string $outcomeServiceUrl
     */
    public function setOutcomeServiceUrl(string $outcomeServiceUrl): void
    {
        $this->outcomeServiceUrl = $outcomeServiceUrl;
    }

    /**
     * @param string $courseOfferingSourcedId
     */
    public function setCourseOfferingSourcedId(string $courseOfferingSourcedId): void
    {
        $this->courseOfferingSourcedId = $courseOfferingSourcedId;
    }

    /**
     * @param string $courseSectionSourcedId
     */
    public function setCourseSectionSourcedId(string $courseSectionSourcedId): void
    {
        $this->courseSectionSourcedId = $courseSectionSourcedId;
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