<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;
use Wongyip\PHPHelpers\Format;


trait LabelsTrait
{
    /**
     * Coulmns labels.
     *
     * @var string[]
     */
    protected $labels = [];
    /**
     * Columns labels that should be rendered as HTML (Twig: |raw filter).
     *
     * @var string[]
     */
    protected $labelsHTML = [];
    
    /**
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function autoLabels()
    {
        $labels = [];
        if (is_object($this->model) && $this->model instanceof Model) {
            foreach (array_keys($this->model->getAttributes()) as $column) {
                $labels[$column] = Format::smartCaps($column);
            }
        }
        return $this->labels($labels);
    }
    
    /**
     * Get or set the label of a column.
     *
     * N.B. This method return the input $column if no label is defined.
     *
     * @param string $column
     * @param string $label
     * @return string|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function label($column, $label = null)
    {
        $returns = $this->getSetPropAssoc('labels', $column, $label);
        return is_null($returns) ? $column: $returns;
    }
    
    /**
     * Get or set the label of a column in HTML format.
     *
     * N.B. This method return FALSE if no label in HTML is defined.
     *
     * @param string $column
     * @param string $labelhtml
     * @return string|\Wongyip\Laravel\Renderable\ModelRenderable|false
     */
    public function labelHTML($column, $labelHTML = null)
    {
        $returns = $this->getSetPropAssoc('labelsHTML', $column, $labelHTML);
        return is_null($returns) ? false : $returns;
    }
    
    /**
     * Get or set labels of columns.
     *
     * Setter will merge into existing $labels unless $replace is TRUE.
     *
     * @param array   $labels
     * @param boolean $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function labels($labels = null, $replace = false)
    {
        return $this->getSetColumnsProp('labels', $labels, $replace);
    }
    
    /**
     * Get or set labels of columns in HTML format.
     *
     * Setter will merge into existing $labelsHTML unless $replace is TRUE.
     *
     * @param array   $labels
     * @param boolean $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function labelsHTML($labelsHTML = null, $replace = false)
    {
        return $this->getSetColumnsProp('labelsHTML', $labelsHTML, $replace);
        // Get
        if (is_null($labelsHTML)) {
            return $this->labelsHTML;
        }
        // Set
        $this->labelsHTML = $labelsHTML;
        return $this;
    }
}