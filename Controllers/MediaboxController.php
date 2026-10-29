<?php

namespace Controllers;

use Models\TexteRepository;
use Models\AudioRepository;
use Models\PhotoRepository;
use Models\VideoRepository;

class MediaboxController extends BaseController implements IController
{
    public function index()
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $query = trim($_GET['q'] ?? '');
        $results = null;

        if ($query !== '') {
            $texteRepo = new TexteRepository();
            $audioRepo = new AudioRepository();
            $photoRepo = new PhotoRepository();
            $videoRepo = new VideoRepository();

            $textes = array_filter($texteRepo->findAllByUser($userId), function ($t) use ($query) {
                return mb_stripos($t->getTitle() ?? '', $query) !== false ||
                    mb_stripos($t->getContent() ?? '', $query) !== false;
            });

            $audios = array_filter($audioRepo->findAllByUser($userId), function ($a) use ($query) {
                return mb_stripos($a->getTitle() ?? '', $query) !== false ||
                    mb_stripos($a->getArtist() ?? '', $query) !== false;
            });

            $photos = array_filter($photoRepo->findAllByUser($userId), function ($p) use ($query) {
                return mb_stripos($p->getTitle() ?? '', $query) !== false ||
                    mb_stripos($p->getDescription() ?? '', $query) !== false ||
                    mb_stripos($p->getOcrText() ?? '', $query) !== false;
            });

            $videos = array_filter($videoRepo->findAllByUser($userId), function ($v) use ($query) {
                return mb_stripos($v->getTitle() ?? '', $query) !== false ||
                    mb_stripos($v->getDescription() ?? '', $query) !== false;
            });

            $results = [
                'textes' => $textes,
                'audios' => $audios,
                'photos' => $photos,
                'videos' => $videos,
                'total'  => count($textes) + count($audios) + count($photos) + count($videos)
            ];
        }

        $datas = [
            'searchQuery' => $query,
            'searchResults' => $results,
            'session' => $this->session
        ];
        generate("Views/main/mediabox.php", $datas, "Views/base.html.php", "Calepin 2.0");
    }

    public function list()
    {
        $this->redirect('mediabox/index');
    }

    public function create()
    {
        $this->redirect('mediabox/index');
    }

    public function update($id = null)
    {
        $this->redirect('mediabox/index');
    }

    public function delete($id = null)
    {
        $this->redirect('mediabox/index');
    }
}
