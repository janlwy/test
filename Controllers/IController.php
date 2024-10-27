<?php

interface IController extends IBaseController {
    public function list();
    public function create();
    public function update($id);
    public function delete($id);
}
