<?php

class dotEnv
{
    private array $envContents = array();

    function __construct()
    {
        $this->parseEnv();
    }

    private function parseEnv(): void
    {
		//Itération dans le fichier .env
        foreach (file(".env") as $line) {
            //prise de la clef
            $key = explode("=",$line)[0];
            //prise de la valeur
            $value = explode("=",$line)[1] ?? "";

            //Mise dans les variables d'environement du serveur
            $_ENV[$key] = trim($value);

            //Mise dans un tableau les variables d'environement
            $this->insertEnv($key, $value);
        }
    }

    /**
     * Donne le contenu 'cru' du fichier .env
     */ 
    protected function &getEnvContents(): array
	{
        return $this->envContents;
    }

    /**
     * Set le contenu 'cru' du fichier .env
     */ 
    protected function setEnvContents($envContents): static
	{
        $this->envContents = $envContents;

        return $this;
    }

    protected function insertEnv(string $key, mixed $value): void
    {
        $this->getEnvContents()[$key] = $value;
    }
}
