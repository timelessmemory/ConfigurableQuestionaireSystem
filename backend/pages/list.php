<header class="nav-top">
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand">Questionaire Backend Configuration Management</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#/create"><i class="glyphicon glyphicon-file"></i> Create User</a></li>
        <li><a href='#/' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        <li><a>Welcome, <?php session_start(); echo $_SESSION['username']; ?></a></li>
      </ul>
    </div>
  </nav>
</header>

<!-- 模态框（Modal） -->
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
            Are you sure to delete the option you choose ?
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" ng-click="delete(userId)">Confirm</button>
         </div>
      </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->

<div class="input-group search-div" style="width:100%;">
  <input style="width:150px;" type="text" class="form-control" ng-model="searchWord" placeholder="Real time search" aria-describedby="basic-addon2">
  <input style="display:inline-block;float:right;width:200px;" type="text" class="form-control" ng-model="keyword" placeholder="Please input keyword" aria-describedby="basic-addon2">
  <span class="input-group-addon" id="basic-addon2" ng-click="search()"><i class="glyphicon glyphicon-search"></i> </span>
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover">
   <caption></caption>
   <thead>
      <tr style="font-size:20px;" class="text-capitalize">
         <th>No</th>
         <th>{{titles[2]}}</th>
         <th>{{titles[3]}}</th>
         <th>{{titles[1]}}</th>
      </tr>
   </thead>
   <tbody>
      <tr ng-repeat="user in users" ng-class="{'info' : ($index + 1) % 2 == 0, 'success' : ($index + 1) % 2 != 0}">
         <td>{{$index + 1}}</td>
         <td style="color : #2e4358; cursor : pointer;" ng-click="detail(user._id.$id)" title="{{user.name}}">{{user.name}}</td>
         <td>{{user.password}}</td>
         <td>{{user.description}}</td>
        <!--  <td>
           <label class="checkbox-inline">
             <input type="checkbox" ng-model="isCheckeds[$index]" ng-click="joinDelete()" />
           </label>
         </td> -->
         <td>
           <button type="button" class="btn btn-default btn-xs" data-target="#myModal" data-toggle="modal" ng-click="sendUserId(user._id.$id)">
             <i class="glyphicon glyphicon-trash"></i>
           </button>
         </td>
      </tr>
   </tbody>
  </table>
</div>

