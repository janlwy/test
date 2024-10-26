<?php

class PhotoController extends BaseController
{
    public function index()
    {
        $this->checkAuth();
        $datas = [];
        generate("Views/main/photo.php", $datas, "Views/base.html.php", "Photo");
    }
}
