<?php

/**
 * oblige les controlleurs à implémenter la fonction getInnerRoutes, sinon le routeur ne marche pas.
 * @Annotation Créer une erreur fatale quand on donne l'implémentation à la class Controller.
 */
interface RoutesInterface
{
    /**
     * vas retourner les sous-routes du controlleur. Contenu de la fonction à copier-coller:
     * ```php
     * <?php
     * public function getInnerRoutes(): array
     * {
     *      return controlleur::ROUTES
     * }
     * ?>
     * ```
     * @return array
     */
    public function getInnerRoutes(): array;
}

?>