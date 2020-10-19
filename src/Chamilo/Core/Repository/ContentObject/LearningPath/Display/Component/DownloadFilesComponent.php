<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class DownloadFilesComponent
 *
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class DownloadFilesComponent extends Manager
{
    /**
     * @return string|void
     */
    function run()
    {
        $get_treenode_content_object = function($treeNode) {
            return $treeNode->getContentObject();
        };

        $is_file_object = function($contentObject) {
            return $contentObject instanceof File;
        };

        $dir = Path::getInstance()->getTemporaryPath(__NAMESPACE__);
        $titleSafeName = Filesystem::create_safe_name($this->learningPath->get_title());
        $dir = Filesystem::create_unique_name($dir . 'learningpath_files_download_' . $titleSafeName);
        Filesystem::create_dir($dir);
        $targetDir = Filesystem::create_unique_name($dir . '/' . $titleSafeName);
        Filesystem::create_dir($targetDir);

        $contentObjects = array_map($get_treenode_content_object, $this->getTree()->getTreeNodes());
        $files = array_filter($contentObjects, $is_file_object);

        foreach ($files as $f) {
            $srcPath = $f->get_full_path();
            $targetFileName = Filesystem::create_unique_name($targetDir, $f->get_filename());
            Filesystem::copy_file($srcPath, $targetDir . '/' . $targetFileName);
        }

        $compression = Filecompression::factory();
        $archiveFile = $compression->create_archive($targetDir);

        $response = new BinaryFileResponse($archiveFile, 200, array('Content-Type' => 'application/zip'));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $titleSafeName . '.zip');
        $response->prepare($this->getRequest());
        $response->send();
    }
}