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
require_once __DIR__."/resources/utils.php";
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<b>Provisioning Tokens</b>
					</td>
					<td width="30%" align="right" valign="top">
						<a href="edit.php" class="btn">Add</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table class="tr_hover" width="100%" border="0" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th>Extension</th>
						<th>Domain</th>
						<th>Type</th>
						<th>Actions</th>
					</tr>
				</thead>
			<?php
			//SELECT v_extensions.extension_uuid,v_extensions.extension,v_domains.domain_name FROM v_extensions FULL JOIN v_domains ON v_extensions.domain_uuid = v_domains.domain_uuid
			$rowclass = "row_style0";
			foreach(do_sql($db, "SELECT better_provisioning_tokens.token_uuid, better_provisioning_tokens.type, better_provisioning_tokens.secret, v_extensions.extension, v_extensions.extension_uuid, v_domains.domain_name FROM better_provisioning_tokens JOIN v_extensions ON better_provisioning_tokens.extension = v_extensions.extension_uuid JOIN v_domains ON v_extensions.domain_uuid = v_domains.domain_uuid;") as $token) {
				echo "<tr>";
				echo "<td class=\"$rowclass\"><a href=\"/app/extensions/extension_edit.php?id=".$token['extension_uuid']."\">".$token['extension']."</a></td>";
				echo "<td class=\"$rowclass\">".$token['domain_name']."</td>";
				echo "<td class=\"$rowclass\">".$token['type']."</td>";
				echo "<td class=\"$rowclass\">[<a href=\"provision.php?secret=".$token['secret']."\" onclick=\"prompt('This is the provisioning URL', this.href); return false;\">Send</a>] [<a href=\"edit.php?token_uuid=".$token['token_uuid']."\">Edit</a>]</td>";
				echo "</tr>";
				if($rowclass == "row_style0") {
					$rowclass = "row_style1";
				} else {
					$rowclass = "row_style0";
				}
			}
			?>
			</table>
		</td>
	</tr>
</table>
<?php
require_once "footer.php";
