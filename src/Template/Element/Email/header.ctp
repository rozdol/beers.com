<?php
use Cake\Core\Configure;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Set up a new password for [Product Name]</title>
        <!-- 
        The style block is collapsed on page load to save you some scrolling.
        Postmark automatically inlines all CSS properties for maximum email client 
        compatibility. You can just update styles here, and Postmark does the rest.
        -->
    </head>
    <body>
        <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <table class="email-content" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="email-masthead">
                                <a href="<?= getenv('PROJECT_URL') ?>" class="email-masthead_name">
                                    <?= Configure::read('Theme.logo.mini'); ?>
                                </a>
                            </td>
                        </tr>
                        <!-- Email Body -->
                        <tr>
                            <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0">
