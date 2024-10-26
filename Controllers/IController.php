<?php

interface IController {
    public function index();
    public function list();
    public function create();
    public function update($id);
    public function delete($id);
}
