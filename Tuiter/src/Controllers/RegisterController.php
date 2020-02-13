<?php

namespace Tuiter\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterController implements \Tuiter\Interfaces\Controller {
    
    public function config($app) {
       
        $app->get('/prueba', function (Request $request, Response $response, array $args) {
            $template = $request->getAttribute("twig")->load('prueba.html');

            $response->getBody()->write(
                $template->render(['name' => 'Edus'])
            );
            return $response;


        });
    }
}