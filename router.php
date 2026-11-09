<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('~^/(storage|config)(/|$)~', $path) || $path === '/README.md' || $path === '/router.php' || str_ends_with($path, '.sqlite') || str_ends_with($path, '.db')) {
    http_response_code(404);
    exit('Não encontrado');
}
return false;
