<?php

namespace App\Source\Composite;

use App\Source\Composite\Interfaces\IMenuComposite;

/**
 * Class Menu
 * @package App\Source\Composite
 */
class Menu extends AMenu
{
    /**
     * @param IMenuComposite $menuItem
     */
    public function add(IMenuComposite $menuItem)
    {
        $this->menu[$menuItem->id] = $menuItem;
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        if (isset($this->menu[$id])){
            unset($this->menu[$id]);
        } elseif($t = $this->getParent($id)) {
            $t->remove($id);
        }
    }

    /**
     * @param int $id
     * @return $this
     */
    public function getChild($id = false)
    {
        if( $id === false )
            return $this->menu;

        if( $id == $this->getId() )
            return $this;

        foreach($this->menu as $item){
            if( $id == $item->getId() )
                return $item;
            elseif( $i = $item->getChild($id) )
                return $i;
        }
    }

    /**
     * @param $id
     */
    public function getParent($id = 0)
    {
        foreach($this->menu as $item){
            if( $item->getId() == $id )
                return $this;
            elseif($i = $item->getParent($id))
                return $i;
        }
    }

    /**
     * Return url
     *
     * @param  array|string  $options
     * @return string
     */
    public function getUrl($options)
    {
        if( !is_array($options) ) {
            return $options;
        }

        if ( isset($options['url']) ) {
            return (string)$options['url'];
        }

        return "";
    }

    /**
     * Extract valid html attributes from user's options
     *
     * @param  array $options
     * @return array
     */
    public function extractAttr($options){
        return array_diff_key($options, array_flip($this->reserved));
    }

    /**
     * Set or get items's attributes
     *
     * @return string|Item
     */
    public function attributes($arAttr=null, $value=null)
    {
        if(is_array($arAttr)) {
            $this->attributes = array_merge($this->attributes, $arAttr);
            return $this;
        } elseif(isset($value)) {
            $this->attributes[$arAttr] = $value;
            return $this;
        } elseif($arAttr) {
            return isset($this->attributes[$arAttr]) ? $this->attributes[$arAttr] : null;
        }

        return $this->attributes;
    }


    /**
     * Count number of items in the menu
     *
     * @return int
     */
    public function length()
    {
        return count($this->menu);
    }

    /**
     * Set or get items's meta data
     *
     * @return string|MenuItem
     */
    public function meta($arData=null, $value=null)
    {
        if(is_array($arData)) {
            $this->meta = array_merge($this->meta, $arData);
            return $this;
        } elseif(isset($value)) {
            $this->meta[$arData] = $value;
            return $this;
        } elseif($arData) {
            return isset($this->meta[$arData]) ? $this->meta[$arData] : null;
        }

        return $this->meta;
    }

    public function getAllItems()
    {
        $arItems = [];
        foreach($this->menu as $item)
        {
            $arItems[$item->getId()] = $item;
            if( $item->length() > 0 )
                $arItems = $arItems + $item->getAllItems();
        }
        return $arItems;
    }

    /**
     * Filter menu items by user callback
     *
     * @param  callable $callback
     * @return Menu
     */
    public function filter($callback)
    {
        $items = $this->getAllItems();

        if( is_callable($callback) ) {
            $this->menuFiltered = array_filter($items, $callback);
        }

        return $this->menuFiltered;
    }

    public function sortByMeta($metaName)
    {
        usort($this->menu, function($a, $b) use ($metaName){
            if ($a->meta($metaName) == $b->meta($metaName)) {
                return 0;
            }
            return ($a->meta($metaName) < $b->meta($metaName)) ? -1 : 1;
        });

        return $this;
    }

    public function clearFilter()
    {
        $this->menuFiltered = $this->menu;
    }
}
