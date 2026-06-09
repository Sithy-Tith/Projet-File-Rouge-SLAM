<?php

namespace App\Service;

use Dompdf\Dompdf;

class PdfGeneratorService
{
    public function generate(string $html): string
    {
        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }
}
