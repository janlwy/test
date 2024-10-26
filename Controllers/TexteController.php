<?php

class TexteController extends BaseController
{
    public function index()
    {
        $this->checkAuth();
        $datas = [];
        generate("Views/main/texte.php", $datas, "Views/base.html.php", "Texte");
    }
}
