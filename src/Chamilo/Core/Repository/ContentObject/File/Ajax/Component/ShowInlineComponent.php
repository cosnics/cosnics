<?php

namespace Chamilo\Core\Repository\ContentObject\File\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

/**
 * Class ClearParameterComponent
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Ajax\Component
 */
class ShowInlineComponent extends \Chamilo\Core\Repository\ContentObject\Assignment\Ajax\Manager
{
    use DependencyInjectionContainerTrait;

    const PARAM_SHOW_INLINE = 'show_inline';
    const PARAM_FILE_ID = 'file_id';

    /**
     * @return array|string[]
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_SHOW_INLINE, self::PARAM_FILE_ID);
    }

    /**
     * @return string|void
     */
    public function run()
    {
        $showInline = (boolean) $this->getPostDataValue(self::PARAM_SHOW_INLINE);
        $fileId = $this->getPostDataValue(self::PARAM_FILE_ID);

        $this->initializeContainer();


        try{
            /**
             * @var File $file
             */
            $file = $this->getDataClassRepository()->retrieveById(File::class, $fileId);

            if($this->getUser()->getId() != $file->get_owner_id()) { //@todo better rights check
                $this->returnError($this->getTranslator()->trans('NotAllowed', [], 'Chamilo\Libraries'));
            } else {
                $file->setShowInline($showInline);
                $file->save();
                JsonAjaxResult::success();
            }
        } catch (\Exception $exception) {
            $this->returnError($exception->getMessage());
        }
    }

    protected function returnError($message) {
        $jsonAjaxResult = new JsonAjaxResult(500);
        $jsonAjaxResult->returnActualStatusCode();
        $jsonAjaxResult->set_result_message($message);

        $jsonAjaxResult->display();
    }
}

