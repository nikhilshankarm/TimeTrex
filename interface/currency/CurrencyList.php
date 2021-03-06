<?php
/*********************************************************************************
 * TimeTrex is a Payroll and Time Management program developed by
 * TimeTrex Payroll Services Copyright (C) 2003 - 2010 TimeTrex Payroll Services.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by
 * the Free Software Foundation with the addition of the following permission
 * added to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED
 * WORK IN WHICH THE COPYRIGHT IS OWNED BY TIMETREX, TIMETREX DISCLAIMS THE
 * WARRANTY OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along
 * with this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact TimeTrex headquarters at Unit 22 - 2475 Dobbin Rd. Suite
 * #292 Westbank, BC V4T 2E9, Canada or at email address info@timetrex.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * "Powered by TimeTrex" logo. If the display of the logo is not reasonably
 * feasible for technical reasons, the Appropriate Legal Notices must display
 * the words "Powered by TimeTrex".
 ********************************************************************************/
/*
 * $Revision: 2740 $
 * $Id: CurrencyList.php 2740 2009-08-19 20:21:50Z ipso $
 * $Date: 2009-08-19 13:21:50 -0700 (Wed, 19 Aug 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('currency','enabled')
		OR !( $permission->Check('currency','view') OR $permission->Check('currency','view_own') ) ) {
	$permission->Redirect( FALSE ); //Redirect
}

//Debug::setVerbosity(11);

$smarty->assign('title', TTi18n::gettext($title = 'Currency List') );

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'page',
												'sort_column',
												'sort_order',
												'ids'
												) ) );

URLBuilder::setURL($_SERVER['SCRIPT_NAME'],
											array(
													'sort_column' => $sort_column,
													'sort_order' => $sort_order,
													'page' => $page
												) );

$sort_array = NULL;
if ( $sort_column != '' ) {
	$sort_array = array($sort_column => $sort_order);
}

Debug::Arr($ids,'Selected Objects', __FILE__, __LINE__, __METHOD__,10);

$action = Misc::findSubmitButton();
switch ($action) {
	case 'update_rates':
		CurrencyFactory::updateCurrencyRates( $current_company->getId() );

		Redirect::Page( URLBuilder::getURL(NULL, 'CurrencyList.php') );
		break;
	case 'add':
		Redirect::Page( URLBuilder::getURL(NULL, 'EditCurrency.php') );
		break;
	case 'delete' OR 'undelete':
		if ( strtolower($action) == 'delete' ) {
			$delete = TRUE;
		} else {
			$delete = FALSE;
		}

		$clf = new CurrencyListFactory();

		if ( isset($ids) AND is_array($ids) ) {
			foreach ($ids as $id) {
				$clf->getByIdAndCompanyId($id, $current_company->getId() );
				foreach ($clf as $c_obj) {
					$c_obj->setDeleted($delete);
					if ( $c_obj->isValid() ) {
						$c_obj->Save();
					}
				}
			}
		}

		Redirect::Page( URLBuilder::getURL(NULL, 'CurrencyList.php') );

		break;

	default:
		BreadCrumb::setCrumb($title);
		$clf = new CurrencyListFactory();

		$clf->getByCompanyId($current_company->getId(), $current_user_prefs->getItemsPerPage(),$page, NULL, $sort_array );

		$pager = new Pager($clf);

		$iso_code_options = $clf->getISOCodesArray();

		$base_currency = FALSE;
		foreach ($clf as $c_obj) {
			if ( $c_obj->getBase() === TRUE ) {
				$base_currency = TRUE;
			}
			$rows[] = array(
								'id' => $c_obj->GetId(),
								'status_id' => $c_obj->getStatus(),
								'name' => $c_obj->getName(),
								'iso_code' => $c_obj->getISOCode(),
								'currency_name' => Option::getByKey($c_obj->getISOCode(), $iso_code_options ),
								'conversion_rate' => $c_obj->getConversionRate(),
								'auto_update' => $c_obj->getAutoUpdate(),
								'is_base' => $c_obj->getBase(),
								'is_default' => $c_obj->getDefault(),
								'deleted' => $c_obj->getDeleted()
							);

		}
		$smarty->assign_by_ref('currencies', $rows);
		$smarty->assign_by_ref('base_currency', $base_currency);

		$smarty->assign_by_ref('sort_column', $sort_column );
		$smarty->assign_by_ref('sort_order', $sort_order );

		$smarty->assign_by_ref('paging_data', $pager->getPageVariables() );

		break;
}
$smarty->display('currency/CurrencyList.tpl');
?>