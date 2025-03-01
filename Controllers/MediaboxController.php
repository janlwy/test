<?php
namespace Controllers;

class MediaboxController extends BaseController implements IController
{
    public function index()
    {
        $this->checkAuth();
        $datas = [];
        generate("Views/main/mediabox.php", $datas, "Views/base.html.php");
    }

    public function list() {
        $this->checkAuth();
        // Not implemented as mediabox is just a navigation hub
        header('Location: ?url=mediabox/index');
        exit();
    }

    public function create() {
        $this->checkAuth();
        $this->checkCSRF();
        // Not implemented as mediabox is just a navigation hub
        header('Location: ?url=mediabox/index');
        exit();
    }

    public function update($id) {
        $this->checkAuth();
        $this->checkCSRF();
        // Not implemented as mediabox is just a navigation hub
        header('Location: ?url=mediabox/index');
        exit();
    }

    public function delete($id) {
        $this->checkAuth();
        $this->checkCSRF();
        // Not implemented as mediabox is just a navigation hub
        header('Location: ?url=mediabox/index');
        exit();
    }
}
