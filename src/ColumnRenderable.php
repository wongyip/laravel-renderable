<?php namespace Wongyip\Laravel\Renderable;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Compiled output for the views.
 * 
 * @author yipli
 */
class ColumnRenderable
{
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $labelHTML;
    /**
     * @var string
     */
    public $name;
    /**
     * @var array
     */
    public $options;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $column
     * @param mixed|$value
     * @param mixed|string|null $type
     * @param mixed|string|null $label
     * @param mixed|string|null $labelHTML
     * @param mixed|array|null $options
     * @return ColumnRenderable
     */
    public static function make(string $column, $value, string $type = null, $label = null, $labelHTML = null, $options = null): ColumnRenderable
    {
        $cr = new ColumnRenderable();
        $cr->name      = $column;
        $cr->value     = $value;
        $cr->type      = (is_string($type) && !empty($type)) ? $type : Renderable::DEFAULT_COLUMN_TYPE;
        $cr->label     = (is_string($label)  && !empty($label)) ? $label : $column;
        $cr->labelHTML = (is_string($labelHTML)  && !empty($labelHTML)) ? $labelHTML : null;
        $cr->options   = is_array($options) ? $options : [];
        return $cr;
    }

    /**
     * Get the formatted output of the $value for the $type, will return FALSE
     * if the input $value is not renderable as input $type.
     *
     * @return mixed
     */
    public function valueRenderable()
    {
        $type = $this->type;
        $value = $this->value;
        $options = is_array($this->options) ? $this->options : [];
        switch ($type) {
            // These types must be an array, so the view could handle it correctly.
            case 'ol':
            case 'ul':
            case 'lines':
                return is_array($value) ? $value : [$value];
            case 'boolean':
                // In case of null and there is a null-replacement.
                if (is_null($value) && key_exists('valueNull', $options)) {
                    return $options['valueNull'];
                }
                // NULL as false now.
                return $value ? $options['valueTrue'] : $options['valueFalse'];
            case 'csv':
                // @todo what if $value is not scalar?
                return is_array($value) ? implode($options['glue'], $value) : $value;
            // Default of Untyped
            case Renderable::DEFAULT_COLUMN_TYPE:
            default:
                $formatted = ColumnRenderable::valueToString($value, $error);
                return $error ? false: $formatted;
        }
    }

    /**
     * Convert the given $value to string for output.
     *
     * N.B. Always returns string for safe output, even if there is conversion
     * error, you should check the value $error for the conversion status.
     *
     * @param mixed $value
     * @param bool $error
     * @return string
     */
    public static function valueToString($value, bool &$error = null): string
    {
        try {
            if (is_scalar($value)) {
                // Boolean to 'Yes' or 'No' or other strings configured.
                if (is_bool($value)) {
                    $value = $value ? Renderable::DEFAULT_VALUE_BOOL_TRUE : Renderable::DEFAULT_VALUE_BOOL_FALSE;
                }
                // Unchanged for int, float and string.
            }
            else {
                // Array to default format.
                if (is_array($value)) {
                    $value = implode(Renderable::DEFAULT_CSV_GLUE, array_values($value));
                }
                // DateTime to string
                elseif ($value instanceof DateTime) {
                    $value->format(LARAVEL_RENDERABLE_DATETIME_FORMAT);
                }
                elseif (is_null($value)) {
                    $value = '';
                }
                elseif (is_resource($value)) {
                    Log::notice('valueToString() conversion ignored as value is a resource, $value is set as en empty string.');
                    $value = '';
                }
                else {
                    throw new Exception('Unhandled type of value.');
                }
            }
        }
        catch (Exception $e) {
            Log::error(sprintf('valueToString() conversion failure. Exception [%d] %s', $e->getCode(), $e->getMessage()));
            $value = '[Data Conversion Error]';
            $error = true;
        }
        return $value;
    }

    /**
     * @return bool
     */
    public function isRenderable(): bool
    {
        return $this->valueRenderable() !== false;
    }
}