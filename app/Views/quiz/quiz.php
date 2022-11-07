<?php
// 题型说明
$question_prefix = [
    'radio' => 'Select one',
    'checkbox' => 'Select all applied',
    'textarea' => 'Input your answer',
];
// 选项标记
$option_marks = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
?>

<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<div id="quiz-container" class="content-container">
    <?php if (!empty($error)) : ?>
        <div class="d-flex flex-column justify-content-center align-items-center err-container">
            <div class="alert alert-danger"><?= $error ?></div>
        </div>

    <?php else : ?>
        <h6 class="quiz-title">A total of <?= count($questions) ?> questions. Please start answering.</h6>
        <div id="questions-container">
            <?php foreach ($questions as $item) : ?>
                <div class="question-item">
                    <div>
                        <span><?= isset($question_prefix[$item['type']]) ? '(' . $question_prefix[$item['type']] . ')' : '' ?></span>
                        <?= $item['title'] ?>
                    </div>
                    <div class="options-container" data-question-type="<?= $item['type'] ?>" data-question-id="<?= $item['id'] ?>">
                        <?php if ($item['type'] === 'radio' || $item['type'] === 'checkbox') : ?>
                            <?php foreach ($item['options'] as $index => $option) : ?>
                                <div class="option-item" data-mark="<?= $option_marks[$index]?>" data-id="<?= $option['id'] ?>">
                                    <span><input class="form-check-input answer-input" type="<?= $item['type'] ?>" name="ques-<?= $item['id'] ?>" data-id="<?= $option['id'] ?>" /></span>
                                    <span><span class="option-mark"><?= $option_marks[$index]?>. </span><?= $option['content'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($item['type'] === 'textarea') : ?>
                            <textarea class="form-control answer-input" cols="3" type="<?= $item['type'] ?>"></textarea>
                        <?php endif; ?>
                        <div class="undone-warning text-danger" style="display: none;" id="warning-<?= $item['id'] ?>">Please enter your answer.</div>
                        <div class="answer-container text-success" style="display: none;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> <!-- questions-container -->
        <div id="submit-container" class="d-grid gap-2 col-4 mx-auto md-auto">
            <button class="btn btn-primary" type="button" id="submit-btn">Subimit</button>
            <button class="btn btn-primary" type="button" id="loading-btn" disabled style="display: none;">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Submitting...
            </button>
        </div>
        <div id="score-container" class="d-flex justify-content-center align-items-center invisible">
            <h5>Final Score: <span class="final-score"></span> points</h5>
        </div>
    <?php endif; ?>

    <?= $this->include('components/err_modal')?>
</div>


<!-- Score Modal -->
<div class="modal fade" id="scoreModal" tabindex="-1" aria-labelledby="scoreModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scoreModalLabel">Congrats!</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
                <p>Thank you for your participation.</p>
				<p>Your score: <span class="final-score"></span> points.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Check Details</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $.when($.ready).then(function() {
        const exam_id = "<?= $exam_id?>";
        const answers = {};
        const errModal = new bootstrap.Modal(document.getElementById('errorModal'))
        $('#submit-btn').on('click', function(){
            if (!checkAnswers()) {
                return
            }
            $('#submit-btn').hide()
            $('#loading-btn').show()

            // {"errCode":0,"final_score":0,"right_answer_list":,"failed_questions":}
            // let test_right_answers = {"1":["5"],"2":["11"],"3":["15"],"5":["27","28","29","30","31"],"9":"The relative noun serves as a \"link\" between the relative clause and its antecedent. It performs two functions: showing concord with its antecedent and indicating its function within the relative clause."}
            // let test_failed = [2,3,5,9]
            // let test_score = 90
            // renderRightAnswers(test_right_answers, test_failed)
            // renderScore(test_score)
            // return

            $.post('/quiz/hand-in', {exam_id, answers}, function(data){
                if (data.errCode !== 0) {
                    $('#errMsg').text('Unexpected error occurred. Please contact with admin.')
                    errModal.show()
                    return
                }
                // render score and right answers
                $('#loading-btn').hide()
                renderRightAnswers(data.right_answer_list, data.failed_questions)
                renderScore(data.final_score)

            }, 'json').fail(function(){
                $('#errMsg').text('Unexpected error occurred. Please contact with admin.')
				errModal.show()

                $('#loading-btn').hide()
                $('#submit-btn').show()
            })

        })

        // if all questions are answered
        function checkAnswers() {
            let is_completed = true
            $('.undone-warning').hide()
            $('.options-container').each(function() {
                let question_id = $(this).data('question-id')
                let question_type = $(this).data('question-type')
                if (question_type === 'textarea') { // dismiss input check
                    answers[question_id] = $(this).find('.answer-input').first().val()
                    return true
                }
                let selected = []
                $(this).find('.answer-input:checked').each(function(){
                    selected.push($(this).data('id'))
                })
                if (selected.length === 0) {
                    is_completed = false
                    $('#warning-' + question_id).show()
                } 

                answers[question_id] = selected
            })

            // scroll to the first warning
            if (!is_completed) {
                $("html,body").stop(true);
                $("html,body").animate({scrollTop: $('.undone-warning').filter(":visible").first().parent().parent().offset().top}, 100);
            }
            
            return is_completed
        }

        // render right answers
        function renderRightAnswers(answers, failed_questions) {
            $('.options-container').each(function(){
                let question_id = $(this).data('question-id')
                let question_type = $(this).data('question-type')
                if (!answers[question_id]) {
                    return true
                }
                let correct_answer = 'Correct Answer: '
                switch (question_type) {
                    case 'textarea':
                        correct_answer += answers[question_id]
                        
                        break;
                    case 'radio':
                    case 'checkbox':
                        $(this).children('.option-item').each(function(){
                            let option_mark = $(this).data('mark')
                            let option_id = $(this).data('id')
                            if (answers[question_id].indexOf(''+option_id) != -1) {
                                correct_answer += option_mark + ' '
                            }
                        })
                        break;
                    default:
                        break;
                }
                $(this).children('.answer-container').text(correct_answer).show()

                // wrong answer alert
                if (failed_questions.indexOf(question_id) != -1) {
                    $(this).find('.answer-input').filter(':checked').addClass('wrong')
                    $(this).find('textarea').addClass('wrong')
                }
            })
        }
        // render final score
        function renderScore(final_score) {
            const scoreModal = new bootstrap.Modal(document.getElementById('scoreModal'))
            
            $('.final-score').text(final_score)
            $('#score-container').removeClass('invisible')

            scoreModal.show()
        }
    })
</script>

<?= $this->endSection() ?>