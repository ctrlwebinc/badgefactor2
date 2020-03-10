<?php
/**
 * CMB colorpicker field type.
 *
 * @since  2.2.2
 *
 * @category  WordPress_Plugin
 *
 * @author    CMB2 team
 * @license   GPL-2.0+
 *
 * @link      https://cmb2.io
 */
class CMB2_Type_Colorpicker extends CMB2_Type_Text
{
    /**
     * The optional value for the colorpicker field.
     *
     * @var string
     */
    public $value = '';

    /**
     * Constructor.
     *
     * @since 2.2.2
     *
     * @param CMB2_Types $types Object for the field type.
     * @param array      $args  Array of arguments for the type.
     * @param string     $value Value that the field type is currently set to, or default value.
     */
    public function __construct(CMB2_Types $types, $args = [], $value = '')
    {
        parent::__construct($types, $args);
        $this->value = $value ? $value : $this->value;
    }

    /**
     * Render the field for the field type.
     *
     * @since 2.2.2
     *
     * @param array $args Array of arguments for the rendering.
     *
     * @return CMB2_Type_Base|string
     */
    public function render($args = [])
    {
        $meta_value = $this->value ? $this->value : $this->field->escaped_value();

        $hex_color = '(([a-fA-F0-9]){3}){1,2}$';
        if (preg_match('/^'.$hex_color.'/i', $meta_value)) {
            // Value is just 123abc, so prepend #.
            $meta_value = '#'.$meta_value;
        } elseif (
            // If value doesn't match #123abc...
            !preg_match('/^#'.$hex_color.'/i', $meta_value)
            // And value doesn't match rgba()...
            && 0 !== strpos(trim($meta_value), 'rgba')
        ) {
            // Then sanitize to just #.
            $meta_value = '#';
        }

        wp_enqueue_style('wp-color-picker');

        $args = wp_parse_args($args, [
            'class' => 'cmb2-text-small',
        ]);

        $args['class'] .= ' cmb2-colorpicker';
        $args['value'] = $meta_value;
        $args['js_dependencies'] = ['wp-color-picker'];

        if ($this->field->options('alpha')) {
            $args['js_dependencies'][] = 'wp-color-picker-alpha';
            $args['data-alpha'] = 'true';
        }

        $args = wp_parse_args($this->args, $args);

        return parent::render($args);
    }

    /**
     * Provide the option to use a rgba colorpicker.
     *
     * @since 2.2.6.2
     */
    public static function dequeue_rgba_colorpicker_script()
    {
        if (wp_script_is('jw-cmb2-rgba-picker-js', 'enqueued')) {
            wp_dequeue_script('jw-cmb2-rgba-picker-js');
            CMB2_JS::register_colorpicker_alpha(true);
        }
    }
}
