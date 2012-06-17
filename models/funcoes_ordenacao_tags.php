<?php
//função responsável por ordenar o array de tags por quantidade de pontos
function ordenarTagsMaiorPontuacao( $a, $b ){
    return $b -> getPontuacaoTag() - $a -> getPontuacaoTag() ;
}

// função responsável por ordenar o array de tags pelo nome da tag
function ordenarTagsOrdemAlfabetica( $a, $b ){

  $nomeTagA = $a -> getNomeTag();
  $nomeTagB = $b -> getNomeTag();

  if( strcasecmp($nomeTagA, $nomeTagB) < 0)
    return -1;
  else if( strcasecmp($nomeTagA, $nomeTagB) > 0 )
    return 1;

  return 0;

}

