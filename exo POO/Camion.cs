using System;

namespace ExoPOO
{
    public class Camion : Vehicule, ITransportMarchandises
    {
        public Camion(string immatriculation, string marque, string modele)
            : base(immatriculation, marque, modele)
        {
        }

        public override void Demarrer()
        {
            Console.WriteLine("Le camion démarre.");
        }

        public override void Arreter()
        {
            Console.WriteLine("Le camion s'arrête.");
        }

        public void TransporterMarchandises()
        {
            Console.WriteLine("Le camion transporte des marchandises.");
        }
    }
}
