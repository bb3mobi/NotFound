<?php
/**
*
* @package Not Found
* @copyright (c) 2014 Anvar http://bb3.mobi
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace bb3mobi\NotFound\migrations;

class v_1_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['notfound_version']) && version_compare($this->config['notfound_version'], '1.0.1', '>=');
	}

	static public function depends_on()
	{
		return array('\bb3mobi\NotFound\migrations\v_1_0_0');
	}

	public function update_data()
	{
		return array(
			// Add config text
			array('config_text.add', array('notfound_text', $this->config['notfound_text'])),

			// Add configs
			array('config.add', array('notfound_recent_topics', '1')),
			array('config.add', array('notfound_recent_limit', '7')),

			// Remove old config
			array('if', array(
				(isset($this->config['notfound_text'])),
				array('config.remove', array('notfound_text')),
			)),

			// Update version
			array('config.update', array('notfound_version', '1.0.1')),

			// Add ACP modules
			array('module.add', array('acp', 'ACP_SERVER_CONFIGURATION', array(
				'module_basename'	=> '\bb3mobi\NotFound\acp\notfound_module',
				'module_langname'	=> 'ACP_NOT_FOUND',
				'module_mode'		=> 'config',
				'module_auth'		=> 'ext_bb3mobi/NotFound',
			))),
		);
	}
}