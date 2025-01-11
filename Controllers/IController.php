<?php

if (!interface_exists('IBaseController')) {
    interface IBaseController {
        // Define any necessary methods here
    }
}

interface IController extends IBaseController {
    public function list();
    public function create();
    public function update($id);
    public function delete($id);
}
