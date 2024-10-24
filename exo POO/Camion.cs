using System;

public class Camion : Vehicule, ITransportMarchandises
{
    public Camion(string immatriculation, string marque, string modele)
        : base(immatriculation, marque, modele)
    {
    }

    public override void Demarrer()
    {
        Console.WriteLine($"Le camion {Marque} {Modele} démarre.");
    }

    public override void Arreter()
    {
        Console.WriteLine($"Le camion {Marque} {Modele} s'arrête.");
    }

    public void TransporterMarchandises()
    {
        Console.WriteLine($"Le camion {Marque} {Modele} transporte des marchandises.");
    }
}
