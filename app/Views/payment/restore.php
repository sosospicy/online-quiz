<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<div id="restore-container" class="notice-container">

    <h3>Restore your subscription</h3>
    <p>
        <?php if (!empty($message)) : ?>
            <p><?= $message?></p>
        <?php endif; ?>

        <p>If you have any question, please contact with the administrator.</p>

        <?php if (!empty($payment_link)) : ?>
            <a target="_blank" href="<?= $payment_link ?>">Click and Pay</a>
            <p>Please return to refresh this page after completing the payment.</p>
        <?php endif; ?>
    </p>

</div>

<?= $this->endSection() ?>