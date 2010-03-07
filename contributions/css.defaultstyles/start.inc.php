<?php
/**
 * @defgroup DefaultStyles
 * @ingroup CSS
 * 
 * Copied css/default.css, which contains CSS for usual Gyro components, like Filters, Breadcrumb, Menu and Debug Block
 */

EventSource::Instance()->register(new CSSDefaultStylesEventSink());
