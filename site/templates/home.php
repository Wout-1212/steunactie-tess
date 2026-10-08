<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?= css('assets/css/app.css') ?>
</head>

<body>

</body>

</html>


<div class="mx-auto max-w-3xl px-6 py-8 md:px-8 md:py-16">
    <h1 class="text-3xl font-semibold leading-tight md:text-4xl">
        <?= $page->title()->esc() ?>
    </h1>

    <div class="mt-6 space-y-4 text-sm leading-relaxed md:text-base">
        <?= $page->text()->kt() ?>
    </div>
</div>