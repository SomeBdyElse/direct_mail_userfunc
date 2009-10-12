<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Xavier Perseguers (typo3@perseguers.ch)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * This class encapsulates display of a user wizard.
 *
 * $Id$
 */
class tx_directmailuserfunc_wizard {
	
	/**
	 * Returns code to show a user-handled wizard associated to current
	 * itemsProcFunc value.
	 * 
	 * @param array $PA TCA configuration passed by reference
	 * @param $pObj
	 * @return string HTML snippet to be put after the itemsProcFunc field 
	 */
	public function user_TCAform_procWizard($PA, $pObj) {
		$itemsProcFunc = $PA['row']['tx_directmailuserfunc_itemsprocfunc'];
		if (!$itemsProcFunc) {
				// Show the required icon
			$PA['item'] = self::getIcon('gfx/required_h.gif') . $PA['item'];
			return;
		}

		list($className, $methodName) = explode('->', $itemsProcFunc);

		if (!($className && $methodName) || !(class_exists($className) && method_exists($className, $methodName))) {
			return self::getIcon('gfx/icon_warning.gif') . ' Unknown class and/or method';
		}
		if (!method_exists($className, 'getWizard')) {
			return self::getIcon('gfx/icon_ok.gif');
		}

		$paramsItemName = 'data[sys_dmail_group][1][tx_directmailuserfunc_params]';
		$currentParams = $PA['row']['tx_directmailuserfunc_params'];
		$wizardJS = trim(call_user_func_array(
			array($className, 'getWizard'),
			array($methodName, $currentParams, $PA['formName'], $paramsItemName)
		));

		if (!$wizardJS) {
			return self::getIcon('gfx/icon_ok.gif');
		}

		if ($wizardJS{strlen($wizardJS) - 1} !== ';') {
			$wizardJS .= ';';
		}
		$wizardJS .= 'return false;';

		$output = '<a href="#" onclick="' . htmlspecialchars($wizardJS) . '" title="Click here to update user parameters">';
		$output .= self::getIcon('gfx/options.gif');
		$output .= '</a>';
		$output .= '<input type="hidden" name="' . $paramsItemName . '" value="' . htmlspecialchars($currentParams) . '" />';

		return $output;
	}

	/**
	 * Returns a HTML image tag with a given icon (taking t3skin into account).
	 * 
	 * @param $src
	 * @return string
	 */
	protected static function getIcon($src) {
		return '<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACKPATH'], $src) . ' alt="" vspace="4" align="absmiddle" />';
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/direct_mail_userfunc/res/scripts/class.tx_directmailuserfunc_wizard.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/direct_mail_userfunc/res/scripts/class.tx_directmailuserfunc_wizard.php']);
}

?>