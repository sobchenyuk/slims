<?php

namespace App\Source\Composite;

use App\Source\Composite\Interfaces\IMenuComposite;

/**
 * Class AMenu
 * @package App\Source\Composite
 */
abstract class AMenu implements IMenuComposite, \ArrayAccess
{
    public function offsetExists ( $id ){ return (isset($this->menu[$id])||$id == $this->getId()); }
    public function offsetGet ( $id ){ return ($id == $this->getId())?$this->menu:$this->menu[$id]; }
    public function offsetSet ( $id, $value ){ $this->add($value); }
    public function offsetUnset ( $id ){ $this->remove($id); }

    /**
     * @var int
     */
    protected static $last_id=0;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $menu = [];

    /**
     * Reserved keys
     *
     * @var array
     */
    protected $reserved = ['url', 'link_attr', 'meta_attr', 'sub_menu', 'menu_name'];

    /**
     * Item's meta data
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Item's attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Item's hyperlink
     *
     * @var Link
     */
    public    $link;

    /**
     * @param $name
     * @param null $options
     */
    public function __construct($name, $options = null, $_name = false)
    {
        $url  = $this->getUrl($options);

        $this->name       = strtolower(preg_replace('/[^\w\d\-\_\.]/s', "", $name));
        $this->attributes = ( is_array($options) ) ? $this->extractAttr($options) : array();

        if($_name)
            $this->name = $_name;

        if( is_array($options) ){
            $linkAttr = $options['link_attr'];
            $this->meta = $options['meta_attr'];
            $this->name = ($options['menu_name'])?$options['menu_name']:$this->name;
            if($options['sub_menu'] && is_array($options['sub_menu'])){
                while ($item = array_shift($options['sub_menu'])) {
                    $this->add($item);
                }
            }
        }

        // Create an object of type Link
        $this->link       = new MenuLink($name, $url, $linkAttr);

        $this->id = ++self::$last_id;
    }

    /**
     * @param IMenuComposite $menuItem
     * @return mixed
     */
    abstract public function add(IMenuComposite $menuItem);

    /**
     * @param $id
     * @return mixed
     */
    abstract public function remove($id);

    /**
     * @return mixed
     */
    abstract public function getChild();

    /**
     * @param int $id
     * @return mixed
     */
    abstract public function getParent($id = 0);

    /**
     * @param $options
     * @return mixed
     */
    abstract public function getUrl($options);

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLastId()
    {
        return self::$last_id;
    }

    public function getById($id)
    {
        return $this->getChild($id);
    }

    public function getByName($name)
    {
        if( $name == $this->name )
            return $this;

        foreach($this->menu as $item){
            if( $name == $item->name )
                return $item;
            elseif( $i = $item->getByName($id) )
                return $i;
        }
    }
}