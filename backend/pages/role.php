<div class="alert alert-warning" id="hintDiv" style="margin-top: -90px;">
   <strong>{{tip}}</strong>
</div>

<header class="nav-top">
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand">Questionaire Backend Configuration Management</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#/list"><i class="glyphicon glyphicon-th-list"></i> Questionaire List</a></li>
        <li><a href="#/create"><i class="glyphicon glyphicon-file"></i> Create Questionaire</a></li>
        <li><a href='#/' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        <li><a>Welcome, <?php session_start(); $name = $_SESSION['username'] == '' ? '<script>window.localStorage.setItem("isLogin", "false");window.location.href = "#/";</script>' : $_SESSION['username']; echo $name; ?></a></li>
      </ul>
    </div>
  </nav>
</header>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" 
               data-dismiss="modal" aria-hidden="true">
                  &times;
            </button>
            <h4 class="modal-title" id="myModalLabel">
               Delete
            </h4>
         </div>
         <div class="modal-body">
            确认删除?
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="button" class="btn btn-danger" ng-click="deleteAdmin()">确定</button>
         </div>
      </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->

<div class="modal fade" id="operatorModal" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" 
               data-dismiss="modal" aria-hidden="true">
                  &times;
            </button>
            <h4 class="modal-title" id="myModalLabel">
               Delete
            </h4>
         </div>
         <div class="modal-body">
            确认删除?
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="button" class="btn btn-danger" ng-click="deleteOperator()">确定</button>
         </div>
      </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->

<div style="margin:-136px 50px 10px 50px;" ng-if="currentRole != 'brand_oprator'">
	<ul class="nav nav-tabs" ng-if="currentRole == 'system_admin'">
	  <li class="active"><a href="javascript:void(0)" ng-click="switchAdmin()" data-toggle="tab">admin</a></li>
	  <li ><a href="javascript:void(0)" ng-click="switchOperator()" data-toggle="tab">operator</a></li>
	</ul>

	<ul class="nav nav-tabs" ng-if="currentRole == 'brand_admin'">
	  <li><a href="javascript:void(0)">operator</a></li>
	</ul>

	<div class="tab-content">
	  <div ng-if="isShowAdmin">
	  	<div style="text-align:right;margin-top:10px;">
	  		<button class="btn btn-info btn-sm" ng-click="addAdmin()">
	  			<label class="glyphicon glyphicon-plus"></label> 添加品牌管理员
	  		</button>
	  	</div>
	  	<div class="table-responsive" style="position:relative;margin-left:0px;top:0px;width:100%;">
		  <table class="table table-striped table-hover table-bordered" style="text-align:center;table-layout:fixed;">
		   <caption></caption>
		   <thead style="font-size:15px;">
		      <tr class="text-capitalize">
		         <th>ID</th>
		         <th>品牌</th>
		         <th>用户名</th>
		         <th>密码</th>
		         <th>创建时间</th>
		         <th>操作</th>
		      </tr>
		   </thead>

		   <tbody ng-if="admins.length < 1">
		     <tr><td colspan="6" class="active">暂无数据</td></tr>
		   </tbody>

		   <tbody>
		      <tr ng-repeat="admin in admins" class="active">
		         <td style="width:2%;">{{admin.id}}</td>
		         <td style="width:20%;" ng-if="!admin.editMode">{{admin.brand}}</td>
		         <td style="width:20%;" ng-if="admin.editMode"><input type="text" class="form-control" ng-model="admin.editBrand"></td>
		         <td style="width:15%;" ng-if="!admin.editMode">{{admin.name}}</td>
		         <td style="width:15%;" ng-if="admin.editMode"><input type="text" class="form-control" ng-model="admin.editName"></td>
		         <td style="width:15%;" class="show-as-dot" ng-if="!admin.editMode">{{admin.password}}</td>
		         <td style="width:15%;" ng-if="admin.editMode"><input type="text" class="form-control" ng-model="admin.editPassword"></td>
		         <td style="width:10%;">{{admin.createTime}}</td>
		         <td style="width:38%;">
		           <button ng-if="!admin.editMode" type="button" title="编辑" class="btn btn-default btn-xs" ng-click="enterEditMode(admin)">
		             <i class="glyphicon glyphicon-edit"></i>
		           </button>
		           <button ng-if="admin.editMode" type="button" title="保存" class="btn btn-default btn-xs" ng-click="saveEditAdmin(admin)">
		             <i class="glyphicon glyphicon-ok"></i>
		           </button>
		           <button ng-if="admin.editMode" type="button" title="取消" class="btn btn-default btn-xs" ng-click="cancelEditAdmin(admin)">
		             <i class="glyphicon glyphicon-remove"></i>
		           </button>
		           <button type="button" title="删除" class="btn btn-default btn-xs" data-target="#myModal" data-toggle="modal" ng-click="showDeleteAdminDialog(admin)">
		             <i class="glyphicon glyphicon-trash"></i>
		           </button>
		         </td>
		      </tr>
		   </tbody>
		  </table>

		  <table class="table table-hover table-bordered" style="text-align:center;table-layout:fixed;">
			   <thead style="font-size:15px;" ng-if="addAdmins.length > 0">
			      <tr class="text-capitalize">
			         <th>编号</th>
			         <th>品牌</th>
			         <th>用户名</th>
			         <th>密码</th>
			         <th>操作</th>
			      </tr>
			   </thead>
			   <tbody>
				   	<tr ng-repeat="ad in addAdmins">
				   		<td>{{$index + 1}}</td>
				      	<td><input type="text" class="form-control" ng-model="ad.brand"></td>
				      	<td><input type="text" class="form-control" ng-model="ad.name"></td>
				      	<td><input type="text" class="form-control" ng-model="ad.password"></td>
				      	<td>
				      		<button type="button" title="保存" class="btn btn-default btn-xs" ng-click="saveAddAdmin(ad, $index)">
					            <i class="glyphicon glyphicon-ok"></i>
					        </button>
					        <button type="button" title="取消" class="btn btn-default btn-xs" ng-click="cancelAddAdmin($index)">
					            <i class="glyphicon glyphicon-remove"></i>
					        </button>
				      	</td>
			      	</tr>
			   </tbody>
		   </table>
		</div>
	  </div>

	  <div ng-if="!isShowAdmin || currentRole == 'brand_admin'">
	  	<div style="text-align:right;margin-top:10px;">
	  		<button class="btn btn-info btn-sm" ng-click="addOperator()">
	  			<label class="glyphicon glyphicon-plus"></label> 添加品牌操作员
	  		</button>
	  	</div>
	  	<div class="table-responsive" style="position:relative;margin-left:0px;top:0px;width:100%;">
		  <table class="table table-striped table-hover table-bordered" style="text-align:center;table-layout:fixed;">
		   <caption></caption>
		   <thead style="font-size:15px;">
		      <tr class="text-capitalize">
		         <th>ID</th>
		         <th>品牌</th>
		         <th>用户名</th>
		         <th>密码</th>
		         <th>创建时间</th>
		         <th>操作</th>
		      </tr>
		   </thead>

		   <tbody ng-if="operators.length < 1">
		     <tr><td colspan="6" class="active">暂无数据</td></tr>
		   </tbody>

		   <tbody>
		      <tr ng-repeat="operator in operators" class="active">
		         <td style="width:2%;">{{operator.id}}</td>
		         <td style="width:20%;" ng-if="(!operator.editMode && currentRole == 'system_admin') || currentRole == 'brand_admin'">{{operator.brand}}</td>
		         <td style="width:20%;" ng-if="operator.editMode && currentRole == 'system_admin'"><input type="text" class="form-control" ng-model="operator.editBrand"></td>
		         <td style="width:15%;" ng-if="!operator.editMode">{{operator.name}}</td>
		         <td style="width:15%;" ng-if="operator.editMode"><input type="text" class="form-control" ng-model="operator.editName"></td>
		         <td style="width:15%;" class="show-as-dot" ng-if="!operator.editMode">{{operator.password}}</td>
		         <td style="width:15%;" ng-if="operator.editMode"><input type="text" class="form-control" ng-model="operator.editPassword"></td>
		         <td style="width:10%;">{{operator.createTime}}</td>
		         <td style="width:38%;">
		           <button ng-if="!operator.editMode" type="button" title="编辑" class="btn btn-default btn-xs" ng-click="enterOperatorEditMode(operator)">
		             <i class="glyphicon glyphicon-edit"></i>
		           </button>
		           <button ng-if="operator.editMode" type="button" title="保存" class="btn btn-default btn-xs" ng-click="saveEditOperator(operator)">
		             <i class="glyphicon glyphicon-ok"></i>
		           </button>
		           <button ng-if="operator.editMode" type="button" title="取消" class="btn btn-default btn-xs" ng-click="cancelEditOperator(operator)">
		             <i class="glyphicon glyphicon-remove"></i>
		           </button>
		           <button type="button" title="删除" class="btn btn-default btn-xs" data-target="#operatorModal" data-toggle="modal" ng-click="showDeleteOperatorDialog(operator, $index)">
		             <i class="glyphicon glyphicon-trash"></i>
		           </button>
		         </td>
		      </tr>
		   </tbody>
		  </table>

		  <table class="table table-hover table-bordered" style="text-align:center;table-layout:fixed;">
			   <thead style="font-size:15px;" ng-if="addOperators.length > 0">
			      <tr class="text-capitalize">
			         <th>编号</th>
			         <th>品牌</th>
			         <th>用户名</th>
			         <th>密码</th>
			         <th>操作</th>
			      </tr>
			   </thead>
			   <tbody>
				   	<tr ng-repeat="ad in addOperators">
				   		<td>{{$index + 1}}</td>
				      	<td ng-if="currentRole == 'system_admin'"><input type="text" class="form-control" ng-model="ad.brand"></td>
				      	<td ng-if="currentRole != 'system_admin'">{{ad.brand}}</td>
				      	<td><input type="text" class="form-control" ng-model="ad.name"></td>
				      	<td><input type="text" class="form-control" ng-model="ad.password"></td>
				      	<td>
				      		<button type="button" title="保存" class="btn btn-default btn-xs" ng-click="saveAddOperator(ad, $index)">
					            <i class="glyphicon glyphicon-ok"></i>
					        </button>
					        <button type="button" title="取消" class="btn btn-default btn-xs" ng-click="cancelAddOperator($index)">
					            <i class="glyphicon glyphicon-remove"></i>
					        </button>
				      	</td>
			      	</tr>
			   </tbody>
		   </table>
		</div>
	  </div>
	</div>
</div>