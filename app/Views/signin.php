<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>


<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v15.0&appId=1160545601517955&autoLogAppEvents=1" nonce="h6BnB5iz"></script>

<div class="d-flex justify-content-center content-container">
    <div id="sign-in-container" class="d-flex flex-column">

        <?php if (session()->getFlashdata('msg')) : ?>
            <div class="text-danger" id="login-error">
                <?= session()->getFlashdata('msg') ?>
            </div>
        <?php endif; ?>


        <div>
            <form action="<?= base_url('sign-in-local'); ?>" method="post">
                <div class="form-group mb-3">
                    <input type="email" name="email" placeholder="Email" value="<?= set_value('email') ?>" class="form-control" autocomplete="email">
                </div>
                <div class="form-group mb-3">
                    <input type="password" name="password" placeholder="Password" class="form-control" autocomplete="current-password">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Sign In</button>
                    <div class="d-flex justify-content-end">
                        or &nbsp;&nbsp;
                        <a href="<?= base_url('sign-up'); ?>">Sign Up</a>
                    </div>
                </div>

                <div class="third-party-btn-container">
                    <div class="login-btn-wrapper">
                        <div id="g_id_onload" data-client_id="751484543020-kpcvh5rdi661dcaqmee3a7p8ca5p5r64.apps.googleusercontent.com" data-login_uri="<?= site_url('sign-in-google')?>" data-auto_prompt="false">
                        </div>
                        <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left">
                        </div>
                    </div>

                    <script>
                        function statusChangeCallback(response) {
                            if (response.status === 'connected') {
                                forwardLogin(response);
                            }
                        }

                        function checkLoginState() {
                            FB.getLoginStatus(function(response) {
                                statusChangeCallback(response);
                            });
                        }

                        window.fbAsyncInit = function() {
                            FB.init({
                                appId: '1160545601517955',
                                cookie: true,
                                xfbml: true,
                                version: 'v15.0'
                            });
                        };

                        function forwardLogin(response) {
                            $.post('/facebook-forward', {
                                accessToken: response.authResponse.accessToken,
                                userID: response.authResponse.userID
                            }, function(data) {
                                if (data.errCode !== 0) {
                                    alert('Sign in failed: ' + data.errMsg)
                                    return
                                }
                                window.location = data.to || "<?= site_url('/') ?>"
                            }, 'json')
                        }
                    </script>

                    <div class="login-btn-wrapper">
                        <div class="fb-login-button" data-width="" data-size="large" data-scope="public_profile,email" data-button-type="login_with" data-layout="rounded" data-auto-logout-link="false" data-use-continue-as="false" onlogin="checkLoginState();"></div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<style>
    #sign-in-container {
        border: 1px solid #f2f2f2;
        padding: 3rem;
        width: 30rem;
    }

    .third-party-btn-container {
        margin-top: 2rem;
        width: 15rem;
    }

    .login-btn-wrapper {
        margin-bottom: .8rem;
    }
</style>
<?= $this->endSection() ?>