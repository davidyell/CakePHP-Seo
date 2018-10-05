<?php

namespace Seo\View\Helper;

use Cake\Core\InstanceConfigTrait;
use Cake\Routing\Router;
use Cake\View\Helper;

/**
 * Helper for outputting various tags to do with SEO
 *
 * @author David Yell <neon1024@gmail.com>
 */
class SeoHelper extends Helper
{
    use InstanceConfigTrait;

    /**
     * Default helper configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'paginatedControllers' => []
    ];

    /**
     * When using a paginated set of pages a link tag is used to show which pages
     * are previous and next in the paginated series
     *
     * @param string $controllerName The name of the controller
     * @return string|null
     */
    public function pagination(string $controllerName): ?string
    {
        if (in_array($controllerName, $this->getConfig('paginatedControllers'))) {
            $pagingParams = $this->request->getParam('paging');

            if (!empty($pagingParams[$controllerName])) {
                $controllerPagingParams = $pagingParams[$controllerName];

                $prev = "<link rel='prev' href='" . $this->pageLink($controllerName, $controllerPagingParams['page'], 'prev') . "'>";
                $next = "<link rel='next' href='" . $this->pageLink($controllerName, $controllerPagingParams['page'], 'next') . "'>";

                if ($controllerPagingParams['prevPage'] === false) { // page 1
                    return $next;
                } elseif ($controllerPagingParams['nextPage'] === false) {
                    return $prev;
                } else {
                    return $next . $prev;
                }
            }
        }

        return null;
    }

    /**
     * Output a canonical tag for content with multiple pages or other dynamic data
     *
     * @return string
     */
    public function canonical(): string
    {
        $url = parse_url($this->request->getUri()->getPath());
        $url = Router::fullbaseUrl() . preg_replace("/[^\w\d-\/]+/", "-", $url['path']);

        return "<link rel='canonical' href='$url'>";
    }

    /**
     * Build a url link for the previous and next pages
     *
     * @param string $controllerName The name of the controller
     * @param int $page The current page
     * @param string $direction 'prev' or 'next'
     * @param bool $full Include the fullBaseUrl?
     * @return string Complete url string
     */
    public function pageLink(string $controllerName, int $page, string $direction, bool $full = false): string
    {
        if ($direction === 'prev') {
            $page--;
        } else {
            $page++;
        }

        if ($page <= 0) {
            $page = 1;
        }

        $url = array_merge(
            ['controller' => $controllerName, 'action' => 'index'],
            ['page' => $page]
        );

        return Router::url($url, $full);
    }
}
