<?php
// 视图：导出题库

// 题型
$type_dict = [
	'radio' => 'Single Choice',
	'checkbox' => 'Multiple Choice',
	'textarea' => 'Input Answer',
];

// 选项标记
$option_marks = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
?>
<?= $this->extend('layout/export') ?>
<?= $this->section('export-content') ?>
<div id="export-container">
	<?php if (empty($questions)) : ?>
		<p class="alert alert-warning">
			No Data.
		</p>

	<?php else : ?>
		<h3>Training Material </h3>

        <div id="questions-container">
            <?php foreach ($questions as $q_index => $item) : ?>
                <div class="question-item">
                    <div>
                        <span><?= $q_index?>. </span>
                        <span><?= isset($type_dict[$item['type']]) ? '(' . $type_dict[$item['type']] . ')' : '' ?></span>
                        <?= $item['title'] ?>
                    </div>
                    <div class="options-container" data-question-type="<?= $item['type'] ?>" data-question-id="<?= $item['id'] ?>">
                        <?php if ($item['type'] === 'radio' || $item['type'] === 'checkbox') : ?>
                            <?php foreach ($item['options'] as $index => $option) : ?>
                                <div class="option-item" data-mark="<?= $option_marks[$index]?>" data-id="<?= $option['id'] ?>">
                                    <span><span class="option-mark"><?= $option_marks[$index]?>. </span><?= $option['content'] ?></span>
                                    <?php if($option['is_right'] == 1):?>
                                        <span>( correct )</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($item['type'] === 'textarea') : ?>
                            <div><?= $item['options'][0]['content']?></div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

	<?php endif; ?>
</div>
<style>
    .question-item {
        margin-top: 20px;
    }
</style>
<?= $this->endSection() ?>