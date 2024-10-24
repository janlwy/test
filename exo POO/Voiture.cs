using System;

namespace ExoPOO
{
    public class Voiture : Vehicule, ITransportPassagers
    {
        public Voiture(string immatriculation, string marque, string modele)
            : base(immatriculation, marque, modele)
        {
        }

        public override void Demarrer()
        {
            Console.WriteLine("La voiture démarre.");
        }

        public override void Arreter()
        {
            Console.WriteLine("La voiture s'arrête.");
        }

        public void TransporterPassagers()
        {
            Console.WriteLine("La voiture transporte des passagers.");
        }
    }
}
