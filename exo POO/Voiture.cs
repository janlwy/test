using System;

public class Voiture : Vehicule, ITransportPassagers
{
    public Voiture(string immatriculation, string marque, string modele)
        : base(immatriculation, marque, modele)
    {
    }

    public override void Demarrer()
    {
        Console.WriteLine($"La voiture {Marque} {Modele} démarre.");
    }

    public override void Arreter()
    {
        Console.WriteLine($"La voiture {Marque} {Modele} s'arrête.");
    }

    public void TransporterPassagers()
    {
        Console.WriteLine($"La voiture {Marque} {Modele} transporte des passagers.");
    }
}
