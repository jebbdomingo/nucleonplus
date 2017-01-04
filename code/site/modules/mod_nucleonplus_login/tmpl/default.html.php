<? if (object('user')->isAuthentic()): ?>
	<?
	$baseUrl = JURI::root();
    $token   = JSession::getFormToken();
    $return  = urlencode(base64_encode($baseUrl));
    ?>
    <div class="pull-right">
				Hello <?= object('user')->getName() ?>
				<small>
					<a href="<?= JRoute::_("index.php?option=com_users&task=user.logout&{$token}=1&return={$return}") ?>"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a>
				</small>
	</div>
<? else: ?>
	<div class="row">
		<div class="col-sm-8 col-xs-12 pull-right">
			<a href="login.html" class="btn btn-primary btn-block" role="button">
				<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Login
			</a>
		</div>
	</div>
<? endif ?>