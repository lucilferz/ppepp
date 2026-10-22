<?php
/**
 * Custom TCPDF Extension for PPEPP Multi-Chapter Executive Report
 * Universitas Katolik Soegijapranata
 */

if (!class_exists('PPEPP_Report_PDF') && class_exists('TCPDF')) {
    class PPEPP_Report_PDF extends TCPDF
    {
        public string $docCode      = '';
        public string $namaProdi    = '';
        public string $namaFakultas = '';

        public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
        {
            parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
            $this->tcpdflink = false;
        }

        /**
         * Running Header for Pages 2+
         */
        public function Header(): void
        {
            if ($this->page > 1) {
                $this->SetY(6);
                $this->SetFont('helvetica', '', 7);
                $this->SetTextColor(70, 70, 70);
                $this->Cell(170, 4, 'UNIVERSITAS KATOLIK SOEGIJAPRANATA | ' . strtoupper($this->namaFakultas) . ' - ' . strtoupper($this->namaProdi), 0, 0, 'L');
                $this->Cell(107, 4, 'KODE DOKUMEN: ' . $this->docCode, 0, 1, 'R');
                $this->SetDrawColor(180, 180, 180);
                $this->SetLineWidth(0.3);
                $this->Line(10, 11, 287, 11);
            }
        }

        /**
         * Formal Page Footer with verification notice & page numbers
         */
        public function Footer(): void
        {
            $this->SetY(-9);
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(100, 100, 100);
            $this->SetDrawColor(200, 200, 200);
            $this->SetLineWidth(0.2);
            $this->Line(10, $this->GetY() - 1, 287, $this->GetY() - 1);
            $this->Cell(160, 4, 'Laporan Siklus SPMI PPEPP | Dokumen Terverifikasi Digital Resmi Institusi', 0, 0, 'L');
            $this->Cell(117, 4, 'Halaman ' . $this->getAliasNumPage() . ' dari ' . $this->getAliasNbPages(), 0, 0, 'R');
        }
    }
}
