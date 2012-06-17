<?php
require_once("Tag.php");

class GenericObjectSlugDAO{

    private $conn;
    private $arrayIdentificacaoObjetoRetornado = array();
    private $nomeTabelaAssoc;
    private $nomeColunaObjeto;
    private $nomeColunaSlug;
    private $nomeTabelaObjeto;
    private $condicaoConsultaObjeto;

    public function __construct($conn){

       if( is_resource($conn) )
            $this -> conn = $conn;
        else
            trigger_error("O parâmetro(\$conn (último parametro da lista)) passado para o construtor de GenericObjectSlugDAO não é uma conexão mysql válida.", E_USER_ERROR);

    }

    public function setNomeTabelaAssoc( $nomeTabelaAssoc ){

        $this -> nomeTabelaAssoc = addslashes($nomeTabelaAssoc);
        return $this;

    }

    public function setNomeColunaObjeto($nomeColunaObjeto){

        $this -> nomeColunaObjeto = addslashes($nomeColunaObjeto);
        return $this;

    }

    public function setNomeColunaSlug($nomeColunaSlug){

        $this -> nomeColunaSlug = addslashes($nomeColunaSlug);
        return $this;

    }

    public function setNomeTabelaObjeto($nomeTabelaObjeto){

       $this -> nomeTabelaObjeto = addslashes($nomeTabelaObjeto);
       return $this;

    }

    public function setNomeChavePrimariaTabelaObjeto($nomeChavePrimariaTabelaObjeto){

        $this -> nomeChavePrimariaTabelaObjeto = addslashes($nomeChavePrimariaTabelaObjeto);
        return $this;
    }

    public function setCondicaoConsultaObjeto($condicaoConsultaObjeto){

       $this -> condicaoConsultaObjeto = $condicaoConsultaObjeto;
       return $this;

    }

    public function __toString(){

        return "Classe genérica para manipular o banco de dados.<br /> Nome da tabela associativa entre os slugs e o object: [". $this -> nomeTabelaAssoc."]<br /> Nome da coluna de identificacao do objeto na tabela associativa: [".$this -> nomeColunaObjeto."]<br /> Nome da coluna de indentificacao do slug na tabela associativa: [".$this -> nomeColunaSlug."] <br /> Nome da tabela onde persistem os objetos: [".$this -> nomeTabelaObjeto."]<br /> Nome da coluna na tabela do objeto que é referente a identificacao dele( coluna do tipo index unique): [".$this -> nomeChavePrimariaTabelaObjeto."]<br />";

    }

    public function getTodosObjectsRelacionadosComSlug( $slugNome ){

        $this -> verificaSePossuiTodasAsInformacoesNecessarias(); // Caso necessite de alguma informação irá dar um erro do php, juntamente com uma mensagem explicativa.

        $slugNome = addslashes($slugNome);

        $sqlIdSlug = "SELECT slugs.id FROM slugs WHERE slugs.nome='$slugNome' ";

        $selecionaObjRel = mysql_query($sqlIdSlug, $this->getConnection()) or trigger_error("Não foi possível selecionar o chave primaria do slug. ", E_USER_WARNING);
        $slugPrimaryKey = (int) mysql_result( $selecionaObjRel,0);

        $sqlIdsPosts = "SELECT {$this->nomeColunaObjeto} FROM {$this->nomeTabelaAssoc} WHERE {$this->nomeColunaSlug} = $slugPrimaryKey";
        $selecionaPostsPeloIdSlug = mysql_query($sqlIdsPosts, $this->getConnection()) or trigger_error("Não foi possível selecionar os registro relacionas com o objeto na tabela associativa. ", E_USER_WARNING);

        $arrayDeIdObjRel = array();

        for( ;$itensDaRelacao = mysql_fetch_assoc($selecionaPostsPeloIdSlug); ){

            $arrayDeIdObjRel[] = $itensDaRelacao[$this->nomeColunaObjeto];

        }

        $stringInSQL = ( count($arrayDeIdObjRel) > 0 ? implode(", ", $arrayDeIdObjRel): 0 );

        $condicaoConsultaObjetos = ( !empty($this -> condicaoConsultaObjeto) ? $this -> condicaoConsultaObjeto." AND ".$this -> nomeChavePrimariaTabelaObjeto." IN($stringInSQL)" : $this -> nomeChavePrimariaTabelaObjeto." IN($stringInSQL)");

        $stringObjectSQL = "SELECT * FROM {$this -> nomeTabelaObjeto} WHERE {$condicaoConsultaObjetos} ";
        $selecionaObjects = mysql_query($stringObjectSQL, $this->getConnection()) or trigger_error("Não foi possível selecionar os registro ligados a esse slug na tabela do objeto. ", E_USER_WARNING);

        $selecionaQntdColunasTableObj = mysql_num_fields($selecionaObjects);
        $arrayDeObjects = array();

        $arrayDeNomesColunas = array();

        for( $i = 0; $i<$selecionaQntdColunasTableObj; $i++ ){

            $arrayDeNomesColunas[] = mysql_field_name($selecionaObjects, $i);

        }

        for( ;$objectsResult = mysql_fetch_assoc($selecionaObjects); ){

          $novoStdObj = new stdClass();

          foreach( $arrayDeNomesColunas as $key => $nomesColunas ){

            $novoStdObj -> $nomesColunas =  $objectsResult[$nomesColunas];

          }

          $arrayDeObjects[] = $novoStdObj;

          // inseri todos os objetos que serão retornados para um um atributo da classe. Para depois poder ser invocado as tags desses objetos.
          $this->setElementoArrayIdentificacaoObjeto( $objectsResult[$this -> nomeChavePrimariaTabelaObjeto] );

        }

        return $arrayDeObjects;

    }

    public function getTagsDoArrayDeObjects(){

        $this -> verificaSePossuiTodasAsInformacoesNecessarias(); // Caso necessite de alguma informação irá dar um erro do php, juntamente com uma mensagem explicativa.

        if(count($this -> getArrayIdentificacaoObjetoRetornado()) > 0){

          $arrayComTodosOsObj = array_unique( $this -> getArrayIdentificacaoObjetoRetornado() );

          $clausulaIn = implode(', ', $arrayComTodosOsObj );

          $sql = "SELECT tags.*, slugs.nome as nome_slug  FROM tags, slugs, {$this->nomeTabelaAssoc} WHERE tags.slug=slugs.id AND {$this->nomeTabelaAssoc}.{$this->nomeColunaSlug} = slugs.id AND {$this->nomeTabelaAssoc}.{$this -> nomeColunaObjeto} IN('$clausulaIn') AND tags.status='1'";

          $selecionaTags = mysql_query($sql, $this->getConnection()) or trigger_error("Não foi possível selecionar as tags relacionadas com esse objeto. ", E_USER_WARNING);

          $arrayDeObjTags = array();

          for( ; $tagsSelecionadas = mysql_fetch_assoc($selecionaTags) ; ){

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

          return $arrayDeObjTags;

        }

        return array();

    }

    public function resetArrayDeObjects(){

        $this -> arrayIdentificacaoObjetoRetornado = array();

    }

    private function setElementoArrayIdentificacaoObjeto( $identificacaoObjeto ){

        $this -> arrayIdentificacaoObjetoRetornado[] = $identificacaoObjeto;

    }

    private function getArrayIdentificacaoObjetoRetornado(){

        return  $this -> arrayIdentificacaoObjetoRetornado;

    }

    private function getConnection(){

        return $this -> conn;

    }

    private function verificaSePossuiTodasAsInformacoesNecessarias(){

        $errorMsg = array();
        $errorExists = FALSE;

        if( empty($this -> nomeTabelaAssoc) ){

            $errorMsg[] = " Você deve setar um valor com o nome da tabela associativa entre objetos e slugs. Através do método setNomeTabelaAssoc( string nome da tabela ) \n";
            $errorExists = TRUE;

        }

        if( empty($this -> nomeColunaObjeto) ){

            $errorMsg[] = " Você deve setar um valor com o nome da coluna na tabela associativa que identifica o objeto. Através do método setNomeColunaObjeto( string nome da coluna do objeto na tabela associativa ) \n";
            $errorExists = TRUE;

        }

        if( empty($this -> nomeColunaSlug) ){

            $errorMsg[] = " Você deve setar um valor com o nome da coluna na tabela associativa que identifica o slug. Através do método setNomeColunaSlug( string nome da coluna do slug na tabela associativa ) \n";
            $errorExists = TRUE;

        }

        if( empty($this -> nomeTabelaObjeto) ){

            $errorMsg[] = " Você deve setar um valor com o nome da tabela do objeto que deseja que seja retornado pelo método getTodosObjectsRelacionadosComSlug(). Através do método setNomeTabelaObjeto( string nome da tabela de objetos ) \n";
            $errorExists = TRUE;

        }

        if( empty($this -> nomeChavePrimariaTabelaObjeto) ){

            $errorMsg[] = " Você deve setar um valor com o nome da chave primária(pois precisa ser unico e estar relacionado com o que foi setado no método setNomeColunaObjeto(), porquê é o que faz a relação) . Através do método setNomeChavePrimariaTabelaObjeto( string nome da chave primária na tabela de objetos ) \n";
            $errorExists = TRUE;

        }

        if($errorExists){

            trigger_error( implode("<br />", $errorMsg), E_USER_ERROR);

        }

    }

}