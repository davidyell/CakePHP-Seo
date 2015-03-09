<?php

/**
 * @category Seo
 * @package SeoHelperTest.php
 * 
 * @author David Yell <neon1024@gmail.com>
 * @when 09/03/15
 *
 */
App::uses('CakeRequest', 'Network');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('SeoHelper', 'Seo.View/Helper');

class SeoHelperTest extends CakeTestCase {

    public function paginationProvider() {
        return [
            [
                ['Test' => [
                    'page' => 5,
                    'prevPage' => true,
                    'nextPage' => true
                ]],
                "<link rel='next' href='/Tests/index/page:6'><link rel='prev' href='/Tests/index/page:4'>"
            ],
            [
                ['Test' => [
                    'page' => 1,
                    'prevPage' => false,
                    'nextPage' => true
                ]],
                "<link rel='next' href='/Tests/index/page:2'>"
            ],
            [
                ['Test' => [
                    'page' => 6,
                    'prevPage' => true,
                    'nextPage' => false
                ]],
                "<link rel='prev' href='/Tests/index/page:5'>"
            ],
        ];
    }

    /**
     * @param $pagingArray
     * @param $expected
     * @dataProvider paginationProvider
     */
    public function testPagination($pagingArray, $expected) {
        $this->Request = new CakeRequest();
        $this->Request->params['paging'] = $pagingArray;

        $this->Controller = new Controller($this->Request);
        $this->Controller->name = 'Tests';

        $this->View = new View($this->Controller);

        $helper = new SeoHelper($this->View);
        $helper->paginatedControllers = ['Tests'];

        $result = $helper->pagination($this->Controller->name);
        $this->assertEqual($result, $expected);
    }

    public function canonicalProvider() {
        return [
            [
               '/tests/index',
               [],
               "<link rel='canonical' href='http://localhost/tests/index'>"
            ],
            [
                '/tests/index/page:5',
                [],
                "<link rel='canonical' href='http://localhost/tests/index'>"
            ],
            [
                '/tests/index/page:5',
                [
                    'sort' => 'id'
                ],
                "<link rel='canonical' href='http://localhost/tests/index'>"
            ],
            [
                '/tests/index/page:5',
                [
                    'dir' => 'desc'
                ],
                "<link rel='canonical' href='http://localhost/tests/index'>"
            ],
            [
                '/tests/index/page:5',
                [
                    'sort' => 'id',
                    'dir' => 'desc'
                ],
                "<link rel='canonical' href='http://localhost/tests/index'>"
            ],
            [
                '/tests/index',
                [
                    'sort' => 'id',
                    'dir' => 'desc'
                ],
                "<link rel='canonical' href='http://localhost/tests/index'>"
            ],
        ];
    }

    /**
     * @param $paramsArray
     * @param $expected
     * @dataProvider canonicalProvider
     */
    public function testCanonical($here, $paramsArray, $expected) {
        $this->Request = new CakeRequest();
        $this->Request->params = $paramsArray;
        $this->Request->here = $here;

        $this->Controller = new Controller($this->Request);
        $this->Controller->name = 'Tests';

        $this->View = new View($this->Controller);

        $helper = new SeoHelper($this->View);
        $result = $helper->canonical('tests', 'index');
        $this->assertEqual($result, $expected);
    }
}