<div class="alert alert-warning" id="hintDiv">
   <a class="close" ng-click="close()">
      &times;
   </a>
   <strong>{{tip}}</strong>
</div>
<header class="nav-top">
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand">Questionaire Backend Configuration Management</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#/create"><i class="glyphicon glyphicon-file"></i> Create Questionaire</a></li>
        <li><a href='#/' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        <li><a>Welcome, <?php session_start(); echo $_SESSION['username']; ?></a></li>
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
            Are you sure to delete the option you choose ?
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" ng-click="delete(questionaireId)">Confirm</button>
         </div>
      </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->

<div class="input-group search-div" style="width:98%;">
  <!-- <input style="width:150px;" type="text" class="form-control" ng-model="searchWord" placeholder="Real time search" aria-describedby="basic-addon2"> -->
  <input style="display:inline-block;float:right;width:200px;" type="text" class="form-control" ng-model="keyword" placeholder="Please input keyword" aria-describedby="basic-addon2">
  <span class="input-group-addon" id="basic-addon2" ng-click="search()"><i class="glyphicon glyphicon-search"></i> </span>
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover table-bordered" style="text-align:center;table-layout:fixed;">
   <caption></caption>
   <thead style="font-size:15px;">
      <tr class="text-capitalize">
         <th>编号</th>
         <th>问卷主题</th>
         <th>问卷说明</th>
         <th>创建日期</th>
         <th>操作</th>
      </tr>
   </thead>
   <tbody ng-if="questionaires.length < 1">
     <tr><td colspan="5" class="active">暂无数据</td></tr>
   </tbody>
   <tbody>
      <tr ng-repeat="questionaire in questionaires" class="active">
         <td style="width:10%;">{{$index + 1}}</td>
         <td style="color:#5bc0de; cursor:pointer;text-decoration:underline;width:20%;" title="访问问卷"><a href="/questionaire/frontend/index.html?questionaireId={{questionaire.id}}">{{questionaire.subject}}</a></td>
         <td style="max-width:40%;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;" title="{{questionaire.description}}">{{questionaire.description}}</td>
         <td style="width:20%;">{{questionaire.createTime}}</td>
         <td style="width:10%;">
           <button type="button" title="生成问卷" class="btn btn-default btn-xs" ng-click="detail(questionaire.id)">
             <i class="glyphicon glyphicon-new-window"></i>
           </button>
           <button type="button" title="编辑" class="btn btn-default btn-xs" ng-click="edit(questionaire.id)">
             <i class="glyphicon glyphicon-edit"></i>
           </button>
           <button type="button" title="删除" class="btn btn-default btn-xs" data-target="#myModal" data-toggle="modal" ng-click="sendId(questionaire.id)">
             <i class="glyphicon glyphicon-trash"></i>
           </button>
         </td>
      </tr>
   </tbody>
  </table>
</div>

