<?php

/**
 * @category Seo
 * @package SeoComponentTest.php
 * 
 * @author David Yell <neon1024@gmail.com>
 * @when 09/03/15
 *
 */
App::uses('SeoComponent', 'Seo.Controller/Component');
App::uses('ComponentCollection', 'Controller');
App::uses('View', 'View');
App::uses('Controller', 'Controller');

class SeoComponentTest extends CakeTestCase {

	public function setUp() {
		$this->ComponentCollection = new ComponentCollection();
		$this->Controller = new Controller();
	}

	/**
	 *
	 */
	public function testWriteSeo() {
		$component = new SeoComponent($this->ComponentCollection);

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

		$event = new CakeEvent('view.beforeLayout', $view, []);

		$result = $component->writeSeo($event);

		$title = $event->subject()->viewVars['title_for_layout'];
		$this->assertEqual($title, 'Test SEO title');
	}

	/**
	 *
	 */
	public function testWriteSeoEmptyConfig() {
		$component = new SeoComponent($this->ComponentCollection);

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