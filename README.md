CakePHP-Seo
===========
[![Build Status](https://travis-ci.org/davidyell/CakePHP-Seo.svg?branch=master)](https://travis-ci.org/davidyell/CakePHP-Seo)
[![Coverage Status](https://coveralls.io/repos/davidyell/CakePHP-Seo/badge.svg)](https://coveralls.io/r/davidyell/CakePHP-Seo)

# What is it?
I always need to add meta tags to my pages for SEO purposes and it was getting tedious writing tools to complete this every time. So I created a component which hooks the event system to catch the `beforeLayout` event to inject SEO data into the view variables.

I found that by containing all the functionality for SEO inside a component it makes it easier to manage.

# Requirements
* CakePHP 3
* PHP 5.4.16+

# Installation
[https://packagist.org/packages/davidyell/seo](https://packagist.org/packages/davidyell/seo)

```bash
composer require 'davidyell/seo:dev-master'
```

# Setup
Firstly you will need to load the plugin in your `/config/bootstrap.php`.
```php
Plugin::load('Seo');
```

Then you will need to attach it to the controller you want it to run on. I tend to attach it to my `AppController`.

```php
// src/Controller/AppController.php
public $components = [
	'Seo.Seo' => [
		'defaults' => [
			'title' => 'Dave is epic',
			'description' => 'This is an epic plugin for epic people',
			'keywords' => 'epic,plugin'
		]
	]
];
```

There are a number of configuration settings you will need to change to match your setup. You can find these in the `$settings` class variable in the component. Primarily you will want to change the `$settings['defaults']` to set the default title, description and keywords for your website.

# How it works
The idea is that your model will have some fields editable in the CMS for SEO. Once this data is set to the view, the component will catch the data and inject it into your layout for you automatically.

As such your layout will need some things to exist in order for the component to correctly add the data.

```php
// For the page title
echo $this->fetch('title');

// For outputting the meta tags inside <head>
echo $this->fetch('meta');
```

# Tips and Tricks
Got two viewVars set in your controller and you want to change it up depending on which is set?
```php
// ProvidersController::beforeRender()
if (isset($this->viewVars['content'])) {
	$this->Components->load('Seo.Seo');
} elseif (isset($this->viewVars['provider'])) {
	$this->Components->load('Seo.Seo', ['viewVar' => 'provider']);
}
```
