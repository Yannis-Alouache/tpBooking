<?php

include("./controller/RegisterController.php");
include("./controller/TestController.php");

class Router {
  private string $request;
  private bool $isRouteFound=false;

  private ?int $param=null;

  public function __construct() {
    $this->request=$_SERVER["REQUEST_URI"];
    $this->defineRoutes();
  }

  private function defineRoutes() {
    /** Placez vos routes ici ;) */
    //J'ai pensé à ta route Yannis :)
    $this->get("/register","RegisterController@render");

    //Routes exemples:
    $this->get("/test/{id}/hey","TestController@hello");
    $this->get("/test/{id}/alo","TestController");
    $this->get("/test","TestController@index");

    //Si la route n'a pas été trouvée, alors on affiche un fallback, la page 404 not found
    if($this->isRouteFound===false) Route::fallBack();
  }

  /**
   * /!\ Attention, le routeur ne prend en charge qu'un seul paramètre, veuillez ne pas mettre plusieurs paramètres dans les routes. /!\
   * /!\ Attention, lorsque vous définissez une route, le controlleur demandé sera instancié, et donc son constructeur ainsi que le méthode indiqué sera invoqué /!\
   * 
   * Exemple 1 (URL classique):
   * ```
   * <?php
   * //Si l'URL match '/test' , alors une nouvelle instance du controlleur 'testController' vas être créée, et sa méthode 'test' vas être appelée.
   * //Il faut absolument un '@' dans l'appel du controlleur pour invoquer une méthode sinon le script plante.
   * $this->get('/test','testController@test');
   * ?>
   * ```
   * 
   * Exemple 2 (URL avec paramètre):
   * ```
   * <?php
   * //Ici, la route possède un paramètre: id (déini par les crochets entre le nom du paramètre).
   * //Dans le controller, la méthode 'article' vas être appelée avec un paramètre portant le même nom indiqué dans le premier paramètre: id.
   * $this->get('/article/{id}/','testController@article');
   * //Pour réceptionner et pouvoir utiliser le paramètre dans le controlleur et vue, il faut mettre en premier paramètre le nom du paramètre de la route. Exemple:
   * class testController {
   *    //SVP mettez=null en valeur par défaut pour la sécurité.
   *    public function article(int $article=null, (autres params)): void {
   *      echo $article
   *      //(...)
   *    }
   * }
   * ?>
   * ```
   * 
   * Exemple 3 (Appel d'un controlleur sans méthode)
   * ```
   * <?php
   * //Il est aussi possible de demander un controlleur sans sa méthode, il faut simplement mettre le nom du controlleur à instancier.
   * $this->get('/test','IndexController');
   * //Ici, le controlleur vas être instancié, et donc seulement sont constructeur sera appellé.
   * ?>
   * ```
   * 
   * Si aucune des routes ne match celles dans le routeur, alors le routeur vas afficher la page 404 (méthode statique fallBack() dans Route).
   * Le routeur marche en 'routes de prioritées': le routeur prend la première route qui match l'URL de requête et ignore toutes les autres.
   * 
   * @param string $url L'URL modèle: celle à matcher à l'URL de requête.
   * @param string $controller Le controller et sa méthode à utiliser (séparé par un '@').
   * @return void
   */
  private function get( string $url, string $controller ): void {
    //Si la route n'a toujours pas été trouvée et si la méthode est bien GET
    if($this->isRouteFound===false && $_SERVER['REQUEST_METHOD']=='GET') {
      //Séparation de l'URL de la requête entre /
      $req_dec=explode("/",$this->request);
      //Idem avec l'URL de la route
      $url_dec=explode("/",$url);

      /** @var bool $isRouteMatch Si la route a été matchée ou non avec l'URL de la route */
      $isRouteMatch=true;

      //Si la requête et l'URL ne sont pas de la même taille.
      if(count($url_dec) != count($req_dec)) {

        //et s'il n'y a rien dans le dernier bout de l'URL demandée et l'URL et si la fin de l'URL demandée est différente de l'URL de la route
        if((end($req_dec)!="" && end($url_dec)!="") && (end($req_dec)!=end($url_dec))) {
          $isRouteMatch=false;
        }
      }

      

      //Itération dans l'URL du controlleur
      for ($i=0; $i < count($url_dec); $i++) {

        //Si la partie de l'URL demandé existe
        if(isset($req_dec[$i])) {
          
          //Si la route a un paramètre ou si la partie de route match la partie de l'URL de route
          if(Route::isParam($url_dec[$i]) || ($url_dec[$i] === $req_dec[$i] && !Route::isParam($url_dec[$i]))) {
            
            //Si le paramètre est bien un nombre
            if(is_numeric($req_dec[$i])) {
              //On donne à l'objet courrent le paramètre donné
              $this->param=intval($req_dec[$i]);
            }
            //Si le paramètre n'est pas un nombre, mais que ce dernier correspond à la partie de l'URL
            else if($url_dec[$i] === $req_dec[$i]) {
              //$isRouteMatch=false;
            }
            //Si la route ne match pas et que ce n'est pas un nombre
            else {
              $isRouteMatch=false;
            }

          } else {
            $isRouteMatch=false;
          }
        } else {
          $isRouteMatch=false;
        }
      }

      //Si l'URL demandée et l'URL de reqûete match
      if($isRouteMatch===true) {
        //alors on met en mémoire dans l'objet le fait que la route match.
        $this->isRouteFound=true;
        //et on appelle le controlleur et sa méthode mise.
        Route::callController($controller,$this->param);
      }
    }
    

  }
}

class Route {
  
  /**
   * fallBack vas inclure la page 404 si aucune route n'a été matchée.
   * @return void
   */
  public static function fallBack(): void {
    include './view/404.php';
  }

  /**
   * callController
   * @var string $controller Le nom du controlleur et sa méthode à appeller. Synthaxe: 'nomControlleur@methode' OU 'nomControlleur'
   * @var int|null $param Le paramètre à passer au controlleur. Initialisé en null car il peut ne pas y avoir de paramètre à la route.
   * @return void
   */
  public static function callController(string $controller, int $param=null): void {
    if(str_contains($controller,"@")) {
      $controller_sepa=explode("@",$controller);

      $controller_instance = new $controller_sepa[0]();
  
      if(isset($param)) {
        call_user_func(
          array($controller_instance,$controller_sepa[1]),
          $param,
        );
      } else {
        call_user_func(
          array($controller_instance,$controller_sepa[1])
        );
      }
    } else {
      $controller_instance = new $controller(
        $param,
      );
    }
    
  }

  /**
   * isParam vas voir si la partie de l'URL est un paramètre, et vas retourner true/false si c'est un paramètre.
   * @param string $url_part
   * @return bool
   */
  public static function isParam(string $url_part): bool {
    if(str_starts_with($url_part,"{") && str_ends_with($url_part,"}")) {
      return true;
    } else {
      return false;
    }
  }
}

?>