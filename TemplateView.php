<?php

class TemplateView{
    private string $content;

    /*

    Construtor que define o padrão de renderização do objeto, seguindo o padrão:

    "Texto generico ${variavel_1_aqui}, seguindo o texto ${variavel_2_aqui=valor_padrao}"

    */
    public function __construct(string $content) {
        $this->content = $content;
    }

    /*

    Renderização do objeto seguindo o padrão definido na hora de sua declaração.

    Variavel $items deve seguir o padrao:

    ['variavel_1_aqui' => 'valor que sera substituido do conteudo template', ...]

    */
    public function render(?array $items) : void {
        if($items === null){
            return;
        }

        $content = $this->content;
        if(preg_match_all("/[\$][\{](.*?)[\}]+/", $content, $matches)){
            foreach($matches[1] as $item){
                $parts = explode('=', $item);

                $key = $parts[0];
                $value = '';
                if(isset($items[$key]) && !empty($items[$key])){
                    $value = $items[$key];
                } else if(isset($parts[1]) && !empty($parts[1])){
                    $value = $parts[1];
                } else {
                    continue;
                }

                $content = preg_replace("/[\$][\{]({$parts[0]}.*?)[\}]/", $value, $content);
            }
        }

        print($content);
    }

    /*

    Renderização de um array contendo os array que possuem os valores a serem renderizados:

    a variavel $objects deve seguir o formato
    [
        ['variavel_1_aqui' => 'valor que sera substituido do conteudo template', ...],
        ['variavel_1_aqui' => 'valor que sera substituido do conteudo template', ...],
        ...
    ]

    */
    public function renderMultiple(array $objects) : void{
        foreach($objects as $item){
            $this->render($item);
        }
    }
}

echo "\nRenderização Unica:";

// Criação do objeto com um padrão de texto simples
$templateDeDiv = new TemplateView('
    <section>
        <h1>${titulo=Titulo indisponivel}</h1>
        <h2>${data}</h2>
        <div>
            <img src="${imagem}" />
        </div>
    </section>
');

/*
Utilização do metodo para renderizar o conteudo, resultado esperado:
    <section>
        <h1>a</h1>
        <h2>23/02/2024</h2>
        <div>
            <img src="Teste de titulo" />
        </div>
    </section>
*/
$templateDeDiv->render([
    'data' => '23/02/2024',
    'imagem' => 'Teste de titulo',
    'titulo' => 'a',
]);

echo "\n\nRenderização Multipla:";

// Declaração dos itens a serem renderizados, em um exemplo real esses valores poderiam ser provenientes de um banco de dados.
$multiplosValores = [
    [
        'titulo' => 'Data de lançamento do Minecraft',
        'data' => 'Dia 18 de Novembro de 2011',
        'imagem' => 'imagem-do-jogo-minecraft.jpg'
    ],
    [
        'titulo' => 'Data para prova de Matematica',
        'data' => 'Dia 29 de maio de 2027',
        'imagem' => 'caminho/para/imagem-generica.png'
    ],
    [
        'data' => '00/00/00',
        'imagem' => ''
    ],
    [
        'titulo' => 'Titulo Generico para Texto',
    ],
    [
        'imagem' => 'nova-imagem.gif'
    ],
];

// Renderização multipla dos valores.
$templateDeDiv->renderMultiple($multiplosValores);