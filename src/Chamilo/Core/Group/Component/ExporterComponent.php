<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use stdClass;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @package Chamilo\Core\Group\Component
 */
class ExporterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $serializer = new Serializer([new ObjectNormalizer()],
            [new XmlEncoder([XmlEncoder::ROOT_NODE_NAME => 'rootItem', XmlEncoder::FORMAT_OUTPUT => true])]);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT, 'group_export.xml'
        );

        $response = new Response(
            $serializer->serialize($this->build_group_tree(0), 'xml'), 200,
            ['Content-Type' => 'text/plain', 'Content-Disposition' => $disposition]
        );

        $response->setCharset('utf-8');

        return $response;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function build_group_tree($parent_group): array
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_group)
        );

        $groups = $this->getGroupService()->findGroups($condition);

        $data = [];

        foreach ($groups as $group)
        {
            $groupObject = new stdClass();

            $groupObject->name = htmlspecialchars($group->get_name());
            $groupObject->description = htmlspecialchars($group->get_description());
            $groupObject->children = $this->build_group_tree($group->getId());

            $data[] = $groupObject;
        }

        return $data;
    }
}
