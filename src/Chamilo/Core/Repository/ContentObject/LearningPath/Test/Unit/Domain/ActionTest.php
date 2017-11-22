<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the Action class
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ActionTest extends ChamiloTestCase
{
    /**
     * @var Action
     */
    protected $action;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        $this->action = new Action(
            'delete', 'Remove', 'https://cosnics.github.io/', 'fa fa-remove', 'Are you sure you want to remove this?'
        );
    }

    /**
     * Tear down after each test
     */
    protected function tearDown()
    {
        unset($this->action);
    }

    public function testGetName()
    {
        $this->assertEquals('delete', $this->action->getName());
    }

    public function testSetName()
    {
        $this->action->setName('testName');
        $this->assertEquals('testName', $this->action->getName());
    }

    public function testGetTitle()
    {
        $this->assertEquals('Remove', $this->action->getTitle());
    }

    public function testSetTitle()
    {
        $this->action->setTitle('testTitle');
        $this->assertEquals('testTitle', $this->action->getTitle());
    }

    public function testGetUrl()
    {
        $this->assertEquals('https://cosnics.github.io/', $this->action->getUrl());
    }

    public function testSetUrl()
    {
        $this->action->setUrl('testUrl');
        $this->assertEquals('testUrl', $this->action->getUrl());
    }

    public function testGetImage()
    {
        $this->assertEquals('fa fa-remove', $this->action->getImage());
    }

    public function testSetImage()
    {
        $this->action->setImage('testImage');
        $this->assertEquals('testImage', $this->action->getImage());
    }

    public function testGetConfirmationMessage()
    {
        $this->assertEquals('Are you sure you want to remove this?', $this->action->getConfirmationMessage());
    }

    public function testSetConfirmationMessage()
    {
        $this->action->setConfirmationMessage('testConfirmationMessage');
        $this->assertEquals('testConfirmationMessage', $this->action->getConfirmationMessage());
    }

    public function testNeedsConfirmation()
    {
        $this->assertTrue($this->action->needsConfirmation());
    }

    public function testNeedsConfirmationWithEmptyConfirmationMessage()
    {
        $this->action->setConfirmationMessage('');
        $this->assertFalse($this->action->needsConfirmation());
    }

    public function testToArray()
    {
        $data = [
            'name' => 'delete',
            'title' => 'Remove',
            'url' => 'https://cosnics.github.io/',
            'image' => 'fa fa-remove',
            'confirm' => true,
            'confirmation_message' => 'Are you sure you want to remove this?'
        ];

        $this->assertEquals($data, $this->action->toArray());
    }
}