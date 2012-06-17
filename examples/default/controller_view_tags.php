<?php
    require_once('../../config/settings.inc.php');
    require_once("../../models/NuvemTagsDefault.php");
    require_once("../../models/TagDAO.php");

    $nuvemMontada = "";

    $dao = new TagDAO( getMysqlConnection() );

    try{

      $nuvemDeTagsDefault = new NuvemTagsDefault();
      $nuvemDeTagsDefault -> setTagEnvolveNuvem("<div class='todaNuvem'>", "</div>");
      $nuvemDeTagsDefault -> setTagEnvolveTag("<span class='tag'>", "</span>");
      $nuvemDeTagsDefault -> setLinkTag("http://localhost/Tag-Manager/examples/default/pagina_tag.php?search=");
      $nuvemDeTagsDefault -> setLimiteTagsNuvem(10); //Padrão já é 30.
      $nuvemDeTagsDefault -> setTamanhoMinFonte(10);
      $nuvemDeTagsDefault -> setArrayDeTags( $dao -> getMelhoresTagsPublicadas(30) );

      $nuvemMontada =  $nuvemDeTagsDefault -> create();

    }catch( Exception $e ){

        printf('A nuvem não pode ser montada pelo seguinte erro: %s <br /> na linha: %d <br />do arquivo: %s', $e -> getMessage(), $e -> getLine(), $e -> getFile() );

    }

?>

<html>
  <head>
      <title>Api de tags</title>
      <style>
        <!--
          .todaNuvem{background:#666699;padding:10px;height:auto;width:200px;float:left;}
          a{text-decoration:none;}
          span.tag{color:white;padding:0px 20px;border:2px solid white;}
          span.tag:hover{background:#00CC00;cursor:pointer;}
        -->
      </style>
  </head>

  <body>
       <?php echo $nuvemMontada; ?>
  </body>
</html>
