<?php

class VideoController extends BaseController
{
    public function index()
    {
        $this->checkAuth();
        $datas = [];
        generate("Views/main/video.php", $datas, "Views/base.html.php", "Video");
    }
}
