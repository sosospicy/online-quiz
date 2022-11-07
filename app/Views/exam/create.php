<?php
// 视图：生成试卷

$type_dict = [
	'radio' => 'Single Choice',
	'checkbox' => 'Multiple Choice',
	'textarea' => 'Input Answer',
];
?>
<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<div id="create-exam-container" class="d-flex flex-column content-container">
	<?php if (empty($list)) : ?>
		<p class="alert alert-warning">
			Sorry. No Data.
		</p>

	<?php else : ?>
		<h6>Exam Title: </h6>
		<div>
			<input type="text" id="exam-title" class="form-control" placeholder="optional" />
		</div>
		<br />
		<h6>Please select questions for your exam: </h6>

		<div id="questions-container" class="list-group flex-grow-1">
			<?php foreach ($list as $item) : ?>
				<div class="list-group-item list-group-item-action" aria-current="true">
					<div class="d-flex w-100">
						<div class="input-wrapper">
							<input type="checkbox" data-score="<?= $item['score'] ?>" data-id="<?= $item['id'] ?>" class="question-checks" />
						</div>
						<div class="mb-1">
							<?= $item['title'] ?>
							<div class="sub-info">
								<span><?= $type_dict[$item['type']] ?></span>
								<span><?= $item['score'] ?> points</span>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div> <!-- questions-container -->


		<div class="operate-bar element-pc">
			<div class="operate-container d-flex justify-content-between align-items-center">
				<span><input type="checkbox" class="checks-all" /> Select All/None</span>

				<span><span class="checked-count">0</span> questions selected</span>
				<span>Total points: <span class="total-points">0</span></span>

				<div>
					<button class="btn btn-primary on-create-btn" data-bs-toggle="modal" data-bs-target="#confirmModal" disabled>Create Exam</button>&nbsp;&nbsp;&nbsp;
					<a href="/export" class="link-primary">Export All</a>
				</div>
			</div>
		</div>

		<div class="operate-bar element-mobile">
			<div class="operate-container d-flex flex-column">
				<div class="d-flex justify-content-between">
					<span><input type="checkbox" class="checks-all" /> Select All/None</span>
					<span>Total points: <span class="total-points">0</span></span>
				</div>

				<div class="d-flex justify-content-between" style="margin-top: 1rem;">
					<button class="btn btn-primary on-create-btn" data-bs-toggle="modal" data-bs-target="#confirmModal" disabled>Create Exam (<span class="checked-count">0</span>)</button>&nbsp;&nbsp;&nbsp;
					<a href="/export" class="link-primary">Export All</a>
				</div>
			</div>
		</div>
	<?php endif; ?>

</div> <!-- create-exam-container -->

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="confirmModalLabel">Confirm</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><span class="checked-count">0</span> questions selected</p>
				<p>Total points: <span class="total-points">0</span></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="confirm-create">Confirm</button>
			</div>
		</div>
	</div>
</div>
<!-- Loading -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="d-flex justify-content-center align-items-center loading-content">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Creating ...</span>
					</div>
					<span>Creating ...</span>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->include('components/err_modal') ?>

<script>
	$.when($.ready).then(function() {
		let exam_id_list = []
		const questions_total = parseInt(<?= count($list) ?>)
		const modals = {}
		modals.loading = new bootstrap.Modal(document.getElementById('loadingModal'), {
			keyboard: false,
			backdrop: 'static',
		})
		modals.err = new bootstrap.Modal(document.getElementById('errorModal'))
		$('.checks-all').on('click', function() {
			$('.question-checks').prop('checked', $(this).is(':checked'))
			statistic()
		})

		$('.question-checks').on('change', function() {
			statistic()
		})


		// submit
		$('#confirm-create').on('click', function() {
			modals.loading.show()

			// return
			$('#errMsg').text('')
			$.post("/create", {
				title: $('#exam-title').val(),
				questions: exam_id_list
			}, function(data) {
				if (data.errCode !== 0) {
					$('#errMsg').text(data.errMsg)
					modals.err.show()
					return
				}
				window.location = '/exam'

			}, "json").fail(function() {
				setTimeout(function() {
					$('#errMsg').text('Unexpected error occurred. Please contact with admin.')
					modals.err.show()
				}, 0)
			}).always(function() {
				// bootstrap.Modal.getOrCreateInstance(document.querySelector('#loadingModal')).hide() // todo: not work, why?
			})
		})

		// summarize selection
		function statistic() {
			exam_id_list = []
			let checked_count = 0
			let total_points = 0
			$('.question-checks:checked').each(function() {
				exam_id_list.push($(this).data('id'))
				checked_count++
				total_points += parseInt($(this).data('score'))
			})

			$('.checked-count').text(checked_count)
			$('.total-points').text(total_points)

			// check all selected
			$('.checks-all').prop('checked', checked_count === questions_total)

			// active btn
			$('.on-create-btn').prop('disabled', checked_count < 1)
		}

	});
</script>

<?= $this->endSection() ?>