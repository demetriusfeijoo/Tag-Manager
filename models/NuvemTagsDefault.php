<?php
require_once("Nuvavel.php");
require_once("funcoes_ordenacao_tags.php");

/**
 * Classe NuvemTagsDefault respons�vel por montar uma nuvem de tags padr�o.
 * Com essa classe � poss�vel dar as configura��es b�sicas de como a nuvem de tags ser�.
 * Roda em qualquer sistema php. S� depende de um array de tags v�lidas(inst�ncias de Tagavel).
 * 
 * @package		tag_manager
 * @author		Demetrius F. Campos
 * @copyright	Copyright (c) 2012.
 * @since		Version 1.0
 */
final class NuvemTagsDefault implements Nuvavel{

    private $tagAberturaNuvem = "<div>";
    private $tagFechamentoNuvem = "</div>";
    private $tagAberturaDaTag = "<span>";
    private $tagFechamentoDaTag = "</span>";
    private $arrayDeTags = array();
    private $limiteTagsNuvem = 30;
    private $linkTag = "";
	private $tamanhoMinFonte = 10;

    public function __construct(){

    }

    public function __toString(){

      $totalDeTags =  ( !$this -> getTotalTagsNaNuvem() ? " vazio" : $this -> getTotalTagsNaNuvem() );
      $nuvemEnvolvidaPelasTags = $this->getTagAberturaNuvem().$this->getTagFechamentoNuvem();
      $tagDaNuvemEnvolvidaPelasTags = $this->getTagAberturaDaTag().$this->getTagFechamentoDaTag();

      $nuvemEnvolvidaPelasTags = ( empty($nuvemEnvolvidaPelasTags) ? " NULL " : htmlentities($nuvemEnvolvidaPelasTags)  );
      $tagDaNuvemEnvolvidaPelasTags = ( empty($tagDaNuvemEnvolvidaPelasTags) ? " NULL " : htmlentities($tagDaNuvemEnvolvidaPelasTags) );

      return sprintf("count tags: %s \n Nuvem envolvida pelas tags: %s DEFAULT(".htmlentities("<div> </div>").") \n Cada tag individualmente ser� envolvida pelas tags: %s DEFAULT(".htmlentities("<span> </span>").") \n Tamanho m�nimo da fonte � de: %d px DEFAULT( 10px ) \n", $totalDeTags, $nuvemEnvolvidaPelasTags, $tagDaNuvemEnvolvidaPelasTags, $this -> tamanhoMinFonte );

    }

    //*** Interface da classe
    
    /**
    * setLinkTag() � respons�vel por determinar qual link que a tag clicada dever� apontar.
    * por padr�o o link da tag utilizada ficar� em branco. E ao invocar esse m�todo ser� necess�rio passar um link.
    * 
    * @author Demetrius F. Campos
    * @version 1.0
    * @param String $linkTag
    * @access public
    */
    public function setLinkTag( $linkTag ){

        if( !empty($linkTag) && is_string($linkTag) ){

           $this -> linkTag = $linkTag;

        }else{

            throw new Exception("O link deve ser uma String e n�o pode ser vazio. exemplo: 'http://localhost.com.br/tags'. O valor inserido � do tipo: ".gettype($linkTag));

        }
        
    }

    /**
    * setTamanhoMinFonte() � respons�vel por determinar um valor m�nimo para a fonte de uma tag gerada na nuvem, ou seja, o menor valor para uma tag � o valor setado por esse m�todo.
    * O valor padr�o da fonte do objeto � de 10px.
    *
    * @author Demetrius F. Campos
    * @version 1.0
    * @param int $tamanhoMinFonte
    * @access public
    */
    public function setTamanhoMinFonte( $tamanhoMinFonte ){

        if( !empty($tamanhoMinFonte) && is_integer($tamanhoMinFonte) ){

           $this -> tamanhoMinFonte = $tamanhoMinFonte;

        }else{

            throw new Exception("N�o � poss�vel inserir um valor de um tipo diferente de inteiro ou um argumento vazio. O valor inserido � do tipo: ".gettype($tamanhoMinFonte));

        }
        
    }
    
    /**
    * setArrayDeTags() seta ao atributo da classe NuvemTagsDefault quais tags estar�o envolvidas na cria��o da nuvem.
    * Essas tags passar�o por um processo de sele��o das melhores tags do array passado para o m�todo.
    *
    * @author Demetrius F. Campos
    * @version 1.0
    * @param Array Tagavel $arrayDeTags
    * @access public
    */
    public function setArrayDeTags( $arrayDeTags ){

          if( is_array($arrayDeTags) ){

            $controlType = true; //finge que � um array v�lido( s� com tipo Tagavel )

            foreach( $arrayDeTags as $tag ){

                if( !$tag instanceof Tagavel ){

                    $controlType = false;
                    break;

                }

            }

            if( $controlType ){

               $this -> arrayDeTags = $arrayDeTags;
               return TRUE;

            }else{

               throw new Exception("O array n�o pode conter um elemento com o tipo diferente de Tagavel.");

            }

          }else{

            throw new Exception("S� � permitido um array do tipo Tagavel ou vazio.");

          }

          return FALSE;
    }

    /**
    * setLimiteTagsNuvem() recebe um inteiro que indica qual ser� o tamanho( em n�mero de tags ) m�ximo da nuvem de tags.
    * Layout geralmente � restrito. Com esse limite controlamos melhor o tamanho ocupado pela nuvem na p�gina.
    * O limite setado por padr�o � de no m�ximo 30 tags.
    *
    * @author Demetrius F. Campos
    * @version 1.0
    * @param int $limiteTagsNuvem
    * @access public
    */
    public function setLimiteTagsNuvem($limiteTagsNuvem){

          if( is_integer($limiteTagsNuvem) ){

            $this -> limiteTagsNuvem = (int) $limiteTagsNuvem;

          }else{

            throw new Exception("S� � permitido um valor inteiro. O tipo inserido foi(".gettype($limiteTagsNuvem).")");

          }
          
    }

    /**
    * setTagEnvolveNuvem() � respons�vel por dizer quais tags html envolver�o a nuvem de tags.
    * Caso n�o queira setar nenhuma tag ser� utilizada a tag html <div> </div>
    * Exemplo de uso: $objNuvemTagsDefault -> setTagEnvolveNuvem("<fieldset>", "</fieldset>");
    * 
    * @author Demetrius F. Campos
    * @version 1.0
    * @param String $tagAbertura
    * @param String $tagFechamento
    * @access public
    */   
    public function setTagEnvolveNuvem( $tagAbertura, $tagFechamento ){

    		$areValidsTags = $this -> areValidsTags($tagAbertura, $tagFechamento);

		    if( empty($tagAbertura) XOR empty($tagFechamento) ){

		    	throw new Exception(" N�o � poss�vel inserir somente uma tag html. A tag usada ser� a default(".htmlentities("<span></span>").")");

	    	}else if( !$areValidsTags ){

		    	throw new Exception(" Tag html inv�lida (".htmlentities("$tagAbertura $tagFechamento").")");

	    	}else{

		    	$this -> tagAberturaNuvem = (String) $tagAbertura;
		    	$this -> tagFechamentoNuvem = (String) $tagFechamento;

	    	}

    }

    /**
    * setTagEnvolveTag() � respons�vel por dizer quais tags html envolver�o as tags na nuvem.
    * Caso n�o queira setar nenhuma tag ser� utilizada a tag html <span> </span>
    * Exemplo de uso: $objNuvemTagsDefault -> setTagEnvolveTag("<strong>", "</strong>");
    * 
    * @author Demetrius F. Campos
    * @version 1.0
    * @param String $tagAberturaDaTag
    * @param String $tagFechamentoDaTag
    * @access public
    */   
    public function setTagEnvolveTag( $tagAberturaDaTag, $tagFechamentoDaTag ){

    		$areValidsTags = $this -> areValidsTags($tagAberturaDaTag, $tagFechamentoDaTag);

		    if( empty($tagAberturaDaTag) XOR empty($tagFechamentoDaTag) ){

		    	throw new Exception(" N�o � poss�vel inserir somente uma tag html. A tag usada ser� a default(".htmlentities("<span></span>").")");

	    	}else if(!$areValidsTags){

		    	throw new Exception(" Tag html inv�lida (".htmlentities("$tagAberturaDaTag $tagFechamentoDaTag").")");

	    	}else{

		    	$this -> tagAberturaDaTag = (String) $tagAberturaDaTag;
		    	$this -> tagFechamentoDaTag = (String) $tagFechamentoDaTag;

	    	}
    	
    }

    /**
    * createNuvem() � respons�vel por criar a nuvem de tags baseada nos estados da inst�ncia.
    * Esse m�todo retorna a estrutura html da nuvem e das tags nela contida( as tags contidas tamb�m com o html ).
    * 
    * @author Demetrius F. Campos
    * @version 1.0
    * @return String
    * @access public
    */   
    public function create(){

       $javascriptApi = $this -> getJavascriptAPI();

       $linkDaTag = $this -> getLinkTag();
       $tagAberturaNuvem = $this -> getTagAberturaNuvem();
       $tagFechamentoNuvem = $this -> getTagFechamentoNuvem();
       $tagAberturaDaTag = $this -> getTagAberturaDaTag();
       $tagFechamentoDaTag = $this -> getTagFechamentoDaTag();
       $totalDeTagsNaNuvem = $this -> getLimiteTagsNuvem();

       $tagsMaisRelevantes = $this -> getMelhoresTags();
       $pontuacaoTotalNuvem = $this -> getTotalNuvem();

       //** Come�a a ser gerado o html.
       $htmlGerado = $javascriptApi.$tagAberturaNuvem;

       //***Percorre todas as tags mais relevantes.
       foreach( $tagsMaisRelevantes as $indice => $tag ){

            $idValorTag = $tag -> getIdentificacaoTag();

	       	$htmlGerado .= "<a href='{$linkDaTag}{$idValorTag}' title='{$idValorTag}' style='font-size:{$this -> getTamanhoFonteDaTag($tag, $pontuacaoTotalNuvem)}px;' onclick='setCookieIdTag({$tag -> getIdTag()})'>";

		          $htmlGerado.= $tagAberturaDaTag;
		       			$htmlGerado .= $tag -> getNomeTag();
		          $htmlGerado.= $tagFechamentoDaTag;

	         $htmlGerado .= " </a>";

       }

       $htmlGerado .= $tagFechamentoNuvem;

       return $htmlGerado;

    }

    private function getLinkTag(){
      return $this -> linkTag;
    }
    
    private function getTamanhoMinFonte(){
    	return $this -> tamanhoMinFonte;
    }

    private function getTotalTagsNaNuvem(){
      return count( $this -> getArrayDeTags() );
    }

    private function getArrayDeTags(){
      return $this -> arrayDeTags;
    }

    private function getLimiteTagsNuvem(){
      return $this -> limiteTagsNuvem;
    }

    private function getTagAberturaNuvem(){
        return $this -> tagAberturaNuvem;
    }

    private function getTagFechamentoNuvem(){
        return $this -> tagFechamentoNuvem;
    }

    private function getTagAberturaDaTag(){
        return $this -> tagAberturaDaTag;
    }

    private function getTagFechamentoDaTag(){
        return $this -> tagFechamentoDaTag;
    }

    private function getTotalNuvem(){

        $pontuacaoTotalNuvem = 0;
        $tagsSelecionadas = $this -> getArrayDeTags();

        foreach( $tagsSelecionadas as $indice => $tag ){

          $pontuacaoTotalNuvem += $tag -> getPontuacaoTag();

        }

        return $pontuacaoTotalNuvem;

    }

    private function getTamanhoFonteDaTag( $tag, $pontuacaoNuvem ){

        $tamanhoFonteDaTag = $this -> getTamanhoMinFonte();

        $dividendo =  $tag -> getPontuacaoTag() * 100;

        if( $dividendo && $pontuacaoNuvem ){  //para n�o dar erro de divis�o por zero

          $porcTagNaNuvem = $dividendo / $pontuacaoNuvem;
          $tamanhoFonteDaTag += $porcTagNaNuvem;

        }

        return $tamanhoFonteDaTag;

    }

    private function getMelhoresTags(){

      $arrayDeTags = $this -> getArrayDeTags();

      $arrayDeTags = array_slice( $arrayDeTags, 0, $this -> getLimiteTagsNuvem(), FALSE );

      return $this -> getTagsSort($arrayDeTags);

    }

    private function getTagsSort( $arrayTags ){

      usort( $arrayTags, "ordenarTagsOrdemAlfabetica" );

      return $arrayTags;

    }
    
    private function areValidsTags($tagAbertura, $tagFechamento){
    
    		return (bool) preg_match("/^<(.+)>$/", $tagAbertura) && preg_match("/^<\/(.+)>$/", $tagFechamento);
    		
    }

    private function getJavascriptAPI(){

        $javascript = <<<JAVASCRIPT

              <script type="text/javascript" language="javascript">

                  function setCookieIdTag(value,exdays)
                  {

                    var exdate=new Date();
                    exdate.setDate(exdate.getDate() + exdays);
                    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString() );
                    document.cookie="idTag="+c_value;

                  }

              </script>
JAVASCRIPT;

        return $javascript;
    }

}