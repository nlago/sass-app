<?php
/**
 * Expects $major & $courses
 */
?>

<optgroup label="<?php echo $major['Name']; ?>">
	<?php foreach ($courses as $course) { ?>
		<?php if ($course['Major'] === $major['Name']) { ?>
			<option
				value="<?php echo $course['Extension'] . $course['Code']; ?>">
				<?php echo $course['Extension'] . " " . $course['Code'] . " " . $course['Name']; ?></option>
		<?php
		}
	}?>
</optgroup>

