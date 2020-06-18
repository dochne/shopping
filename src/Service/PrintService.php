<?php


namespace Dochne\Shopping\Service;


use Dochne\Shopping\Entity\Category;
use Dochne\Shopping\Repository\CategoryRepository;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class PrintService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    private function optimalLines(array $items, int $lineLength = 42) : array
    {
        $output = [];
        $line = [];
        foreach ($items as $item) {
            if (count($line) === 0) {
                $line[] = $item;
                continue;
            }

            // Remember we need at least one space!
            if (strlen($line[0]) + strlen($item) + 1 > $lineLength) {
                $output[] = $line;
                $line = [$item];
                continue;
            }

            $line[] = $item;

            if (count($line) === 2) {
                $output[] = $line;
                $line = [];
                continue;
            }
        }

        if (count($line) > 0) {
            $output[] = $line;
        }

        $outputStrings = [];
        foreach ($output as $line) {
            if (!isset($line[1])) {
                $outputStrings[] = $line[0];
                continue;
            }

            $string = str_pad($line[0], $lineLength - strlen($line[1])) . $line[1];
            $outputStrings[] = $string;
        }
        return $outputStrings;
    }


    public function print() : bool
    {
        //$connector = new RemotePrintConnector();
        $connector = new FilePrintConnector("/dev/usb/lp0");
        $printer = new Printer($connector);

        $categories = array_filter($this->categoryRepository->all(), function(Category $category) {
            return strlen(trim($category->shopping)) > 0;
        });

        if (count($categories) === 0) {
            return false;
        }

        date_default_timezone_set("Europe/London");

        // Todo: possibly fingerprint devices so we know who printed it
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->setTextSize(1, 1);
        $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
        $printer->text( date("Y-m-d H:i") . "\n");
        $printer->selectPrintMode(Printer::MODE_FONT_A);

        foreach ($categories as $category) {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
            $printer->text($category->name . "\n");
            $printer->selectPrintMode(Printer::MODE_FONT_A);

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            //$printer->setTextSize(1, 1);
            $trimmed = $category->shopping;
            $lines = explode("\n", $trimmed);

            foreach ($this->optimalLines($lines) as $string) {
                $printer->text($string . "\n");
            }
//
//            foreach ($lines as $n => $line) {
//                if ($n % 2 === 0) {
//                    $printer->setJustification(Printer::JUSTIFY_LEFT);
//                    $printer->text($line);
//                } else {
//                    $printer->setJustification(Printer::JUSTIFY_RIGHT);
//                    $printer->text($line . "\n");
//                }
//            }





        }

        $printer->cut();
        $printer->close();

        return true;
    }
}