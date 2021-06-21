<?php

namespace core\classes;

use Mpdf\Mpdf;

class PDF{

    // ============================================================
    // CLASSE PARA CONSTRUÇÃO DE PDFS ATRAVÉS DO mPDF
    // ============================================================
    private $pdf;
    private $html;

    // Dimensões do PDF
    private $x; // left
    private $y; // top
    private $largura; // width
    private $altura; // height
    private $alinhamento; // text-align

    // Cores do PDF
    private $cor; // fore-color
    private $fundo; // background-color

    // Tipos de letra do PDF
    private $letra_familia; // font-family
    private $letra_tamanho; // font-size
    private $letra_tipo; // font-weight

    // Contornos para construção do PDF
    private $mostrar_areas;

    // ============================================================
    public function __construct($mostrar_areas = false, $format = 'A4', $orientation = 'P', $mode = 'utf-8'){

        // Cria a instância da classe mPDF
        $this->pdf = new Mpdf([
            'format'      => $format,
            'orientation' => $orientation,
            'mode'        => $mode
        ]);

        // Iniciar o HTML
        $this->cleanHTML();

        // Mostra ou esconde contornos para construção do PDF
        $this->mostrar_areas = $mostrar_areas;
    }

    // ============================================================
    public function setTemplate($template){

        // Define o template de PDFs
        $this->pdf->SetDocTemplate($template);
    }

    // ============================================================
    public function cleanHTML(){

        // Coloca o HTML em branco
        $this->html = '';
    }

    // ============================================================
    public function outputPDF(){

        // Output para o browser ou para ficheiro PDF
        $this->pdf->WriteHTML($this->html);
        $this->pdf->Output();
    }

    // ============================================================
    public function savePDF($nome_ficheiro){

        // Guarda o ficheiro PDF com o nome pretendido
        $this->pdf->WriteHTML($this->html);
        $this->pdf->Output(PDF_PATH . $nome_ficheiro);
    }

    // ============================================================
    public function newPage(){

        // Acrescenta uma nova página ao PDF
        $this->html .= '<pagebreak>';
    }

    // ============================================================
    // MÉTODOS PARA DEFINIR A POSIÇÃO E DIMENSÃO DO TEXTO
    // ============================================================
    public function set_x($x){
        $this->x = $x;
    }

    // ============================================================
    public function set_y($y){
        $this->y = $y;
    }

    // ============================================================
    public function set_largura($largura){
        $this->largura = $largura;
    }

    // ============================================================
    public function set_altura($altura){
        $this->altura = $altura;
    }

    // ============================================================
    public function posicao($x, $y){
        $this->x = $x;
        $this->y = $y;
    }

    // ============================================================
    public function dimensao($largura, $altura){
        $this->largura = $largura;
        $this->altura = $altura;
    }

    // ============================================================
    public function posicao_dimensao($x, $y, $largura, $altura){

        // Define a posição e a dimensão do espaço do texto
        $this->posicao($x, $y);
        $this->dimensao($largura, $altura);
    }

    // ============================================================
    // CORES
    // ============================================================
    public function set_cor($cor){

        // Define a cor do texto
        $this->cor = $cor;
    }

    // ============================================================
    public function set_cor_fundo($cor_fundo){

        // Define a cor de fundo
        $this->fundo = $cor_fundo;
    }

    // ============================================================
    // CARACTERISTICAS DO TEXTO
    // ============================================================
    public function set_alinhamento($alinhamento){

        // Define o alinhamento do texto dentro do espaço
        $this->alinhamento = $alinhamento;
    }

    // ============================================================
    public function set_letra_familia($familia){

        // Define os tipos de letra possiveis na construção do PDF
        $familias_possiveis = [
            'Arial',
            'Courier New',
            'Lucida Sans',
            'Times New Roman',
            'Franklin Gothic Medium'
        ];

        // Verifica se familia de letra pertence ao conjunto de familias permitidas
        if (!in_array($familia, $familias_possiveis)) {
            $this->letra_familia = 'Arial';
        } else {
            $this->letra_familia = $familia;
        }
    }

    // ============================================================
    public function set_letra_tamanho($tamanho){

        $this->letra_tamanho = $tamanho;
    }

    // ============================================================
    public function set_letra_tipo($tipo){

        $this->letra_tipo = $tipo;
    }

    // ============================================================
    public function set_permissoes($permissoes = [], $password = ''){

        // Define permissoes para o documento a ser criado
        $this->pdf->SetProtection($permissoes, $password);
    }

    // ============================================================
    public function writeHTML($texto){

        // Escreve texto no PDF
        $this->html .= '<div style="';

        // Posicionamento e dimensão
        $this->html .= 'position: absolute;';
        $this->html .= 'left: ' . $this->x . 'px;';
        $this->html .= 'top: ' . $this->y . 'px;';
        $this->html .= 'width: ' . $this->largura . 'px;';
        $this->html .= 'height: ' . $this->altura . 'px;';
        $this->html .= 'text-align: ' . $this->alinhamento . ';';
        // Cores
        $this->html .= 'color: ' . $this->cor . ';';
        $this->html .= 'background-color: ' . $this->fundo . ';';
        // Letra
        $this->html .= 'font-family: ' . $this->letra_familia . ';';
        $this->html .= 'font-size: ' . $this->letra_tamanho . ';';
        $this->html .= 'font-weight: ' . $this->letra_tipo . ';';

        // Contornos para construção do PDF
        if ($this->mostrar_areas) {
            $this->html .= 'box-shadow: inset 0px 0px 0px 1px red;';
        }

        $this->html .= '">' . $texto . '</div>';
    }

    // ============================================================
    // GESTÃO DA CLASSE PARA CRIAÇÃO DE PDFS
    // ============================================================
    public function createPDF(){

        // Faz a criação e o output dos PDFs através do mPDF
        $pdf = new PDF();

        // Define um template de fundo
        $pdf->setTemplate(getcwd() . '/assets/templates/template.pdf');

        $pdf->set_letra_familia('Arial');
        $pdf->set_letra_tamanho('1.2em');
        $pdf->set_letra_tipo('bold');

        $pdf->set_cor('blue');
        $pdf->set_cor_fundo('');

        $pdf->set_alinhamento('left');
        $pdf->posicao_dimensao(200, 300, 300, 300);
        $pdf->writeHTML('Frase de corpo de texto 1 inserida no template de PDF');

        $pdf->set_alinhamento('left');
        $pdf->posicao_dimensao(200, 600, 300, 300);
        $pdf->writeHTML('Frase de corpo de texto 2 inserida no template de PDF');

        $pdf->outputPDF();
    }
}
