using System;

namespace ExoPOO
{
    public abstract class Vehicule
    {
        public string Immatriculation { get; private set; }
        public string Marque { get; private set; }
        public string Modele { get; private set; }

        public Vehicule(string immatriculation, string marque, string modele)
        {
            Immatriculation = immatriculation;
            Marque = marque;
            Modele = modele;
        }

        public abstract void Demarrer();
        public abstract void Arreter();
    }
}
