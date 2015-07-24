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

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\View\View;
use Seo\View\Helper\SeoHelper;

class SeoHelperTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Configure::write('App.fullBaseUrl', 'http://localhost');
    }

    public function paginationProvider()
    {
        return [
            [
                ['Test' => [
                    'page' => 5,
                    'prevPage' => true,
                    'nextPage' => true
                ]],
                [
                    'next' => '/tests?page=6',
                    'prev' => '/tests?page=4'
                ],
                "<link rel='next' href='/tests?page=6'><link rel='prev' href='/tests?page=4'>"
            ],
            [
                ['Test' => [
                    'page' => 1,
                    'prevPage' => false,
                    'nextPage' => true
                ]],
                [
                    'next' => '/tests?page=2'
                ],
                "<link rel='next' href='/tests?page=2'>"
            ],
            [
                ['Test' => [
                    'page' => 6,
                    'prevPage' => true,
                    'nextPage' => false
                ]],
                [
                    'prev' => '/tests?page=5'
                ],
                "<link rel='prev' href='/tests?page=5'>"
            ],
        ];
    }

    /**
     * @param array $pagingArray
     * @param array $urls
     * @param string $expected
     * @dataProvider paginationProvider
     */
    public function testPagination(array $pagingArray, array $urls, $expected)
    {
        $this->Request = new Request();
        $this->Request->params['paging'] = $pagingArray;

        $this->Controller = new Controller($this->Request);
        $this->Controller->name = 'Tests';

        $this->View = new View($this->Request);

        $helper = $this->getMockBuilder('Seo\View\Helper\SeoHelper')
            ->setConstructorArgs([$this->View])
            ->setMethods(['pageLink'])
            ->getMock();

        if ($pagingArray['Test']['prevPage']) {
            $helper->expects($this->at(0))
                ->method('pageLink')
                ->with($this->Controller->name, $pagingArray['Test']['page'], 'prev')
                ->willReturn($urls['prev']);
        }

        if ($pagingArray['Test']['nextPage']) {
            $helper->expects($this->at(1))
                ->method('pageLink')
                ->with($this->Controller->name, $pagingArray['Test']['page'], 'next')
                ->willReturn($urls['next']);
        }

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
