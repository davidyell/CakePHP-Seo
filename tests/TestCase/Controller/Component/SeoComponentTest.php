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

use Seo\Controller\Component\SeoComponent;
use Cake\Controller\ComponentRegistry;
use Cake\View\View;
use Cake\Controller\Controller;
use Cake\Event\Event;

class SeoComponentTest extends \PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->Controller = new Controller();
		$this->ComponentRegistry = new ComponentRegistry($this->Controller);
	}

	public function testWriteSeo()
	{
		$component = new SeoComponent($this->ComponentRegistry);

		$view = $this->getMockBuilder('View')
			->setConstructorArgs([$this->Controller])
			->setMethods(['append'])
			->getMock();

		$view->expects($this->exactly(2))
			->method('append');

		$view->set('content', [
			'Content' => [
				'seo_title' => 'Test SEO title',
				'seo_description' => 'Test SEO description',
				'seo_keywords' => 'Test SEO keywords'
			]
		]);

		$event = new Event('view.beforeLayout', $view, []);

		$result = $component->writeSeo($event);

		$title = $event->subject()->viewVars['title_for_layout'];
		$this->assertEqual($title, 'Test SEO title');
	}

	public function testWriteSeoEmptyConfig()
	{
		$component = new SeoComponent($this->ComponentRegistry);

		$view = $this->getMockBuilder('View')
			->setConstructorArgs([$this->Controller])
			->setMethods(['append'])
			->getMock();

		$view->expects($this->exactly(2))
			->method('append');

		$event = new CakeEvent('view.beforeLayout', $view, []);

		$result = $component->writeSeo($event);

		$title = $event->subject()->viewVars['title_for_layout'];
		$this->assertEqual($title, 'The homepage | My Awesome Website');
	}
}