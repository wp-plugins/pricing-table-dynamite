var app = angular.module('ptd-app', ["ngModal", 'ngSanitize']);

app.controller('MainCtrl', function($scope, $http, columns, postId, title, ajaxNonce) {
	
	$scope.pTable = columns;
	
	$scope.postId = postId;
	
	$scope.title = title;
	$scope.ajaxNonce = ajaxNonce
	
	$scope.addRow = function(column){
		column.rows.push({id:Math.random().toString(36).substr(2, 9), name:'', text:'feature'});
	}
	
	$scope.removeRow = function(index, column) { 
	  	column.rows.splice(index, 1);     
	}
	
	$scope.addColumn = function(id){
		//$scope.pTable.columns.push({id:id, title:'Column', price:'$19', cta:{ctaText:'Buy', ctaUrl:'', ctaTemp:'', isCtaModal:'false'}, rows:[{ id : "", name: "", text: "feature 1"}]});
		$scope.pTable.columns.push({id:Math.random().toString(36).substr(2, 9), title:'Column', price:'$19', cta:{ctaText:'Buy', ctaUrl:'', ctaTemp:'', isCtaModal:'false'}, rows:[{ id : "", name: "", text: "feature 1"}]});
	}
	
	$scope.removeColumn = function(index){
		$scope.pTable.columns.splice(index, 1);
	  }
	  
	  function handleError( response ) {
	  	alert('error');
	  }
	  
	   function handleSuccess( response ) {
 
		alert( response.data );
		 
	  }
	  
	$scope.editUrl = function(){
		alert("edit url");
	}
	
	$scope.submitTable = function(){
		var tableString = JSON.stringify($scope.pTable)
		//alert (tableString)
		//alert('testing');
		/*
		var data = {
			action: 'test_response',
                        post_var: JSON.stringify($scope.pTable)
		};
		 var request = $http({
                          method: 'post',
                          url:the_ajax_script.ajaxurl,
                                      data:{
			action: 'test_response',
                        post_var: JSON.stringify($scope.pTable)
		},
                                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                      });
                 request.then( handleSuccess, handleError )
                */
                var data = {
			action: 'test_response',
                        post_var: JSON.stringify($scope.pTable),
                        title: $scope.title,
                        postId: $scope.postId,
                        security: $scope.ajaxNonce
		};
		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	jQuery.post(the_ajax_script.ajaxurl, data, function(response) {
			//alert(response);
			alert("Your table was saved.");
			//$scope.pTable = response;
			$scope.postId = response;
	 	});
	 	return false; 
	}
	
	
	$scope.checkEmpty = function(row){
		//alert (row.text);
		//row.text = "goodbye";
	
		
		if(row.text == '' || /^(<br>)+$/.test(row.text)){
			row.text = "-";
		}
		
	}
	
	$scope.clearHolderSpaces = function(row){
		if(row.text == '-'){
			row.text = '';
		}
	}
	
	$scope.checkEmptyTitle = function(column){
		//if(column.title == '' || column.title == '<br>'){
		if(column.title == '' || /^(<br>)+$/.test(column.title)){
			column.title = "-";
		}
		
	}
	
	$scope.clearHolderSpacesTitle = function(column){
		if(column.title == '-'){
			column.title = '';
		}
	}
	
	$scope.checkEmptyPrice = function(column){
		if(column.price == '' || /^(<br>)+$/.test(column.price)){
			column.price = "-";
		}
		
	}
	
	$scope.clearHolderSpacesPrice = function(column){
		if(column.price == '-'){
			column.price = '';
		}
	}
	
	$scope.getHtml = function(){
		//alert(angular.element("html")[0].outerHTML);
		var tableArray = angular.element(".pricing-table");
		var tableString = '';
		//tableString = tableString + angular.element("head")[0].outerHTML
		//tableString = tableString + <body>
		angular.forEach(tableArray, function(value, key) {
			tableString = tableString + '<div style="float:left">';
			tableString = tableString + value.outerHTML;
			tableString = tableString + '</div>';
		})
		
		alert(tableString);
	}
	
	
	$scope.dialogShown = false;
	  $scope.toggleModal = function() {
	  $scope.dialogShown = !$scope.dialogShown;
	};
	
	
	
	
	$scope.modalTemp = {"temp" : ''};
	$scope.modalEditing = {"editing": ''};
	
	$scope.editInput = function(inputElement){
		inputElement.isCtaModal = true;
		//$scope.toggleModal();
		//$scope.modalEditing.editing = inputElement.ctaText;
		//$scope.modalTemp.temp = inputElement.ctaText;
		//console.log($scope.modalTemp.temp);
		
		inputElement.ctaTemp = inputElement.ctaUrl;
	}
	
	
	
	$scope.saveEdit = function(inputElement){
		//inputElement.ctaText = $scope.modalEditing.editing;
		//$scope.modalTemp.temp = '';
		//$scope.modalEditing.editing = '';
		inputElement.ctaUrl= inputElement.ctaTemp;
		inputElement.ctaTemp = '';
		inputElement.isCtaModal = false;
		//$scope.toggleModal();
		//console.log($scope.modalTemp.temp);
		
	}
	
	$scope.cancelEdit = function(inputElement){
		//inputElement.ctaUrl= $scope.modalTemp.temp;
		
		//$scope.modalTemp.temp = '';
		//$scope.modalEditing.editing = '';
		inputElement.ctaTemp = '';
		inputElement.isCtaModal = false;
		//$scope.toggleModal();
		
	}
	
	
	
});



app.directive("contenteditable", function() {
  return {
    restrict: "A",
    require: "ngModel",
    link: function(scope, element, attrs, ngModel) {

      function read() {
        ngModel.$setViewValue(element.html());
      }

      ngModel.$render = function() {
        element.html(ngModel.$viewValue || "");
      };

      element.bind("blur keyup change", function() {
        scope.$apply(read);
      });
    }
  };
});