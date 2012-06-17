<?php
require_once("Nuvavel.php");
require_once("funcoes_ordenacao_tags.php");

/**
 * Classe NuvemTagsDefault responsável por montar uma nuvem de tags padrão.
 * Com essa classe é possível dar as configurações básicas de como a nuvem de tags será.
 * Roda em qualquer sistema php. Só depende de um array de tags válidas(instâncias de Tagavel).
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

      return sprintf("count tags: %s \n Nuvem envolvida pelas tags: %s DEFAULT(".htmlentities("<div> </div>").") \n Cada tag individualmente será envolvida pelas tags: %s DEFAULT(".htmlentities("<span> </span>").") \n Tamanho mínimo da fonte é de: %d px DEFAULT( 10px ) \n", $totalDeTags, $nuvemEnvolvidaPelasTags, $tagDaNuvemEnvolvidaPelasTags, $this -> tamanhoMinFonte );

    }

    //*** Interface da classe
    
    /**
    * setLinkTag() é responsável por determinar qual link que a tag clicada deverá apontar.
    * por padrão o link da tag utilizada ficará em branco. E ao invocar esse método será necessário passar um link.
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

            throw new Exception("O link deve ser uma String e não pode ser vazio. exemplo: 'http://localhost.com.br/tags'. O valor inserido é do tipo: ".gettype($linkTag));

        }
        
    }

    /**
    * setTamanhoMinFonte() é responsável por determinar um valor mínimo para a fonte de uma tag gerada na nuvem, ou seja, o menor valor para uma tag é o valor setado por esse método.
    * O valor padrão da fonte do objeto é de 10px.
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

            throw new Exception("Não é possível inserir um valor de um tipo diferente de inteiro ou um argumento vazio. O valor inserido é do tipo: ".gettype($tamanhoMinFonte));

        }
        
    }
    
    /**
    * setArrayDeTags() seta ao atributo da classe NuvemTagsDefault quais tags estarão envolvidas na criação da nuvem.
    * Essas tags passarão por um processo de seleção das melhores tags do array passado para o método.
    *
    * @author Demetrius F. Campos
    * @version 1.0
    * @param Array Tagavel $arrayDeTags
    * @access public
    */
    public function setArrayDeTags( $arrayDeTags ){

          if( is_array($arrayDeTags) ){

            $controlType = true; //finge que é um array válido( só com tipo Tagavel )

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

               throw new Exception("O array não pode conter um elemento com o tipo diferente de Tagavel.");

            }

          }else{

            throw new Exception("Só é permitido um array do tipo Tagavel ou vazio.");

          }

          return FALSE;
    }

    /**
    * setLimiteTagsNuvem() recebe um inteiro que indica qual será o tamanho( em número de tags ) máximo da nuvem de tags.
    * Layout geralmente é restrito. Com esse limite controlamos melhor o tamanho ocupado pela nuvem na página.
    * O limite setado por padrão é de no máximo 30 tags.
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

            throw new Exception("Só é permitido um valor inteiro. O tipo inserido foi(".gettype($limiteTagsNuvem).")");

          }
          
    }

    /**
    * setTagEnvolveNuvem() é responsável por dizer quais tags html envolverão a nuvem de tags.
    * Caso não queira setar nenhuma tag será utilizada a tag html <div> </div>
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

		    	throw new Exception(" Não é possível inserir somente uma tag html. A tag usada será a default(".htmlentities("<span></span>").")");

	    	}else if( !$areValidsTags ){

		    	throw new Exception(" Tag html inválida (".htmlentities("$tagAbertura $tagFechamento").")");

	    	}else{

		    	$this -> tagAberturaNuvem = (String) $tagAbertura;
		    	$this -> tagFechamentoNuvem = (String) $tagFechamento;

	    	}

    }

    /**
    * setTagEnvolveTag() é responsável por dizer quais tags html envolverão as tags na nuvem.
    * Caso não queira setar nenhuma tag será utilizada a tag html <span> </span>
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

		    	throw new Exception(" Não é possível inserir somente uma tag html. A tag usada será a default(".htmlentities("<span></span>").")");

	    	}else if(!$areValidsTags){

		    	throw new Exception(" Tag html inválida (".htmlentities("$tagAberturaDaTag $tagFechamentoDaTag").")");

	    	}else{

		    	$this -> tagAberturaDaTag = (String) $tagAberturaDaTag;
		    	$this -> tagFechamentoDaTag = (String) $tagFechamentoDaTag;

	    	}
    	
    }

    /**
    * createNuvem() é responsável por criar a nuvem de tags baseada nos estados da instância.
    * Esse método retorna a estrutura html da nuvem e das tags nela contida( as tags contidas também com o html ).
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

       //** Começa a ser gerado o html.
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

        if( $dividendo && $pontuacaoNuvem ){  //para não dar erro de divisão por zero

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