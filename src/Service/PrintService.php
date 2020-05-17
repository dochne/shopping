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

        // Todo: possibly fingerprint devices so we know who printed it
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->setTextSize(1, 1);
        $printer->text( date("Y-m-d H:i") . "\n");

        foreach ($categories as $category) {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text("\n" . $category->name . "\n\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setTextSize(1, 1);
            $trimmed = $category->shopping;
            $lines = explode("\n", $trimmed);
            foreach ($lines as $line) {
                $printer->text($line . "\n");
            }
        }

        $printer->cut();
        $printer->close();

        return true;
    }
}