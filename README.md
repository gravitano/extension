Orchestra Platform Extension Component
==============

`Orchestra\Extension` allows components or packages to be added dynamically to Orchestra Platform without the hassle of modifying the configuration.

[![Latest Stable Version](https://poser.pugx.org/orchestra/extension/v/stable.png)](https://packagist.org/packages/orchestra/extension) 
[![Total Downloads](https://poser.pugx.org/orchestra/extension/downloads.png)](https://packagist.org/packages/orchestra/extension) 
[![Build Status](https://travis-ci.org/orchestral/extension.png?branch=master)](https://travis-ci.org/orchestral/extension) 
[![Coverage Status](https://coveralls.io/repos/orchestral/extension/badge.png?branch=master)](https://coveralls.io/r/orchestral/extension?branch=master) 
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/orchestral/extension/badges/quality-score.png?s=bc07078a1276c92c6b4c25eaa491fd5244925d74)](https://scrutinizer-ci.com/g/orchestral/extension/) 

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/extension": "2.1.*"
	}
}
```

Next add the following service provider in `app/config/app.php`.

```php
'providers' => array(

	// ...

	'Orchestra\Extension\ExtensionServiceProvider',
	'Orchestra\Memory\MemoryServiceProvider',
	'Orchestra\Extension\PublisherServiceProvider',

	'Orchestra\Extension\CommandServiceProvider',
),
```

### Migrations

Before we can start using `Orchestra\Extension`, please run the following:

```bash
php artisan extension:migrate
```

> The command utility is enabled via `Orchestra\Extension\CommandServiceProvider`.

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/extension)
* [Change Log](http://orchestraplatform.com/docs/latest/components/extension/changes#v2-1)
