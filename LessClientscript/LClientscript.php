<?php
/*
 * CClientscript extension. Adds auto Less compiling to registerCSSFile method
 * Based on LessCSS for Yii extension by zsoltlengyelit
 * orginal url: https://github.com/zsoltlengyelit/LessCSS-for-Yii
 * edited by André Kramer
 */
class LClientScript extends CClientScript{
    public $caching = true; //use cached less css file if available
    public $compress = false; //compress less css to 1 line
    public $importDir = array();

    public function registerCssFile($url, $media='')
    {
        //if the file extension is .less, use the lessparser.
        $file_extension = end(explode('.',$url));
        if($file_extension == 'less'){
            $url = $this->compileLess($url, false);
        }
        
        //run the parent method to parse the less/css file like Yii normally does.
        return parent::registerCssFile($url, $media);
    }

    protected function compileLess($less_input, $returnString = true)
    {
        //webroot of the application
        $basepath = Yii::getPathOfAlias('webroot') . DIRECTORY_SEPARATOR;       
        
        //path to assets (default /assets)
        $assetspath = Yii::app()->getAssetManager()->basePath . DIRECTORY_SEPARATOR;
        $assetName = md5($less_input);

        //reference where to store the temporary css file, before sending it to registerCSSFile
        $assetCssOrig = $assetspath . $assetName . '.css';

        $parsed = false;
        //only parse the file if caching is off and the file doesn't exist
        //Todo: Perhaps a check if the content is altered (date_modified) or file size changed
        if(!($this->caching && is_file($assetCssOrig))){  
            //make the path absolute, but make sure basepath isn't in the url first.
            $sourcepath = $basepath . str_replace($basepath, '', $less_input);
            if (is_file($sourcepath))
            {
                //start parsing the less file and store it in assets folder
                if($parsed = $this->parseLess(file_get_contents($sourcepath))){
                    file_put_contents($assetCssOrig, $parsed);
                    //@chmod($assetCssOrig, 0777);
                }                 
            }
            else {
                throw new CException(__CLASS__.': Less stylesheet not found: '. $sourcepath);
            }
        }

        // return the parsed css string or file
        if ($returnString) {
            return $parsed;
        } else {
            return Yii::app()->getAssetManager()->publish($assetCssOrig);
        }
    }

    protected function parseLess($input)
    {
        $parsed = false;

        try {
            //include lessc library
            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include'. DIRECTORY_SEPARATOR . 'lessc.inc.php');
            
            //setup the parser
            $lessc = new lessc();
            if($this->compress){
                $lessc->setFormatter("compressed"); 
            }

            $importDir = array_merge($this->importDir, array(dirname($input)));

            $lessc->setImportDir($importDir);
            
            //parse the file
            $parsed = $lessc->parse($input);            
        } catch (exception $e) {
            throw new CException(__CLASS__.': Failed to compile less file with message: '.$e->getMessage().'.');            
        }
        return $parsed;
    }
}