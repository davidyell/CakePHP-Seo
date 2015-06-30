<?php

namespace Seo\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

/**
 * Component to find and load seo data and inject it into the view
 *
 * @author David Yell <neon1024@gmail.com>
 */

class SeoComponent extends Component implements EventListenerInterface
{
    /**
     * The controller instance
     *
     * @var \Cake\Controller\Controller
     */
    protected $_controller;

    /**
     * Store component settings
     *
     * @var array
     */
    protected $_defaultConfig = [
        'viewVar' => 'content', // Name of the view variable being used in views
        'fields' => [
            'title' => 'seo_title',
            'description' => 'seo_description',
            'keywords' => 'seo_keywords'
        ],
        'noSeoPrefix' => ['admin'], // Any routing prefixes you do not SEO
        'defaults' => [
            'title' => 'The homepage | My Awesome Website',
            'description' => 'Find out about how awesome I am by reading my website',
            'keywords' => 'my, website, is, totally, awesome'
        ]
    ];
    
    /**
     * Create the class
     *
     * @param array $config The component configuration array
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_controller = $this->_registry->getController();

        if (!in_array($this->_controller->request->prefix, $this->config('noSeoPrefix'))) {
            $this->_controller->eventManager()->on($this);
        }
    }

    /**
     * List of callable functions which are attached to system events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'View.beforeLayout' => 'writeSeo'
        ];
    }

    /**
     * Inject the seo data into the view
     *
     * @param \Cake\Event\Event $event Event instance
     * @return void
     */
    public function writeSeo(Event $event)
    {
        if (!empty($event->subject()->viewVars[$this->config('viewVar')]->get($this->config('fields.title')))) {
            $seoTitle = $event->subject()->viewVars[$this->config('viewVar')]->get($this->config('fields.title'));
            $event->subject()->assign('title', $seoTitle);
        }

        if (!empty($event->subject()->viewVars[$this->config('viewVar')]->get($this->config('fields.description')))) {
            $seoDescription = $event->subject()->viewVars[$this->config('viewVar')]->get($this->config('fields.description'));
            $event->subject()->Html->meta(
                'description',
                $seoDescription,
                ['block' => 'meta']
            );
        }

        if (!empty($event->subject()->viewVars[$this->config('viewVar')]->get($this->config('fields.keywords')))) {
            $seoKeywords = $event->subject()->viewVars[$this->config('viewVar')]->get($this->config('fields.keywords'));
            $event->subject()->Html->meta(
                'keywords',
                $seoKeywords,
                ['block' => 'meta']
            );
        }

        // If no values can be found, fall back to the defaults
        if (empty($seoTitle)) {
            $event->subject()->assign('title', $this->config('defaults.title'));
        }
        if (empty($seoDescription)) {
            $event->subject()->Html->meta('description', $this->config('defaults.description'), ['block' => 'meta']);
        }
        if (empty($seoKeywords)) {
            $event->subject()->Html->meta('keywords', $this->config('defaults.keywords'), ['block' => 'meta']);
        }
    }
}
