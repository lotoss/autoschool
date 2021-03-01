<h2>Cartellone</h2>
<div class="mt-1">
    <div class="mt-1">
        <?php foreach ($mems as $mem) : ?>
            <hr>
            <div class="row m-0">
                <?php foreach ($mem as $img) : ?>
                    <div class="mb-1 mr-1" style="width: 8%; min-width: 100px">
                        <img data-id="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" title="Immagine <?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" src="/img/segnali/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>.jpg" alt="Immagine <?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" class="shadow rounded cart-img w-100">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
    $(function() {
        $(".cart-img").click(function() {
            window.open(`?controller=cartelloneController&action=viewImg&id=${$(this).attr('data-id')}`);
        })
    })
</script>
<style>
    img[data-id] {
        cursor: pointer;
    }
</style>