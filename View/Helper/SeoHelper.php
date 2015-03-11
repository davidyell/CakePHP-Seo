<?php
/*
 * SeoHelper
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Helper for outputting various tags to do with SEO
 *
 * @author David Yell <neon1024@gmail.com>
 */
class SeoHelper extends AppHelper {

/**
 * Keep a list of all the controllers which generate paginated lists of items
 *
 * @var array
 */
	public $paginatedControllers = array();

/**
 * When using a paginated set of pages a link tag is used to show which pages
 * are previous and next in the paginated series
 *
 * @param string $controller The name of the controller
 * @return string
 */
	public function pagination($controller) {
		if (in_array($controller, $this->paginatedControllers)) {

			$className = Inflector::classify($controller);

			if (isset($this->request->params['paging'][$className])) {
				$prev = "<link rel='prev' href='" . Router::url(array('controller' => $controller, 'action' => 'index', 'page' => $this->request->params['paging'][$className]['page'] - 1)) . "'>";
				$next = "<link rel='next' href='" . Router::url(array('controller' => $controller, 'action' => 'index', 'page' => $this->request->params['paging'][$className]['page'] + 1)) . "'>";

				if ($this->request->params['paging'][$className]['prevPage'] === false) { // page 1
					return $next;
				} elseif ($this->request->params['paging'][$className]['nextPage'] === false) {
					return $prev;
				} else {
					return $next . $prev;
				}
			}
		}
	}

/**
 * Output a canonical tag for content with multiple pages or other dynamic data
 *
 * @param string $controller The name of the controller
 * @param string $action The name of the action
 * @return string
 */
	public function canonical($controller, $action) {
		$url = preg_replace("/page:[0-9]+/", '', $this->here);

		if (isset($this->request->params['sort'])) {
			$url = str_replace($this->request->params['sort'], '', $url);
		}
		if (isset($this->request->params['dir'])) {
			$url = str_replace($this->request->params['dir'], '', $url);
		}
		$url = rtrim($url, '/');
		$url = Router::fullbaseUrl() . $url;

		return "<link rel='canonical' href='$url'>";
	}

}