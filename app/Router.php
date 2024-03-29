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

		if($_ENV["APP_ENV"] === 'development')
		{
			$this->checkRoutes();
		}
		else {
			try {
				//Check des routes
				$this->checkRoutes();
			} catch (Exception $e) {
				//Ecran erreur 500 si une erreur se passe.
				self::serverError();
			}
		}
    }

    /**
     * Vas checker les routes mises dans le controlleur général
     * @return void
     */
    private function checkRoutes(): void
    {
        /** @var bool $isRouteFound sert à route fallback pour si une a été trouvée ou non */
        $isRouteFound = false;

        //Itération dans les routes du controlleur général
        foreach (Controller::getRoutes() as $route => $controller) {

			if(str_contains($this->getUrl(),"?"))
			{
				$this->setUrl(explode("?",$this->getUrl())[0]);
			}

            //Séparation de l'URL actuelle
            $explodeUrl = explode("/",$this->getUrl());

			if(empty($explodeUrl[0]))
			{
				array_shift($explodeUrl);
			}

            //Si la route n'a pas encore été trouvée et si la première portion de la route (dans controller.php) correspond à l'URL actuelle
            if(in_array($route,$explodeUrl) && !$isRouteFound)
            {
                //Appel du controller et check des routes
                $this->callController($controller);

                $isRouteFound = true;
            }

        }

        //affichage de la page 404 si pas de route trouvée
        if($isRouteFound === false) self::fallBack();
    }

    /**
     * Vas appeler le controlleur demandé, et checker les sous-routes
     * @param string $controller Le controlleur à appeler
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
        if(isset($explodeUrl[0]) && !isset($explodeUrl[1]))
        {
            $subUrl = $explodeUrl[0];
        } else {
            $subUrl = implode("/", $explodeUrl);
        }

		if(!str_starts_with($subUrl, "/"))
		{
			$subUrl = "/".$subUrl;
		}

        /** @var bool $isSubRouteFound Sert à vois si la sous-route a été trouvée ou non */
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

		if($isSubRouteFound === false) self::fallBack();
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
     * Appelle la méthode du controlleur avec la route matchée
     * @param string $controller Le controlleur avec la méthode à appeller
     * @param string $method La méthode à utiliser
	 * @return void
     */
    private function callControllerMethod($controller, $method): void
    {
        call_user_func(
            array($controller,$method)
        );
    }

	/**
	 * vas checker si lma sous-route du controlleur correspond à la sous-route de l'URL
	 * @param mixed $subRouteMethod
	 * @param mixed $subUrl
	 * @return bool
	 */
    private function checkSubRoute(mixed $subRouteMethod, mixed $subUrl): bool
    {

		//Unification/normalisation de la sous-URL actuelle
		$finalSubUrl = $subUrl;

		//Quand la route est (par ex) "/register/", la sous-URL est un tableau vide. Je le convertis donc en string vide, car se sont les mêmes routes (à un '/' près)
		if((gettype($subUrl) === 'array' && count($subUrl) === 0))
		{
			$finalSubUrl = "";
		}

		//Unification/normalisation de la sous-route du controlleur
		//Séparation de la sous-route et de sa méthode
		$separatedRoute = explode("@",$subRouteMethod);
		//Si la sous-route est isset, alors je la donne à la variable pour la comparer à la route de l'URL. Sinon, je donne ce que l'explode m'a donné, au cas-ou.
		$finalSubRoute = $separatedRoute[0] ?? $separatedRoute;

		if(!str_starts_with($finalSubRoute,"/"))
		{
			$finalSubRoute = "/".$finalSubRoute;
		}


		//Retour si la sous-route de l'URL et la sous-route du controlleur match.
        return $finalSubUrl === $finalSubRoute;
    }

    public static function fallBack(): void
    {
        include './view/errors/404.php';
    }

	public static function serverError(): void
	{
		include './view/errors/500.php';
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
