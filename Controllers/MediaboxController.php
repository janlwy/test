<?php

class MediaboxController extends BaseController
{
    public function index()
    {
        $this->checkAuth();
        $datas = [];
        generate("Views/main/mediabox.php", $datas, "Views/base.html.php");
    }
}
