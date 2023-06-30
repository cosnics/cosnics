<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupExportForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use stdClass;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @package Chamilo\Core\Group\Component
 */
class ExporterComponent extends Manager
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $form = new GroupExportForm(GroupExportForm::TYPE_EXPORT, $this->get_url());

        if ($form->validate())
        {
            $export = $form->exportValues();
            $file_type = $export['file_type'];

            $serializer = new Serializer([new ObjectNormalizer()],
                [new XmlEncoder([XmlEncoder::ROOT_NODE_NAME => 'rootItem', XmlEncoder::FORMAT_OUTPUT => true])]);

            header('Content-Type: text/plain; charset=utf-8');
            echo $serializer->serialize($this->build_group_tree(0), 'xml');
            exit;

            $data['groups'] = $this->build_group_tree(0);

            $this->exportGroups($file_type, $data['groups'][0]);
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function build_group_tree($parent_group): array
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
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

    public function exportGroups(string $file_type, array $data): void
    {
        $filename = 'export_groups_' . date('Y-m-d_H-i-s');

        $this->getExporter($file_type)->sendtoBrowser($filename, $data);
    }

    protected function getExporter($fileType): Export
    {
        return $this->getService('Chamilo\Libraries\File\Export\\' . $fileType . '\\' . $fileType . 'Export');
    }
}
