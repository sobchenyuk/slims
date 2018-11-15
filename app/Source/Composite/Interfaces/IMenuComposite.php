<?php

namespace App\Source\Composite\Interfaces;

interface IMenuComposite
{
    public function add(IMenuComposite $menuItem);
    public function remove($id);
    public function getChild();
}