<?php

use MediaMindAI\Core\Routing\Router;
use MediaMindAI\Http\Controllers\HomeController;

/** @var Router $router */

// Home page
$router->get('/', [HomeController::class, 'index']);

// About page
$router->get('/about', [HomeController::class, 'about']);

// Contact form submission
$router->post('/contact', [HomeController::class, 'contact']);

// API Documentation
$router->get('/api/docs', [HomeController::class, 'apiDocs']);

// 404 Not Found - This should be the last route
$router->get('{any:.*}', [HomeController::class, 'notFound']);

// Add more routes as needed for your application
// Example:
// $router->get('/features', [FeatureController::class, 'index']);
// $router->get('/pricing', [PricingController::class, 'index']);
// $router->get('/blog', [BlogController::class, 'index']);

// Example of resourceful routes for a blog post:
// $router->get('/posts', [PostController::class, 'index']);
// $router->get('/posts/create', [PostController::class, 'create']);
// $router->post('/posts', [PostController::class, 'store']);
// $router->get('/posts/{id}', [PostController::class, 'show']);
// $router->get('/posts/{id}/edit', [PostController::class, 'edit']);
// $router->put('/posts/{id}', [PostController::class, 'update']);
// $router->delete('/posts/{id}', [PostController::class, 'destroy']);

// Example of API routes (you might want to put these in a separate api.php file):
// $router->get('/api/posts', [Api\PostController::class, 'index']);
// $router->post('/api/posts', [Api\PostController::class, 'store']);
// $router->get('/api/posts/{id}', [Api\PostController::class, 'show']);
// $router->put('/api/posts/{id}', [Api\PostController::class, 'update']);
// $router->delete('/api/posts/{id}', [Api\PostController::class, 'destroy']);
