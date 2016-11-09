<?php
/**************************************************
	Basic Setting
**************************************************/
	include "configuration.php";
	include "include/htmlheader.php";
	
	$DW_PageControl = "MAIN";
	$CurrPageName = "index.php";
/**************************************************
	End Basic Setting
**************************************************/
echo "GGccccccccczzzz";
echo dware_location_href ( "delivery_sheet_management.php" );
exit;

/************************** Function Setting **************************/
/* Incomplete Record */
// echo "<br />".
// $Sql_Select = "SELECT d.*, de.tablestr, de.consignment, de.collection_date FROM `".TABLE_DELIVERYSHEET_EXPRESS."` d INNER JOIN `".TABLE_DELIVERYSHEET."` de ON (de.delivery_id=d.delivery_id AND de.active=1) WHERE d.active = 1 AND de.team = '".$_LoginUser_Department_."' AND (de.collection_date = '0000-00-00' OR (de.collection_date <> '0000-00-00' AND d.sales_status <> 'APPROVE')) UNION SELECT d.*, de.tablestr, de.consignment, de.collection_date FROM `".TABLE_DELIVERYSHEET_NORMAL."` d INNER JOIN `".TABLE_DELIVERYSHEET."` de ON (de.delivery_id=d.delivery_id AND de.active=1) WHERE d.active = 1 AND de.team = '".$_LoginUser_Department_."' AND (de.collection_date = '0000-00-00' OR (de.collection_date <> '0000-00-00' AND d.sales_status <> 'APPROVE')) ORDER BY consignment, created";

/* NS Record */
// $Sql_Select = "SELECT d.*, de.tablestr, de.consignment, de.collection_date FROM `".TABLE_DELIVERYSHEET_NS."` d INNER JOIN `".TABLE_DELIVERYSHEET."` de ON (de.delivery_id=d.delivery_id AND de.active=1) LEFT JOIN `".TABLE_AGREEMENT_CODE."` c ON (c.agreementcode_id=d.agreementcode_id) WHERE d.active = 2 AND de.team = '".$_LoginUser_Department_."' ORDER BY de.consignment, de.created";

/* Pending Record */
$Sql_Select = "SELECT d.*, de.tablestr, de.consignment, de.collection_date FROM `".TABLE_DELIVERYSHEET_EXPRESS."` d INNER JOIN `".TABLE_DELIVERYSHEET."` de ON (de.delivery_id=d.delivery_id AND de.active=1) INNER JOIN ".TABLE_BRANCH." em ON (em.branch_name=de.team) LEFT JOIN `".TABLE_AGREEMENT_CODE."` c ON (c.agreementcode_id=d.agreementcode_id) WHERE d.active = 1 AND d.sales_status = 'PENDING' AND de.collection_date <> '0000-00-00' AND de.team = '".$_LoginUser_Department_."' $FilterStr UNION SELECT d.*, de.tablestr, de.consignment, de.collection_date FROM `".TABLE_DELIVERYSHEET_NORMAL."` d INNER JOIN `".TABLE_DELIVERYSHEET."` de ON (de.delivery_id=d.delivery_id AND de.active=1) INNER JOIN ".TABLE_BRANCH." em ON (em.branch_name=de.team) LEFT JOIN `".TABLE_AGREEMENT_CODE."` c ON (c.agreementcode_id=d.agreementcode_id) WHERE d.active = 1 AND d.sales_status = 'PENDING' AND de.collection_date <> '0000-00-00' AND de.team = '".$_LoginUser_Department_."' $FilterStr $OrderStr";
$Rst_Select = mysql_query($Sql_Select);
while ( $RowData = mysql_fetch_array($Rst_Select) ) {
	$Info_Status = "";
	$Info_ICNo = $RowData[icno];
	$Info_OldICNo = $RowData[oldicno];
	$Info_ICNoDisplay = ( empty($Info_ICNo) && empty($Info_OldICNo) ? "No IC Number Record" : ( !empty($Info_ICNo) && !empty($Info_OldICNo) ? $Info_ICNo . ", ".$Info_OldICNo : ( !empty($Info_ICNo) ? $Info_ICNo : $Info_OldICNo ) ) );
	if ( $RowData[collection_date] != "0000-00-00" && !empty($RowData[sales_status]) ) $Info_Status = "<br /><font color='#FF0000'>Application Status: ".dware_ucfirst_format($RowData[sales_status])."</font>";
	
	$LogsData["INFO"]["INCOMPLETED"][ $RowData[delivery_id] . "_" . $RowData[sheet_id] ]["INFO"] = dware_form_decision( $RowData[tablestr] ) . ": " . $RowData[consignment] . ", IC: ".$Info_ICNoDisplay . $Info_Status;
	$LogsData["INFO"]["INCOMPLETED"][ $RowData[delivery_id] . "_" . $RowData[sheet_id] ]["AHREF"] = "report_form_pending.php";
	$LogsData["INFO"]["INCOMPLETED"][ $RowData[delivery_id] . "_" . $RowData[sheet_id] ]["DELIVERYID"] = $RowData[delivery_id];
}

$Sql_Select = "SELECT d.*, de.tablestr, de.consignment FROM `".TABLE_DELIVERYSHEET_EXPRESS."` d INNER JOIN `".TABLE_DELIVERYSHEET."` de ON (de.delivery_id=d.delivery_id AND de.active=1) WHERE d.active = 1 AND de.team = '".$_LoginUser_Department_."' AND de.collection_date <> '0000-00-00' AND d.sales_status = '' UNION SELECT d.*, de.tablestr, de.consignment FROM `".TABLE_DELIVERYSHEET_NORMAL."` d INNER JOIN `".TABLE_DELIVERYSHEET."` de ON (de.delivery_id=d.delivery_id AND de.active=1) WHERE d.active = 1 AND de.team = '".$_LoginUser_Department_."' AND de.collection_date <> '0000-00-00' AND d.sales_status = '' ORDER BY consignment";
$Rst_Select = mysql_query($Sql_Select);
while ( $RowData = mysql_fetch_array($Rst_Select) ) {
	$Info_ICNo = $RowData[icno];
	$Info_OldICNo = $RowData[oldicno];
	$Info_ICNoDisplay = ( empty($Info_ICNo) && empty($Info_OldICNo) ? "No IC Number Record" : ( !empty($Info_ICNo) && !empty($Info_OldICNo) ? $Info_ICNo . ", ".$Info_OldICNo : ( !empty($Info_ICNo) ? $Info_ICNo : $Info_OldICNo ) ) );
		
	$LogsData["INFO"]["NOTREPLY"][ $RowData[delivery_id] . "_" . $RowData[sheet_id] ]["INFO"] = dware_form_decision( $RowData[tablestr] ) . ": " . $RowData[consignment] . ", IC: ".$Info_ICNoDisplay;
	$LogsData["INFO"]["NOTREPLY"][ $RowData[delivery_id] . "_" . $RowData[sheet_id] ]["AHREF"] = "delivery_sheet_control.php";
	$LogsData["INFO"]["NOTREPLY"][ $RowData[delivery_id] . "_" . $RowData[sheet_id] ]["DELIVERYID"] = $RowData[delivery_id];
}
/************************ End Function Setting ************************/

// echo "<pre>";
// print_r($LogsData["INFO"]["INCOMPLETED"]);
// echo "</pre>";

?>
<form id="form" name="form" action="" method="post">
	<table align="center" class="BorderDesign" height="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<?php include "include/midline.php"; ?>
		<td width="100%" class="mid_color" valign="top">
		
	<?php /*************** Start Contant Design ***************/ ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr><td align="left" class="header_design1">
			<table width="100%" border="0">
				<tr>
				<td align="left" class="header_design1"><strong></strong></td>
				</tr>
			</table>
		</td></tr>
		
		<tr><td>
			<table width="100%" border="0" align="center">
			<tr>
				<td width="48%" valign="top">
				<table class="BorderDesign" width="90%" align="center" cellspacing="3" cellpadding="3">
					<tr><td class="speTableDesHand">Pending delivery sheet</td></tr>
					
					<?php
						$i = 0;
						$ArrValue = $LogsData["INFO"]["INCOMPLETED"];
						if ( is_array($ArrValue) ) {
							foreach ($ArrValue as $ArrKey => $ArrVal) {
							    if( $i%2 == 0 ) $RowStyle = TABLE_DEFAULT_COLOR_2;
									else $RowStyle = TABLE_DEFAULT_COLOR_1;
								$i++;
								
							    $DisplayWord = $ArrVal["INFO"];
							    $AHref = $ArrVal["AHREF"];
							    $FormID = $ArrVal["DELIVERYID"];
								?><tr bgcolor='<?php echo $RowStyle; ?>'><td class="speTable" style="border-bottom: 1px solid #ccc;"><?php echo $DisplayWord;?>&nbsp;&nbsp;&nbsp;<a href="javascript:goto_Check('<?php echo $AHref; ?>', '<?php echo $FormID; ?>')"><font color="#0000FF">View</font></a></td></tr><?php
							}
						} else {
							?><tr bgcolor='<?php echo $RowStyle; ?>'><td class="speTable" style="border-bottom: 1px solid #ccc;">No Records Found</td></tr><?php
						}
					?>
		
				</table>
				</td>
				<td width="2%">&nbsp;</td>
				<td width="48%" valign="top">
				<table class="BorderDesign" width="90%" align="center" cellspacing="3" cellpadding="3">
					<tr><td class="speTableDesHand">Not Reply Delivery Sheet</td></tr>
					
					<?php
						$i = 0;
						$ArrValue = $LogsData["INFO"]["NOTREPLY"];
						if ( is_array($ArrValue) ) {
							foreach ($ArrValue as $ArrKey => $ArrVal) {
							    if( $i%2 == 0 ) $RowStyle = TABLE_DEFAULT_COLOR_2;
									else $RowStyle = TABLE_DEFAULT_COLOR_1;
								$i++;
								
							    $DisplayWord = $ArrVal["INFO"];
							    $AHref = $ArrVal["AHREF"];
							    $FormID = $ArrVal["DELIVERYID"];
								?><tr bgcolor='<?php echo $RowStyle; ?>'><td class="speTable" style="border-bottom: 1px solid #ccc;"><?php echo $DisplayWord;?>&nbsp;&nbsp;&nbsp;<a href="javascript:goto_Check('<?php echo $AHref; ?>', '<?php echo $FormID; ?>')"><font color="#0000FF">View</font></a></td></tr><?php
							}
						} else {
							?><tr bgcolor='<?php echo $RowStyle; ?>'><td class="speTable" style="border-bottom: 1px solid #ccc;">No Records Found</td></tr><?php
						}
					?>
		
				</table>
				</td>
			</tr>
			</table>
		</td></tr>
	
		<tr><td>&nbsp;</td></tr>
	</table>
	<?php /*************** End Contant Design ***************/ ?>
	
		</td>
	</tr>
	
	<input type="hidden" name="status_view" id="status_view" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<input type="hidden" name="backpage" id="backpage" value="main.php">
	
	</table>
</form>

<script>

function goto_Check( page_name, selected_id ) {
	$("#status_view").val("VIEW");
	$("#selected_id").val(selected_id);
	
	$("#form").attr("action", page_name);
	$('#form').submit();
}

</script>

<?php include "include/htmlfooter.php"; ?>