<?php
require_once GYRO_CORE_DIR . 'view/widgets/input/base/multiselect.input.widget.php';
/**
 * A multiselect input
 * 
 * Renders either as a set of checkboxes or a multiselect select box
 * 
 * @author Gerd Riesselmann
 * @ingroup Bootstrap3
 */
class InputWidgetMultiselect extends InputWidgetMultiselectBase {
    /**
     * Render a label around widget
     */
    protected function render_label($widget, $html_attrs, $params, $name, $title, $value, $policy) {
        return html::div(
            html::label($title, '')  . $widget,
            'form-group'
        );
    }
}