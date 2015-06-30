<?php

namespace Seo\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\EventListenerInterface;

/**
 * Component to find and load seo data and inject it into the view
 *
 * @author David Yell <neon1024@gmail.com>
 */

class SeoComponent extends Component implements EventListenerInterface
{

    /**
     * Store component settings
     *
     * @var array
     */
    protected $_defaultConfig = [
        'viewVar' => 'content', // Name of the view variable being used in views
        'model' => 'Content', // Model containing the fields
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
        if (!in_array($controller->request->prefix, $this->settings['noSeoPrefix'])) {
            $controller->getEventManager()->attach($this);
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
     * @param CakeEvent $event Event instance
     * @return void
     */
    public function writeSeo(CakeEvent $event)
    {
        if (!empty($event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['title']])) {
            $seoTitle = $event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['title']];
            $event->subject()->viewVars['title_for_layout'] = $seoTitle;
        }

        if (!empty($event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['description']])) {
            $seoDescription = $event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['description']];
            $event->subject()->Html->meta(
                'description',
                $seoDescription,
                ['block' => 'meta']
            );
        }

        if (!empty($event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['keywords']])) {
            $seoKeywords = $event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['keywords']];
            $event->subject()->Html->meta(
                'keywords',
                $seoKeywords,
                ['block' => 'meta']
            );
        }

        // If no values can be found, fall back to the defaults
        if (empty($seoTitle)) {
            $event->subject()->viewVars['title_for_layout'] = $this->settings['defaults']['title'];
        }
        if (empty($seoDescription)) {
            $event->subject()->Html->meta('description', $this->settings['defaults']['description'], ['block' => 'meta']);
        }
        if (empty($seoKeywords)) {
            $event->subject()->Html->meta('keywords', $this->settings['defaults']['keywords'], ['block' => 'meta']);
        }
    }
}
