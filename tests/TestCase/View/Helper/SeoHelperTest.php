<?php

namespace Seo\Tests\View\Helper;

/**
 * @category Seo
 * @package SeoHelperTest.php
 * 
 * @author David Yell <neon1024@gmail.com>
 * @when 09/03/15
 *
 */

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Controller\Controller;
use Cake\View\View;
use Seo\View\Helper\SeoHelper;

class SeoHelperTest extends \PHPUnit_Framework_TestCase {

	public function paginationProvider() {
		return [
			[
				['Test' => [
					'page' => 5,
					'prevPage' => true,
					'nextPage' => true
				]],
				"<link rel='next' href='/tests?page=6'><link rel='prev' href='/tests?page=4'>"
			],
			[
				['Test' => [
					'page' => 1,
					'prevPage' => false,
					'nextPage' => true
				]],
				"<link rel='next' href='/tests?page=2'>"
			],
			[
				['Test' => [
					'page' => 6,
					'prevPage' => true,
					'nextPage' => false
				]],
				"<link rel='prev' href='/tests?page=5'>"
			],
		];
	}

    /**
     * @param $pagingArray
     * @param $expected
     * @dataProvider paginationProvider
     */
	public function testPagination($pagingArray, $expected)
    {
		$this->Request = new Request();
		$this->Request->params['paging'] = $pagingArray;

		$this->Controller = new Controller($this->Request);
		$this->Controller->name = 'Tests';

		$this->View = new View($this->Request);

		$helper = new SeoHelper($this->View);
		$helper->paginatedControllers = ['Tests'];

		$result = $helper->pagination($this->Controller->name);
		$this->assertEquals($expected, $result);
	}

	public function canonicalProvider()
    {
		return [
			[
				'/tests',
				"<link rel='canonical' href='http://localhost/tests'>"
			],
			[
				'/tests?page=5',
				"<link rel='canonical' href='http://localhost/tests'>"
			],
			[
				'/tests?sort=id&page=5',
				"<link rel='canonical' href='http://localhost/tests'>"
			],
			[
				'/tests?dir=desc&page=5',
				"<link rel='canonical' href='http://localhost/tests'>"
			],
			[
				'/tests?sort=id&dir=desc',
				"<link rel='canonical' href='http://localhost/tests'>"
			],
            [
                '/tests?page=5&sort=id&dir=desc',
                "<link rel='canonical' href='http://localhost/tests'>"
            ],
		];
	}

    /**
     * @param $expected
     * @dataProvider canonicalProvider
     */
	public function testCanonical($here, $expected)
    {
        Configure::write('App.fullBaseUrl', 'http://localhost');

		$this->Request = new Request();
		$this->Request->here = $here;

		$this->Controller = new Controller($this->Request);
		$this->Controller->name = 'Tests';

		$this->View = new View($this->Request);

		$helper = new SeoHelper($this->View);
		$result = $helper->canonical('Tests', 'index');
		$this->assertEquals($expected, $result);
	}
}