using System;
using System.Collections.Generic;

namespace ExoPOO
{
    class Program
    {
        static void Main(string[] args)
        {
            List<Vehicule> vehicules = new List<Vehicule>
            {
                new Voiture("123-ABC", "Toyota", "Corolla"),
                new Camion("456-DEF", "Volvo", "FH")
            };

            foreach (var vehicule in vehicules)
            {
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
        }
    }
}
