<?php
require_once('Tag.php');

class TagDAO{

    private $conn;

	public function __construct( $injectionConn ){

        if( is_resource($injectionConn) )
            $this -> conn = $injectionConn;
        else
            trigger_error("O parâmetro passado para TagDAO não é uma conexão mysql válida.", 256);

	}

    private function getConnection(){
      return $this -> conn;
    }

	public function getTodasAsTagsBanco(){

		$todasAsTagsSel = mysql_query("SELECT tags.*, slugs.nome as nome_slug FROM tags, slugs WHERE tags.slug=slugs.id ORDER BY id DESC", $this -> getConnection()) or ( trigger_error("Não é possível retornar todas as tags por causa de um erro no sql.", 512));

        return $this -> montaArrayDeTags($todasAsTagsSel);

    }

	public function getTagsPublicadas( $larguraConsulta = NULL, $offset = NULL ){

        $limite = $this -> criaLimiteConsulta($larguraConsulta , $offset);

		$tagsPublicadasSel = mysql_query("SELECT tags.*, slugs.nome as nome_slug FROM tags, slugs WHERE tags.slug=slugs.id AND tags.status='1' ORDER BY id DESC $limite ", $this -> getConnection()) or ( trigger_error("Não é possível retornar todas as tags por causa de um erro no sql.", 512));


        return $this -> montaArrayDeTags($tagsPublicadasSel);

	}
	
	public function getMelhoresTagsPublicadas( $larguraConsulta = NULL, $offset = NULL ){

        $limite = $this -> criaLimiteConsulta($larguraConsulta , $offset);

		$melhoresTagsPublicadasSel = mysql_query("SELECT tags.*, slugs.nome as nome_slug FROM tags, slugs WHERE tags.slug=slugs.id AND tags.status='1' ORDER BY cliques DESC $limite ", $this -> getConnection()) or ( trigger_error("Não é possível retornar todas as tags por causa de um erro no sql.", 512));

        return $this -> montaArrayDeTags($melhoresTagsPublicadasSel);

	}

    public function getTagSlugIgual( $idSlug ){

        $idSlug =(int) $idSlug ;

        $tagProcuradaSel = mysql_query( "SELECT tags.*, slugs.nome as nome_slug FROM tags, slugs WHERE tags.slug=slugs.id AND slugs.id='$idSlug' AND tags.status='1' ORDER BY id DESC ", $this -> getConnection() ) or ( trigger_error("Não é possível retornar a tag por causa de um erro no sql.", 512));

        return  $this -> montaArrayDeTags($tagProcuradaSel);
    }

    public function getTagIdIgual( $idTag ){

        $idTag = (int) $idTag;

        $tagDeIdSel = mysql_query( "SELECT tags.*, slugs.nome as nome_slug FROM tags, slugs WHERE tags.slug=slugs.id AND tags.id='$idTag' ", $this -> getConnection() ) or trigger_error('Não é possível retornar a tag por causa de um erro no sql.', 512);

        $tag =  $this -> montaArrayDeTags($tagDeIdSel);

        return ( count($tag) == 1 ? $tag[0] : "" );

    }

    public function incrementaCliquesTagPeloCookie(){
                                 
        if( isset($_COOKIE['idTag']) ){

            $tag = $this -> getTagIdIgual((int) $_COOKIE['idTag']);

            if( $tag instanceof Tag ){

                $tag -> setCliquesDaTag( $tag -> getCliquesDaTag() + 1 );

                setcookie('idTag', '', time() - 3600 );    //deleta cookie

                return $this -> updateTag( $tag );

            }

            setcookie('idTag', '', time() - 3600 );    //deleta cookie

        }

        return FALSE;

    }

    public function updateTag( Tag $tag ){

        $sql = sprintf("UPDATE tags SET slug='%d', nome='%s', data_criacao='%s', status='%b', cliques='%d' WHERE id='%d' ", $tag->getIdSlug(), $tag->getNomeTag(), $tag->getDataCriacao(), $tag->getStatus(), $tag->getCliquesDaTag(), $tag->getIdTag() );

        $controleUpdate = mysql_query($sql, $this -> getConnection()) or trigger_error('Não é possível retornar a tag por causa de um erro no sql.', 512);

        return $controleUpdate;

    }

    public function insertTag( Tag $tag ){

        $sql = sprintf("INSERT INTO tags SET slug='%d', nome='%s', data_criacao='%s', status='%b', cliques='%d' ", $tag->getIdSlug(), $tag->getNomeTag(), $tag->getDataCriacao(), $tag->getStatus(), $tag->getCliquesDaTag() );

        $controleInsert = mysql_query( $sql, $this -> getConnection() ) or trigger_error('Não é possível retornar a tag por causa de um erro no sql.', 512);

        return $controleInsert;

    }

    public function montaArrayDeTags( $resourceConsultaTags ){

      $arrayDeObjTags = array();

      if( is_resource($resourceConsultaTags) ){

        for( ; $tagsSelecionadas = mysql_fetch_assoc($resourceConsultaTags) ; ){

      	  $tag = new Tag();
          $tag -> setId((int) $tagsSelecionadas['id']);
          $tag -> setNomeTag($tagsSelecionadas["nome"]);
          $tag -> setNomeSlug($tagsSelecionadas["nome_slug"]);
          $tag -> setIdSlug($tagsSelecionadas["slug"]);
          $tag -> setDataCriacao($tagsSelecionadas["data_criacao"]);
          $tag -> setStatus($tagsSelecionadas["status"]);
          $tag -> setCliquesDaTag((int) $tagsSelecionadas["cliques"]);

          $arrayDeObjTags[] = $tag;

        }

      }

      return $arrayDeObjTags;

    }

    private function criaLimiteConsulta($larguraConsulta , $offset){

      $larguraConsulta = (int) $larguraConsulta;
      $offset = (int) $offset;

      $limiteString = "";

      if( !empty($offset) && !empty($larguraConsulta)){

        $limiteString .= "LIMIT $offset, $larguraConsulta";

      }else if( empty($offset) && !empty($larguraConsulta)){

        $limiteString .= "LIMIT $larguraConsulta";

      }

      return $limiteString;
    }
	
}
?>