<?php if (@$gsExport == "") { ?>
<?php if (@!$gbSkipHeaderFooter) { ?>
				<!-- right column (end) -->
				<?php if (isset($gTimer)) $gTimer->Stop() ?>
			</div>
		</div>
	</div>
	<!-- content (end) -->
	<!-- footer (begin) --><!-- ** Note: Only licensed users are allowed to remove or change the following copyright statement. ** -->
	<div id="ewFooterRow" class="ewFooterRow">	
		<div class="ewFooterText"><?php echo base64_decode("wqkyMDI0IERldmVsb3BtZW50IGJ5IFNhZWVkIE5vYmFraHQ="); ?></div>
		<!-- Place other links, for example, disclaimer, here -->		
	</div>
	<!-- footer (end) -->	
</div>
<?php } ?>
<!-- search dialog -->
<div id="ewSearchDialog" class="modal" role="dialog" aria-labelledby="ewSearchDialogTitle" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="ewSearchDialogTitle"></h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $Language->Phrase("Search") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal"><?php echo $Language->Phrase("CancelBtn") ?></button></div></div></div></div>
<!-- email dialog -->
<div id="ewEmailDialog" class="modal" role="dialog" aria-labelledby="ewEmailDialogTitle" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="ewEmailDialogTitle"></h4></div>
<div class="modal-body">
<?php include_once "ewemail12.php" ?>
</div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $Language->Phrase("SendEmailBtn") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal"><?php echo $Language->Phrase("CancelBtn") ?></button></div></div></div></div>
<!-- message box -->
<div id="ewMsgBox" class="modal" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton" data-dismiss="modal"><?php echo $Language->Phrase("MessageOK") ?></button></div></div></div></div>
<!-- prompt -->
<div id="ewPrompt" class="modal" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton"><?php echo $Language->Phrase("MessageOK") ?></button><button type="button" class="btn btn-default ewButton" data-dismiss="modal"><?php echo $Language->Phrase("CancelBtn") ?></button></div></div></div></div>
<!-- session timer -->
<div id="ewTimer" class="modal" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary ewButton" data-dismiss="modal"><?php echo $Language->Phrase("MessageOK") ?></button></div></div></div></div>
<!-- tooltip -->
<div id="ewTooltip"></div>
<?php } ?>
<?php if (@$gsExport == "") { ?>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/userevt12.js"></script>
<script type="text/javascript">

// Write your global startup script here
// document.write("page loaded");

</script>
<?php } ?>
<script async type="text/javascript">
$( document ).ready(function() {
	var element = window.location.hash;
	if (element && $(element).length) (
		$([document.documentElement, document.body]).animate({
			scrollTop: $(element).offset().top
		}, 1500)
	)
});
</script>
</body>
</html>
