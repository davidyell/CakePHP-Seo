<?php

namespace Seo\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Utility\Hash;

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
     * Has the SEO been injected already? Prevent duplicate amends to the meta block
     *
     * @var bool
     */
    private $hasRun = false;

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
        if ($this->hasRun === true) {
            return;
        }

        if (!empty($event->subject()->viewVars[$this->config('viewVar')])) {
            $seoTitle = Hash::get(
                $event->subject()->viewVars[$this->getConfig('viewVar')],
                $this->getConfig('fields.title'),
                $this->getConfig('defaults.title')
            );

            $event->subject()->assign('title', $seoTitle);
        }

        if (!empty($event->subject()->viewVars[$this->config('viewVar')])) {
            $seoDescription = Hash::get(
                $event->subject()->viewVars[$this->getConfig('viewVar')],
                $this->getConfig('fields.description'),
                $this->getConfig('defaults.description')
            );

            $event->subject()->Html->meta(
                'description',
                $seoDescription,
                ['block' => true]
            );
        }

        if (!empty($event->subject()->viewVars[$this->config('viewVar')])) {
            $seoKeywords = Hash::get(
                $event->subject()->viewVars[$this->getConfig('viewVar')],
                $this->getConfig('fields.keywords'),
                $this->getConfig('defaults.keywords')
            );

            $event->subject()->Html->meta(
                'keywords',
                $seoKeywords,
                ['block' => true]
            );
        }

        // If no values can be found, fall back to the defaults
        if (empty($seoTitle)) {
            $event->subject()->assign('title', $this->config('defaults.title'));
        }
        if (empty($seoDescription)) {
            $event->subject()->Html->meta('description', $this->config('defaults.description'), ['block' => true]);
        }
        if (empty($seoKeywords)) {
            $event->subject()->Html->meta('keywords', $this->config('defaults.keywords'), ['block' => true]);
        }

        $this->hasRun = true;
    }
}
