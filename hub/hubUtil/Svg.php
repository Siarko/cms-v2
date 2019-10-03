<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 28.01.2018
 * Time: 23:36
 */

class Svg {
    private $content;
    public $width;
    public $height;

    private $_DEFS_ = <<<HTML
<defs>
    <marker id="arrow" markerWidth="10" markerHeight="10" refX="0" refY="3" orient="auto" markerUnits="strokeWidth" viewBox="0 0 20 20">
      <path d="M0,0 L0,6 L9,3 z" fill="#CB3D30"/>
    </marker>
</defs>
HTML;


    public function __construct($w = 300, $h = 200) {
        $this->create($w, $h);
    }

    public function create($width = 300, $height = 200) {
        $this->width = $width;
        $this->height = $height;
    }

    private function add($text) {
        $this->content .= $text;
    }

    public function addRect($x, $y, $width, $heigth, $color) {
        $this->add("<rect x='$x' y='$y' width='$width' height='$heigth' fill='$color'/>");
    }

    public function addCircle($x, $y, $radius, $color) {
        $this->add("<circle cx='$x' cy='$y' r='$radius' fill='$color'/>");
    }

    public function addText($x, $y, $text, $color, $size = 10, $pos = 'start') {
        $this->add("<text x='$x' y='$y' font-size='$size' fill='$color' text-anchor='$pos'>$text</text>");
    }

    /* @var Vec $v1 $v2 */
    public function addLine($v1, $v2, $color, $width = 2) {
        $this->add("<line x1='$v1->x' y1='$v1->y' x2='$v2->x' y2='$v2->y' stroke='$color' stroke-width='$width'/>");
    }

    public function addArrow($v1, $v2, $color, $width = 2) {
        $this->add("<line x1='$v1->x' y1='$v1->y' x2='$v2->x' y2='$v2->y' stroke='$color' stroke-width='$width' marker-end='url(#arrow)'/>");
    }

    public function get() {
        return
            "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' width='" . $this->width . "' height='" . $this->height . "'>"
            . $this->_DEFS_
            . $this->content . "</svg>";

    }
}