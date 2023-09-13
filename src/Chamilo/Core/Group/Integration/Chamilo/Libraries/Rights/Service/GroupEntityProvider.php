<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupEntityProvider implements RightsEntityProvider
{
    const ENTITY_NAME = 'group';
    const ENTITY_TYPE = 2;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     * @var integer[]
     */
    private $groupCache = [];

    /**
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function __construct(
        GroupService $groupService, Translator $translator, StringUtilities $stringUtilities
    )
    {
        $this->groupService = $groupService;
        $this->translator = $translator;
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntityItems(Condition $condition = null)
    {
        return $this->getGroupService()->countGroups($condition);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return mixed
     */
    public function findEntityItems(
        Condition $condition = null, int $offset = null, int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getGroupService()->findGroups($condition, $offset, $count, $orderBy);
    }

    /**
     * @param integer $entityIdentifier
     *
     * @return string
     */
    public function getEntityDescriptionByIdentifier(int $entityIdentifier)
    {
        $group = $this->getGroupService()->findGroupByIdentifier($entityIdentifier);

        if (!$group instanceof Group)
        {
            return null;
        }

        return strip_tags($group->get_fully_qualified_name() . ' [' . $group->get_code() . ']');
    }

    /**
     * @param integer $entityIdentifier
     *
     * @return mixed
     */
    public function getEntityElementFinderElement(string $entityIdentifier)
    {
        $group = $this->getGroupService()->findGroupByIdentifier($entityIdentifier);

        if (!$group instanceof Group)
        {
            return null;
        }

        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        return new AdvancedElementFinderElement(
            static::ENTITY_TYPE . '_' . $entityIdentifier, $glyph->getClassNamesString(),
            $this->getEntityTitleByIdentifier($entityIdentifier),
            $this->getEntityDescriptionByIdentifier($entityIdentifier)
        );
    }

    /**
     * @return mixed
     */
    public function getEntityElementFinderType()
    {
        return new AdvancedElementFinderElementType(
            'groups', $this->getTranslator()->trans('PlatformGroups', [], 'Chamilo\Core\Group'), Manager::CONTEXT,
            'GroupEntityFeed', []
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    public function getEntityGlyph()
    {
        return new FontAwesomeGlyph('users', [], $this->getEntityTranslatedName());
    }

    /**
     * @param integer $userIdentifier
     *
     * @return integer[]
     * @throws \Exception
     */
    public function getEntityItemIdentifiersForUserIdentifier($userIdentifier)
    {
        if (!isset($this->groupCache[$userIdentifier]))
        {
            $this->groupCache[$userIdentifier] =
                $this->getGroupService()->findAllSubscribedGroupIdentifiersForUserIdentifier($userIdentifier);
        }

        return $this->groupCache[$userIdentifier];
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @param integer $entityIdentifier
     *
     * @return string
     */
    public function getEntityTitleByIdentifier(int $entityIdentifier)
    {
        $group = $this->getGroupService()->findGroupByIdentifier($entityIdentifier);

        if (!$group instanceof Group)
        {
            return null;
        }

        return $group->get_name();
    }

    /**
     * @return string
     */
    public function getEntityTranslatedName()
    {
        $variable = (string) $this->getStringUtilities()->createString(self::ENTITY_NAME)->upperCamelize();

        return $this->getTranslator()->trans($variable, [], 'Chamilo\Core\Group');
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return static::ENTITY_TYPE;
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function setGroupService(GroupService $groupService): void
    {
        $this->groupService = $groupService;
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
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}