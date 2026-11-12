<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
readfile(dirname(__DIR__) . '/config/produtos.json');
