<?php
/**
*
* @package Not Found Page
* @copyright (c) 2014 Anvar http://bb3.mobi
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace bb3mobi\NotFound\acp;

class notfound_info
{
	function module()
	{
		return array(
			'filename'	=> '\bb3mobi\NotFound\acp\notfound_module',
			'title'		=> 'ACP_NOT_FOUND_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'config'		=> array('title' => 'ACP_NOT_FOUND', 'auth' => 'ext_bb3mobi/NotFound', 'cat' => array('ACP_SERVER_CONFIGURATION')),
			),
		);
	}
}
