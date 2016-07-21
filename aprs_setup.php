<?php
/*
* e107 website system
*
* Copyright (C) 2008-2013 e107 Inc (e107.org)
* Released under the terms and conditions of the
* GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
*
* Custom install/uninstall/update routines for aprs plugin
**
*/
if ( !defined( 'e107_INIT' ) ) {
	exit;
}
class _aprs_setup
{

 	function install_pre($var)
	{
		// print_a($var);
		// echo "custom install 'pre' function<br /><br />";
	}

	/**
	 * For inserting default database content during install after table has been created by the aprs_sql.php file.
	 */
	function install_post($var)
	{
		$sql = e107::getDb();
		$mes = e107::getMessage();

		$e107_aprs = array(
			'aprs_id'				=>'1',
			'aprs_icon'			=>'{e_PLUGIN}aprs/images/aprs_32.png',
			'aprs_type'			=>'type_1',
			'aprs_name'			=>'My Name',
			'aprs_folder'			=>'Folder Value',
			'aprs_version'			=>'1',
			'aprs_author'			=>'bill',
			'aprs_authorURL'		=>'http://e107.org',
			'aprs_date'			=>'1352871240',
			'aprs_compatibility'	=>'2',
			'aprs_url'				=>'http://e107.org'
		);

		if($sql->insert('aprs',$e107_aprs))
		{
			$mes->add("Custom - Install Message.", E_MESSAGE_SUCCESS);
		}
		else
		{
			$mes->add("Custom - Failed to add default table data.", E_MESSAGE_ERROR);
		}

	}

	function uninstall_options()
	{

		$listoptions = array(0=>'option 1',1=>'option 2');

		$options = array();
		$options['mypref'] = array(
				'label'		=> 'Custom Uninstall Label',
				'preview'	=> 'Preview Area',
				'helpText'	=> 'Custom Help Text',
				'itemList'	=> $listoptions,
				'itemDefault'	=> 1
		);

		return $options;
	}


	function uninstall_post($var)
	{
		// print_a($var);
	}

	function upgrade_post($var)
	{
		// $sql = e107::getDb();
	}

}
?>