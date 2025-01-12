<?php
namespace Controllers;

require_once __DIR__ . '/BaseController.php';

class AccueilController extends BaseController
{
    public function index()
    {
        logInfo("Début de la méthode index dans AccueilController");

        if (!$this->session->isAuthenticated()) {
            logInfo("Utilisateur non connecté, affichage de l'accueil");
            $datas = ['hideNav' => true]; // Masquer le menu de navigation
            generate("Views/main/accueil.html.php", $datas, "Views/base.html.php", "Calepin 2.5", true);
        } else {
            logInfo("Utilisateur connecté, redirection vers mediabox");
            $this->redirect('mediabox/index');
        }
    }
}
?>
