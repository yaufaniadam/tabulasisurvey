<?php 
	$year = isset( $_GET["year"] ) ? (int)$_GET["year"] : ta_sekarang();
	$title="Standar 4. Sumber Daya Manusia";
	wordOnline($title,4, $year);
	lampiran($course_id, 4, $year);
	
	$totalRow= Upload::getProgress($course->getValueEncoded('course_id'), $year); 
	
	if($totalRow ==8 ) {
	addComment($course_id, 4, $year); 
	}		
?>
