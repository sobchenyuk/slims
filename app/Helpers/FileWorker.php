<?php

namespace App\Helpers;

class FileWorker
{
	static public $mode = 0776;

	/**
     * @param $allConfig
     */
    public static function savePhpReturnFile($pathFileName, $data)
    {
        $strData = var_export($data, true);
        $content = sprintf('<?php ' . PHP_EOL . PHP_EOL . 'return %s;', $strData);
        $path = pathinfo($pathFileName);
        if($p = realpath($path['dirname'])){
            $path['dirname'] = $p;
        }
        if (!is_dir($path['dirname'])) {
            self::mkDir($path['dirname']);
        }
        $fileName = $path['dirname'].DIRECTORY_SEPARATOR.$path['basename'];
        self::saveFile($fileName, $content);
    }

    public static function saveJsonFile($pathFileName, $data)
    {
    	self::saveFile($pathFileName, json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function saveFile($pathFileName, $str)
    {
    	file_put_contents($pathFileName, $str);
    }

    public static function mkDir($path)
    {
    	$status = mkdir($path, self::$mode, true);

    	return $status;
    }

    public static function getJsonDataFile($path)
    {
    	return json_decode(file_get_contents($path));
    }

    public static function copy($source, $target)
    {
        if (!is_dir($source)) {//it is a file, do a normal copy
            if( !copy($source, $target) ){
                return false;
            }
            return true;
        }

        //it is a folder, copy its files & sub-folders
        if( !self::mkDir($target) ){
            return false;
        }

        $d = dir($source);
        $navFolders = array('.', '..');
        while (false !== ($fileEntry=$d->read() )) {//copy one by one
            //skip if it is navigation folder . or ..
            if (in_array($fileEntry, $navFolders) ) {
                continue;
            }

            //do copy
            $s = "$source/$fileEntry";
            $t = "$target/$fileEntry";
            self::copy($s, $t);
        }
        $d->close();

        return true;
    }

    public static function replaseInFile($file, $arIn, $arOut)
    {
        $file_contents = file_get_contents($file);
        $file_contents = str_replace($arIn, $arOut, $file_contents);
        return file_put_contents($file, $file_contents);
    }

    public static function addItemInModelsFillable($modelName, $fieldName){
        self::workInModel($modelName, $fieldName, 'add');
    }

    public static function removeItemInModelsFillable($modelName, $fieldName)
    {
        self::workInModel($modelName, $fieldName, 'remove');
    }

    protected static function workInModel($modelName, $fieldName, $type){
        $path = APP_PATH.'Models/'.$modelName.'.php';
        $file_contents = file_get_contents($path);

        $cl = "App\\Models\\$modelName";
        $model = new $cl;
        $fillable = $model->getFillable();
        if($type == 'add'){
            if( array_search($fieldName, $fillable)===false ){
                $fillable[] = $fieldName;
            }
        }elseif($type == 'remove'){
            if( ($k = array_search($fieldName, $fillable))!==false ){
                unset($fillable[$k]);
            }
        }

        $strData = var_export($fillable, true);

        if(preg_match('/protected \$fillable = ([^;]+);(\r|\n)/', $file_contents, $m)){
            self::replaseInFile($path, $m[1], $strData);
        }
    }
}