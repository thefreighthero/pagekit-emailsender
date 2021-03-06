<?php
/**
 * @var string $mailContent;
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style type="text/css">

		/*
		 * Client-specific
		 */

		/* Force Outlook to provide a "view in browser" message */
		#outlook a { padding: 0; }

		/* Force Hotmail to display emails at full width */
		.ReadMsgBody,
		.ExternalClass { width: 100%; }

		/* Force Hotmail to display normal line spacing */
		.ExternalClass,
		.ExternalClass p,
		.ExternalClass span,
		.ExternalClass font,
		.ExternalClass td,
		.ExternalClass div { line-height: 100%; }

		/* Prevent WebKit and Windows mobile changing default text sizes */
		body, table, td, p, a, li, blockquote {
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
		}

		/* Remove spacing between tables in Outlook 2007 and up */
		table, td {
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
		}

		/* Help Microsoft platforms smoothly render resized images */
		img { -ms-interpolation-mode: bicubic; }

		/* Hotmail header color reset */
		h1, h2, h3, h4, h5, h6 { color: #333 !important; }
		h1 a, h2 a, h3 a, h4 a, h5 a, h6 a { color: #333 !important; }
		h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active { color: #333 !important; }
		h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited { color: #333 !important; }

		/*
		 * Reset
		 */

		body, #body-table, #body-cell {
			width: 100% !important;
			height: 100% !important;
			margin: 0;
			padding: 0;
		}

		table { border-collapse: collapse !important; }

		/*
		 * Note: All custom styles are inline to support Gmail
		 */

		/*
		 * Mobile
		 * All declarations have to be important to override the inline styles
		 */

		@media (max-width: 480px) {

			#panel-table {
				max-width: 480px !important;
				width: 100% !important;
			}

		}

	</style>
</head>
<body>

<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" id="body-table" style="background-color:#fff;">
	<tr>
		<td valign="top" align="center" id="body-cell" style="padding: 20px 10px">

			<table cellpadding="0" cellspacing="0" border="0" width="600" id="panel-table">
				<tr>
					<td valign="top" align="left" style="font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666;">

						<?= $mailContent; ?>

					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>

</body>
</html>
