<?php

namespace App\Controllers\Admin;

use App\Helpers\SessionManager as Session;
use App\Models\Users;

class AuthController extends BaseController
{
	public function login($req, $res){
		$this->csrf($req);
		if(Session::get('auth') && Session::get('user'))
		    return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('dashboard'));
		$this->data['auth_type'] = $this->containerSlim->get('systemOptions')->getValue('email_or_login');

		$this->view->render($res, 'admin\login.twig', $this->data);
	}

	public function logout($req, $res){
		Session::forget('auth');
		Session::forget('user');

		return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('login'));
	}

	public function doLogin($req, $res){
		$allPostPutVars = $req->getParsedBody();
		
		$auth_type = $this->containerSlim->get('systemOptions')->getValue('email_or_login') or 'email';

		$errors = false;
		if( !$allPostPutVars['password'] ){
			$errors = true;
			$this->flash->addMessage('errors', 'The password attribute is required. ');
		}
		if( !$allPostPutVars[$auth_type] ){
			$this->flash->addMessage('errors', 'The login attribute is required. ');
			$errors = true;
		}

		$user = Users::where($auth_type, $allPostPutVars[$auth_type])->get();

		if( !isset($user[0]) ){
			$this->flash->addMessage('errors', 'User no find in db.');
			$errors = true;
		} elseif( !$user[0]->active ) {
			$this->flash->addMessage('errors', 'User is no active. Please contact administrator system.');
			$errors = true;
		} elseif( !$user[0]->verifyPassword($allPostPutVars['password']) ){
			$this->flash->addMessage('errors', 'User no find in system.');
			$errors = true;
		}

		if( $errors )
			return $res->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));

		Session::put('auth', true);
		Session::put('user', $user[0]->toArray());

		return $res->withStatus(301)->withHeader('Location', '/admin/dashboard');
	}
}
