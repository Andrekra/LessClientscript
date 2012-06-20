<?php
/*
 * CClientscript extension. Adds auto Less compiling to registerCSSFile method
 * Based on LessCSS for Yii extension by zsoltlengyelit
 * orginal url: https://github.com/zsoltlengyelit/LessCSS-for-Yii
 * edited by André Kramer
 */
class LClientscript extends CClientScript{
    public $caching = true; //use cached less css file if available
    public $compress = false; //compress less css to 1 line
    protected static $staticPath = null;

    public function registerCssFile($url,$media='')
    {
        //if the file extension is .less, use the lessparser.
        $file_extension = end(explode('.',$url));
        if($file_extension == 'less'){
            $url = $this->compileLess($url, $this->caching);
        }
        //run the parent method to parse the less/css file like Yii normally does.
        parent::registerCssFile($url, $media);
    }
    /**
     * Compliles the input CSS file. If the $cache is setted, the output will be chached by it.
     *
     * @param string input file path relative to the application path (e.g. '/css/test.less' if the file is /absolute/path/to/your_app/css/test.less)
     * @param boolean cache enabled = true, disabled = false
     * @param boolean if true, returns string, else the path of the compiled CSS file
     *
     * @return string file path or compiled string according to 3th param
     */
    protected function compileLess($less, $returnString = true)
    {
        if (is_null(self::$staticPath))
            self::$staticPath = str_replace('/protected', '', dirname(Yii::app()->basePath)) . '/';

        $assetName = str_replace(DIRECTORY_SEPARATOR, '.', str_replace(self::$staticPath, '', $less));
        $assetName = md5($assetName);

        //store less in assets folder
        $assetCssOrig = Yii::app()->getAssetManager()->basePath . DIRECTORY_SEPARATOR . $assetName . '.css';

        $parsed = false;
       // $lessc = null;

        if($this->caching && is_file($assetCssOrig)){
            Yii::trace('used cache set by global: ' . $assetName); // TRACE
        } else {
            Yii::trace('Less parsed by no cache: ' . $less); // TRACE
            $this->parseLess(file_get_contents($less), $parsed);
            $this->putIntoFile($assetCssOrig, $parsed);
        }


        // parsed string or file is done
        if ($returnString) {
            return $parsed;
        } else {
            return Yii::app()->getAssetManager()->publish($assetCssOrig);
        }
    }

    /**
     * Parses $input file into $output
     * @param string input less CSS
     * @param string output parsed CSS
     *
     * @return boolean true if parse was successfull
     */
    protected function parseLess($input, &$output)
    {
        $parsed = null;

        try {
            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include'. DIRECTORY_SEPARATOR . 'lessc.inc.php');
            $lessc = new lessc();
            $lessc->setFormatter("compressed");
            ob_start();
            $parsed = trim($lessc->parse($input));
            ob_end_clean();
        } catch (exception $e) {
            Yii::log(
                'Failed to compile LessCss input, reason:' . $e->getMessage(),
                'error',
                __CLASS__
            );
            return false;
        }
        $output = $parsed;

        return true;
    }

    protected function putIntoFile($file, $content)
    {
        file_put_contents($file, $content);
        @chmod($file, 0777);
    }
}
?>