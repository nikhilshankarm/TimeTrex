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
 * $Revision: 2875 $
 * $Id: EditRecurringHoliday.php 2875 2009-10-07 20:27:06Z ipso $
 * $Date: 2009-10-07 13:27:06 -0700 (Wed, 07 Oct 2009) $
 */
require_once('../../includes/global.inc.php');
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

if ( !$permission->Check('holiday_policy','enabled')
		OR !( $permission->Check('holiday_policy','edit') OR $permission->Check('holiday_policy','edit_own') ) ) {

	$permission->Redirect( FALSE ); //Redirect

}

$smarty->assign('title', TTi18n::gettext($title = 'Edit Recurring Holiday')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'id',
												'data'
												) ) );

$rhf = new RecurringHolidayFactory();

$action = Misc::findSubmitButton();
$action = strtolower($action);
switch ($action) {
	case 'submit':
		//Debug::setVerbosity(11);

		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__,10);

		$rhf->setId( $data['id'] );
		$rhf->setCompany( $current_company->getId() );
		$rhf->setName( $data['name'] );
		$rhf->setType( $data['type_id'] );
		/*
		if ( isset($data['easter']) ) {
			$rhf->setEaster( TRUE );
		} else {
			$rhf->setEaster( FALSE );
		}
		*/
		$rhf->setSpecialDay( $data['special_day_id'] );
		$rhf->setWeekInterval( $data['week_interval'] );
		$rhf->setPivotDayDirection( $data['pivot_day_direction_id'] );

		if ( $data['type_id'] == 20 ) {
			$rhf->setDayOfWeek( $data['day_of_week_20'] );
		} elseif ( $data['type_id'] == 30 ) {
			$rhf->setDayOfWeek( $data['day_of_week_30'] );
		}

		$rhf->setDayOfMonth( $data['day_of_month'] );
		$rhf->setMonth( $data['month'] );

		if ( $rhf->isValid() ) {
			$rhf->Save();

			Redirect::Page( URLBuilder::getURL( NULL, 'RecurringHolidayList.php') );

			break;
		}

	default:
		if ( isset($id) ) {
			BreadCrumb::setCrumb($title);

			$rhlf = new RecurringHolidayListFactory();
			$rhlf->getByIdAndCompanyID( $id, $current_company->getID() );

			foreach ($rhlf as $rh_obj) {
				//Debug::Arr($station,'Department', __FILE__, __LINE__, __METHOD__,10);

				$data = array(
									'id' => $rh_obj->getId(),
									'name' => $rh_obj->getName(),
									'type_id' => $rh_obj->getType(),
									'special_day_id' => $rh_obj->getSpecialDay(),
									'week_interval' => $rh_obj->getWeekInterval(),
									'pivot_day_direction_id' => $rh_obj->getPivotDayDirection(),
									'day_of_week' => $rh_obj->getDayOfWeek(),
									'day_of_month' => $rh_obj->getDayOfMonth(),
									'month' => $rh_obj->getMonth(),
									'created_date' => $rh_obj->getCreatedDate(),
									'created_by' => $rh_obj->getCreatedBy(),
									'updated_date' => $rh_obj->getUpdatedDate(),
									'updated_by' => $rh_obj->getUpdatedBy(),
									'deleted_date' => $rh_obj->getDeletedDate(),
									'deleted_by' => $rh_obj->getDeletedBy()
								);
			}
		}

		//Select box options;
		$data['special_day_options'] = $rhf->getOptions('special_day');
		$data['type_options'] = $rhf->getOptions('type');
		$data['week_interval_options'] = $rhf->getOptions('week_interval');
		$data['pivot_day_direction_options'] = $rhf->getOptions('pivot_day_direction');
		$data['day_of_week_options'] = TTDate::getDayOfWeekArray();
		$data['month_of_year_options'] = TTDate::getMonthOfYearArray();
		$data['day_of_month_options'] = TTDate::getDayOfMonthArray();

		$smarty->assign_by_ref('data', $data);

		break;
}

$smarty->assign_by_ref('rhf', $rhf);

$smarty->display('policy/EditRecurringHoliday.tpl');
?>