<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 6/14/16
 * Time: 8:47 PM
 */

namespace Modules\Core\Source\Libs\Middleware;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\RequestParams;
use App\Middleware\ABaseMiddleware;
use App\Source\Factory\ModelsFactory;
use App\Source\Events\BaseContainerEvent;
use App\Helpers\SessionManager as Session;

class OrderTypeMiddleware extends ABaseMiddleware
{

    /**
     * OrderTypeMiddleware constructor.
     * @param $container
     */
    protected $variableName = ['order_by', 'order_type'];
    protected $groupName = 'order.type.';

    protected $req;
    protected $res;

    public function __invoke(Request $request, Response $response, $next)
    {
        $this->req = $request;
        $this->res = $response;

        $allParams = new RequestParams($this->req);
        if (Session::has('auth') &&
            Session::get('auth') &&
            ($allParams->all($this->variableName[0]) ||
                $allParams->all($this->variableName[1]))
        ) {
            $this->groupName = $this->groupName . basename($allParams->getRequest()->getUri()->getPath());

            $varName = $this->variableName[0];
            if ($allParams->all($varName)) {
                $this->setOption($varName, $allParams);
            }
            $varName = $this->variableName[1];
            if ($allParams->all($varName)) {
                $this->setOption($varName, $allParams);
            }
        }

        return $next($request, $response);
    }

    protected function storeParams($value, $varName)
    {
        $u_id = Session::get('user')['id'];
        $model = ModelsFactory::getModel('UserViewsSettings');
        $result = $model->where('user_id', $u_id)->where('group', $this->groupName)->where('code', $varName)->first();

        if (!$result) {
            $result = ModelsFactory::getModel('UserViewsSettings', ['user_id' => $u_id, 'group' => $this->groupName, 'code' => $varName]);
            $result->user_id = $u_id;
        }

        $result->value = $value;
        $result->save();

        return $result;
    }

    public function setOption($varName, RequestParams $allParams)
    {
        $value = $allParams->all($varName);

        $arParams = [
            'value' => $value,
            'codeName' => $this->variableName,
            'groupName' => $this->groupName
        ];

        $event = new BaseContainerEvent($this->c, $arParams);
        $event = $this->c->dispatcher->dispatch('middleware.'.$varName.'.before', $event);

        $value = ($event->getParams()['value']) ? $event->getParams()['value'] : $allParams->all($this->variableName);

        $result = $this->storeParams($value, $varName);

        $arParams = [
            'result' => $result,
            'allParams' => $allParams
        ];

        $event = new BaseContainerEvent($this->c, $arParams);
        $this->c->dispatcher->dispatch('middleware.'.$varName.'.after', $event);
    }
}