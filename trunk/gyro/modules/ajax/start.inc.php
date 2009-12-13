<?php
/**
 * @defgroup Ajax
 * @ingroup Modules
 *  
 * Content view for Ajax (JSON) result data
 * 
 * To use the model you simply use the AjaxRenderDecorator, when defining a route
 * 
 * @code
 * $routes = array(
 *   new ExactMatchRoute('my/fancy/ajax', $this, 'my_fancy_ajax', new AjaxRenderDecorator())
 * );
 * @endcode
 * 
 * In the according action, you do not need to create a view. Simply set ajax_data on the PageData
 * instance. Error return values are respected.
 * 
 * @code
 * public function action_my_fancy_ajax(PageData $page_data) {
 *   if (today_is_monday()) {
 *      return self::INTERNAL_ERROR; // We are on strike on mondays!
 *   }
 *   
 *   $page_data->ajax_data = array(
 *   	'fishes' => 'fancy',
 *   	'squirrels' => 'fierce'
 *   );
 * }
 * @endcode
 * 
 * This will return the following array on success (JSON encoded):
 * 
 * @code
 * array(
 *   'is_error' => false,
 *   'result' => array(
 *     'fishes' => 'fancy',
 *     'squirrels' => 'fierce'
 *   )
 * )
 * @endcode
 * 
 * Execpt on mondays, when it returns the error:
 * 
 * @code
 * array(
 *   'is_error' => true,
 *   'error' => 'Server error'
 * )
 * @endcode
 */

// Register our views
ViewFactory::set_implementation(new ViewFactoryAjax());
