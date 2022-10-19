<?php class CatalogModel{
    private $srcJson;
    private $catalogue;
    public function __construct($srcJson){
        $this->srcJson=$srcJson;
        $this->catalogue=json_decode(file_get_contents($this->srcJson));
    }
    public function getCatalog(){
        $catalog_array=$this->catalogue;
        return $catalog_array;
    }

}