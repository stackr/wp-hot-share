<?php
/**
 * Plugin Name: 소셜 공유
 * Plugin URI:  http://www.stackr.co.kr
 * Description: 작성한 글을 소셜미디어로 공유할 수 있도록 공유 버튼을 추가해 줍니다. (페이스북, 트위터, 카카오톡, 카카오스토리, 라인 지원)
 * Author:      Stackr Inc.
 * Author URI:  http://www.stackr.co.kr
 * License: GPL2+
 * Version:     1.0
 */
require_once(dirname(__FILE__).'/includes/class.hot_share.php');
if(class_exists('Hot_Share')){
	new Hot_Share();
}