<?php namespace Pulib\Laravel\ModleView\Layouts;

use Illuminate\Database\Eloquent\Model;



class SingleTable
{
    /**
     * @var string
     */
    public $captionField = 'Field';
    
    /**
     * @var string
     */
    public $captionValue = 'Value';
    
    public function __construct(Model $model, $layout, $options)
    {
        $this->labels = [];
        $this->htmlLabels = [];
    }
    
    static function singleTable(Model $model, $captionField = 'Field', $captionValue = 'Value')
    {
        
    }
    
}