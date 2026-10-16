<?php
/**
 * Shared frame for HTML emails: header with the site name, white card for the content, muted footer.
 * Inline styles and tables only, because many mail clients ignore <style> and modern CSS.
 *
 * @var string $slot   Email content
 * @var string $footer Plain footer text (escaped here)
 */
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= site()->title()->esc() ?></title>
</head>

<body style="margin:0;padding:0;background-color:#F5D7E6;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#F5D7E6" style="background-color:#F5D7E6;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                    style="max-width:600px;background-color:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:#27222A;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #F5D7E6;color:#C43B75;font-size:22px;font-weight:bold;">
                            <?= site()->title()->esc() ?></td>
                    </tr>
                    <tr>
                        <td style="padding:24px;"><?= $slot ?></td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;border-top:1px solid #F5D7E6;font-size:12px;color:#6E6672;">
                            <?= esc($footer ?? '') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
