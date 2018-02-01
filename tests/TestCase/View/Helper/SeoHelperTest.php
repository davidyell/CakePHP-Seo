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
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Seo\View\Helper\SeoHelper;

class SeoHelperTest extends TestCase
{
    /**
     * Request instance
     *
     * @var \Cake\Http\ServerRequest
     */
    private $Request;

    /**
     * Controller instance
     *
     * @var \Cake\Controller\Controller
     */
    private $Controller;

    /**
     * View instance
     *
     * @var \Cake\View\View
     */
    private $View;

    /**
     * Setup the test
     */
    public function setUp()
    {
        parent::setUp();
        Configure::write('App.fullBaseUrl', 'http://localhost');
    }

    /**
     * Provider data sets for testing pagination
     *
     * @return array
     */
    public function paginationProvider()
    {
        return [
            [
                [
                    'Tests' => [
                        'page' => 5,
                        'prevPage' => true,
                        'nextPage' => true
                    ]
                ],
                [
                    'next' => '/tests?page=6',
                    'prev' => '/tests?page=4'
                ],
                "<link rel='next' href='/tests?page=6'><link rel='prev' href='/tests?page=4'>"
            ],
            [
                [
                    'Tests' => [
                        'page' => 1,
                        'prevPage' => false,
                        'nextPage' => true
                    ]
                ],
                [
                    'next' => '/tests?page=2'
                ],
                "<link rel='next' href='/tests?page=2'>"
            ],
            [
                [
                    'Tests' => [
                        'page' => 6,
                        'prevPage' => true,
                        'nextPage' => false
                    ]
                ],
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
        $this->Request = new ServerRequest([
            'params' => [
                'paging' => $pagingArray
            ]
        ]);

        $this->Controller = new Controller($this->Request);
        $this->Controller->name = 'Tests';

        $this->View = new View($this->Request);

        $helper = $this->getMockBuilder('Seo\View\Helper\SeoHelper')
            ->setConstructorArgs([$this->View])
            ->setMethods(['pageLink'])
            ->getMock();

        $helper->setConfig('paginatedControllers', ['Tests']);

        if ($pagingArray['Tests']['prevPage']) {
            $helper->expects($this->at(0))
                ->method('pageLink')
                ->with($this->Controller->name, $pagingArray['Tests']['page'], 'prev')
                ->willReturn($urls['prev']);
        }

        if ($pagingArray['Tests']['nextPage']) {
            $helper->expects($this->at(1))
                ->method('pageLink')
                ->with($this->Controller->name, $pagingArray['Tests']['page'], 'next')
                ->willReturn($urls['next']);
        }

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
        $this->Request = new ServerRequest($here);

        $this->Controller = new Controller($this->Request);
        $this->Controller->name = 'Tests';

        $this->View = new View($this->Request);

        $helper = new SeoHelper($this->View);
        $result = $helper->canonical('Tests', 'index');
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensure that if the helper is called against a non-paginated controller it doesn't break the front-end with a
     * hard error
     */
    public function testNonPaginatedController()
    {
        $this->Request = new ServerRequest([
            'params' => [
                'paging' => [
                    'Tests' => [
                        'page' => 5,
                        'prevPage' => true,
                        'nextPage' => true
                    ]
                ]
            ]
        ]);

        $this->Controller = new Controller($this->Request);
        $this->Controller->name = 'Tests';

        $this->View = new View($this->Request);

        $helper = $this->getMockBuilder('Seo\View\Helper\SeoHelper')
            ->setConstructorArgs([$this->View])
            ->setMethods(['pageLink'])
            ->getMock();

        $helper->expects($this->never())
            ->method('pageLink');

        $helper->expects($this->never())
            ->method('pageLink');

        $result = $helper->pagination($this->Controller->name);
        $this->assertNull($result);
    }

    public function pageLinkProvider()
    {
        return [
            'Next page on page 1' => ['Examples', 1, 'next', false, '/examples?page=2'],
            'Prev page on page 4' => ['Examples', 4, 'prev', false, '/examples?page=3'],
            'Next page on page 4' => ['Examples', 4, 'next', false, '/examples?page=5'],
            'Prev page on page 1' => ['Examples', 1, 'prev', false, '/examples?page=1'],
            'Next on invalid page' => ['Examples', 0, 'next', false, '/examples?page=1'],
            'Prev on invalid page' => ['Examples', 0, 'prev', false, '/examples?page=1'],
            'Invalid pagination direction' => ['Examples', 2, 'fish', false, '/examples?page=3'],
            'Full url, next page on page 1' => ['Examples', 1, 'next', true, 'http://localhost/examples?page=2'],
        ];
    }

    /**
     * @dataProvider pageLinkProvider
     *
     * @param string $controller
     * @param int $page
     * @param string $type
     * @param bool $full
     * @param string $expected
     */
    public function testPageLink(string $controller, int $page, string $type, bool $full, string $expected)
    {
        Router::connect('/examples', ['controller' => 'Examples', 'action' => 'index']);

        $helper = new SeoHelper(
            new View(),
            ['paginatedControllers' => ['Examples']]
        );

        $result = $helper->pageLink($controller, $page, $type, $full);
        $this->assertEquals($expected, $result);
    }
}
