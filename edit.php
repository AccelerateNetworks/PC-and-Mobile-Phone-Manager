<?php
/*
	GNU Public License
	Version: GPL 3
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";

//add multi-lingual support
$language = new text;
$text = $language->get();

//additional includes
require_once "resources/header.php";
require_once "resources/paging.php";
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/resources/utils.php";

use IcyApril\CryptoLib;

$extension = "";
$type = "linphone";
$secret = "hi";
$new_item = True;
$token_uuid;

$action_url = $_SERVER['PHP_SELF'];

if(count($_POST) > 0) {

	$extension = $_POST['extension'];
	$type = $_POST['type'];
	$secret = NULL;
	if(isset($_POST['secret'])) {
		$secret = $_POST['secret'];
	} else {
		$secret = CryptoLib::randomString(50);
	}
	$new_item = False;
	$token_uuid = NULL;
	$action = "Created";

	if($_POST['token_uuid']) {
		$token_uuid = $_POST['token_uuid'];
		do_sql($db,
			"UPDATE better_provisioning_tokens SET extension = :extension, type = :type, secret = :secret WHERE token_uuid = :token_uuid",
			array(':token_uuid' => $token_uuid, ':extension' => $extension, ':type' => $type, ':secret' => $secret)
		);
	} else {
		$token_uuid = uuid();
		do_sql($db,
			"INSERT INTO better_provisioning_tokens(token_uuid, extension, type, secret) VALUES (:token_uuid, :extension, :type, :secret)",
			array(':token_uuid' => $token_uuid, ':extension' => $extension, ':type' => $type, ':secret' => $secret)
		);
	}
} else {
	if(isset($_GET['token_uuid'])) {
		$token_uuid = $_GET['token_uuid'];
		$row = do_sql($db, "SELECT extension,type,secret FROM better_provisioning_tokens WHERE token_uuid = :token_uuid", array(':token_uuid' => $token_uuid));
		if(count($row) > 0) {
			$extension = $row[0]['extension'];
			$type = $row[0]['type'];
			$secret = $row[0]['secret'];
			$new_item = False;
			$action_url = $_SERVER['PHP_SELF']."?token_uuid=".sanatize($token_uuid);
		} else {
			$_SESSION['message'] = "No such token $token_uuid!";
		}
	}
}
?>
	<form method="post" action="<?php echo $action_url; ?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="70%" align="right" valign="top">
								<input type="button" class="btn" alt="Back" onclick="window.location='/app/better-provisioning'" value="Back">
								<input type="button" class="btn" alt="New" onclick="window.location='edit.php'" value="New">
								<?php if(!$new_item) {
									echo "<input type=\"button\" class=\"btn\" alt=\"Delete\" onclick=\"window.location='delete.php?token_uuid=".sanatize($item_uuid)."'\" value=\"Delete\">\n";
								} ?>
								<input type="submit" class="btn" value="Save">
								<br /><br />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<?php if(!$new_item) { ?>
						<input type="hidden" name="token_uuid" value="<?php echo sanatize($token_uuid) ?>" />
						<?php } ?>
						<tr>
							<td class="vncellreq" valign="top" align="left" nowrap="nowrap">Extension</td>
							<td class="vtable" valign="top" align="left" nowrap="nowrap">
								<input type="text" name="extension" value="<?php echo sanatize($extension); ?>" class="extension" />
							</td>
						</tr>
						<tr>
							<td class="vncellreq" valign="top" align="left" nowrap="nowrap">Type</td>
							<td class="vtable" valign="top" align="left" nowrap="nowrap">
								<select type="text" name="type">
									<?php
									foreach(scandir(__DIR__."/provisioning") as $option) {
										if($option != "." && $option != "..") {
											$selected = "";
											if($option == $type) {
												$seleceted = " selected";
											}
											echo "<option value=\"".sanatize($option)."\"$selected>".sanatize($option)."</option>";
										}
									}
									?>
								</select>
							</td>
						</tr>
						<?php if(!$new_item) { ?>
						<tr>
							<td class="vncellreq" valign="top" align="left" nowrap="nowrap">Secret</td>
							<td class="vtable" valign="top" align="left" nowrap="nowrap">
								<input type="password" name="secret" class="secret" value="<?php echo sanatize($secret); ?>" onmouseover="this.setAttribute('type', 'text')" onmouseout="this.setAttribute('type', 'password')"/>
							</td>
						</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<script src="vendor/typeahead.js/dist/typeahead.bundle.min.js"></script>
	<script src="edit.js"></script>
	<?php
require "footer.php";
