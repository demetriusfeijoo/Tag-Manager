<?php

define("SERVIDOR_BANCO", "localhost");
define("USUARIO_BANCO", "root");
define("SENHA_BANCO", "");
define("DATABASE_NOME", "api_tag");

if(!function_exists("getMysqlConnection")) {

  function getMysqlConnection(){

    $regBd = mysql_connect(SERVIDOR_BANCO, USUARIO_BANCO, SENHA_BANCO) or trigger_error("Não foi possível conexão com o banco de dados. Confira todos os campos.", E_USER_ERROR);
    mysql_select_db(DATABASE_NOME, $regBd);

    return $regBd;
  }

}

?>
