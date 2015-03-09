<?php

/**
 * @category Seo
 * @package AllTestsTest.php
 * 
 * @author David Yell <neon1024@gmail.com>
 * @when 09/03/15
 *
 */
App::uses('CakePlugin', 'Core');

class AllTestsTest extends CakeTestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All CakePHP-Seo plugin tests');
		$suite->addTestDirectory(CakePlugin::path('Seo') . 'Test' . DS . 'Case' . DS . 'Controller' . DS . 'Component');
		$suite->addTestDirectory(CakePlugin::path('Seo') . 'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper');
		return $suite;
	}

}