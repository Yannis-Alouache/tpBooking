<?php

class TestController {
  public function __construct(int $id=null) {
    echo "Bonjour, je suis le constructeur de la classe TestController :) <br/>";

    if(isset($id)) {
      echo "Un paramètre est présent: \$id avec comme valeur $id";
    } else {
      echo "Pas de paramètre détecté dans le constructeur.";
    }

    echo "<br/>";
  }

  public function index() {
    echo "Bonjour, je suis la function index du controlleur TestController :) <br/>";
  }

  public function hello(int $id=null) {
    echo "Bonsoir, la fonction 'hello' a été appelée du controlleur TestController !. <br/>";
    if(isset($id)) {
      echo "Un paramètre est présent: \$id avec comme valeur $id";
    } else {
      echo "Pas de paramètre détecté dans la fonction hello.";
    }
    echo "<br/>";
  }
}

?>