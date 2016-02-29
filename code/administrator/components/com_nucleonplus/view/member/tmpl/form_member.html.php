<form method="post" class="-koowa-form">

    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Member Details'); ?></h3>
        </div>

        <table class="table">

            <tbody>
                <tr>
                    <td><label><strong><?= translate('Name') ?></strong></label></td>
                    <td>
                        <input name="name" id="name" value="<?= $member->name ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Username') ?></strong></label></td>
                    <td>
                        <input name="username" id="username" value="<?= $member->username ?>" />
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

</form>