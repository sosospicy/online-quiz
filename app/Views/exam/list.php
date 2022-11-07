<?php

?>
<?= $this->extend('layout/default') ?>
<?= $this->section('content') ?>
<div id="list-exam-container" class="content-container">
    <a class="btn btn-primary" href="/create">Create New Exam</a>
    <?php if (empty($list)) : ?>
        <p class="alert">
            No Data.
        </p>

    <?php else : ?>
        <table class="table table-hover element-pc">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Quiz Link</th>
                    <th scope="col">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $item) : ?>
                    <tr>
                        <th><?= $item['title'] ?></th>
                        <td>
                            <a target="_blank" href="<?= site_url('quiz/' . $item['code']) ?>">open quiz</a>
                            <button type="button" class="btn btn-outline-primary btn-sm copy-link" data-link="<?= site_url('quiz/' . $item['code']) ?>">
                                <i class="bi bi-clipboard"></i> copy
                            </button>
                            <!-- <button type="button" class="btn btn-outline-primary btn-sm share-link" data-link="<?= site_url('quiz/' . $item['code']) ?>">
                                <i class="bi bi-share"></i> share
                            </button> -->
                        </td>
                        <td><?= $item['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="list-group element-mobile">
            <?php foreach ($list as $item) : ?>
                <div class="list-group-item list-group-item-action" aria-current="true">
                    <div class="d-flex w-100">
                        <div class="mb-1">
                            <h6><?= $item['title'] ?></h6>
                            <p class="sub-info">created at: <?= $item['created_at'] ?></p>
                            <a href="<?= site_url('quiz/' . $item['code']) ?>">open quiz</a>&nbsp;&nbsp;
                            <button type="button" class="btn btn-outline-primary btn-sm copy-link" data-link="<?= site_url('quiz/' . $item['code']) ?>">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-outline-primary btn-sm share-link" data-link="<?= site_url('quiz/' . $item['code']) ?>">
                                <i class="bi bi-share"></i>
                            </button> -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="toast-container position-fixed bottom-1 end-0 p-3">
    <div id="copied-toast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Quiz link copied!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $.when($.ready).then(function() {
        const toast = new bootstrap.Toast($('#copied-toast'))

        $('.copy-link').on('click', function() {
            var copyText = $(this).data('link')

            if (navigator.clipboard) {
                navigator.clipboard.writeText(copyText).then(function() {
                    // toast.show()
                    alert('copied')
                })
            } else {
                if (fallbackCopyTextToClipboard(copyText)) {
                    // toast.show()
                    alert('copied')
                }
            }
        })

        if (!navigator.share) {
            $('.share-link').hide()
        }

        $('.share-link').on('click', function() {
            var link = $(this).data('link')
            navigator.share(link)
        })
    })

    function fallbackCopyTextToClipboard(text) {
        var textArea = document.createElement("textarea");
        textArea.value = text
        textArea.style.top = "0"
        textArea.style.left = "0"
        textArea.style.position = "fixed"

        document.body.appendChild(textArea)
        textArea.focus()
        textArea.select()
        var ok = false
        try {
            ok = document.execCommand('copy')
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err)
        }

        document.body.removeChild(textArea)
        return ok
    }
</script>


<?= $this->endSection() ?>