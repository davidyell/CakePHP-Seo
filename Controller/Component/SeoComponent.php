<?php

App::uses('CakeEventListener', 'Event');
App::uses('CakeEvent', 'Event');

/**
 * Component to find and load seo data and inject it into the view
 *
 * @author David Yell <neon1024@gmail.com>
 */

class SeoComponent extends Component implements CakeEventListener {

/**
 * Store component settings
 *
 * @var array
 */
	public $settings = [
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
 * Merge component settings
 *
 * @param ComponentCollection $collection
 * @param array $settings
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$settings = array_merge($this->settings, $settings);
		parent::__construct($collection, $settings);
	}


/**
 * Setup the component
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param Controller $controller
 */
	public function startup(Controller $controller) {
		parent::startup($controller);
		if (!in_array($controller->request->prefix, $this->settings['noSeoPrefix'])) {
			$controller->getEventManager()->attach($this);
		}
	}

/**
 * List of callable functions which are attached to system events
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'View.beforeLayout' => 'writeSeo'
		);
	}

/**
 * Inject seo data into the view
 *
 * @param CakeEvent $event
 * @return void
 */
	public function writeSeo(CakeEvent $event) {

		$seo = [];
		foreach ($this->settings['fields'] as $type => $fieldName) {
			$d = Hash::extract($event->subject()->viewVars, "{s}.{s}.$fieldName");
			if (!empty($d[0])) {
				$seo[$type] = $d[0];
			} else {
				$seo[$type] = null;
			}
		}

		if (!empty($seo['title'])) {
			$event->subject()->viewVars['title_for_layout'] = $seo['title'];
		}
		if (!empty($seo['description'])) {
			$event->subject()->Html->meta('description', $seo['description'], ['block' => 'meta']);
		}
		if (!empty($seo['keywords'])) {
			$event->subject()->Html->meta('keywords', $seo['keywords'], ['block' => 'meta']);
		}


		// If no values can be found, fall back to the defaults
		if (empty($seo['title'])) {
			$event->subject()->viewVars['title_for_layout'] = $this->settings['defaults']['title'];
		}
		if (empty($seo['description'])) {
			$event->subject()->Html->meta('description', $this->settings['defaults']['description'], ['block' => 'meta']);
		}
		if (empty($seo['keywords'])) {
			$event->subject()->Html->meta('keywords', $this->settings['defaults']['keywords'], ['block' => 'meta']);
		}

	}
}