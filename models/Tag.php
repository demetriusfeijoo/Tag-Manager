<?php
require_once("Tagavel.php");

/**
 * Classe Tag implementa a interface Tagavel.php.
 * Foi feita para rodar em qualquer sistema.
 *
 * @package		tag_manager
 * @author		Demetrius F. Campos
 * @copyright	Copyright (c) 2011 - 2012.
 * @license		http://www.acessoroot.com.br/manual/tag_manager
 * @link		http://www.acessoroot.com.br
 * @since		Version 1.0
 * @filesource  http://www.acessoroot.com.br/manual/tag_manager/Tag
 */
class Tag implements Tagavel{

    private $id;
    private $nome;
    private $slugNome;
    private $slugId;
    private $dataCriacao;
    private $status;
    private $cliquesDaTag;

    public function __toString(){
    	return sprintf("Tag: %s - %s", $this -> getNomeTag(), $this -> getDataCriacao());
    }
    
    public function __construct(){}

    /**
    * Override
    * Retorna o $this -> getNomeSlug()
    * @author Demetrius F. Campos
    * @version 1.0
    * @return mixed
    * @access public
    */
    public function getIdentificacaoTag(){
        return $this -> getNomeSlug();
    }

    /**
    * Retorna o id da tag caso exista. Caso não, retorna 0
    * @author Demetrius F. Campos
    * @version 1.0
    * @return int
    * @access public
    */
    public function getIdTag(){
      return (int) $this -> id;
    }

    /**
    * Atribui um id a instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @param int id
    * @access public
    */
    public function setId( $id ){

	      if( is_integer($id)){
	      	$this -> id = (int) $id;
	      }else{
	        throw new Exception("O método só aceita tipo inteiro. Foi inserido um valor do tipo: ".gettype($id).". Para funcionar corretamente coloque um valor do tipo inteiro.");
	      }  
	       
    }

    /**
    * Override
    * Retorna um nome a instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @return string
    * @access public
    */
    public function getNomeTag(){
      return (String) $this -> nome;
    }

    /**
    * Atribui um nome a instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @param string nome
    * @access public
    */
    public function setNomeTag( $nome ){
      $this -> nome = (string) $nome;
    }

    /**
    * Retorna o nome slug da instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @return string
    * @access public
    */
    public function getNomeSlug(){
      return (string) $this -> slugNome;
    }

    /**
    * Atribui o nome do slug a instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @param string slugNome
    * @access public
    */
    public function setNomeSlug( $slugNome ){
      $this -> slugNome = (string) $slugNome;
    }

    /**
    * Retorna um id do slug da instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @return int
    * @access public
    */
    public function getIdSlug(){
      return (int) $this -> slugId;
    }

    /**
    * Atribui um id do slug da instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @param int slugId
    * @access public
    */
    public function setIdSlug( $slugId ){
      $this -> slugId = (int) $slugId;
    }

    /**
    * Retorna uma data a instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @return string
    * @access public
    */
    public function getDataCriacao(){
      return (string) $this -> dataCriacao;
    }

    /**
    * Atribui uma data a instância em uso
    * @author Demetrius F. Campos
    * @version 1.0
    * @param string dataCriacao
    * @access public
    */
    public function setDataCriacao( $dataCriacao ){
        $this -> dataCriacao = (string) $dataCriacao;
    }

    /**
    * Retorna o status da a instância em uso. false = inativo, true = ativo.
    * @author Demetrius F. Campos
    * @version 1.0
    * @return bool
    * @access public
    */
    public function getStatus(){
      return (bool) $this -> status;
    }

    /**
    * Atribui um status a instância em uso. false = inativo, true = ativo.
    * @author Demetrius F. Campos
    * @version 1.0
    * @param bool status
    * @access public
    */
    public function setStatus( $status ){
        $this -> status = (bool) $status;
    }

    /**
    * Retorna o status da a instância em uso. false = inativo, true = ativo.
    * @author Demetrius F. Campos
    * @version 1.0
    * @return int
    * @access public
    */
    public function getCliquesDaTag(){
        return (int) $this -> cliquesDaTag;
    }

    /**
    * Atribui um total de clique da tag.
    * @author Demetrius F. Campos
    * @version 1.0
    * @param int quantidadeCliques
    * @access public
    */
    public function setCliquesDaTag( $quantidadeCliques ){
    
	      if( is_integer($quantidadeCliques) ){
	      	 $this -> cliquesDaTag = (int) $quantidadeCliques;
	      }else{
	        throw new Exception("Só é permitido um valor inteiro. Foi inserido um valor do tipo: ".gettype($quantidadeCliques));
	      }  
	 
    }

    /**
    * Override.
    * Retorna getCliquesDaTag()
    * 
    * @author Demetrius F. Campos
    * @version 1.0
    * return int
    * @access public
    */
    public function getPontuacaoTag(){
      return $this -> getCliquesDaTag();
    }
}