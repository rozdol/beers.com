                                    <tr>
                                        <td class="content-cell">
                                            <p>Thanks,
                                             <br>The <?= $this->SystemInfo->getProjectName() ?> Team</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-cell" align="center">
                                            <p class="sub align-center"><?= $this->SystemInfo->getCopyright() ?></p>
                                            <p class="sub align-center">
                                            <?= $this->HtmlEmail->getFooterInfo() ?>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
