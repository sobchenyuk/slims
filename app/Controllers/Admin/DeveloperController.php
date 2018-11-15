<?php

namespace App\Controllers\Admin;

use App\Source\Factory\ModelsFactory;

class DeveloperController extends BaseController
{
	public function phpinfo($req, $res){
		$this->resourse = false;
		$this->initRoute($req, $res);
		$this->data['h1'] = 'PHP info';
		ob_start();
		phpinfo();
		$this->data['content'] = ob_get_contents();
		ob_get_clean();

		$this->data['content'] = substr($this->data['content'], strrpos($this->data['content'], "<body>"));
		$this->data['content'] = substr($this->data['content'], 0, strrpos($this->data['content'], "</body>"));
		$this->view->render($res, 'admin\phpinfo.twig', $this->data);
	}

	public function phpCommandLine(){
		$this->resourse = false;
		$this->data['h1'] = 'PHP command line';

		$this->view->render($res, 'admin\phpinfo.twig', $this->data);
	}

	public function phpCommandLineExec(){
		$this->resourse = false;
		$this->data['h1'] = 'PHP command line';
		ob_start();
		$query = rtrim($_POST['query'], ";\x20\n").";\n";
		eval($query);
		$this->data['content'] = ob_get_contents();
		ob_get_clean();
	}
}
