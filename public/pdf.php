<?php

require_once '../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML('<h3 style="color: green; text-decoration: underline">PDF para notas de pagamento</h3>');
$mpdf->Output();