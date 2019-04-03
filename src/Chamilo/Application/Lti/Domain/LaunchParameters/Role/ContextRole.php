<?php

namespace Chamilo\Application\Lti\Domain\LaunchParameters\Role;

/**
 * Class SystemRole
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ContextRole extends Role
{
    const ROLE_LEARNER = 'urn:lti:role:ims/lis/Learner';
    const ROLE_LEARNER_LEARNER = 'urn:lti:role:ims/lis/Learner/Learner';
    const ROLE_LEARNER_NON_CREDIT_LEARNER = 'urn:lti:role:ims/lis/Learner/NonCreditLearner';
    const ROLE_LEARNER_GUEST_LEARNER = 'urn:lti:role:ims/lis/Learner/GuestLearner';
    const ROLE_LEARNER_EXTERNAL_LEARNER = 'urn:lti:role:ims/lis/Learner/ExternalLearner';
    const ROLE_LEARNER_INSTRUCTOR = 'urn:lti:role:ims/lis/Learner/Instructor';
    const ROLE_INSTRUCTOR = 'urn:lti:role:ims/lis/Instructor';
    const ROLE_INSTRUCTOR_PRIMARY_INSTRUCTOR = 'urn:lti:role:ims/lis/Instructor/PrimaryInstructor';
    const ROLE_INSTRUCTOR_LECTURER = 'urn:lti:role:ims/lis/Instructor/Lecturer';
    const ROLE_INSTRUCTOR_GUEST_INSTRUCTOR = 'urn:lti:role:ims/lis/Instructor/GuestInstructor';
    const ROLE_INSTRUCTOR_EXTERNAL_INSTRUCTOR = 'urn:lti:role:ims/lis/Instructor/ExternalInstructor';
    const ROLE_CONTENT_DEVELOPER = 'urn:lti:role:ims/lis/ContentDeveloper';
    const ROLE_CONTENT_DEVELOPER_CONTENT_DEVELOPER = 'urn:lti:role:ims/lis/ContentDeveloper/ContentDeveloper';
    const ROLE_CONTENT_DEVELOPER_LIBRARIAN = 'urn:lti:role:ims/lis/ContentDeveloper/Librarian';
    const ROLE_CONTENT_DEVELOPER_CONTENT_EXPERT = 'urn:lti:role:ims/lis/ContentDeveloper/ContentExpert';
    const ROLE_CONTENT_DEVELOPER_EXTERNAL_CONTENT_EXPERT = 'urn:lti:role:ims/lis/ContentDeveloper/ExternalContentExpert';
    const ROLE_MEMBER = 'urn:lti:role:ims/lis/Member';
    const ROLE_MEMBER_MEMBER = 'urn:lti:role:ims/lis/Member/Member';
    const ROLE_MANAGER = 'urn:lti:role:ims/lis/Manager';
    const ROLE_MANAGER_AREA_MANAGER = 'urn:lti:role:ims/lis/Manager/AreaManager';
    const ROLE_MANAGER_COURSE_COORDINATOR = 'urn:lti:role:ims/lis/Manager/CourseCoordinator';
    const ROLE_MANAGER_OBSERVER = 'urn:lti:role:ims/lis/Manager/Observer';
    const ROLE_MANAGER_EXTERNAL_OBSERVER = 'urn:lti:role:ims/lis/Manager/ExternalObserver';
    const ROLE_MENTOR = 'urn:lti:role:ims/lis/Mentor';
    const ROLE_MENTOR_MENTOR = 'urn:lti:role:ims/lis/Mentor/Mentor';
    const ROLE_MENTOR_REVIEWER = 'urn:lti:role:ims/lis/Mentor/Reviewer';
    const ROLE_MENTOR_ADVISOR = 'urn:lti:role:ims/lis/Mentor/Advisor';
    const ROLE_MENTOR_AUDITOR = 'urn:lti:role:ims/lis/Mentor/Auditor';
    const ROLE_MENTOR_TUTOR = 'urn:lti:role:ims/lis/Mentor/Tutor';
    const ROLE_MENTOR_LEARNING_FACILITATOR = 'urn:lti:role:ims/lis/Mentor/LearningFacilitator';
    const ROLE_MENTOR_EXTERNAL_MENTOR = 'urn:lti:role:ims/lis/Mentor/ExternalMentor';
    const ROLE_MENTOR_EXTERNAL_REVIEWER = 'urn:lti:role:ims/lis/Mentor/ExternalReviewer';
    const ROLE_MENTOR_EXTERNAL_ADVISOR = 'urn:lti:role:ims/lis/Mentor/ExternalAdvisor';
    const ROLE_MENTOR_EXTERNAL_AUDITOR = 'urn:lti:role:ims/lis/Mentor/ExternalAuditor';
    const ROLE_MENTOR_EXTERNAL_TUTOR = 'urn:lti:role:ims/lis/Mentor/ExternalTutor';
    const ROLE_MENTOR_EXTERNAL_LEARNING_FACILITATOR = 'urn:lti:role:ims/lis/Mentor/ExternalLearningFacilitator';
    const ROLE_ADMINISTRATOR = 'urn:lti:role:ims/lis/Administrator';
    const ROLE_ADMINISTRATOR_ADMINISTRATOR = 'urn:lti:role:ims/lis/Administrator/Administrator';
    const ROLE_ADMINISTRATOR_SUPPORT = 'urn:lti:role:ims/lis/Administrator/Support';
    const ROLE_ADMINISTRATOR_DEVELOPER = 'urn:lti:role:ims/lis/Administrator/Developer';
    const ROLE_ADMINISTRATOR_SYSTEM_ADMINISTRATOR = 'urn:lti:role:ims/lis/Administrator/SystemAdministrator';
    const ROLE_ADMINISTRATOR_EXTERNAL_SYSTEM_ADMINISTRATOR = 'urn:lti:role:ims/lis/Administrator/ExternalSystemAdministrator';
    const ROLE_ADMINISTRATOR_EXTERNAL_DEVELOPER = 'urn:lti:role:ims/lis/Administrator/ExternalDeveloper';
    const ROLE_ADMINISTRATOR_EXTERNAL_SUPPORT = 'urn:lti:role:ims/lis/Administrator/ExternalSupport';
    const ROLE_TEACHING_ASSISTANT = 'urn:lti:role:ims/lis/TeachingAssistant';
    const ROLE_TEACHING_ASSISTANT_TEACHING_ASSISTANT = 'urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistant';
    const ROLE_TEACHING_ASSISTANT_SECTION = 'urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantSection';
    const ROLE_TEACHING_ASSISTANT_SECTION_ASSOCIATION = 'urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantSectionAssociation';
    const ROLE_TEACHING_ASSISTANT_OFFERING = 'urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantOffering';
    const ROLE_TEACHING_ASSISTANT_TEMPLATE = 'urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantTemplate';
    const ROLE_TEACHING_ASSISTANT_GROUP = 'urn:lti:role:ims/lis/TeachingAssistant/TeachingAssistantGroup';
    const ROLE_TEACHING_ASSISTANT_GRADER = 'urn:lti:role:ims/lis/TeachingAssistant/Grader';

    /**
     * @return array
     */
    public function getAvailableRoles()
    {
        return [
            self::ROLE_LEARNER, self::ROLE_LEARNER_LEARNER, self::ROLE_LEARNER_NON_CREDIT_LEARNER,
            self::ROLE_LEARNER_GUEST_LEARNER, self::ROLE_LEARNER_EXTERNAL_LEARNER, self::ROLE_LEARNER_INSTRUCTOR,
            self::ROLE_INSTRUCTOR, self::ROLE_INSTRUCTOR_PRIMARY_INSTRUCTOR, self::ROLE_INSTRUCTOR_LECTURER,
            self::ROLE_INSTRUCTOR_GUEST_INSTRUCTOR, self::ROLE_INSTRUCTOR_EXTERNAL_INSTRUCTOR,
            self::ROLE_CONTENT_DEVELOPER, self::ROLE_CONTENT_DEVELOPER_CONTENT_DEVELOPER,
            self::ROLE_CONTENT_DEVELOPER_LIBRARIAN, self::ROLE_CONTENT_DEVELOPER_CONTENT_EXPERT,
            self::ROLE_CONTENT_DEVELOPER_EXTERNAL_CONTENT_EXPERT, self::ROLE_MEMBER, self::ROLE_MEMBER_MEMBER,
            self::ROLE_MANAGER, self::ROLE_MANAGER_AREA_MANAGER, self::ROLE_MANAGER_COURSE_COORDINATOR,
            self::ROLE_MANAGER_OBSERVER, self::ROLE_MANAGER_EXTERNAL_OBSERVER, self::ROLE_MENTOR,
            self::ROLE_MENTOR_MENTOR, self::ROLE_MENTOR_REVIEWER, self::ROLE_MENTOR_ADVISOR, self::ROLE_MENTOR_AUDITOR,
            self::ROLE_MENTOR_TUTOR, self::ROLE_MENTOR_LEARNING_FACILITATOR, self::ROLE_MENTOR_EXTERNAL_MENTOR,
            self::ROLE_MENTOR_EXTERNAL_REVIEWER, self::ROLE_MENTOR_EXTERNAL_ADVISOR, self::ROLE_MENTOR_EXTERNAL_AUDITOR,
            self::ROLE_MENTOR_EXTERNAL_TUTOR, self::ROLE_MENTOR_EXTERNAL_LEARNING_FACILITATOR, self::ROLE_ADMINISTRATOR,
            self::ROLE_ADMINISTRATOR_ADMINISTRATOR, self::ROLE_ADMINISTRATOR_SUPPORT,
            self::ROLE_ADMINISTRATOR_DEVELOPER, self::ROLE_ADMINISTRATOR_SYSTEM_ADMINISTRATOR,
            self::ROLE_ADMINISTRATOR_EXTERNAL_SYSTEM_ADMINISTRATOR, self::ROLE_ADMINISTRATOR_EXTERNAL_DEVELOPER,
            self::ROLE_ADMINISTRATOR_EXTERNAL_SUPPORT, self::ROLE_TEACHING_ASSISTANT,
            self::ROLE_TEACHING_ASSISTANT_TEACHING_ASSISTANT, self::ROLE_TEACHING_ASSISTANT_SECTION,
            self::ROLE_TEACHING_ASSISTANT_SECTION_ASSOCIATION, self::ROLE_TEACHING_ASSISTANT_OFFERING,
            self::ROLE_TEACHING_ASSISTANT_TEMPLATE, self::ROLE_TEACHING_ASSISTANT_GROUP,
            self::ROLE_TEACHING_ASSISTANT_GRADER

        ];
    }
}