<?php
class TourModel{
    private $srcJson;
    private $tour;
    public function __construct($srcJson){
        $this->srcJson=$srcJson;
        $this->tour=json_decode(file_get_contents($srcJson));
    }
    public function getTour(){
        $tour_array=$this->tour;
        return $tour_array;
    }
}