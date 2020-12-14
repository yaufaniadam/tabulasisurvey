<?php
require_once(dirname(__FILE__) . '/common.inc.php');
session_start();
get_header("Participants");
checkLogin();

$survey_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$votesByQuestion = 3375;
include "sidebar.php";

if ($login == 1) {

?>
	<style>
		.canvas-cart {
			padding: 20px;
			width: 100%;
			text-align: center;
		}

		.canvas-cart canvas {
			display: inline-block !important;
		}

		.chartjsLegend {
			padding: 20px 0;
		}

		.chartjsLegend ul {
			list-style: none;
			margin: 0;
			padding: 0;
		}

		.chartjsLegend li span {
			display: inline-block;
			width: 12px;
			height: 12px;
			margin-right: 5px;
			border-radius: 25px;
		}
	</style>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	<div class="content-wrapper">
		<section class="content-header">


			<?php// echo Survey::getQuestionaresByUser(15); ?>
			<h1> Data yang Masuk
				<?php $survey = Survey::getSurvey($survey_id);
				//echo $survey->getValueEncoded('name');
				?>
				<small><?php echo $survey->getValueEncoded('description'); ?></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Survey</li>
			</ol>
		</section>
		<section class="content">

			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Rangkuman</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<?php
					// list($participants, $totalparticipants) = Survey::getSurveyParticipants($survey_id);
					// list($participants_all, $totalparticipants_all) = Survey::getSurveyParticipants_all($survey_id);
					// list($participants_date, $totalparticipants_all) = Survey::getSurveyParticipantsByDate($survey_id);
					// 
					?>

					<div class="row">
						<div class="col-md-3 text-center">
							<h1><i class="fa fa-users"></i></h1>
							<h4>Jumlah Responden</h4>
							<h2><?php echo $votesByQuestion ?></h2>
						</div>

					</div>
					<div class="row">
						<div class="col-md-12">

						</div>
					</div>
				</div>
			</div>
			<?php
			// tampilkan kategori pertanyaan
			list($questionnaires, $totalRows) = Survey::getQuestionnaires($survey_id);
			foreach ($questionnaires as $q) { ?>
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo $q->getValueEncoded('name'); ?></h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<?php // tampilkan isi pertanyaan
						list($questionnaire_items, $totalRows) = Survey::getQuestionnaire_items($q->getValueEncoded('id'));
						?>

						<?php $a = 1;
						foreach ($questionnaire_items as $q_item) {

							$q_type = $q_item->getValueEncoded('question_type_id');
							//id pertanyaan
							$id_q = $q_item->getValueEncoded('id');

							if ($q_type == 1) {
								// tampilkan pilihan jawaban
								list($questionnaire_item_variants, $totalRows) = Survey::getQuestionnaire_item_variants(0, 15, $id_q);
						?>
								<div class="row">
									<div class="col-md-3"><?php echo $a++ . ". ";
																				echo $q_item->getValueEncoded('question_text'); ?>
									</div>
									<div class="col-md-4">
										<div class="canvas-cart">
											<canvas id="myChart-<?php echo $q_item->getValueEncoded('id'); ?>" width="200"></canvas>
										</div>
									</div>
									<div class="col-md-5">
										<div id="chartjsLegend-<?php echo $q_item->getValueEncoded('id'); ?>" class="chartjsLegend"></div>
									</div>
								</div><!-- //row -->
								<script>
									var ctx = document.getElementById("myChart-<?php echo $q_item->getValueEncoded('id'); ?>");
									var myChart = new Chart(ctx, {
										type: 'pie',
										data: {
											labels: [
												<?php foreach ($questionnaire_item_variants as $q_item_variant) {

													$countVotes = Survey::countVotes($q_item_variant->getValueEncoded('id'));
													$percent = round(@(($countVotes->getValueEncoded('votes') / $votesByQuestion) * 100), 1);
													echo '"<strong>' . $q_item_variant->getValueEncoded('content') . ' </strong>( ' . $percent . '% )(' . $countVotes->getValueEncoded('votes') . ')",';
												}
												?>
											],
											datasets: [{
												label: '# of Votes',
												data: [
													<?php foreach ($questionnaire_item_variants as $q_item_variant) {

														$countVotes = Survey::countVotes($q_item_variant->getValueEncoded('id'));

														echo '"' . $countVotes->getValueEncoded('votes') . '",';
													}
													?>

												],
												backgroundColor: [
													'rgba(255, 99, 132, 4)',
													'rgba(54, 162, 235, 4)',
													'rgba(255, 206, 86, 4)',
													'rgba(46, 204, 113, 4)',
													'rgba(155, 89, 182, 4)',
													'rgba(52, 73, 94, 4)',
													'rgba(26, 188, 156, 4)',
													'rgba(192, 57, 43,  4)',
													'rgba(189, 195, 199, 4)',
													'rgba(241, 196, 15, 4)',
													'rgba(142, 27, 53,  4)',
													'rgba(159, 145, 199, 4)',
													'rgba(211, 126, 15, 4)'
												],
												borderColor: [
													'rgba(255,99,132,1)',
													'rgba(54, 162, 235, 1)',
													'rgba(255, 206, 86, 1)',
													'rgba(75, 192, 192, 1)',
													'rgba(153, 102, 255, 1)',
													'rgba(255, 159, 64, 1)'
												],
												borderWidth: 0
											}]
										},
										options: {
											responsive: false,
											animation: false,
											tooltips: {
												enabled: false
											},
											hover: {
												mode: null
											},
											legend: {
												display: false,
											}
										}
									});

									document.getElementById('chartjsLegend-<?php echo $q_item->getValueEncoded('id'); ?>').innerHTML = myChart.generateLegend();
								</script>


							<?php } else if (($q_type == 2) or ($q_type == 4)) {



								// tampilkan pilihan jawaban
								list($questionnaire_item_variants, $totalRows) = Survey::getQuestionnaire_item_variants(0, 15, $id_q);
							?>
								<div class="row">
									<div class="col-md-12"><?php echo $a++ . ". ";
																					echo $q_item->getValueEncoded('question_text'); ?>
									</div>
									<div class="col-md-12">
										<div class="canvas-cart">
											<canvas id="myChart-<?php echo $q_item->getValueEncoded('id'); ?>" width="500"></canvas>
										</div>
									</div>

								</div><!-- //row -->
								<script>
									var ctx = document.getElementById("myChart-<?php echo $q_item->getValueEncoded('id'); ?>");
									var myChart = new Chart(ctx, {
										type: 'horizontalBar',
										data: {
											labels: [
												<?php foreach ($questionnaire_item_variants as $q_item_variant) {
													$countVotes = Survey::countVotes($q_item_variant->getValueEncoded('id'));
													$percent = round(@(($countVotes->getValueEncoded('votes') / $votesByQuestion) * 100), 2);
													echo '"' . $q_item_variant->getValueEncoded('content') . ' (' . $percent . '%)(' . $countVotes->getValueEncoded('votes') . ')",';
												}
												?>
											],
											datasets: [{
												label: '# of Votes',
												data: [
													<?php foreach ($questionnaire_item_variants as $q_item_variant) {
														$countVotes = Survey::countVotes($q_item_variant->getValueEncoded('id'));
														$percent = round(@(($countVotes->getValueEncoded('votes') / $votesByQuestion) * 100), 2);
														echo '"' . $countVotes->getValueEncoded('votes') . '",';
													}
													?>
												],
												backgroundColor: [
													'rgba(255, 99, 132, 4)',
													'rgba(54, 162, 235, 4)',
													'rgba(255, 206, 86, 4)',
													'rgba(46, 204, 113, 4)',
													'rgba(155, 89, 182, 4)',
													'rgba(52, 73, 94, 4)',
													'rgba(26, 188, 156, 4)',
													'rgba(192, 57, 43,  4)',
													'rgba(189, 195, 199, 4)',
													'rgba(241, 196, 15, 4)',

												],
												borderColor: [
													'rgba(255,99,132,1)',
													'rgba(54, 162, 235, 1)',
													'rgba(255, 206, 86, 1)',
													'rgba(75, 192, 192, 1)',
													'rgba(153, 102, 255, 1)',
													'rgba(255, 159, 64, 1)'
												],
												borderWidth: 0
											}]
										},
										options: {
											responsive: true,
											animation: false,
											scales: {
												yAxes: [{
													ticks: {
														beginAtZero: true
													}
												}]
											},
											legend: {
												display: false
											},
											tooltips: {
												callbacks: {
													label: function(tooltipItem) {
														return tooltipItem.yLabel;
													}
												}
											}

										}
									});

									//document.getElementById('chartjsLegend-<?php echo $q_item->getValueEncoded('id'); ?>').innerHTML = myChart.generateLegend();
								</script>


						<?php } else if ($q_type == 6) {
							} // endif
						} // end foreach questionare_items 
						?>
					</div>
					<!-- /.box-body -->
				</div> <!-- /.box -->
			<?php } //end foreach $questionairies 
			?>
		</section>
	</div>



<?php

} else {

	echo "<script>window.location.href =' " . BASE_URL . "/login.php';</script>";
}

get_footer(); ?>