using System;
using System.Collections.Generic;

class Program
{
    static void Main(string[] args)
    {
        List<Vehicule> vehicules = new List<Vehicule>();

        while (true)
        {
            Console.WriteLine("Gestion des véhicules");
            Console.WriteLine("1. Ajouter une voiture");
            Console.WriteLine("2. Ajouter un camion");
            Console.WriteLine("3. Afficher tous les véhicules");
            Console.WriteLine("4. Quitter");
            Console.Write("Choisissez une option: ");
            string choix = Console.ReadLine();

            switch (choix)
            {
                case "1":
                    Console.Write("Immatriculation: ");
                    string immatriculationVoiture = Console.ReadLine();
                    Console.Write("Marque: ");
                    string marqueVoiture = Console.ReadLine();
                    Console.Write("Modèle: ");
                    string modeleVoiture = Console.ReadLine();
                    vehicules.Add(new Voiture(immatriculationVoiture, marqueVoiture, modeleVoiture));
                    break;
                case "2":
                    Console.Write("Immatriculation: ");
                    string immatriculationCamion = Console.ReadLine();
                    Console.Write("Marque: ");
                    string marqueCamion = Console.ReadLine();
                    Console.Write("Modèle: ");
                    string modeleCamion = Console.ReadLine();
                    vehicules.Add(new Camion(immatriculationCamion, marqueCamion, modeleCamion));
                    break;
                case "3":
                    foreach (var vehicule in vehicules)
                    {
                        Console.WriteLine($"Véhicule: {vehicule.Marque} {vehicule.Modele} ({vehicule.Immatriculation})");
                        vehicule.Demarrer();
                        if (vehicule is ITransportPassagers passagers)
                        {
                            passagers.TransporterPassagers();
                        }
                        if (vehicule is ITransportMarchandises marchandises)
                        {
                            marchandises.TransporterMarchandises();
                        }
                        vehicule.Arreter();
                        Console.WriteLine();
                    }
                    break;
                case "4":
                    return;
                default:
                    Console.WriteLine("Option invalide. Veuillez réessayer.");
                    break;
            }
        }
    }
}
