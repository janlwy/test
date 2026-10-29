<?php

namespace Controllers;

use Models\PhotoRepository;
use Models\Photo;
use Models\Texte;
use Models\TexteRepository;
use Exception;
use finfo;

class PhotoController extends BaseController implements IController
{
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new PhotoRepository();
    }

    public function index()
    {
        $this->checkAuth();
        $this->redirect('photo/list');
    }

    public function list()
    {
        $this->checkAuth();
        try {
            $userId = (int)$this->session->get('user_id');
            if (!$userId) {
                throw new Exception("Utilisateur non identifié");
            }

            $photos = $this->repository->findAllByUser($userId);
            $datas = [
                'photos' => $photos,
                'session' => $this->session
            ];

            generate("Views/main/photoList.php", $datas, "Views/base.html.php", "Mes Photos & Notes Scannées");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('mediabox/index');
        }
    }

    public function viewer()
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');

        $singleId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $multipleIds = $_GET['ids'] ?? [];

        $photos = [];
        if ($singleId) {
            $p = $this->repository->findById($singleId);
            if ($p && $p->getUserId() === $userId) {
                $photos[] = $p;
            }
        } elseif (!empty($multipleIds) && is_array($multipleIds)) {
            foreach ($multipleIds as $id) {
                $p = $this->repository->findById((int)$id);
                if ($p && $p->getUserId() === $userId) {
                    $photos[] = $p;
                }
            }
        } else {
            $photos = $this->repository->findAllByUser($userId);
        }

        $datas = [
            'photos' => $photos,
            'session' => $this->session
        ];
        generate("Views/main/photo.php", $datas, "Views/base.html.php", "Visionneuse Photos & OCR");
    }

    public function create()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCSRF();
            try {
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $ocrText = trim($_POST['ocr_text'] ?? '');

                if (empty($title)) {
                    throw new Exception("Le titre de la photo est requis.");
                }

                if (!isset($_FILES['photo_file']) || $_FILES['photo_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Veuillez sélectionner une image valide.");
                }

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['photo_file']['tmp_name']);

                if (!array_key_exists($mimeType, $allowedTypes)) {
                    throw new Exception("Format d'image non supporté ($mimeType). Formats acceptés : JPG, PNG, WEBP.");
                }

                if ($_FILES['photo_file']['size'] > 10 * 1024 * 1024) {
                    throw new Exception("L'image dépasse la taille maximale de 10 Mo.");
                }

                $uploadDir = 'Ressources/images/photos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $ext = $allowedTypes[$mimeType];
                $uniqueName = uniqid('pho_') . '.' . $ext;
                $targetPath = $uploadDir . $uniqueName;

                if (!move_uploaded_file($_FILES['photo_file']['tmp_name'], $targetPath)) {
                    throw new Exception("Erreur lors de l'enregistrement de l'image sur le serveur.");
                }

                $photo = new Photo([
                    'title' => $title,
                    'description' => $description,
                    'ocr_text' => $ocrText,
                    'path' => $uniqueName,
                    'user_id' => (int)$this->session->get('user_id')
                ]);

                $this->repository->save($photo);
                $_SESSION['message'] = "Photo enregistrée avec succès.";
            } catch (Exception $e) {
                logError("Erreur dans PhotoController::create: " . $e->getMessage());
                $_SESSION['erreur'] = $e->getMessage();
            }
        }
        $this->redirect('photo/list');
    }

    public function update($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        try {
            $photo = $this->repository->findById($id);
            if (!$photo || $photo->getUserId() !== $userId) {
                throw new Exception("Photo introuvable ou accès non autorisé.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->checkCSRF();
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $ocrText = trim($_POST['ocr_text'] ?? '');

                if (empty($title)) {
                    throw new Exception("Le titre est requis.");
                }

                $photo->setTitle($title);
                $photo->setDescription($description);
                $photo->setOcrText($ocrText);

                // Si une nouvelle image remplace l'ancienne
                if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK && $_FILES['photo_file']['size'] > 0) {
                    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($_FILES['photo_file']['tmp_name']);

                    if (!array_key_exists($mimeType, $allowedTypes)) {
                        throw new Exception("Format d'image non autorisé.");
                    }

                    $uploadDir = 'Ressources/images/photos/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $uniqueName = uniqid('pho_') . '.' . $allowedTypes[$mimeType];
                    if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $uploadDir . $uniqueName)) {
                        $oldPath = $photo->getFullPath();
                        if (file_exists($oldPath) && is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                        $photo->setPath($uniqueName);
                    }
                }

                $this->repository->save($photo);
                $_SESSION['message'] = "Photo mise à jour avec succès.";
                $this->redirect('photo/list');
            }

            $photos = $this->repository->findAllByUser($userId);
            $datas = [
                'photos' => $photos,
                'editPhoto' => $photo,
                'session' => $this->session
            ];
            generate("Views/main/photoList.php", $datas, "Views/base.html.php", "Modifier la Photo");
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('photo/list');
        }
    }

    public function delete($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        try {
            $photo = $this->repository->findById($id);
            if (!$photo || $photo->getUserId() !== $userId) {
                throw new Exception("Photo introuvable ou suppression non autorisée.");
            }
            $this->repository->delete($id);
            $_SESSION['message'] = "Photo supprimée avec succès.";
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
        }
        $this->redirect('photo/list');
    }

    /**
     * Bonus : Convertit le texte OCR (ou la description) d'une photo en véritable Note Texte
     */
    public function toTexte($id = null)
    {
        $this->checkAuth();
        $userId = (int)$this->session->get('user_id');
        $id = (int)$id;

        try {
            $photo = $this->repository->findById($id);
            if (!$photo || $photo->getUserId() !== $userId) {
                throw new Exception("Photo introuvable.");
            }

            $content = trim(($photo->getOcrText() ?: '') . "\n\n" . ($photo->getDescription() ?: ''));
            if (empty($content)) {
                throw new Exception("Aucun texte OCR ou description à convertir sur cette photo.");
            }

            $texte = new Texte([
                'title' => "Note OCR : " . $photo->getTitle(),
                'content' => $content,
                'user_id' => $userId
            ]);

            $texteRepo = new TexteRepository();
            $texteRepo->save($texte);

            $_SESSION['message'] = "Le texte de la photo a été converti en Note Texte avec succès !";
            $this->redirect('texte/list');
        } catch (Exception $e) {
            $_SESSION['erreur'] = $e->getMessage();
            $this->redirect('photo/list');
        }
    }
}
