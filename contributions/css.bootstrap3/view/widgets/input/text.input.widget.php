<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/text.input.widget.php';

/**
 * A text widget
 * 
 * @author Gerd Riesselmann
 * @ingroup Bootstrap3
 */
class InputWidgetText extends InputWidgetTextBase {
    /**
     * Render the actual widget
     */
    protected function render_input($attrs, $params, $name, $title, $value, $policy) {
        $needs_div = false;

        $type = Arr::get_item($attrs, 'type', 'text');
        $text_box = html::input($type, $name, $attrs);
        $pre = Arr::get_item($params, 'addon-pre');
        if ($pre) {
            $text_box = html::span(String::escape($pre), 'input-group-addon') . $text_box;
            $needs_div = true;
        }

        $post = Arr::get_item($params, 'addon-post');
        if ($post) {
            $text_box = $text_box . html::span(String::escape($post), 'input-group-addon');
            $needs_div = true;
        }

        if ($needs_div) {
            $text_box = html::div($text_box, 'input-group');
        }

        return $text_box;
    }

    /**
     * Create default attribute array
     */
    protected function create_default_attributes($params, $name, $policy) {
        $attrs = parent::create_default_attributes($params, $name, $policy);

        unset($attrs['addon-pre']);
        unset($attrs['addon-post']);
        return $attrs;
    }
}