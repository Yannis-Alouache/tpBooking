<?php

/**
 * Oblige les controllers à implémenter la fonction getInnerRoutes, sinon le routeur ne marche pas.
 * Créer une erreur fatale quand on donne l'implémentation à la classe Controller.
 */
interface RoutesInterface
{
    /**
     * vas retourner les sous-routes du controller. Contenu de la fonction à copier-coller:
     * ```php
     * <?php
     * public function getInnerRoutes(): array
     * {
     *      return controller::ROUTES
     * }
     * ?>
     * ```
     * @return array
     */
    public function getInnerRoutes(): array;
}

?>
