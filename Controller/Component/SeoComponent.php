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
		'viewVar' => 'content', // Name of the view variable being used in views
		'model' => 'Content', // Model containing the fields
		'fields' => [
			'title' => 'seo_title',
			'description' => 'seo_description',
			'keywords' => 'seo_keywords'
		],
		'noSeoPrefix' => ['admin'], // Any routing prefixes you do not SEO run
		'defaults' => [
			'title' => 'The homepage | My Awesome Website',
			'description' => 'Find out about how awesome I am by reading my website',
			'keywords' => 'my, website, is, totally, awesome'
		]
	];

/**
 * Merge component settings
 *
 * @param \ComponentCollection $collection
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
 * Inject the seo data into the view
 *
 * @param CakeEvent $event
 * @return void
 */
	public function writeSeo(CakeEvent $event) {

		$seoTitle = @$event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['title']];
		if (isset($seoTitle) && !empty($seoTitle)) {
			$event->subject()->viewVars['title_for_layout'] = $seoTitle;
		}

		$seoDescription = @$event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['description']];
		if (isset($seoDescription) && !empty($seoDescription)) {
			$event->subject()->Html->meta('description', $seoDescription, ['block' => 'meta']);
		}

		$seoKeywords = @$event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['keywords']];
		if (isset($seoKeywords) && !empty($seoKeywords)) {
			$event->subject()->Html->meta('keywords', $seoKeywords, ['block' => 'meta']);
		}


		// Default
		if (!isset($event->subject()->viewVars[$this->settings['viewVar']][$this->settings['model']][$this->settings['fields']['title']])) {
			$event->subject()->viewVars['title_for_layout'] = $this->settings['defaults']['title'];
			$event->subject()->Html->meta('description', $this->settings['defaults']['description'], ['block' => 'meta']);
			$event->subject()->Html->meta('keywords', $this->settings['defaults']['keywords'], ['block' => 'meta']);
		}

	}
}