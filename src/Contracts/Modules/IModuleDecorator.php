<?php

namespace SlimCMS\Contracts\Modules;

interface IModuleDecorator extends IModule
{
	public function __construct(IModule $module);
}