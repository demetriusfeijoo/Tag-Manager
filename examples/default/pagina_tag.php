<?php
require_once('../../config/settings.inc.php');
require_once("../../models/TagDAO.php");
require_once("../../models/NuvemTagsDefault.php");
require_once("../../models/GenericObjectSlugDAO.php");

$connDBS = getMysqlConnection();

$dao = new TagDAO($connDBS);

$tagClicadaBool = $dao -> incrementaCliquesTagPeloCookie();

$search = addslashes( $_GET["search"] ); // servirá para consultar qualquer coisa relacionada..um post, um produto...

//Cria instancia de GenericObjectSlugDAO(CLASSE RESPONSÁVEL POR FAZER CONSULTA NO BANCO DE DADOS DE FORMA GENÉRICA). Para criar a instancia precisa de um resource connection mysql.
$objGeneric =  new GenericObjectSlugDAO($connDBS);

//Seta as configurações para o objeto referenciado por $objGeneric, consiga fazer todas as consultas no banco. o método setCondicaoConsultaObjeto(), não é obrigatório.
$objGeneric -> setNomeTabelaAssoc("posts_slugs") -> setNomeColunaObjeto("id_post") -> setNomeColunaSlug("id_slug") -> setNomeTabelaObjeto("posts") -> setNomeChavePrimariaTabelaObjeto("id") -> setCondicaoConsultaObjeto("status='1'");

// Array com todos as referencias de objetos que nesse caso são posts.
$todosOsObjetos = $objGeneric->getTodosObjectsRelacionadosComSlug($search);

$arrayDeTagsPreparado = $objGeneric -> getTagsDoArrayDeObjects(); // todas as tags ja preparadas.

$nuvemComAsTagsDosObjetos = "";

try{

    $nuvemDeTagsDefault = new NuvemTagsDefault();
    $nuvemDeTagsDefault -> setTagEnvolveNuvem("<div class='todaNuvem'>", "</div>");
    $nuvemDeTagsDefault -> setTagEnvolveTag("<span class='tag'>", "</span>");
    $nuvemDeTagsDefault -> setArrayDeTags( $arrayDeTagsPreparado );

    $nuvemComAsTagsDosObjetos =  $nuvemDeTagsDefault -> create();

}catch( Exception $e ){

    printf('A nuvem não pode ser montada pelo seguinte erro: %s <br /> na linha: %d <br />do arquivo: %s', $e -> getMessage(), $e -> getLine(), $e -> getFile() );

}

?>

<html>
  <head>
      <title>Visualização itens relacionados com a tag</title>
      <style>
        <!--
          .todaNuvem{background:#666699;padding:10px;}
          a{text-decoration:none;}
          span.tag{color:white;padding:0px 20px;border:2px solid white;}
          span.tag:hover{background:#00CC00;cursor:pointer;}
        -->
      </style>
  </head>

  <body>

    <?php echo $nuvemComAsTagsDosObjetos; ?>

    <div> <br /><br />
        Informações do funcionamento deste arquivo!     <br /><br />

        - Este arquivo serve para aumentar a pontuação da tag que foi clicada, e não do slug dela.  <br />
        - Este arquivo serve para selecionar algo com o slug do link clicado. <br />
            ex: selecionar os posts que estejam relacionados com o slug x. <br /><br />
    </div>
  </body>
</html>
