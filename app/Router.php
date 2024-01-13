<?php

include("./controller/Controller.php");

class Router
{
    private string $url;
    private string $httpMethod;

	function __construct()
    {
        $this->setUrl($_SERVER["REQUEST_URI"]);
        $this->setHttpMethod($_SERVER['REQUEST_METHOD']);
        $this->checkRoutes();
    }

    /**
     * Vas checker les routes mises dans le controlleur général
     * @return void
     */
    private function checkRoutes(): void
    {
        /** @var bool sert à route fallback pour si une a été trouvée ou non */
        $isRouteFound = false;

        //Itération dans les routes du controlleur général
        foreach (Controller::getRoutes() as $route => $controller) {

			if(str_contains($this->getUrl(),"?"))
			{
				$this->setUrl(explode("?",$this->getUrl())[0]);
			}

            //Séparation de l'URL actuelle
            $explodeUrl = explode("/",$this->getUrl());

            //Si la route n'a pas encore été trouvée et si la première portion de la route (dans controller.php) correspond à l'URL actuelle
            if(in_array($route,$explodeUrl) && !$isRouteFound)
            {
                //Appel su controller et check des routes
                $this->callController($controller);

                $isRouteFound = true;
            }
        }

        //affichage de la page 404 si pas de route trouvée
        if(!$isRouteFound) $this->fallBack();
    }

    /**
     * vas appeller le controlleur demandé, et checker les sous-routes
     * @param $controller Le controlleur à appeller
     */
    private function callController($controller): void
    {
        /**  @suppressWarnings @var ControllerInstance l'instance du controlleur invoqué */
        $controllerInstance = new $controller();
        /** @var string[]|string Les/La sous-route(s) à checker contenues dans le controlleur invoqué  */
        $subRoutes = $controllerInstance->getInnerRoutes();

        /** @var string[]|string séparation de l'URL actuelle entre / et prise de la sous-route */
        $explodeUrl = array_slice(explode("/",$this->getUrl()),2);
        /** @var string La sous-route actuelle de l'URL */
        $subUrl = "";

        //Des fois, il n'y a qu'une seule portion de sous-route, et peut créer des erreurs car ce n'est pas un tableau.
        if(isset($explodeUrl[0]))
        {
            $subUrl = $explodeUrl[0];
        } else {
            $subUrl = $explodeUrl;
        }

        /** @var bool Sert à vois si la sous-route a été trouvée ou non */
        $isSubRouteFound = false;

        //Itération dans les sous-routes du controller invoqué
        /** @var string $route_method La méthode du controlleur invoqué à appeller */
        /** @var string $controllerMethod La méthode HTTP précisée dans le controlleur invoqué */
        foreach ($subRoutes as $route_method => $controllerMethod) {

            //Si la sous-route est la même que le sous-URL, que la méthode HTTP est la même précisée dans le controlleur invoqué et que la sous-route n'a pas été trouvée
            if($this->checkSubRoute($route_method, $subUrl) && $this->checkHttpMethod($route_method,$this->getHttpMethod()) && !$isSubRouteFound)
            {
                $isSubRouteFound = true;
				//Appel de la méthode du controlleur demandé
                $this->callControllerMethod($controllerInstance,$controllerMethod);
            }
        }
    }

    /**
     * checke si la méthode HTTP indiquée dans le route d'un controlleur est bien celle quiest utilisée
     * @param string $route La sous-route et sa méthode
     * @param string $method La méthode HTTP à checker
     */
    private function checkHttpMethod(string $route, string $method): bool
    {
        $routeHttp = explode("@",$route)[1];
        return strtolower($method) === strtolower($routeHttp);
    }

    /**
     * appelle la méthode du controlleur avec la route matchée
     * @param $controller Le controlleur avec la méthode à appeller
     * @param $method La méthode à utiliser
	 * @return void
     */
    private function callControllerMethod($controller, $method): void
    {
        call_user_func(
            array($controller,$method)
        );
    }

    /**
     * vas checker si lma sous-route du controlleurcorrespond à la sous-route de l'URL
     * @var mixed $subRouteMethod
     */
    private function checkSubRoute(mixed $subRouteMethod, mixed $subUrl): bool
    {

		//Unification/normalisation de la sous-URL acutelle
		$finalSubUrl = $subUrl;

		//Quand la route est (par ex) "/register/", la sous-URL est un tableau vide. Je le convertis donc en string vide, car se sont les mêmes routes (à un '/' près)
		if(gettype($subUrl) === 'array' && count($subUrl) === 0)
		{
			$finalSubUrl = "";
		}

		//Unification/normalisation de la sous-route du controlleur
		//Séparation de la sous-route et de sa méthode
		$separatedRoute = explode("@",$subRouteMethod);
		//Si la sous-route est isset, alors je la donne à la varible pour la comparer à la route de l'URL. Sinon, je donne ce que l'explode m'a donné, au cas-ou.
		$finalSubRoute = $separatedRoute[0] ?? $separatedRoute;

		//Retour si la sous-route de l'URL et la sous-route du controlleur match.
        return $finalSubUrl === $finalSubRoute;
    }

    public static function fallBack(): void
    {
        include './view/404.php';
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function setHttpMethod(string $method): void
    {
        $this->httpMethod = $method;
    }
}
