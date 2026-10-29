<?php
/**
 * Shared frame for HTML emails on white: header with the site name, the content, footer.
 * Inline styles and tables only, because many mail clients ignore <style> and modern CSS.
 * Colours and font follow the site tokens in assets/css/tailwind.css.
 *
 * @var string      $slot    Email content
 * @var string      $footer  Plain footer text (escaped here)
 * @var string|null $preview Plain inbox preview text, hidden in the email itself (escaped here)
 * @var string|null $lang    Language code of the email
 */
?>
<!DOCTYPE html>
<html lang="<?= esc($lang ?? 'nl', 'attr') ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Light only: stops mail apps from recolouring the pink in dark mode. -->
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title><?= site()->title()->esc() ?></title>
    <!--[if mso]><style>table, td { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]-->
</head>

<body style="margin:0;padding:0;background-color:#FFFFFF;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
    <?php if (!empty($preview)): ?>
        <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;"><?= esc($preview) ?></div>
    <?php endif ?>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF" style="background-color:#FFFFFF;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <!-- Outlook on Windows ignores max-width, so it gets a fixed 600px table instead. -->
                <!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF"
                    style="max-width:600px;background-color:#FFFFFF;font-family:Montserrat,Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:#27222A;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #F5D7E6;color:#C43B75;font-size:22px;font-weight:bold;line-height:1.2;">
                            <?= site()->title()->esc() ?></td>
                    </tr>
                    <tr>
                        <td style="padding:24px;"><?= $slot ?></td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;border-top:1px solid #F5D7E6;font-size:12px;color:#27222A;">
                            <?= esc($footer ?? '') ?></td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>

</html>
