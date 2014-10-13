<?php

/**
 * Created by PhpStorm.
 * User: rdok
 * Date: 9/18/2014
 * Time: 4:30 PM
 */
class AppointmentHasStudentFetcher
{
	const DB_TABLE = "appointment_has_student";
	const DB_COLUMN_ID = "id";
	const DB_COLUMN_APPOINTMENT_ID = "appointment_id";
	const DB_COLUMN_STUDENT_ID = "student_id";
	const DB_COLUMN_REPORT_ID = "report_id";
	const DB_COLUMN_INSTRUCTOR_ID = "instructor_id";

	public static function insert($appointmentId, $studentId, $instructorId) {
		try {
			$queryInsertUser = "INSERT INTO `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "` (`" . self::DB_COLUMN_APPOINTMENT_ID
				. "`,	`" . self::DB_COLUMN_STUDENT_ID . "`, `" . self::DB_COLUMN_INSTRUCTOR_ID . "`
				)
				VALUES(
					:appointment_id,
					:student_id,
					:instructor_id
				)";

			$dbConnection = DatabaseManager::getConnection();
			$queryInsertUser = $dbConnection->prepare($queryInsertUser);

			$queryInsertUser->bindParam(':appointment_id', $appointmentId, PDO::PARAM_STR);
			$queryInsertUser->bindParam(':student_id', $studentId, PDO::PARAM_STR);
			$queryInsertUser->bindParam(':instructor_id', $instructorId, PDO::PARAM_STR);

			$queryInsertUser->execute();

		} catch (Exception $e) {
			throw new Exception("Could not insert user into database.");
		}

	}

	public static function update($appointmentId, $reportId) {
		$query = "UPDATE `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "`
					SET `" . self::DB_COLUMN_REPORT_ID . "`= :report_id
					WHERE `" . self::DB_COLUMN_ID . "` = :appointment_id";

		try {
			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);
			$query->bindParam(':report_id', $reportId, PDO::PARAM_INT);

			$query->execute();

			if ($query->rowCount() == 0) {
				throw new Exception();
			}
			return true;
		} catch (Exception $e) {
			throw new Exception("Could not update data.");
		}
		return false;
	}

	public static function updateStudentId($oldStudentIds, $newStudentIds, $appointmentId) {
		$query = "UPDATE `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "`
					SET `" . self::DB_COLUMN_STUDENT_ID . "`= :new_student_id
					WHERE `" . self::DB_COLUMN_STUDENT_ID . "` = :old_student_id
					AND  `" . self::DB_COLUMN_APPOINTMENT_ID . "` = :appointment_id";

		try {
			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->bindParam(':new_student_id', $newStudentIds, PDO::PARAM_INT);
			$query->bindParam(':old_student_id', $oldStudentIds, PDO::PARAM_INT);
			$query->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);

			$query->execute();

			if ($query->rowCount() == 0) return false;
			return true;
		} catch (Exception $e) {
			throw new Exception("Could not update data.");
		}
	}

	public static function updateInstructorIdForStudentId($oldInstructorId, $studentId, $newInstructorId, $appointmentId) {
		$query = "UPDATE `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "`
					SET `" . self::DB_COLUMN_INSTRUCTOR_ID . "`= :new_instructor_id
					WHERE `" . self::DB_COLUMN_INSTRUCTOR_ID . "` = :old_instructor_id
					AND  `" . self::DB_COLUMN_APPOINTMENT_ID . "` = :appointment_id
					AND  `" . self::DB_COLUMN_STUDENT_ID . "` = :student_id";


		try {
			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->bindParam(':new_instructor_id', $newInstructorId, PDO::PARAM_INT);
			$query->bindParam(':old_instructor_id', $oldInstructorId, PDO::PARAM_INT);
			$query->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);
			$query->bindParam(':student_id', $studentId, PDO::PARAM_INT);

			$query->execute();

			if ($query->rowCount() == 0) return false;
			return true;
		} catch (Exception $e) {
			throw new Exception("Could not update data.");
		}
	}


	public static function existsId($id) {
		try {
			$query = "SELECT COUNT(" . self::DB_COLUMN_ID . ") FROM `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" .
				self::DB_TABLE . "` WHERE `" . self::DB_COLUMN_ID . "` = :id";
			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->bindParam(':id', $id, PDO::PARAM_INT);
			$query->execute();

			if ($query->fetchColumn() === 0) return false;
		} catch (Exception $e) {
			throw new Exception("Could not check if data already exists.");
		}

		return true;
	}

	public static function retrieveAll() {
		$query =
			"SELECT `" . self::DB_COLUMN_ID . "` , `" . self::DB_COLUMN_APPOINTMENT_ID . "` , `" . self::DB_COLUMN_STUDENT_ID . "`,
			 `" . self::DB_COLUMN_REPORT_ID . "`,  `" . self::DB_COLUMN_INSTRUCTOR_ID . "`
			FROM `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "`";

		try {
			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->execute();

			return $query->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new Exception("Could not retrieve data from database.");
		}
	}

	public static function retrieveAllOnCurTerm() {
		$query =
			"SELECT `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_ID . "` , `" . self::DB_TABLE . "`.`" .
			self::DB_COLUMN_APPOINTMENT_ID . "` , `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_STUDENT_ID . "`,
			`" . self::DB_TABLE . "`.`" . self::DB_COLUMN_REPORT_ID . "`,  `" . self::DB_TABLE . "`.`" .
			self::DB_COLUMN_INSTRUCTOR_ID . "`, `" . StudentFetcher::DB_TABLE . "`.`" . StudentFetcher::DB_COLUMN_STUDENT_ID . "`
			FROM `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "`
			INNER JOIN  `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . AppointmentFetcher::DB_TABLE . "`
			ON `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . AppointmentFetcher::DB_TABLE . "`.`" .
			AppointmentFetcher::DB_COLUMN_ID . "`  = `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_APPOINTMENT_ID . "`
			INNER JOIN  `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . TermFetcher::DB_TABLE . "`
			ON `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . TermFetcher::DB_TABLE . "`.`" .
			TermFetcher::DB_COLUMN_ID . "`  = `" . AppointmentFetcher::DB_TABLE . "`.`" .
			AppointmentFetcher::DB_COLUMN_TERM_ID . "`
			INNER JOIN  `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . StudentFetcher::DB_TABLE . "`
			ON `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . StudentFetcher::DB_TABLE . "`.`" .
			StudentFetcher::DB_COLUMN_ID . "`  = `" . self::DB_TABLE . "`.`" .
			self::DB_COLUMN_STUDENT_ID . "`
			WHERE :now
				BETWEEN `" . TermFetcher::DB_COLUMN_START_DATE . "`
				AND `" . TermFetcher::DB_COLUMN_END_DATE . "`";


		try {
			date_default_timezone_set('Europe/Athens');
			$now = new DateTime();
			$now = $now->format(Dates::DATE_FORMAT_IN);

			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->bindParam(':now', $now, PDO::PARAM_STR);
			$query->execute();

			return $query->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new Exception("Could not retrieve data from database." . $e->getMessage());
		}
	}

	public static function retrieveJoinReport($appointmentId) {
		$query =
			"SELECT `" . self::DB_COLUMN_ID . "` , `" . self::DB_COLUMN_APPOINTMENT_ID . "` , `" . self::DB_COLUMN_STUDENT_ID . "`,
			 `" . self::DB_COLUMN_REPORT_ID . "`,  `" . self::DB_COLUMN_INSTRUCTOR_ID . "`
			FROM `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "`
			WHERE `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_APPOINTMENT_ID . "`=:appointment_id";

		try {
			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);
			$query->execute();

			return $query->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new Exception("Could not retrieve data from database." . $e->getMessage());
		}
	}

	public static function retrieveStudentsWithAppointment($appointmentId) {
		$query =
			"SELECT `" . UserFetcher::DB_TABLE . "`.`" . UserFetcher::DB_COLUMN_FIRST_NAME . "` AS
            " . UserFetcher::DB_TABLE . "_" . UserFetcher::DB_COLUMN_FIRST_NAME . ",
            `" . UserFetcher::DB_TABLE . "`.`" . UserFetcher::DB_COLUMN_ID . "` AS
            " . UserFetcher::DB_TABLE . "_" . UserFetcher::DB_COLUMN_ID . ",
            `" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_START_TIME . "`,
            `" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_END_TIME . "`,
            `" . UserFetcher::DB_TABLE . "`.`" . UserFetcher::DB_COLUMN_LAST_NAME . "` AS
            " . UserFetcher::DB_TABLE . "_" . UserFetcher::DB_COLUMN_LAST_NAME . ",
            `" . InstructorFetcher::DB_TABLE . "`.`" . InstructorFetcher::DB_COLUMN_ID . "` AS
            " . InstructorFetcher::DB_TABLE . "_" . InstructorFetcher::DB_COLUMN_ID . ",
            `" . InstructorFetcher::DB_TABLE . "`.`" . InstructorFetcher::DB_COLUMN_FIRST_NAME . "` AS
            " . InstructorFetcher::DB_TABLE . "_" . InstructorFetcher::DB_COLUMN_FIRST_NAME . ",
            `" . InstructorFetcher::DB_TABLE . "`.`" . InstructorFetcher::DB_COLUMN_LAST_NAME . "` AS
            " . InstructorFetcher::DB_TABLE . "_" . InstructorFetcher::DB_COLUMN_LAST_NAME . ",
            `" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_COURSE_ID . "`,
             `" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_TERM_ID . "`,
            `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_ID . "` , `" . self::DB_TABLE . "`.`" .
			self::DB_COLUMN_APPOINTMENT_ID . "` ,  `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_STUDENT_ID . "`,
            `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_REPORT_ID . "`,  `" . self::DB_TABLE . "`.`" .
			self::DB_COLUMN_INSTRUCTOR_ID . "`, `" . StudentFetcher::DB_TABLE . "`.`" .
			StudentFetcher::DB_COLUMN_FIRST_NAME . "` AS " . StudentFetcher::DB_TABLE . "_" .
			StudentFetcher::DB_COLUMN_FIRST_NAME . ", `" . StudentFetcher::DB_TABLE . "`.`" .
			StudentFetcher::DB_COLUMN_LAST_NAME . "` AS " . StudentFetcher::DB_TABLE . "_" .
			StudentFetcher::DB_COLUMN_LAST_NAME . ",  `" . AppointmentFetcher::DB_TABLE . "`.`" .
			AppointmentFetcher::DB_COLUMN_LABEL_MESSAGE . "`,
			`" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_ID . "` AS
			" . AppointmentFetcher::DB_TABLE . "_" . AppointmentFetcher::DB_COLUMN_ID . "
			,  `" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_LABEL_COLOR . "`
			FROM `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . self::DB_TABLE . "`
			INNER JOIN  `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . StudentFetcher::DB_TABLE . "`
			ON `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . StudentFetcher::DB_TABLE . "`.`" . StudentFetcher::DB_COLUMN_ID . "`  = `" .
			self::DB_TABLE . "`.`" . self::DB_COLUMN_STUDENT_ID . "`
            INNER JOIN  `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . AppointmentFetcher::DB_TABLE . "`
			ON `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_ID . "`  = `" .
			self::DB_TABLE . "`.`" . self::DB_COLUMN_APPOINTMENT_ID . "`
            INNER JOIN  `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . UserFetcher::DB_TABLE . "`
			ON `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . AppointmentFetcher::DB_TABLE . "`.`" . AppointmentFetcher::DB_COLUMN_TUTOR_USER_ID
			. "`  = `" . UserFetcher::DB_TABLE . "`.`" . UserFetcher::DB_COLUMN_ID . "`
            INNER JOIN  `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . InstructorFetcher::DB_TABLE . "`
			ON `" . DatabaseManager::$dsn[DatabaseManager::DB_NAME] . "`.`" . InstructorFetcher::DB_TABLE . "`.`" . InstructorFetcher::DB_COLUMN_ID . "`  = `" .
			self::DB_TABLE . "`.`" . self::DB_COLUMN_INSTRUCTOR_ID . "`
			WHERE `" . self::DB_TABLE . "`.`" . self::DB_COLUMN_APPOINTMENT_ID . "`=:appointment_id";

		try {
			$dbConnection = DatabaseManager::getConnection();
			$query = $dbConnection->prepare($query);
			$query->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);
			$query->execute();

			return $query->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new Exception("Could not retrieve data from database." . $e->getMessage());
		}
	}

} 