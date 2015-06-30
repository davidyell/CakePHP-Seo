<?php

namespace Seo\View\Helper;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Helper for outputting various tags to do with SEO
 *
 * @author David Yell <neon1024@gmail.com>
 */
class SeoHelper extends Helper
{

    /**
     * Keep a list of all the controllers which generate paginated lists of items
     *
     * @var array
     */
    public $paginatedControllers = [];

    /**
     * When using a paginated set of pages a link tag is used to show which pages
     * are previous and next in the paginated series
     *
     * @param string $controller The name of the controller
     * @return string
     */
    public function pagination($controller)
    {
        if (in_array($controller, $this->paginatedControllers)) {
            $className = Inflector::classify($controller);

            if (isset($this->request->params['paging'][$className])) {
                $prev = "<link rel='prev' href='" . Router::url(['controller' => $controller, 'action' => 'index', 'page' => $this->request->params['paging'][$className]['page'] - 1]) . "'>";
                $next = "<link rel='next' href='" . Router::url(['controller' => $controller, 'action' => 'index', 'page' => $this->request->params['paging'][$className]['page'] + 1]) . "'>";

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
     * @return string
     */
    public function canonical()
    {
        $url = parse_url($this->request->here);
        $url = Router::fullbaseUrl() . $url['path'];

        return "<link rel='canonical' href='$url'>";
    }
}
