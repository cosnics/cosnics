<?php

namespace Chamilo\Application\Lti\Domain\Role;

/**
 * Class SystemRole
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class InstitutionRole extends Role
{
    const ROLE_STUDENT = 'urn:lti:instrole:ims/lis/Student';
    const ROLE_FACULTY = 'urn:lti:instrole:ims/lis/Faculty';
    const ROLE_MEMBER = 'urn:lti:instrole:ims/lis/Member';
    const ROLE_LEARNER = 'urn:lti:instrole:ims/lis/Learner';
    const ROLE_INSTRUCTOR = 'urn:lti:instrole:ims/lis/Instructor';
    const ROLE_MENTOR = 'urn:lti:instrole:ims/lis/Mentor';
    const ROLE_STAFF = 'urn:lti:instrole:ims/lis/Staff';
    const ROLE_ALUMNI = 'urn:lti:instrole:ims/lis/Alumni';
    const ROLE_PROSPECTIVE_STUDENT = 'urn:lti:instrole:ims/lis/ProspectiveStudent';
    const ROLE_GUEST = 'urn:lti:instrole:ims/lis/Guest';
    const ROLE_OTHER = 'urn:lti:instrole:ims/lis/Other';
    const ROLE_ADMINISTRATOR = 'urn:lti:instrole:ims/lis/Administrator';
    const ROLE_OBSERVER = 'urn:lti:instrole:ims/lis/Observer';
    const ROLE_NONE = 'urn:lti:instrole:ims/lis/None';

    /**
     * @return array
     */
    public function getAvailableRoles()
    {
        return [
            self::ROLE_STUDENT, self::ROLE_FACULTY, self::ROLE_MEMBER, self::ROLE_LEARNER, self::ROLE_INSTRUCTOR,
            self::ROLE_MENTOR, self::ROLE_STAFF, self::ROLE_ALUMNI, self::ROLE_PROSPECTIVE_STUDENT,
            self::ROLE_GUEST, self::ROLE_OTHER, self::ROLE_ADMINISTRATOR, self::ROLE_OBSERVER, self::ROLE_NONE
        ];
    }
}