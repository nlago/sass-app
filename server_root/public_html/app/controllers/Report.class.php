<?php

/**
 * Created by PhpStorm.
 * User: rdok
 * Date: 9/26/2014
 * Time: 9:50 PM
 */
class Report
{

	const LABEL_COLOR_WARNING = "warning";
	const LABEL_MESSAGE_PENDING_FILL = "pending fill";
	const LABEL_MESSAGE_PENDING_VALIDATION = "pending validation";
	const LABEL_MESSAGE_COMPLETE = "complete";
	const LABEL_COLOR_SUCCESS = "success";


	public static function getAllWithAppointmentId($appointmentId) {
		Appointment::validateId($appointmentId);
		return ReportFetcher::retrieveAllWithAppointmentId($appointmentId);
	}

	public static function updateLabel($formReportId, $message, $color) {
		ReportFetcher::updateLabel($formReportId, $message, $color);

	}

	public static function getWithAppointmentId($allReports, $appointmentId) {
		$reports = [];
		foreach ($allReports as $report) {
			if (strcmp($report[AppointmentFetcher::DB_TABLE . "_" . AppointmentFetcher::DB_COLUMN_ID], $appointmentId) === 0) {
				$reports[] = $report;
			}
		}

		return $reports;
	}

	public static function getSingle($reportId) {
		self::validateId($reportId);
		return ReportFetcher::retrieveSingle($reportId);
	}

	public static function validateId($id) {
		if (!preg_match('/^[0-9]+$/', $id) || !ReportFetcher::existsId($id)) {
			throw new Exception("Data tempering detected.
			<br/>You&#39;re trying to hack this app.<br/>Developers are being notified about this.<br/>Expect Us.");
		}
	}

	public static function updateProjectTopicOtherText($reportId, $oldText, $newText) {
		if (strcmp($oldText, $newText) === 0) return false;
		self::validateId($reportId);
		self::validateTextarea($newText, true);
		return ReportFetcher::updateSingleColumn($reportId, $newText, ReportFetcher::DB_COLUMN_PROJECT_TOPIC_OTHER);
	}

	public static function validateTextarea($text, $notRequired) {
		$notReqStringValidation = "/^[\\w\t\n\r\\ .,\\-]{0,512}$/";
		$reqStringValidation = "/^[\\w\t\n\r\\ .,\\-]{1,512}$/";
		$stringValidation = !$notRequired ? $reqStringValidation : $notReqStringValidation;

		if (!preg_match($stringValidation, $text)) {
			throw new Exception("Textareas can contain only <a href='http://www.regular-expressions.info/shorthand.html'
			target='_blank'>word characters</a>, spaces, carriage returns, line feeds and special characters <strong>.,-2</strong> of max size 512 characters.");
		}
	}

	public static function updateOtherText($reportId, $oldText, $newText) {
		if (strcmp($oldText, $newText) === 0) return false;
		self::validateId($reportId);
		self::validateTextarea($newText, true);
		return ReportFetcher::updateSingleColumn($reportId, $newText, ReportFetcher::DB_COLUMN_OTHER_TEXT_AREA);
	}

	public static function updateStudentsConcerns($reportId, $oldText, $newText) {
		if (strcmp($oldText, $newText) === 0) return false;
		self::validateId($reportId);
		self::validateTextarea($newText, true);
		return ReportFetcher::updateSingleColumn($reportId, $newText, ReportFetcher::DB_COLUMN_STUDENT_CONCERNS);
	}

	public static function updateRelevantFeedbackGuidelines($reportId, $oldText, $newText) {
		if (!isset($newText) || strcmp($oldText, $newText) === 0) return false;
		self::validateId($reportId);
		self::validateTextarea($newText, true);
		return ReportFetcher::updateSingleColumn($reportId, $newText, ReportFetcher::DB_COLUMN_RELEVANT_FEEDBACK_OR_GUIDELINES);
	}

	public static function updateAdditionalComments($reportId, $oldText, $newText) {
		if (strcmp($oldText, $newText) === 0) return false;
		self::validateId($reportId);
		self::validateTextarea($newText, true);
		return ReportFetcher::updateSingleColumn($reportId, $newText, ReportFetcher::DB_COLUMN_ADDITIONAL_COMMENTS);
	}

	public static function updateAllFields($reportId, $projectTopicOtherNew, $otherTextArea, $studentsConcernsTextArea,
	                                       $relevantFeedbackGuidelines, $studentBroughtAlongNew, $studentBroughtAlongOld,
	                                       $conclusionAdditionalComments) {
		self::validateId($reportId);
		self::validateTextarea($projectTopicOtherNew, false);
		self::validateTextarea($otherTextArea, true);
		self::validateTextarea($studentsConcernsTextArea, false);
		self::validateTextarea($relevantFeedbackGuidelines, true);
		self::validateOptionsStudentBroughtAlong($studentBroughtAlongNew);

		self::validateTextarea($conclusionAdditionalComments, true);
		return ReportFetcher::updateAllColumns($reportId, $projectTopicOtherNew, $otherTextArea, $studentsConcernsTextArea,
			$relevantFeedbackGuidelines, $studentBroughtAlongNew, $studentBroughtAlongOld, $conclusionAdditionalComments);
	}

	public static function validateOptionsStudentBroughtAlong($newOptions) {
		foreach ($newOptions as $option => $value) {
			switch ($option) {
				case StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_GRADED:
				case StudentBroughtAlongFetcher::DB_COLUMN_DRAFT:
				case StudentBroughtAlongFetcher::DB_COLUMN_INSTRUCTORS_FEEDBACK:
				case StudentBroughtAlongFetcher::DB_COLUMN_TEXTBOOK:
				case StudentBroughtAlongFetcher::DB_COLUMN_NOTES:
				case StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_SHEET:
				case StudentBroughtAlongFetcher::DB_COLUMN_EXERCISE_ON:
				case StudentBroughtAlongFetcher::DB_COLUMN_OTHER:
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_EXERCISE_ON . "text":
				case StudentBroughtAlongFetcher::DB_COLUMN_OTHER . "text":
//					self::validateTextarea($newOptions[$option], true);
					// TODO: validate input fields
					break;
				default:
					throw new Exception("Data have been malformed.");
					break;
			}
		}
	}

	public static function updateStudentBroughtAlong($reportId, $newOptions, $oldOptions) {
		if ($newOptions === NULL) $newOptions = [];
		self::validateOptionsStudentBroughtAlong($newOptions);
		if (!self::validateIfUpdateIsNeeded($newOptions, $oldOptions)) return false;
		self::validateId($reportId);
		return StudentBroughtAlongFetcher::update($newOptions, $oldOptions, $reportId);
	}

	public static function validateIfUpdateIsNeeded($newOptions, $oldOptions) {
		foreach ($oldOptions as $key => $value) {
			switch ($key) {

				case StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_GRADED:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_GRADED])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) === 0)
						|| (isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_GRADED])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_NOT_SELECTED) === 0)
					) return true;
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_DRAFT:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_DRAFT])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) === 0)
						|| (isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_DRAFT])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_NOT_SELECTED) === 0)
					) return true;
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_INSTRUCTORS_FEEDBACK:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_INSTRUCTORS_FEEDBACK])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) === 0)
						|| (isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_INSTRUCTORS_FEEDBACK])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_NOT_SELECTED) === 0)
					) return true;
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_TEXTBOOK:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_TEXTBOOK])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) === 0)
						|| (isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_TEXTBOOK])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_NOT_SELECTED) === 0)
					) return true;
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_NOTES:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_NOTES])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) === 0)
						|| (isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_NOTES])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_NOT_SELECTED) === 0)
					) return true;
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_SHEET:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_SHEET])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) === 0)
						|| (isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_ASSIGNMENT_SHEET])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_NOT_SELECTED) === 0)
					) return true;
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_EXERCISE_ON:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_EXERCISE_ON])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) !== NULL)
						|| (isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_EXERCISE_ON])
							&& strcmp($value, $newOptions[StudentBroughtAlongFetcher::DB_COLUMN_EXERCISE_ON . "text"]) !== 0)
					) return true;
					break;
				case StudentBroughtAlongFetcher::DB_COLUMN_OTHER:
					if ((!isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_OTHER])
							&& strcmp($value, StudentBroughtAlongFetcher::IS_SELECTED) !== NULL)
						||
						(isset($newOptions[StudentBroughtAlongFetcher::DB_COLUMN_OTHER])
							&& strcmp($value, $newOptions[StudentBroughtAlongFetcher::DB_COLUMN_OTHER . "text"]) !== 0)
					) return true;
					break;
				default:
					return false;
					break;
			}
		}
	}
} 