<script>

<?php 
		$ajax_nonce = wp_create_nonce( "my-special-string" );
		//$postId = isset($_GET['post']) ? $_GET['post'] : null;
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

<!-- ptd version 1.0 -->
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
		<div class="getTable ptd-column" ng-attr-style="width:{{100/pTable.columns.length}}%;min-width:140px;" ng-repeat="column in pTable.columns track by column.id">
			<ul class="pricing-table">
				<!-- Row <input type="text" ng-model="row.text"/> -->
				<li class="title" ng-bind-html="column.title"></li>
				<li class="price" ng-bind-html="column.price"></li>
				<li ng-repeat="row in column.rows track by row.id" class="bullet-item">
				<span ng-bind-html="row.text" class="rowspan"></span>
					
				</li>
				<li class="cta-button" ><a class="btn" ng-href="{{column.cta.ctaUrl}}">{{column.cta.ctaText}}</a></li>
			</ul>
		
		</div>
	</div>



</div>
<div class="clear">
</div>