<script>

<?php 
		$ajax_nonce = wp_create_nonce( "my-special-string" );

		$postId = isset($_GET['post']) ? $_GET['post'] : null;
		if($postId){
			$thePost = get_post($postId);
			//echo $thePost ->post_type;
			$ptd_table_data = get_post_meta( $postId, 'data', true );
			//echo $ptd_table_data;
?>
		

		var initColumns = <?php echo $ptd_table_data; ?>;
		var postId = <?php echo $postId; ?>;
		app.value("postId" , postId);
		app.value("title", "<?php echo $thePost->post_title; ?>");
		
	
		
<?php
		} else{ 
?>
			var initColumns = {
				"columns" : [
					{
						"id"	: "0",
						"title" : "Column",
						"price" : "$29",
						"cta"	: {"ctaText":"Buy", ctaUrl:"", "ctaTemp":'', "isCtaModal":'false'},
						"rows" : [
							{ "id" : "", "name": "", "text": "feature 1"}
						]
					}
				]
			};
			app.value("postId" , "");
			app.value("title", "");
<?php
			
		} //end php else
?>
		app.value("columns", initColumns);
		var ajaxNonce = '<?php echo $ajax_nonce; ?>';
		app.value("ajaxNonce", ajaxNonce);
		
		
</script>

<div>
	Create or edit your pricing table
</div>

<div id="ptd-container" ng-app="ptd-app" ng-controller="MainCtrl">
<style>
	@media screen and (min-width: 501px) {
		.ptd-column{
			float:left;
			//width:{{100/pTable.columns.length}}%
		}
	}
</style>

	<div>
		<div style="margin-bottom:10px">
			Title<input type="text" ng-model="title"/>
		</div>
		<div style="margin-bottom:10px">
			[pricing_table_dynamite tableid="{{postId}}"]
		</div>
		<div class="getTable ptd-column" ng-attr-style="width:{{100/pTable.columns.length}}%;min-width:140px;" ng-repeat="column in pTable.columns track by column.id">
			<ul class="pricing-table">
				<!-- Row <input type="text" ng-model="row.text"/> -->
				<li class="title"><span contenteditable ng-model="column.title" ng-blur="checkEmptyTitle(column)" ng-focus="clearHolderSpacesTitle(column)"></span></li>
				<li class="price"><span contenteditable ng-model="column.price" ng-blur="checkEmptyPrice(column)" ng-focus="clearHolderSpacesPrice(column)"></span></li>
				<li ng-repeat="row in column.rows track by row.id" class="bullet-item">
					<span contenteditable ng-model="row.text" ng-blur="checkEmpty(row)" ng-focus="clearHolderSpaces(row)" class="rowspan">{{row.text}}</span><br /><a ng-click="removeRow($index, column)">Delete</a>
				</li>
				<li class="cta-button" ><a class="btn" contenteditable ng-model="column.cta.ctaText" ng-dblclick="editInput(column.cta)">Buy</a></li>
				<modal-dialog show='column.cta.isCtaModal' dialog-title='Edit Call To Action Button'>
				  <p>Call To Action Url</p>
				  <input type="text" ng-model="column.cta.ctaTemp" />
				  <button ng-click="saveEdit(column.cta)">Save</button>
				  <button ng-click="cancelEdit(column.cta)">Cancel</button>
				</modal-dialog>
				
			</ul>
			<button ng-click="addRow(column)">Add Row</button>
			<button ng-click="addColumn($index + 1)">Add Column</button>
			<button ng-click="removeColumn($index)">Remove Column</button>
			
			
		</div>
		
		<div style="clear:both; margin-top:30px">
			<button ng-click="submitTable()">Save</button>
			<!--<button ng-click="getHtml()">Get Html</button>-->
		</div>	
	
	</div>



</div>
<div class="clear">
</div>