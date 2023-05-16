<?php
namespace Chamilo\Libraries\Test\Unit\Storage\Service;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository;
use Chamilo\Libraries\Storage\Service\DisplayOrderHandler;
use Chamilo\Libraries\Test\Stub\DisplayOrderDataClassStub;
use Symfony\Component\Translation\Translator;

/**
 * Tests the DisplayOrderHandler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DisplayOrderHandlerTest extends ChamiloTestCase
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $displayOrderRepositoryMock;

    /**
     * @var \Symfony\Component\Translation\Translator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $translatorMock;

    /**
     * @var DisplayOrderHandler
     */
    protected $displayOrderHandler;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->displayOrderRepositoryMock = $this->getMockBuilder(DisplayOrderRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()->getMock();

        $this->displayOrderHandler = new DisplayOrderHandler($this->displayOrderRepositoryMock, $this->translatorMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->translatorMock);
        unset($this->displayOrderRepositoryMock);
        unset($this->displayOrderHandler);
    }

    public function testCreate()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('findNextDisplayOrderValue')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(10));

        $this->assertEquals(true, $this->displayOrderHandler->handleDisplayOrderBeforeCreate($displayOrderDataClass));
        $this->assertEquals(10, $displayOrderDataClass->getSort());
    }

    public function testCreateWithExistingDisplayOrder()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(15));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('addDisplayOrderToContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(true));

        $this->assertEquals(true, $this->displayOrderHandler->handleDisplayOrderBeforeCreate($displayOrderDataClass));
        $this->assertEquals(5, $displayOrderDataClass->getSort());
    }

    public function testCreateWithExistingDisplayOrderWhenFailed()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(15));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('addDisplayOrderToContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(false));

        $this->assertEquals(false, $this->displayOrderHandler->handleDisplayOrderBeforeCreate($displayOrderDataClass));
        $this->assertEquals(5, $displayOrderDataClass->getSort());
    }

    /**
     * @expectedException \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function testWithInvalidDisplayOrder()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(2));

        $this->displayOrderHandler->handleDisplayOrderBeforeCreate($displayOrderDataClass);
        $this->assertEquals(5, $displayOrderDataClass->getSort());
    }

    public function testDelete()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('deleteDisplayOrderFromContext')
            ->with($displayOrderDataClass, [DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0], 5)
            ->will($this->returnValue(true));

        $this->assertTrue($this->displayOrderHandler->handleDisplayOrderAfterDelete($displayOrderDataClass));
    }

    public function testUpdateWithDisplayOrderChanged()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);
        $displayOrderDataClass->setParentId(0);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(15));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('findDisplayOrderPropertiesRecord')
            ->will(
                $this->returnValue(
                    [DisplayOrderDataClassStub::PROPERTY_SORT => 3, DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0]
                )
            );

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('deleteDisplayOrderFromContext')
            ->with($displayOrderDataClass, [DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0], 3)
            ->will($this->returnValue(true));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('addDisplayOrderToContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(true));

        $this->assertTrue($this->displayOrderHandler->handleDisplayOrderBeforeUpdate($displayOrderDataClass));

    }

    public function testUpdateWithDisplayOrderChangedButFailsToDecrementPreviousDisplayOrders()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);
        $displayOrderDataClass->setParentId(0);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(15));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('findDisplayOrderPropertiesRecord')
            ->will(
                $this->returnValue(
                    [DisplayOrderDataClassStub::PROPERTY_SORT => 3, DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0]
                )
            );

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('deleteDisplayOrderFromContext')
            ->with($displayOrderDataClass, [DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0], 3)
            ->will($this->returnValue(false));

        $this->displayOrderRepositoryMock->expects($this->never())
            ->method('addDisplayOrderToContext')
            ->with($displayOrderDataClass);

        $this->assertFalse($this->displayOrderHandler->handleDisplayOrderBeforeUpdate($displayOrderDataClass));

    }

    public function testUpdateWithDisplayOrderChangedButFailsToIncrementNextDisplayOrders()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);
        $displayOrderDataClass->setParentId(0);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(15));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('findDisplayOrderPropertiesRecord')
            ->will(
                $this->returnValue(
                    [DisplayOrderDataClassStub::PROPERTY_SORT => 3, DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0]
                )
            );

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('deleteDisplayOrderFromContext')
            ->with($displayOrderDataClass, [DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0], 3)
            ->will($this->returnValue(true));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('addDisplayOrderToContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(false));

        $this->assertFalse($this->displayOrderHandler->handleDisplayOrderBeforeUpdate($displayOrderDataClass));

    }

    public function testUpdateWithContextChanged()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(3);
        $displayOrderDataClass->setParentId(1);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('findDisplayOrderPropertiesRecord')
            ->will(
                $this->returnValue(
                    [DisplayOrderDataClassStub::PROPERTY_SORT => 3, DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0]
                )
            );

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(15));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('deleteDisplayOrderFromContext')
            ->with($displayOrderDataClass, [DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0], 3)
            ->will($this->returnValue(true));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('addDisplayOrderToContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(true));

        $this->assertTrue($this->displayOrderHandler->handleDisplayOrderBeforeUpdate($displayOrderDataClass));
    }

    public function testUpdateWithContextChangedFailsToDecrementPreviousDisplayOrders()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);
        $displayOrderDataClass->setParentId(1);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('findDisplayOrderPropertiesRecord')
            ->will(
                $this->returnValue(
                    [DisplayOrderDataClassStub::PROPERTY_SORT => 3, DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0]
                )
            );

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('deleteDisplayOrderFromContext')
            ->with($displayOrderDataClass, [DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0], 3)
            ->will($this->returnValue(false));

        $this->displayOrderRepositoryMock->expects($this->never())
            ->method('addDisplayOrderToContext');

        $this->assertFalse($this->displayOrderHandler->handleDisplayOrderBeforeUpdate($displayOrderDataClass));
    }

    public function testUpdateWithContextChangedFailsToIncrementNextDisplayOrders()
    {
        $displayOrderDataClass = new DisplayOrderDataClassStub();
        $displayOrderDataClass->setSort(5);
        $displayOrderDataClass->setParentId(1);

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('findDisplayOrderPropertiesRecord')
            ->will(
                $this->returnValue(
                    [DisplayOrderDataClassStub::PROPERTY_SORT => 3, DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0]
                )
            );

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('countOtherDisplayOrdersInContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(15));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('deleteDisplayOrderFromContext')
            ->with($displayOrderDataClass, [DisplayOrderDataClassStub::PROPERTY_PARENT_ID => 0], 3)
            ->will($this->returnValue(true));

        $this->displayOrderRepositoryMock->expects($this->once())
            ->method('addDisplayOrderToContext')
            ->with($displayOrderDataClass)
            ->will($this->returnValue(false));

        $this->assertFalse($this->displayOrderHandler->handleDisplayOrderBeforeUpdate($displayOrderDataClass));
    }
}

