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
			$rowclass = "row_style0";
			foreach(do_sql($db, "SELECT token_uuid, type FROM better_provisioning_tokens") as $token) {
				echo "<tr href=\"edit.php?provision=".$row['token_uuid']."\">";
				echo "<td class=\"$rowclass\"></td>";
				echo "<td class=\"$rowclass\">".$row['type']."</td>";
				echo "<td class=\"$rowclass\">[<a href=\"send.php?provision=".$row['token_uuid']."\">Send</a>]</td>";
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
require_once "resources/footer.php";
