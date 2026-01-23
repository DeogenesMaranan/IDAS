<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->view('home', ['title' => 'ID System']);
    }
}
