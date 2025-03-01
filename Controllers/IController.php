<?php
namespace Controllers;

if (!interface_exists('Controllers\\IBaseController')) {
    interface IBaseController {
        // Define any necessary methods here
        public function index();
    }
}

if (!interface_exists('Controllers\\IController')) {
    interface IController extends IBaseController {
        public function list();
        public function create();
        public function update($id);
        public function delete($id);
    }
}
