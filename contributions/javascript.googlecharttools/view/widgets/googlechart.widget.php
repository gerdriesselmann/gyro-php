<?php
/**
 * Renders a chart using Google Chart Tools
 */
class WidgetGoogleChart implements IWidget {
	const COLUMN_CHART = 1000;
	const PIE_CHART = 1001;
	const LINE_CHART = 1002;
	const COMBO_CHART = 1003;

	public $title;
	public $column_names;
	public $data;
	public $options;

	public static function output($title, $column_names, $data, $options = array(), $policy = self::COLUMN_CHART) {
		$w = new WidgetGoogleChart($title, $column_names, $data, $options);
		return $w->render($policy);
	}

	public function __construct($title, $column_names, $data, $options) {
		$this->title = $title;
		$this->column_names = $column_names;
		$this->data = $data;
		$this->options = $options;
	}

	/**
	 * Renders what should be rendered
	 *
	 * @param int $policy Defines how to render, meaning depends on implementation
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		GoogleChartTools::enable(GoogleChartTools::CORE_CHART);

		$elem_id = GyroString::plain_ascii($this->title, '_');
		$func_name = 'draw_' . $elem_id;
		$data = $this->data;
		if (!empty($this->column_names)) {
			array_unshift($data, $this->column_names);
		}
		$data_json = ConverterFactory::encode($data, CONVERTER_JSON);
		$options = $this->options;
		$options['title'] = $this->title;
		$options_json = ConverterFactory::encode($options, CONVERTER_JSON);
		$type = $this->chart_type($policy);
		$js = array(
			"function $func_name() {",
				"var data = google.visualization.arrayToDataTable($data_json);",
				"var options = $options_json;",
    			"var chart = new google.visualization.{$type}(document.getElementById('$elem_id'));",
    			"chart.draw(data, options);",
    		"}",
			"google.setOnLoadCallback($func_name);"
		);
		$js = implode("\n", $js);

		StaticPageData::data()->head->add_js_snippet($js);

		return html::tag('div', '', array('id' => $elem_id, 'class' => 'js-chart'));
    }

	private function chart_type($policy) {
		switch($policy) {
			case self::COLUMN_CHART:
				return 'ColumnChart';
			case self::COMBO_CHART:
				return 'ComboChart';
			case self::PIE_CHART:
				return 'PieChart';
			case self::LINE_CHART:
				return 'LineChart';
			default:
				return 'ColumnChart';
		}
	}

}