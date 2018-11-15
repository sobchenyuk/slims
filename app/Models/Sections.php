<?php

namespace App\Models;

class Sections extends BaseModel
{
    protected $table = 'sections';

    protected static $reCount = true;
    protected static $pathOldValue;
    protected static $pathNewValue;

    const PATH_DELIMITER = '/';

    protected $fillable = ['name', 'code', 'parent_id', 'detail_text', 'detail_picture', 'show_in_menu', 'name_for_menu', 'active', 'sort', 'path'];

    public function save(array $options = array())
    {
        if ($this->parent_id && !self::$pathNewValue) {
        	$item = $this->find($this->parent_id);

            self::$pathOldValue = $this->pathAddItem($this->path, $this->id);

            $this->path = $this->pathAddItem($item->path, $this->parent_id);

        	if( isset($this->id) && in_array($this->id, explode(self::PATH_DELIMITER, $this->path)) ){
        		$GLOBALS['app']->getContainer()->flash->addMessage('errors', 'Create recursion section');
        		return;
        	}
        } elseif(self::$pathNewValue) {
            $this->path = str_replace(self::$pathOldValue, self::$pathNewValue, $this->path);
        } else {
            self::$pathOldValue = $this->pathAddItem($this->path, $this->id);
        	$this->path = self::PATH_DELIMITER.'0'.self::PATH_DELIMITER;
        }

        parent::save($options);

	    if( self::$reCount ){
            $items = $this->where('path', 'LIKE', '%/'.$this->id.'/%')->orderBy('path', 'ASC')->get();
            self::$reCount = false;
            self::$pathNewValue = $this->pathAddItem($this->path, $this->id);
            if($items){
            	foreach ($items as $item) {
            		$item->save();
            	}
            }
            self::$pathNewValue = null;
            self::$reCount = true;
        }
    }

    protected function pathAddItem($dbPath, $item)
    {
        $path = explode(self::PATH_DELIMITER, $dbPath);
        array_pop($path);
        $path[] = $item;
        $path[] = '';
        return implode(self::PATH_DELIMITER, $path);
    }

    public static function getAllGlobalActive($id = 0){
        return self::getAllGlobalActiveRaw($id)->get();
    }

    public static function getAllGlobalActiveRaw($id = 0){
        $noActive = self::where('active', 0)->where('path', 'LIKE', '%/'.$id.'/%')->get()->keyBy('id')->toArray();
        $noActive = array_keys($noActive);
        
        $data = self::where('active', 1)->where('path', 'LIKE', '%/'.$id.'/%');

        foreach ($noActive as $id) {
            $data->where('path', 'NOT LIKE', '%/'.$id.'/%');
        }

        return $data;
    }

    public static function getSubSections($id = 0)
    {
        return self::where('active', 1)->where('parent_id', $id)->get()->keyBy('id')->toArray();
    }
}
