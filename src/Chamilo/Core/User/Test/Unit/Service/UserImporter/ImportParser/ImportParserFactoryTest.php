<?php

namespace Chamilo\Core\User\Test\Unit\Service\UserImporter\ImportParser;

use Chamilo\Core\User\Service\UserImporter\ImportParser\CsvImportParser;
use Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserFactory;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Tests the ImportParserFactory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportParserFactoryTest extends ChamiloTestCase
{
    /**
     * @var ImportParserFactory
     */
    protected $importParserFactory;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->importParserFactory = new ImportParserFactory();
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->importParserFactory);
    }

    public function testGetImportParserForUploadedFile()
    {
        /** @var UploadedFile | \PHPUnit_Framework_MockObject_MockObject $uploadedFile */
        $uploadedFile = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()->getMock();

        $uploadedFile->expects($this->once())
            ->method('getClientMimeType')
            ->will($this->returnValue('text/csv'));

        $this->assertInstanceOf(
            CsvImportParser::class, $this->importParserFactory->getImportParserForUploadedFile($uploadedFile)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetImportParserForUploadedFileWithNoParser()
    {
        /** @var UploadedFile | \PHPUnit_Framework_MockObject_MockObject $uploadedFile */
        $uploadedFile = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()->getMock();

        $uploadedFile->expects($this->once())
            ->method('getClientMimeType')
            ->will($this->returnValue('text/csv2'));

        $this->importParserFactory->getImportParserForUploadedFile($uploadedFile);
    }
}