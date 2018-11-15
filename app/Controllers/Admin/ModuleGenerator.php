<?php

namespace App\Controllers\Admin;

use App\Helpers\FileWorker;
use App\Helpers\RequestParams;
use Illuminate\Support\Str;

class ModuleGenerator extends BaseController
{
    public function index($req, $res)
    {
        $this->controllerName = $req->getAttribute('route')->getName();
        $this->resourse = false;
        $this->initRoute($req, $res);
        $this->data['h1'] = 'Module Generator';

        $this->view->render($res, 'admin\moduleGenerator.twig', $this->data);
    }

    public function doAdd($req, $res)
    {
        $params = new RequestParams($req);

        $name = $params->post('name');
        $sys_name = $params->post('system_name');
        $desc = $params->post('description');
        $version = $params->post('version');
        $author = $params->post('author');

        $sys_name = preg_replace("/[^A-Za-z0-9_]/", "", $sys_name);

        if (!$sys_name) {
            $this->flash->addMessage('errors', 'Module system_name is empty!');
            return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('developers.module.generator'));
        }

        $sys_name = Str::studly($sys_name);

        if (is_dir(MODULE_PATH . $sys_name)) {
            $this->flash->addMessage('errors', 'Module exist!');
            return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('developers.module.generator'));
        }

        $path = MODULE_PATH . $sys_name;

        if (!FileWorker::copy(MODULE_PATH . 'ModuleGenerator'.DIRECTORY_SEPARATOR.'Source'.DIRECTORY_SEPARATOR.'.default', $path)) {
            $this->flash->addMessage('errors', 'Module dir \"' . MODULE_PATH . '\" - is write protect. Check permissions!');
            return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('developers.module.generator'));
        }

        $ret = FileWorker::replaseInFile(
            $path . '/info.json',
            ["%name%", "%description%", "%system_name%", "%version%", "%author%"],
            [$name, $desc, $sys_name, $version, $author]
        );

        if ($ret === false) {
            $this->flash->addMessage('errors', 'Replace file content for \"' . $path . '/info.json' . '\" - is write protect. Check permissions!');
            return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('developers.module.generator'));
        }

        $ret = FileWorker::replaseInFile(
            $path . '/Module.php',
            ["%system_name%"],
            [$sys_name]
        );

        if ($ret === false) {
            $this->flash->addMessage('errors', 'Replace file content for \"' . $path . '/Module.php' . '\" - is write protect. Check permissions!');
            return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('developers.module.generator'));
        }

        $this->flash->addMessage('success', 'Module create!');
        return $res->withStatus(301)->withHeader('Location', $this->router->pathFor('developers.module.generator'));
    }
}