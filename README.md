CakePHP-Seo
===========
[![Build Status](https://travis-ci.org/davidyell/CakePHP-Seo.svg?branch=master)](https://travis-ci.org/davidyell/CakePHP-Seo)
[![Coverage Status](https://coveralls.io/repos/davidyell/CakePHP-Seo/badge.svg)](https://coveralls.io/r/davidyell/CakePHP-Seo)

# What is it?
I always need to add meta tags to my pages for SEO purposes and it was getting tedious writing tools to complete this 
every time. So I created a component which hooks the event system to catch the `beforeLayout` event to inject SEO 
data into the view variables.

I found that by containing all the functionality for SEO inside a component it makes it easier to manage.

# Requirements
* CakePHP 3.6+
* PHP 7.2+

# Installation
[https://packagist.org/packages/davidyell/seo](https://packagist.org/packages/davidyell/seo)

```bash
composer require davidyell/seo
```

# Setup
Firstly you will need to load the plugin in your `/config/bootstrap.php`.
```php
Plugin::load('Seo');
```

Then you will need to attach it to the controller you want it to run on. I tend to attach it to my `AppController`.

```php
// src/Controller/AppController.php initialize() method
$this->loadComponent('Seo.Seo' => [
	'defaults' => [
		'title' => 'Dave is epic',
		'description' => 'This is an epic plugin for epic people',
		'keywords' => 'epic,plugin'
	]
];
```

There are a number of configuration settings you will need to change to match your setup. You can find these in the 
`$settings` class variable in the component. Primarily you will want to change the `$settings['defaults']` to set the 
default title, description and keywords for your website.

# How it works
The idea is that your model will have some fields editable in the CMS for SEO. Once this data is set to the view, the 
component will catch the data and inject it into your layout for you automatically.

As such your layout will need some things to exist in order for the component to correctly add the data.

```php
// For the page title
echo $this->fetch('title');

// For outputting the meta tags inside <head>
echo $this->fetch('meta');
```

# Database configuration
This is for you to do. How you store your SEO data is outside the scope of this plugin. However I would recommend 
creating fields either in your `Contents` table or associated to it, with `seo_title VARCHAR(255)`, 
`seo_description TEXT`, and `seo_keywords VARCHAR(255)`. 

# Tips and Tricks
Got two viewVars set in your controller and you want to change it up depending on which is set?
```php
// ProvidersController::beforeRender()

if (isset($this->viewVars['content'])) {
    $this->components()->get('Seo')->setConfig('viewVar', 'article');
} elseif (isset($this->viewVars['provider'])) {
    $this->components()->get('Seo')->setConfig('viewVar', 'provider');
}
```

The viewVar access accepts a hash path, so you can use dot notation to access deeply nested data.

```php
$this->Components->load('Seo.Seo', [
    'viewVar' => 'catalog',
    'fields' => [
        'title' => 'assigned_content.content.seo_title'
    ]
]);
```

Don't forget that you can set the config directly on an instance of the component.

```php
// ExamplesController.php

$this->components()->get('Seo')->setConfig('fields.title', 'My new title');
```

# Error handler middleware
It is very helpful to be able to catch 404 errors and use them to manage your SEO redirecting. This allows for only urls 
which do not match your application to be redirecting, avoiding any overhead.

The plugin provides a basic middleware for this purpose which can be implemented into your `/src/Application.php`

```
$this->redirects = [
    '/examples/first-example' => [
        'target' => '/tutorials/first',
        'code' => 301
    ]
];
$queue->add(new \Seo\Error\Middleware\ErrorHandlerMiddleware($this->redirects))
```

