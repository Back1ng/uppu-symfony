<?php


namespace App\Service\Writer;


class SizeWriter implements Writer
{
    /**
     * @var int $size Size in bytes.
     */
    private $size;

    private $unit = "B";

    private $iterations = 0;

    private function convertToOptimalSize() : self
    {
        while($this->size > 1024) {
            $this->size /= 1024;
            $this->iterations++;
        }

        return $this->detectUnit();
    }

    private function detectUnit() : self
    {
        if ($this->iterations === 0) {
            $this->unit = "B";
        } elseif ($this->iterations === 1) {
            $this->unit = "KB";
        } elseif ($this->iterations === 2) {
            $this->unit = "MB";
        }

        return $this;
    }

    public function write($size)
    {
        $this->size = (int)$size;

        $this->convertToOptimalSize();

        return round($this->size, 2).$this->unit;
    }
}