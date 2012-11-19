# Installation

Copy this repo to your Yii application extension folder (default protected/extensions)

# Usage

Add the extension to your configuration
```php
    ...
    'components' => array(
        ...
        'clientScript' => array(
            'class' => 'ext.LessClientscript.LClientscript',
            'caching' => true,
            'compress' => false,
            'importDir' => array('themes/classic/less/')
        )
        ...
    )
    ...
```

To call it into your view

```php
    // SiteController.php file
    public function actionIndex()
    {
        Yii::app()->clientScript->registerCSSFile('/assets/css/test.less','screen');
    	  $this->render('index');
    }
```

# Attributes

caching: Boolean true or false, default true
Use a cached version of the compiled less if available.

compress: Boolean, default false
Removes whitespace and linearizes the css to 1 line.

# Credits

Based on LessCSS for Yii by [zsoltlengyelit](https://github.com/zsoltlengyelit/LessCSS-for-Yii)
Lessc parser by Leaf [Corcoran](http://leafo.net/lessphp)