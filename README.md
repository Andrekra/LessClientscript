<h1>Installation</h1>
<p>
    Copy this repo to your Yii application extension folder (default protected/extensions)
</p>

<h1>Usage</h1>
<p>
  Add the extension to your configuration
  <pre>
    'components' => array(
        'clientScript' => array(
            'class' => 'ext.LessClientscript.LClientscript',
            'caching' => true,
            'compress' => false,
            'importDir' => array('themes/classic/less/')
        )
    )
  </pre>
  To call it into your view
  <pre>
  // SiteController.php file
  public function actionIndex()
	{
      Yii::app()->clientScript->registerCSSFile('/assets/css/test.less','screen');
		  $this->render('index');
	}
  </pre>
  
</p>
<h1>Attributes</h1>
<p>
    caching: Boolean true or false, default true
    Use a cached version of the compiled less if available.

    compress: Boolean, default false
    Removes whitespace and linearizes the css to 1 line.
</p>
<h1>Credits</h1>
<p>
    Based on LessCSS for Yii by zsoltlengyelit (https://github.com/zsoltlengyelit/LessCSS-for-Yii)
    Lessc parser by Leaf Corcoran, http://leafo.net/lessphp
</p>

