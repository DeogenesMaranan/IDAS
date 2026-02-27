<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/AppointmentController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/middlewares.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$router = new Router();

$router->get('/', function () {
    $controller = new HomeController();
    $controller->index();
});

$router->post('/login', function () {
    $controller = new AuthController();
    $controller->login();
});

$router->post('/register', function () {
    $controller = new AuthController();
    $controller->register();
});

$router->post('/logout', function () {
    $controller = new AuthController();
    $controller->logout();
});


$router->post('/appointments', [
    ['mw_require_auth'],
    function () {
        $controller = new AppointmentController();
        $controller->storeAppointment();
    }
]);


$router->post('/admin/appointments/list', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->listAppointmentsAjax();
    }
]);

$router->post('/admin/appointments/reschedule', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->rescheduleAppointment();
    }
]);

$router->get('/api/student', [
    ['mw_require_auth'],
    function () {
        $controller = new UserController();
        $controller->student();
    }
]);

$router->post('/admin/appointments/view', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->viewAppointment();
    }
]);

$router->post('/admin/appointments/approve', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->approveAppointment();
    }
]);

$router->post('/admin/appointments/cancel', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->cancelAppointment();
    }
]);

$router->get('/admin/appointments/export-excel', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->exportAppointmentsExcel();
    }
]);

$router->post('/admin/appointments/complete', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->completeAppointment();
    }
]);

$router->get('/admin/appointments/daily-counts', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->dailyCounts();
    }
]);

$router->get('/admin/appointments/slot-counts', [
    ['mw_require_admin'],
    function () {
        $controller = new AppointmentController();
        $controller->slotCounts();
    }
]);

$router->get('/appointments/availability', [
    ['mw_require_auth'],
    function () {
        $controller = new AppointmentController();
        $controller->availability();
    }
]);

return $router;
