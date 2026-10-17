<?php
// Section heading on the homepage.
// $text:   the heading text (escaped here)
// $id:     optional id, for aria-labelledby
// $margin: space below the heading (default 18px)
$margin ??= 'mb-[18px]';
?>
<h2<?= isset($id) ? ' id="' . esc($id, 'attr') . '"' : '' ?> class="<?= $margin ?> text-[25px] leading-[1.2] md:text-[clamp(26px,2.4vw,32px)]"><?= esc((string)$text) ?></h2>
