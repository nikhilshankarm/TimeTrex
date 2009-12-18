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
 * $Revision: 2741 $
 * $Id: HolidayPolicyRecurringHolidayFactory.class.php 2741 2009-08-19 22:11:46Z ipso $
 * $Date: 2009-08-19 15:11:46 -0700 (Wed, 19 Aug 2009) $
 */

/**
 * @package Module_Policy
 */
class HolidayPolicyRecurringHolidayFactory extends Factory {
	protected $table = 'holiday_policy_recurring_holiday';
	protected $pk_sequence_name = 'holiday_policy_recurring_holiday_id_seq'; //PK Sequence name
	function getHolidayPolicy() {
		if ( isset($this->data['holiday_policy_id']) ) {
			return $this->data['holiday_policy_id'];
		}

		return FALSE;
	}
	function setHolidayPolicy($id) {
		$id = trim($id);

		$hplf = new HolidayPolicyListFactory();

		if (
			  $this->Validator->isNumeric(	'holiday_policy',
											$id,
											TTi18n::gettext('Holiday Policy is invalid')

			/*
			  $this->Validator->isResultSetWithRows(	'holiday_policy',
													$hplf->getByID($id),
													TTi18n::gettext('Holiday Policy is invalid')
			 */
															) ) {
			$this->data['holiday_policy_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	function getRecurringHoliday() {
		if ( isset($this->data['recurring_holiday_id']) ) {
			return $this->data['recurring_holiday_id'];
		}
	}
	function setRecurringHoliday($id) {
		$id = trim($id);

		$rhlf = new RecurringHolidayListFactory();

		if ( $id != 0
				AND $this->Validator->isResultSetWithRows(	'recurring_holiday',
															$rhlf->getByID($id),
															TTi18n::gettext('Selected Recurring Holiday is invalid')
															)
			) {

			$this->data['recurring_holiday_id'] = $id;

			return TRUE;
		}

		return FALSE;
	}

	//This table doesn't have any of these columns, so overload the functions.
	function getDeleted() {
		return FALSE;
	}
	function setDeleted($bool) {
		return FALSE;
	}

	function getCreatedDate() {
		return FALSE;
	}
	function setCreatedDate($epoch = NULL) {
		return FALSE;
	}
	function getCreatedBy() {
		return FALSE;
	}
	function setCreatedBy($id = NULL) {
		return FALSE;
	}

	function getUpdatedDate() {
		return FALSE;
	}
	function setUpdatedDate($epoch = NULL) {
		return FALSE;
	}
	function getUpdatedBy() {
		return FALSE;
	}
	function setUpdatedBy($id = NULL) {
		return FALSE;
	}


	function getDeletedDate() {
		return FALSE;
	}
	function setDeletedDate($epoch = NULL) {
		return FALSE;
	}
	function getDeletedBy() {
		return FALSE;
	}
	function setDeletedBy($id = NULL) {
		return FALSE;
	}
}
?>