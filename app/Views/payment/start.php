<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>

<!-- advertisement page -->
<div id="charge-container" class="content-container">
    <section class="ad">We're the best!</section>
    <div class="btn-container d-flex justify-content-center">
        <?php if ($is_paid === TRUE) : ?>

            You already made a subscription. &nbsp;&nbsp;<a href="/exam">Manage Exams</a>

        <?php elseif (session()->is_logged_in) : ?>
        
            <button type="button" class="btn btn-primary" id="purchase-btn">Start free trial now!</button>
            <button id="loading-btn" class="btn btn-primary" type="button" disabled style="display: none;">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Loading ...
            </button>
        <?php else:?>
            <a href="<?= site_url('sign-in')?>" type="button" class="btn btn-primary">Start free trial now!</a>
        <?php endif; ?>
    </div>

</div>
<script type="text/javascript">
    $.when($.ready).then(function() {
        $('#purchase-btn').on('click', function() {
            $('#purchase-btn').hide()
            $('#loading-btn').show()
            $.post('/purchase', function(data) {
                if (data.errCode !== 0) {
                    alert(data.errMsg || 'server error')
                    $('#purchase-btn').show()
                    $('#loading-btn').hide()
                    return
                }

                window.location = data.url

            }, 'json').fail(function() {
                alert('Service not available. Please ask administrator for help.')
                $('#purchase-btn').show()
                $('#loading-btn').hide()
            })
        })
    })
</script>

<style>
    .ad {
        width: 100%;
        height: 20rem;
        border: 1px solid #f2f2f2;
        margin-bottom: 3rem;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all .2s;
    }

    .ad:hover {
        box-shadow: 0 0 5px rgba(0, 0, 0, .3);
    }
</style>
<?= $this->endSection() ?>