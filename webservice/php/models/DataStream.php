<?php
class DataStream
{
    protected $taxonomy;
    protected $data;
    protected $datacount;

    public function __construct($taxonomy, $data, $datacount)
    {

        $this->taxonomy = $taxonomy;
        $this->data = $data;
        $this->datacount = $datacount;
    }
    
    public function taxonomy(){return $this->taxonomy;}
    public function data(){return $this->data;}
    public function dataCount(){return $this->datacount;}

}
?>