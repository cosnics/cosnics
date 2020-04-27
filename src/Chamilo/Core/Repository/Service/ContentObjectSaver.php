<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Configuration;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\UUID;

/**
 * @package Chamilo\Core\Repository\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectSaver
{
    /**
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    private $sessionUtilities;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     * @var \Chamilo\Core\Repository\Service\RepositoryCategoryService
     */
    private $repositoryCategoryService;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService
     */
    private $contentObjectRelationService;

    /**
     * @var \Chamilo\Core\Repository\Service\IncludeParserManager
     */
    private $includeParserManager;

    /**
     * @param \Chamilo\Core\Repository\Service\RepositoryCategoryService $repositoryCategoryService
     * @param \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService $contentObjectRelationService
     * @param \Chamilo\Core\Repository\Service\IncludeParserManager $includeParserManager
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function __construct(
        RepositoryCategoryService $repositoryCategoryService,
        ContentObjectRelationService $contentObjectRelationService, IncludeParserManager $includeParserManager,
        SessionUtilities $sessionUtilities, StringUtilities $stringUtilities
    )
    {
        $this->repositoryCategoryService = $repositoryCategoryService;
        $this->contentObjectRelationService = $contentObjectRelationService;
        $this->includeParserManager = $includeParserManager;
        $this->sessionUtilities = $sessionUtilities;
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    protected function allowsCategorySelection(ContentObject $contentObject)
    {
        return !$contentObject->isIdentified() || ($contentObject->isIdentified() &&
                $contentObject->get_owner_id() == $this->getSessionUtilities()->getUserId());
    }

    public function createContentObject(ContentObject $contentObject)
    {
        // version:
        // if the ID is set, we create a new version,
        // otherwise a new CO.

        $now = time();
        $contentObject->set_creation_date($now);
        $contentObject->set_modification_date($now);

        if (!$contentObject->get_template_registration_id())
        {
            $default_template_registration = Configuration::registration_default_by_type(
                ClassnameUtilities::getInstance()->getNamespaceParent($contentObject->context(), 2)
            );

            $contentObject->set_template_registration_id($default_template_registration->getId());
        }

        if ($contentObject->isIdentified())
        { // id changes in create new version, so location needs to be fetched
            // now
            $contentObject->set_current(ContentObject::CURRENT_MULTIPLE);
        }
        else
        {
            $contentObject->set_object_number(UUID::v4());
            $contentObject->set_current(ContentObject::CURRENT_SINGLE);
        }

        if (!call_user_func_array(
            array($content_object, '\Chamilo\Libraries\Storage\DataClass\DataClass::create'), array()
        ))
        {

            return false;
        }

        if ($contentObject->isIdentified())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER
                ), new StaticConditionVariable($content_object->get_object_number())
            );
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new StaticConditionVariable($content_object->get_id())
                )
            );
            $condition = new AndCondition($conditions);
            $parameters = new DataClassRetrievesParameters($condition);
            $objects = DataManager::retrieve_content_objects($content_object::class_name(), $parameters);

            while ($object = $objects->next_result())
            {
                $object->set_current(ContentObject::CURRENT_OLD);
                $object->update(false);
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param array $values
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function createContentObjectFromInstanceAndValuesInWorkspace(
        WorkspaceInterface $workspace, ContentObject $contentObject, array $values
    )
    {
        $contentObject->set_title($values[ContentObject::PROPERTY_TITLE]);
        $contentObject->set_description($values[ContentObject::PROPERTY_DESCRIPTION]);

        if ($this->allowsCategorySelection($contentObject) && $workspace instanceof PersonalWorkspace)
        {
            $this->setCategoryFromValuesInPersonalWorkspace($workspace, $contentObject, $values);
        }

        $this->createContentObject($contentObject);

        if ($contentObject->has_errors())
        {
            return null;
        }

        if ($this->allowsCategorySelection($contentObject) && $workspace instanceof Workspace)
        {
            $this->setCategoryFromValuesInWorkspace($workspace, $contentObject, $values);
        }

        // Process includes
        $this->getIncludeParserManager()->parseContentObjectValues($contentObject, $values);

        // Process attachments
        if ($contentObject instanceof AttachmentSupport)
        {
            $contentObject->attach_content_objects(
                $values[ContentObjectForm::PROPERTY_ATTACHMENTS]['content_object'], ContentObject::ATTACHMENT_NORMAL
            );
        }

        return $contentObject;
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param array $values
     *
     * @return integer
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getCategoryIdentifierFromValuesInWorkspace(WorkspaceInterface $workspace, array $values)
    {
        $parentIdentifier = (int) $values[ContentObject::PROPERTY_PARENT_ID];
        $newCategoryName = $values[ContentObjectForm::NEW_CATEGORY];

        if (!$this->getStringUtilities()->isNullOrEmpty($newCategoryName, true))
        {
            $newCategory = $this->getRepositoryCategoryService()->createNewCategoryInWorkspace(
                $workspace, $newCategoryName, $parentIdentifier
            );

            if ($newCategory instanceof RepositoryCategory)
            {
                $parentIdentifier = $newCategory->getId();
            }
        }

        return $parentIdentifier;
    }

    /**
     * @param integer $templateIdentifier
     * @param integer $userIdentfier
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObjectInstanceForTemplateAndUserIdentfiers(
        int $templateIdentifier, int $userIdentfier
    )
    {
        $contentObjectInstance = $this->getContentObjectInstanceForTemplateIdentfier($templateIdentifier);
        $contentObjectInstance->set_owner_id($userIdentfier);

        return $contentObjectInstance;
    }

    /**
     * @param integer $templateIdentifier
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObjectInstanceForTemplateIdentfier(int $templateIdentifier)
    {
        $templateRegistration = Configuration::registration_by_id($templateIdentifier);

        $contentObjectInstance = $templateRegistration->get_template()->get_content_object();
        $contentObjectInstance->set_template_registration_id($templateIdentifier);

        return $contentObjectInstance;
    }

    /**
     * @return \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService
     */
    public function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->contentObjectRelationService;
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService $contentObjectRelationService
     */
    public function setContentObjectRelationService(ContentObjectRelationService $contentObjectRelationService): void
    {
        $this->contentObjectRelationService = $contentObjectRelationService;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\IncludeParserManager
     */
    public function getIncludeParserManager(): IncludeParserManager
    {
        return $this->includeParserManager;
    }

    /**
     * @param \Chamilo\Core\Repository\Service\IncludeParserManager $includeParserManager
     */
    public function setIncludeParserManager(IncludeParserManager $includeParserManager): void
    {
        $this->includeParserManager = $includeParserManager;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\RepositoryCategoryService
     */
    public function getRepositoryCategoryService(): RepositoryCategoryService
    {
        return $this->repositoryCategoryService;
    }

    /**
     * @param \Chamilo\Core\Repository\Service\RepositoryCategoryService $repositoryCategoryService
     */
    public function setRepositoryCategoryService(
        RepositoryCategoryService $repositoryCategoryService
    ): void
    {
        $this->repositoryCategoryService = $repositoryCategoryService;
    }

    public function getSessionUtilities()
    {
        return $this->sessionUtilities;
    }

    public function setSessionUtilities(SessionUtilities $sessionUtilities)
    {
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities): void
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\PersonalWorkspace $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param array $values
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function setCategoryFromValuesInPersonalWorkspace(
        PersonalWorkspace $workspace, ContentObject $contentObject, array $values
    )
    {
        $categoryIdentifier = $this->getCategoryIdentifierFromValuesInWorkspace($workspace, $values);

        $contentObject->set_parent_id($categoryIdentifier);
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param array $values
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function setCategoryFromValuesInWorkspace(Workspace $workspace, ContentObject $contentObject, array $values)
    {
        $categoryIdentifier = $this->getCategoryIdentifierFromValuesInWorkspace($workspace, $values);

        $contentObjectRelationService = $this->getContentObjectRelationService();
        $contentObjectRelation = $contentObjectRelationService->getContentObjectRelationForWorkspaceAndContentObject(
            $workspace, $contentObject
        );

        if ($contentObjectRelation instanceof WorkspaceContentObjectRelation)
        {
            $contentObjectRelationService->updateContentObjectRelation(
                $contentObjectRelation, $workspace->getId(), $contentObject->get_object_number(), $categoryIdentifier
            );
        }
        else
        {
            $contentObjectRelationService->createContentObjectRelation(
                $workspace->getId(), $contentObject->get_object_number(), $categoryIdentifier
            );
        }
    }
}