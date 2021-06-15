# Criando mapa utilizando Leaflet Maps (opensource)
> Funções em php para quem quer criar mapas sem manipular o javascript.

<p align="center">
<img src="https://img.shields.io/badge/VERSÃO-1.0.1-green">
<img src="https://img.shields.io/badge/Licença-GNU 3.0-success">
<img src="https://img.shields.io/badge/PHP-Adianti-blue">
<img src="https://img.shields.io/badge/PHP->7.2-blueviolet">
</p>

<img src="https://github.com/andre-gasparin/leafletadianti/blob/main/src/readme.png">

Link Leaflet.js:
https://leafletjs.com/

## Instalação

Para instalar no Adianti Builder, vá na aba de "composer packages" e adicione:
```html
andregasparin/leafletadianti
```

É necessário que você tenha o composer instalado.

Abra seu cmd (prompt), com o comando "cd c:/pasta/do/projeto" navegue até a raiz do seu projeto em adianti.

Execute o seguinte comando (podem variar no caso de usar linux ou mac, ex utilizar sudo no início):

```html
composer require andregasparin/leafletadianti
```

## Utilização

Adicione a linha no início de onde você irá utilizar:
use AndreGasparin\Leafletadianti\LeafletMap;

Depois utilize a classe e insira o mapa em qualquer element que desejar, exemplo:


```html
<?php
use AndreGasparin\Leafletadianti\LeafletMap;
 
class LeafletPage extends TPage
{
    function __construct()
    {
        parent::__construct();
        
        $map = new LeafletMap('51.505','-0.09','13', 'google'); // set initial coordinates
        $show_map = $map->show();
        
        $content = new TElement('div');
        $content->id = 'my-map';
        $content->add( $show_map );
 
        parent::add( $content );
    }
}
```

Exemplo de uso de todas as funções:

```html
<?php
use AndreGasparin\Leafletadianti\LeafletMap;
 
class LeafletPage extends TPage
{
    function __construct()
    {

        $points = array();
        $points[] = ['lat' => 50.505, 'lng'=> -0.09, 'description'];  
        $points[] = ['lat' => 49.505, 'lng'=> -0.09, 'description49'];  

        $points_json = json_encode($points);

        parent::__construct();
        
        $map = new LeafletMap('51.505','-0.09','13', 'google'); // set initial coordinates
        $map->setSize('100%', '400'); //set map size  
        //$map->width = '100%';
        //$map->height = '600px';
        //$map->myLocation(true); // use gps to show and center my location, use true to display poupup with precision
        $map->addMarker('51.505', '-0.09', 'teste'); // add point on map
        $map->addJsonMarker($points_json);
        $map->center(); //center map to view all points  
	$show_map = $map->show();        

        //#Version 1.0.1
            $map->enableAddOnePoint('allPointsJson');
            $map->enableAddPoints('allPointsJson');
            //field
            $allPointsJson = new TEntry('allPointsJson');
            $allPointsJson->setSize("100%");
            parent::add( $allPointsJson );
        //#End Version 1.0.1

        //#Version 1.0.2
            $map->searchAddress('autocompleteLeaflet');
            $map = $map->show(); //create map
            //field
            $autocompleteLeaflet = new TCombo('autocompleteLeaflet');
            $autocompleteLeaflet->setSize("100%");     
            parent::add( $autocompleteLeaflet );    
        //#End Version 1.0.2

        $content = new TElement('div');
        $content->id = 'testediv';
        $content->add( $show_map );
        parent::add( $content );
    }
}
```

## Configuração para Desenvolvimento

Caso queira implementar algo no sistema, utilize os padrões do Adianti Framework, ficaremos felizes com sua participação!

## Precisa de melhoria ou ajuda com algum BUG?

<a href="https://github.com/andre-gasparin/leafletadianti/issues">Issues</a>


## Histórico (ChangeLog)

* 1.0.0
    * Projeto criado
* 1.0.1
    * adicionado enableAddOnePoint(RETORNO)
		Você pode adicionar um mapa que aceite apenas 1 marcador, o usuário tem a opção de clicar novamente no mapa e alterar o local.
		"Retorno" deve passar o nome do campo TEntry/THidden criado no formulário
	* adicionado enableAddPoints(RETORNO)
		Você pode adicionar um mapa que aceite vários marcadores, o usuário tem a opção de clicar novamente no marcador para exclui-lo.
		"Retorno" deve passar o nome do campo TEntry/THidden criado no formulário
* 1.0.2
    * adicionado searchAddress(RETORNO, addMarker)
		Você pode adicionar uma busca de geolocation, ou seja, pesquisa por endereço.
		"Retorno" deve passar o nome do campo TCombo criado no formulário
		"addMarker" = true, ele irá inserir automaticamente um alfinete no endereço localizado
* 1.0.3
    * pacote do composer criado
* 1.0.4
* 1.0.5
   * Correção de bugs

## Meta

André Gasparin – [@andre-gasparin] – andre@gasparimsat.com.br / andre.gasparin@hotmail.com

Distribuído sob a Licença Pública Geral GNU (GPLv3) 


## Contributing

1. Faça o _fork_ do projeto (<https://https://github.com/andre-gasparin/leafletadianti/fork>)
2. Crie uma _branch_ para sua modificação (`git checkout -b feature/fooBar`)
3. Faça o _commit_ (`git commit -am 'Add some fooBar'`)
4. _Push_ (`git push origin feature/fooBar`)
5. Crie um novo _Pull Request_
