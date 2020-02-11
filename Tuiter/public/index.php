<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) return false;
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$mongoconn = new \MongoDB\Client("mongodb://localhost");
$userService = new \Tuiter\Services\UserService($mongoconn->tuiter->users);
$postService = new \Tuiter\Services\PostService($mongoconn->tuiter->posts);
$likeService = new \Tuiter\Services\LikeService($mongoconn->tuiter->likes);
$followService = new \Tuiter\Services\FollowService($mongoconn->tuiter->follows, $userService);
$loginService = new \Tuiter\Services\LoginService($userService);


$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, array $args) use ($twig) {
    
    $template = $twig->load('home.html');
    $response->getBody()->write(
        $template->render()
    );
    return $response;
});
$app->post('/logIn', function (Request $request, Response $response, array $args) use($loginService) {
    // $_SESSION['logueado'] = false; no hace falta porque ya lo hace el LoginService.
    // $usuario = $_SESSION['nombre'];
    $usuario = $loginService->login($_POST['nombre'],$_POST['contraseña']);
    if($usuario instanceof \Tuiter\Models\UserNull){
        $response = $response->withStatus(302);
        $response = $response->withHeader('Location','/');
    }else{
        $response = $response->withStatus(302);
        $response = $response->withHeader('Location','/feed');
    }
    return $response;
});
$app->post('/register', function (Request $request, Response $response, array $args) use($userService) {
    
    $usuario = $userService->register($_POST['userId'],$_POST['nombre'],$_POST['contraseña']);
    if($usuario){
        $response = $response->withStatus(302);
        $response = $response->withHeader('Location','/');
    }else{
        $response = $response->withStatus(302);
        $response = $response->withHeader('Location','/');
    }

    return $response;
    

});
$app->get('/feed', function (Request $request, Response $response, array $args) use($userService,$followService,$postService) {

    // hay que llamar a todos los post de los usuarios que sigo. 
    // $usuario = $_SESSION['user'];
    // $us = $followService->getFollowers($usuario);
    // $posts = $postService->getAllPosts($usuario);
    // $posteos = array();
    // foreach($us as $posts){
    //     $posteos[] = 
    // }






});
$app->get('/follow{userName}', function (Request $request, Response $response, array $args) use($twig) {
     
    $template = $twig->load('follow.username.html');
    $response->getBody()->write(
        $template->render()
    );
    return $response;


});
$app->run();