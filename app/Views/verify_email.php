<?= $this->extend('layout/blank') ?>
<?= $this->section('content') ?>

<div class="notice-container">
    <h3>Verification</h3>
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
        Retry <a href="<?= site_url('sign-up') ?>">Sign Up</a> or <a href="<?= site_url('sign-in') ?>">Sign In</a>
    <?php else : ?>
        <div class="alert alert-success">
            <h3>Congrats! </h3>
            <br />
            <br />
            <p>Thank you for register.</p>
            <a href="<?= site_url('sign-in') ?>">Sign In</a>
        </div>
    <?php endif; ?>
</div>


<?= $this->endSection() ?>