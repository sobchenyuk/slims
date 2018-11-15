<?php

namespace App\Controllers\Admin;

use App\Helpers\SessionManager as Session;
use \Psr\Http\Message\ServerRequestInterface as request;
use App\Source\Factory\ModelsFactory;
//use App\Source\ModelFieldBuilder\BuildFields;

class UniversalAjaxController extends BaseController
{
	public function __construct($container){
		parent::__construct($container);

		$this->data = array(
			'data'=> array(
				'html' => '',
				'success' => '',
				'error' => '',
				'data' => ''
			)
		);
	}

	public function update($request, $response, $args){
		$params = $request->getParsedBody();

		$model = ModelsFactory::getModel('UserViewsSettings');

		$u_id = Session::get('user')['id'];
		$result = $model->where('user_id', $u_id)->where('group', $_REQUEST['group'])->where('code', $_REQUEST['code'])->first();

		if( !$result ){
			$result = ModelsFactory::getModel('UserViewsSettings', $_REQUEST);
			$result->user_id = $u_id;
		}

		$result->value = json_encode($_REQUEST['show']);
		$result->save();

		$this->data['data']['success'] = true;
		
		$this->view->render($response, 'json.twig', $this->data);
		
		return $response->withStatus(200)->withHeader('Content-type', 'application/json');
	}
	/*public function index(request $req, $res){
		$this->initRoute($req);

		$model = ModelsFactory::getModelWithRequest($req);

		$this->data['items'] = $model->paginate($this->pagecount);
		$this->data['items']->setPath($this->router->pathFor($this->data['all_e_link']));
		$this->data['fields'] = $this->getFields($model->getColumnsNames(), array('id'));
		$this->data['allFields'] = $model->getColumnsNames();

		$this->view->render($res, 'admin\dataTables.twig', $this->data);
	}

	public function add(request $req, $res){
		$this->initRoute($req);

		$model = ModelsFactory::getModelWithRequest($req);
		$this->data['fields'] = $this->getFields($model->getColumnsNames());

$builder = new BuildFields();
$builder->setFields($model->getColumnsNames())->addJsonShema($model->getAnnotations());
$this->data['ttt'] = $builder->getAll();

		$this->view->render($res, 'admin\addTables.twig', $this->data);
	}

	public function edit(request $req, $res, $args){
		$this->initRoute($req);

		$model = ModelsFactory::getModelWithRequest($req);
		$this->data['fields'] = $this->getFields($model->getColumnsNames(), ['id']);
		$this->data['fieldsValues'] = $model->find($args['id']);
		$this->data['type_link'] = $this->data['save_link'];

$builder = new BuildFields();
$builder->setFields($model->getColumnsNames())->addJsonShema($model->getAnnotations());
$builder->build();
$builder->setType('id', 'hidden');

foreach ($this->data['fields'] as $name) {
	$builder->getField($name)->setValue($this->data['fieldsValues']->$name);
}

$this->data['ttt'] = $builder->getAll();

		$this->view->render($res, 'admin\addTables.twig', $this->data);
	}*/

	public function doAdd(request $req, $res, $args){
		$this->initRoute($req);
		$model = ModelsFactory::getModelWithRequest($req, $req->getParsedBody());
		$model->save();
		
		$this->flash->addMessage('success', $this->controllerName.' success added!');

		return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('list.'.$this->controllerName));
	}

	public function doEdit(request $req, $res, $args){
		$this->initRoute($req);
		$reqData = $req->getParsedBody();
		$model = ModelsFactory::getModelWithRequest($req);
		$model = $model->find($reqData['id']);

		$model->update($reqData);
		$this->flash->addMessage('success', $this->controllerName.' success edited!');

		return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('list.'.$this->controllerName));
	}

	public function doDelete(request $req, $res, $args){
		$this->initRoute($req);
		$model = ModelsFactory::getModelWithRequest($req);
		$model = $model->find($args['id']);
		$model->delete();

		$this->flash->addMessage('success', $this->controllerName.' success deleted!');

		return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('list.'.$this->controllerName));
	}
}
