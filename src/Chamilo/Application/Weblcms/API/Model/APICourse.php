<?php

namespace Chamilo\Application\Weblcms\API\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'Course')]

class APICourse
{
    #[OA\Property]
    protected int $id;
    #[OA\Property]
    protected ?string $sisCourseId = null;
    #[OA\Property]
    protected ?string $uuid = null;
    #[OA\Property]
    protected ?string $integrationId = null;
    #[OA\Property]
    protected ?int $sisImportId = null;
    #[OA\Property]
    protected string $name;
    #[OA\Property]
    protected string $courseCode;
    #[OA\Property]
    protected ?string $originalName = null;
    //the current state of the course one of 'unpublished', 'available',
    #[OA\Property]
    protected ?string $workflowState = null;
    #[OA\Property]
    protected int $accountId = 0;
    #[OA\Property]
    protected int $rootAccountId = 0;
    #[OA\Property]
    protected ?int $enrollmentTermId = null;
    #[OA\Property]
    protected ?array $gradingPeriods = null;
    #[OA\Property]
    protected int $gradingStandardId = 0;
    #[OA\Property]
    protected ?string $gradePassbackSetting = null;
    #[OA\Property]
    protected string $createdAt = '';
    #[OA\Property]
    protected ?string $startAt = null;
    #[OA\Property]
    protected ?string $endAt = null;
    #[OA\Property]
    protected ?string $locale = null;
    #[OA\Property]
    protected ?array $enrollments = null;
    #[OA\Property]
    protected ?int $totalStudents = null;
    #[OA\Property]
    protected ?string $calendar = null;
    #[OA\Property]
    protected ?string $defaultView = null;
    #[OA\Property]
    protected ?string $syllabusBody = null;
    #[OA\Property]
    protected ?int $needsGradingCount = null;
    #[OA\Property]
    protected ?array $term = null;
    #[OA\Property]
    protected ?array $courseProgress = null;
    #[OA\Property]
    protected ?bool $applyAssignmentGroupWeights = null;
    #[OA\Property]
    protected ?array $permissions = null;
    #[OA\Property]
    protected ?bool $isPublic = null;
    #[OA\Property]
    protected ?bool $isPublicToAuthUsers = null;
    #[OA\Property]
    protected ?bool $publicSyllabus = null;
    #[OA\Property]
    protected ?bool $publicSyllabusToAuth = null;
    #[OA\Property]
    protected ?string $publicDescription = null;
    #[OA\Property]
    protected ?int $storageQuotaMb = null;
    #[OA\Property]
    protected ?int $storageQuotaUsedMb = null;
    #[OA\Property]
    protected ?bool $hideFinalGrades = null;
    #[OA\Property]
    protected ?string $license = null;
    #[OA\Property]
    protected ?bool $allowStudentAssignmentEdits = null;
    #[OA\Property]
    protected ?bool $allowWikiComments = null;
    #[OA\Property]
    protected ?bool $allowStudentForumAttachments = null;
    #[OA\Property]
    protected ?bool $openEnrollment = null;
    #[OA\Property]
    protected ?bool $selfEnrollment = null;
    #[OA\Property]
    protected ?bool $restrictEnrollmentsToCourseDates = null;
    #[OA\Property]
    protected ?string $courseFormat = null;
    #[OA\Property]
    protected ?bool $accessRestrictedByDate = null;
    #[OA\Property]
    protected ?string $timeZone = null;
    #[OA\Property]
    protected ?bool $blueprint = null;
    #[OA\Property]
    protected ?array $blueprintRestrictions = null;
    #[OA\Property]
    protected ?array $blueprintRestrictionsByObjectType = null;
    #[OA\Property]
    protected ?bool $template = null;

    public function getCourseCode(): string
    {
        return $this->courseCode;
    }

    public function setCourseCode(string $courseCode): APICourse
    {
        $this->courseCode = $courseCode;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): APICourse
    {
        $this->id = $id;
        return $this;
    }

    public function getSisCourseId(): ?string
    {
        return $this->sisCourseId;
    }

    public function setSisCourseId(?string $sisCourseId): APICourse
    {
        $this->sisCourseId = $sisCourseId;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): APICourse
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getIntegrationId(): ?string
    {
        return $this->integrationId;
    }

    public function setIntegrationId(?string $integrationId): APICourse
    {
        $this->integrationId = $integrationId;
        return $this;
    }

    public function getSisImportId(): ?int
    {
        return $this->sisImportId;
    }

    public function setSisImportId(?int $sisImportId): APICourse
    {
        $this->sisImportId = $sisImportId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): APICourse
    {
        $this->name = $name;
        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): APICourse
    {
        $this->originalName = $originalName;
        return $this;
    }

    public function getWorkflowState(): ?string
    {
        return $this->workflowState;
    }

    public function setWorkflowState(?string $workflowState): APICourse
    {
        $this->workflowState = $workflowState;
        return $this;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function setAccountId(int $accountId): APICourse
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function getRootAccountId(): int
    {
        return $this->rootAccountId;
    }

    public function setRootAccountId(int $rootAccountId): APICourse
    {
        $this->rootAccountId = $rootAccountId;
        return $this;
    }

    public function getEnrollmentTermId(): ?int
    {
        return $this->enrollmentTermId;
    }

    public function setEnrollmentTermId(?int $enrollmentTermId): APICourse
    {
        $this->enrollmentTermId = $enrollmentTermId;
        return $this;
    }

    public function getGradingPeriods(): ?array
    {
        return $this->gradingPeriods;
    }

    public function setGradingPeriods(?array $gradingPeriods): APICourse
    {
        $this->gradingPeriods = $gradingPeriods;
        return $this;
    }

    public function getGradingStandardId(): int
    {
        return $this->gradingStandardId;
    }

    public function setGradingStandardId(int $gradingStandardId): APICourse
    {
        $this->gradingStandardId = $gradingStandardId;
        return $this;
    }

    public function getGradePassbackSetting(): ?string
    {
        return $this->gradePassbackSetting;
    }

    public function setGradePassbackSetting(?string $gradePassbackSetting): APICourse
    {
        $this->gradePassbackSetting = $gradePassbackSetting;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): APICourse
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStartAt(): ?string
    {
        return $this->startAt;
    }

    public function setStartAt(?string $startAt): APICourse
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?string
    {
        return $this->endAt;
    }

    public function setEndAt(?string $endAt): APICourse
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): APICourse
    {
        $this->locale = $locale;
        return $this;
    }

    public function getEnrollments(): ?array
    {
        return $this->enrollments;
    }

    public function setEnrollments(?array $enrollments): APICourse
    {
        $this->enrollments = $enrollments;
        return $this;
    }

    public function getTotalStudents(): ?int
    {
        return $this->totalStudents;
    }

    public function setTotalStudents(?int $totalStudents): APICourse
    {
        $this->totalStudents = $totalStudents;
        return $this;
    }

    public function getCalendar(): ?string
    {
        return $this->calendar;
    }

    public function setCalendar(?string $calendar): APICourse
    {
        $this->calendar = $calendar;
        return $this;
    }

    public function getDefaultView(): ?string
    {
        return $this->defaultView;
    }

    public function setDefaultView(?string $defaultView): APICourse
    {
        $this->defaultView = $defaultView;
        return $this;
    }

    public function getSyllabusBody(): ?string
    {
        return $this->syllabusBody;
    }

    public function setSyllabusBody(?string $syllabusBody): APICourse
    {
        $this->syllabusBody = $syllabusBody;
        return $this;
    }

    public function getNeedsGradingCount(): ?int
    {
        return $this->needsGradingCount;
    }

    public function setNeedsGradingCount(?int $needsGradingCount): APICourse
    {
        $this->needsGradingCount = $needsGradingCount;
        return $this;
    }

    public function getTerm(): ?array
    {
        return $this->term;
    }

    public function setTerm(?array $term): APICourse
    {
        $this->term = $term;
        return $this;
    }

    public function getCourseProgress(): ?array
    {
        return $this->courseProgress;
    }

    public function setCourseProgress(?array $courseProgress): APICourse
    {
        $this->courseProgress = $courseProgress;
        return $this;
    }

    public function getApplyAssignmentGroupWeights(): ?bool
    {
        return $this->applyAssignmentGroupWeights;
    }

    public function setApplyAssignmentGroupWeights(?bool $applyAssignmentGroupWeights): APICourse
    {
        $this->applyAssignmentGroupWeights = $applyAssignmentGroupWeights;
        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): APICourse
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(?bool $isPublic): APICourse
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getIsPublicToAuthUsers(): ?bool
    {
        return $this->isPublicToAuthUsers;
    }

    public function setIsPublicToAuthUsers(?bool $isPublicToAuthUsers): APICourse
    {
        $this->isPublicToAuthUsers = $isPublicToAuthUsers;
        return $this;
    }

    public function getPublicSyllabus(): ?bool
    {
        return $this->publicSyllabus;
    }

    public function setPublicSyllabus(?bool $publicSyllabus): APICourse
    {
        $this->publicSyllabus = $publicSyllabus;
        return $this;
    }

    public function getPublicSyllabusToAuth(): ?bool
    {
        return $this->publicSyllabusToAuth;
    }

    public function setPublicSyllabusToAuth(?bool $publicSyllabusToAuth): APICourse
    {
        $this->publicSyllabusToAuth = $publicSyllabusToAuth;
        return $this;
    }

    public function getPublicDescription(): ?string
    {
        return $this->publicDescription;
    }

    public function setPublicDescription(?string $publicDescription): APICourse
    {
        $this->publicDescription = $publicDescription;
        return $this;
    }

    public function getStorageQuotaMb(): ?int
    {
        return $this->storageQuotaMb;
    }

    public function setStorageQuotaMb(?int $storageQuotaMb): APICourse
    {
        $this->storageQuotaMb = $storageQuotaMb;
        return $this;
    }

    public function getStorageQuotaUsedMb(): ?int
    {
        return $this->storageQuotaUsedMb;
    }

    public function setStorageQuotaUsedMb(?int $storageQuotaUsedMb): APICourse
    {
        $this->storageQuotaUsedMb = $storageQuotaUsedMb;
        return $this;
    }

    public function getHideFinalGrades(): ?bool
    {
        return $this->hideFinalGrades;
    }

    public function setHideFinalGrades(?bool $hideFinalGrades): APICourse
    {
        $this->hideFinalGrades = $hideFinalGrades;
        return $this;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(?string $license): APICourse
    {
        $this->license = $license;
        return $this;
    }

    public function getAllowStudentAssignmentEdits(): ?bool
    {
        return $this->allowStudentAssignmentEdits;
    }

    public function setAllowStudentAssignmentEdits(?bool $allowStudentAssignmentEdits): APICourse
    {
        $this->allowStudentAssignmentEdits = $allowStudentAssignmentEdits;
        return $this;
    }

    public function getAllowWikiComments(): ?bool
    {
        return $this->allowWikiComments;
    }

    public function setAllowWikiComments(?bool $allowWikiComments): APICourse
    {
        $this->allowWikiComments = $allowWikiComments;
        return $this;
    }

    public function getAllowStudentForumAttachments(): ?bool
    {
        return $this->allowStudentForumAttachments;
    }

    public function setAllowStudentForumAttachments(?bool $allowStudentForumAttachments): APICourse
    {
        $this->allowStudentForumAttachments = $allowStudentForumAttachments;
        return $this;
    }

    public function getOpenEnrollment(): ?bool
    {
        return $this->openEnrollment;
    }

    public function setOpenEnrollment(?bool $openEnrollment): APICourse
    {
        $this->openEnrollment = $openEnrollment;
        return $this;
    }

    public function getSelfEnrollment(): ?bool
    {
        return $this->selfEnrollment;
    }

    public function setSelfEnrollment(?bool $selfEnrollment): APICourse
    {
        $this->selfEnrollment = $selfEnrollment;
        return $this;
    }

    public function getRestrictEnrollmentsToCourseDates(): ?bool
    {
        return $this->restrictEnrollmentsToCourseDates;
    }

    public function setRestrictEnrollmentsToCourseDates(?bool $restrictEnrollmentsToCourseDates): APICourse
    {
        $this->restrictEnrollmentsToCourseDates = $restrictEnrollmentsToCourseDates;
        return $this;
    }

    public function getCourseFormat(): ?string
    {
        return $this->courseFormat;
    }

    public function setCourseFormat(?string $courseFormat): APICourse
    {
        $this->courseFormat = $courseFormat;
        return $this;
    }

    public function getAccessRestrictedByDate(): ?bool
    {
        return $this->accessRestrictedByDate;
    }

    public function setAccessRestrictedByDate(?bool $accessRestrictedByDate): APICourse
    {
        $this->accessRestrictedByDate = $accessRestrictedByDate;
        return $this;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function setTimeZone(?string $timeZone): APICourse
    {
        $this->timeZone = $timeZone;
        return $this;
    }

    public function getBlueprint(): ?bool
    {
        return $this->blueprint;
    }

    public function setBlueprint(?bool $blueprint): APICourse
    {
        $this->blueprint = $blueprint;
        return $this;
    }

    public function getBlueprintRestrictions(): ?array
    {
        return $this->blueprintRestrictions;
    }

    public function setBlueprintRestrictions(?array $blueprintRestrictions): APICourse
    {
        $this->blueprintRestrictions = $blueprintRestrictions;
        return $this;
    }

    public function getBlueprintRestrictionsByObjectType(): ?array
    {
        return $this->blueprintRestrictionsByObjectType;
    }

    public function setBlueprintRestrictionsByObjectType(?array $blueprintRestrictionsByObjectType): APICourse
    {
        $this->blueprintRestrictionsByObjectType = $blueprintRestrictionsByObjectType;
        return $this;
    }

    public function getTemplate(): ?bool
    {
        return $this->template;
    }

    public function setTemplate(?bool $template): APICourse
    {
        $this->template = $template;
        return $this;
    }
}
