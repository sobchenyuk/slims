<?php

namespace App\Controllers\Admin;

use App\Helpers\SessionManager as Session;
use \Illuminate\Database\Capsule\Manager as Schema;
use Illuminate\Pagination\UrlWindow;
use \Psr\Http\Message\ServerRequestInterface as request;
use App\Source\Factory\ModelsFactory;
use App\Source\ModelFieldBuilder\BuildFields;

class UniversalController extends BaseController
{
	public function index(request $req, $res){
		$this->initRoute($req, $res);

		$model = ModelsFactory::getModelWithRequest($req);

		$this->data['items'] = $model->orderBy($this->pageOrderBy, $this->pageOrderType)->paginate($this->pagecount);

		$this->data['items']->setPath($this->router->pathFor($this->data['all_e_link']));
		$this->data['items']->pagenItems = UrlWindow::make($this->data['items']);

		$this->data['fields'] = $this->getFields($model->getColumnsNames(), array('id'));

		$userField = ModelsFactory::getModel('UserViewsSettings');
		$userField = $userField->where('user_id', Session::get('user')['id'])->where('group', $this->data['all_e_link'])->where('code', 'show_fields_in_table')->first();

		$this->data['showFields'] = array();
		if( $userField ){
			$this->data['showFields'] = (array)json_decode($userField->toArray()['value']);
			$this->data['fields'] = $this->data['showFields'];
		}

		$this->data['allFields'] = array_diff($model->getColumnsNames(), $this->data['showFields']);

		$this->render('admin\dataTables.twig');
	}

	public function add(request $req, $res){
		$this->initRoute($req, $res);

		$model = ModelsFactory::getModelWithRequest($req);
		$this->data['fields'] = $this->getFields($model->getColumnsNames());

		$builder = new BuildFields();
		$builder->setFields($model->getColumnsNames())->addJsonShema($model->getAnnotations());
		$this->data['ttt'] = $builder->getAll();

		$this->render('admin\addTables.twig');
	}

	public function edit(request $req, $res, $args){
		$this->initRoute($req, $res);

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

		$this->render('admin\addTables.twig');
	}

	public function doAdd(request $req, $res, $args){
		$this->initRoute($req, $res);
		$model = ModelsFactory::getModelWithRequest($req, $req->getParsedBody());
		$reqData = $this->uploadFiles($req, array());

		if(!empty($reqData)){
			foreach ($reqData as $k=>$v) {
				$model->$k = $v;
			}
		}

		$model->save();

		$this->flash->addMessage('success', $this->controllerName.' success added!');

		return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('list.'.$this->controllerName));
	}

	public function doEdit(request $req, $res, $args){
		$this->initRoute($req, $res);
		$reqData = $req->getParsedBody();
		$model = ModelsFactory::getModelWithRequest($req);
		$model = $model->find($reqData['id']);

		$reqData = $this->uploadFiles($req, $reqData);

		$model->update($reqData);
		$this->flash->addMessage('success', $this->controllerName.' success edited!');

		return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('list.'.$this->controllerName));
	}

	public function doDelete(request $req, $res, $args){
		$this->initRoute($req, $res);
		$model = ModelsFactory::getModelWithRequest($req);
		$model = $model->find($args['id']);
		$model->delete();

		$this->flash->addMessage('success', $this->controllerName.' success deleted!');

		return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('list.'.$this->controllerName));
	}

	protected function uploadFiles($req, $reqData){
		$files = $req->getUploadedFiles();
		$arFields = array_keys($files);

		if( !$arFields )
			return $reqData;

		$path = "uploads/".$this->controllerName.'/';
		if( !is_dir(PUBLIC_PATH.$path) ){
			mkdir(PUBLIC_PATH.$path);
		}

		foreach($arFields as $field){
			$newfile = $files[$field][0];

			if ($newfile->getError() === UPLOAD_ERR_OK) {
				$uploadFileName = $newfile->getClientFilename();
				$newfile->moveTo(PUBLIC_PATH.$path.$uploadFileName);
				$reqData[$field] = $path.$uploadFileName;
			}
		}

		return $reqData;
	}
}
