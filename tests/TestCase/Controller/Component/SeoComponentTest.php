<?php

namespace Seo\Tests\Controller\Component;

/**
 * @category Seo
 * @package SeoComponentTest.php
 *
 * @author David Yell <neon1024@gmail.com>
 * @when 09/03/15
 *
 */

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use Seo\Controller\Component\SeoComponent;

class SeoComponentTest extends TestCase
{

    public function setUp()
    {
        $this->Controller = new Controller();
        $this->Controller->name = 'TestsController';
        $this->ComponentRegistry = new ComponentRegistry($this->Controller);
        $this->Seo = new SeoComponent($this->ComponentRegistry);
    }

    public function testWriteSeo()
    {
        $view = $this->getMockBuilder('Cake\View\View')
            ->setMethods(['append'])
            ->getMock();

        $view->expects($this->exactly(2))
            ->method('append');

        $view->set('content', new Entity([
            'seo_title' => 'Test SEO title',
            'seo_description' => 'Test SEO description',
            'seo_keywords' => 'Test SEO keywords'
        ]));

        $event = new Event('view.beforeLayout', $view, []);

        $this->Seo->writeSeo($event);

        $this->assertEquals(true, $event->getSubject()->exists('title'));
        $this->assertEquals('Test SEO title', $event->getSubject()->fetch('title'));
    }

    public function testWriteSeoEmptyConfig()
    {
        $view = $this->getMockBuilder('Cake\View\View')
            ->setMethods(['append'])
            ->getMock();

        $view->expects($this->exactly(2))
            ->method('append');

        $event = new Event('view.beforeLayout', $view, []);

        $this->Seo->writeSeo($event);

        $this->assertEquals(true, $event->getSubject()->exists('title'));
        $this->assertEquals('The homepage | My Awesome Website', $event->getSubject()->fetch('title'));
    }
}
