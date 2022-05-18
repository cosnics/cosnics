<?php
namespace Chamilo\Configuration\Test\Archive;

use PHPUnit_Framework_TestCase;

class FilesystemTest extends PHPUnit_Framework_TestCase
{

    private $work_dir;

    private $source_file;

    private $source_dir;

    private $temp_dir;

    private $file1;

    private $file2;

    private $file11;

    private $file12;

    private $dir1;

    public function setUp(): void
    {
        $this->work_dir = __DIR__ . "/__generated_during_test";
        $this->source_file = $this->work_dir . "/source_file";
        $this->source_dir = $this->work_dir . "/source_dir";
        $this->temp_dir = $this->work_dir . "/temp_dir";
        $this->file1 = $this->work_dir . "/source_dir/file1";
        $this->file2 = $this->work_dir . "/source_dir/file2";
        $this->dir1 = $this->work_dir . "/source_dir/dir1";
        $this->file11 = $this->work_dir . "/source_dir/dir1/file11";
        $this->file12 = $this->work_dir . "/source_dir/dir1/file12";
        
        mkdir($this->work_dir);
        touch($this->source_file);
        mkdir($this->source_dir);
        mkdir($this->temp_dir);
        touch($this->file1);
        touch($this->file2);
        mkdir($this->dir1);
        touch($this->file11);
        touch($this->file12);
    }

    private function delTree($dir)
    {
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file)
        {
            if (is_dir($file))
                $this->delTree($file);
            else
                unlink($file);
        }
        
        if (is_dir($dir))
            rmdir($dir);
    }

    public function tearDown()
    {
        $this->delTree($this->work_dir);
    }

    public function test_creation_and_deletion_should_be_recursive()
    {
        $long_path = $this->work_dir . "/this/path/does/not/exists";
        $short_path = $this->work_dir . "/this";
        
        $this->assertFileNotExists($short_path);
        Filesystem::create_dir($long_path);
        $this->assertFileExists($long_path);
        Filesystem::remove($short_path);
        $this->assertFileNotExists($short_path);
    }

    public function test_copied_file_is_equal_to_source()
    {
        $dest_file = $this->temp_dir . "/dest_file";
        
        $this->assertFileNotExists($dest_file);
        $this->assertFileExists($this->source_file);
        Filesystem::copy_file($this->source_file, $dest_file);
        $this->assertFileExists($dest_file);
        $this->assertFileExists($this->source_file);
        $this->assertFileEquals($this->source_file, $dest_file);
    }

    public function test_recurse_copy_take_care_of_subdirs()
    {
        $dest_dir = $this->temp_dir . "/dest_dir";
        
        $this->assertFileNotExists($dest_dir);
        $this->assertFileExists($this->source_dir);
        Filesystem::recurse_copy($this->source_dir, $dest_dir);
        $this->assertFileExists($this->source_dir);
        $this->assertFileExists($dest_dir);
        $this->assertFileExists($dest_dir . '/file1');
        $this->assertFileExists($dest_dir . '/file2');
        $this->assertFileExists($dest_dir . '/dir1/file11');
        $this->assertFileExists($dest_dir . '/dir1/file12');
    }

    public function test_recurse_move_take_care_of_subdirs()
    {
        $dest_dir = $this->temp_dir . "/dest_dir";
        
        $this->assertFileNotExists($dest_dir);
        $this->assertFileExists($this->source_dir);
        Filesystem::recurse_move($this->source_dir, $dest_dir);
        $this->assertFileNotExists($this->source_dir);
        $this->assertFileExists($dest_dir);
        $this->assertFileExists($dest_dir . '/file1');
        $this->assertFileExists($dest_dir . '/file2');
        $this->assertFileExists($dest_dir . '/dir1/file11');
        $this->assertFileExists($dest_dir . '/dir1/file12');
    }
}
