<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CopierComponent extends Manager
{

    public function run()
    {
        $complexContentObjectItemIdentifiers = $this->getRequest()->getFromRequestOrQuery(
            Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
        );

        if (!$complexContentObjectItemIdentifiers)
        {
            throw new InvalidArgumentException();
        }
        elseif (!is_array($complexContentObjectItemIdentifiers))
        {
            $complexContentObjectItemIdentifiers = [$complexContentObjectItemIdentifiers];
        }

        $failedActions = 0;
        $copiedComplexContentObjectItemIdentifiers = [];

        foreach ($complexContentObjectItemIdentifiers as $complexContentObjectItemIdentifier)
        {
            $complexContentObjectItem = DataManager::retrieve_by_id(
                ComplexContentObjectItem::class, $complexContentObjectItemIdentifier
            );

            try
            {
                if (!$complexContentObjectItem instanceof ComplexContentObjectItem)
                {
                    throw new ObjectNotExistException(Translation::get('ComplexContentObjectItem'));
                }

                $contentObject = DataManager::retrieve_by_id(
                    ContentObject::class, $complexContentObjectItem->get_ref()
                );

                if (!$contentObject instanceof ContentObject)
                {
                    throw new ObjectNotExistException(Translation::get('ContentObject'));
                }

                $contentObjectCopier = new ContentObjectCopier(
                    $this->getUser(), [$contentObject->get_id()], $this->getCurrentWorkspace(),
                    $contentObject->get_owner_id(), $this->getCurrentWorkspace(), $this->getUser()->getId()
                );

                $copiedContentObjectIdentifiers = $contentObjectCopier->run();

                if ($contentObjectCopier->has_messages(ContentObjectCopier::TYPE_ERROR))
                {
                    throw new Exception($contentObjectCopier->get_messages(ContentObjectCopier::TYPE_ERROR));
                }
                else
                {
                    $parentIdentifier = $this->get_parent_content_object_id();
                    $copiedContentObjectId = $copiedContentObjectIdentifiers[$contentObject->getId()];

                    if (method_exists($this->get_parent(), 'get_helper_object'))
                    {
                        $helperObject = $this->get_parent()->get_helper_object($contentObject->getType());

                        if ($helperObject)
                        {
                            $helperObject->set_title($helperObject->getTypeName());
                            $helperObject->set_description($helperObject->getTypeName());
                            $helperObject->set_owner_id($this->getUser()->getId());
                            $helperObject->set_reference($copiedContentObjectId);
                            $helperObject->set_parent_id(0);

                            if (!$helperObject->create())
                            {
                                throw new Exception(Translation::get('HelperObjectCreationFailed'));
                            }

                            $copiedContentObjectId = $helperObject->get_id();
                        }
                    }

                    $copiedContentObjectType = DataManager::determineDataClassType(
                        ContentObject::class, $copiedContentObjectId
                    );

                    $complexContentObjectItem = ComplexContentObjectItem::factory(
                        $copiedContentObjectType
                    );
                    $complexContentObjectItem->set_ref($copiedContentObjectId);
                    $complexContentObjectItem->set_parent($parentIdentifier);
                    $complexContentObjectItem->set_display_order(
                        DataManager::select_next_display_order($parentIdentifier)
                    );
                    $complexContentObjectItem->set_user_id($this->getUser()->getId());

                    if (!$complexContentObjectItem->create())
                    {
                        throw new Exception(Translation::get('ComplexContentObjectItemCreationFailed'));
                    }
                    else
                    {
                        $copiedComplexContentObjectItemIdentifiers[] = $complexContentObjectItem->getId();
                    }
                }
            }
            catch (Exception $exception)
            {
                $failedActions ++;
            }
        }

        if ($failedActions > 0)
        {
        }
        else
        {
            $numberOfCopiedContentOBjects = count($copiedComplexContentObjectItemIdentifiers);

            if ($numberOfCopiedContentOBjects > 1 || $numberOfCopiedContentOBjects == 0)
            {
                $this->redirectWithMessage(
                    Translation::get(
                        'ObjectCopied', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                    ), false, [
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE,
                        Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_parent()
                            ->get_complex_content_object_item_id()
                    ]
                );
            }
            else
            {
                $this->redirectWithMessage(
                    Translation::get(
                        'ObjectCopied', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                    ), false, [
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => array_pop(
                            $copiedComplexContentObjectItemIdentifiers
                        ),
                        Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_parent()
                            ->get_complex_content_object_item_id()
                    ]
                );
            }
        }
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }
}
