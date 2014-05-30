CakePHP-Seo
===========

# What is it?
I always need to add meta tags to my pages for SEO purposes and it was getting tedious writing tools to complete this every time. So I created a component which hooks the event system to catch the `beforeLayout` event to inject SEO data into the view variables.

I found that by containing all the functionality for SEO inside a component it makes it easier to manage.

# Installation
## Git
Clone the repo into your `app/Plugin` folder. `git clone https://github.com/davidyell/CakePHP-Seo.git app/Plugin/Seo`
## Composer
(https://packagist.org/packages/davidyell/seo)[https://packagist.org/packages/davidyell/seo]
`composer require davidyell/seo 0.0.3`

# Setup
Firstly you will need to load the plugin in your `app/Config/bootstrap.php`.
`CakePlugin::load('Seo');`

Then you will need to attach it to the controller you want it to run on. I tend to attach it to my `AppController`.

```php
// app/Controller/AppController.php
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
