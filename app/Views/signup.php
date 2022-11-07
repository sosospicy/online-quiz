<?= $this->extend('layout/blank') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-center content-container">
    <div id="sign-up-container">
        <?php if (isset($validation)) : ?>
            <div class="alert alert-warning">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>
        <form action="<?= site_url('/sign-up') ?>" method="post" autocomplete="off">
            <div class="form-group mb-3">
                <input type="text" name="name" placeholder="Name" value="<?= set_value('name') ?>" class="form-control">
            </div>
            <div class="form-group mb-3">
                <input type="email" name="email" placeholder="Email" value="<?= set_value('email') ?>" class="form-control">
            </div>
            <div class="form-group mb-3">
                <input type="password" name="password" placeholder="Password" class="form-control">
            </div>
            <div class="form-group mb-3">
                <input type="password" name="confirmpassword" placeholder="Confirm Password" class="form-control">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Sign Up</button>
                <br />
                <div class="d-flex justify-content-end">
                    or &nbsp;&nbsp;
                    <a href="<?= site_url('sign-in') ?>">Sign In</a>
                </div>
            </div>
            <?php if (!empty($error)) : ?>
                <div class="text-danger"><?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($is_mail_sent) && $is_mail_sent) : ?>
                <div class="text-success">
                    Verification link has been sent to your email.
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<style>
    #sign-up-container {
        border: 1px solid #f2f2f2;
        padding: 3rem;
        width: 30rem;
    }
</style>
<?= $this->endSection() ?>