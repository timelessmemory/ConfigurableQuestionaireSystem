<div class="alert alert-warning" id="hintDiv">
   <a class="close" ng-click="close()">
      &times;
   </a>
   <strong>{{ tip | translate}}</strong>
</div>
<header class="nav-top">
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand">{{ 'system_full_title' | translate}}</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li ng-if="currentRole != 'brand_operator'"><a href="#/role"><i class="glyphicon glyphicon-user"></i> {{'user_management' | translate}}</a></li>
        <li><a href="#/create"><i class="glyphicon glyphicon-file"></i> {{'create_questionaire' | translate}}</a></li>
        <li><a href='#/' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> {{'log_out' | translate}}</a></li>
        <li><a>{{ 'welcome' | translate}}, <?php session_start(); $name = $_SESSION['username'] == '' ? '<script>window.localStorage.setItem("isLogin", "false");window.location.href = "#/";</script>' : $_SESSION['username']; echo $name; ?></a></li>
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
               {{ 'delete' | translate}}
            </h4>
         </div>
         <div class="modal-body">
            {{ 'confirm_delete_questionaire_tip' | translate}}
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ 'cancel' | translate}}</button>
            <button type="button" class="btn btn-danger" ng-click="delete(questionaireId)">{{ 'confirm' | translate}}</button>
         </div>
      </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->

<div class="input-group search-div" style="width:98%;">
  <!-- <input style="width:150px;" type="text" class="form-control" ng-model="searchWord" placeholder="Real time search" aria-describedby="basic-addon2"> -->
  <input style="display:inline-block;float:right;width:200px;" type="text" class="form-control" ng-model="keyword" placeholder="{{'questionaire_subject' | translate}}" aria-describedby="basic-addon2">
  <span class="input-group-addon" id="basic-addon2" ng-click="search()"><i class="glyphicon glyphicon-search"></i> </span>
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover table-bordered" style="text-align:center;table-layout:fixed;">
   <caption></caption>
   <thead style="font-size:15px;">
      <tr class="text-capitalize">
         <th>{{'questionaire_number' | translate}}</th>
         <th>{{'questionaire_subject' | translate}}</th>
         <th>{{'questionaire_brand' | translate}}</th>
         <th>{{'questionaire_date' | translate}}</th>
         <th>{{'questionaire_operation' | translate}}</th>
      </tr>
   </thead>
   <tbody ng-if="questionaires.length < 1">
     <tr><td colspan="5" class="active">{{'questionaire_subject' | translate}}</td></tr>
   </tbody>
   <tbody>
      <tr ng-repeat="questionaire in questionaires" class="active">
         <td style="width:10%;">{{$index + 1}}</td>
         <td style="color:#5bc0de; cursor:pointer;text-decoration:underline;width:20%;" title="{{'visit_questionaire' | translate}}"><a href="/questionaire/frontend/index.html?questionaireId={{questionaire.id}}">{{questionaire.subject}}<i class="glyphicon glyphicon-new-window"></i></a></td>
         <td style="max-width:40%;">{{questionaire.brand}}</td>
         <td style="width:20%;">{{questionaire.createTime}}</td>
         <td style="width:10%;">
           <button type="button" title="{{'edit' | translate}}" class="btn btn-default btn-xs" ng-click="edit(questionaire.id)">
             <i class="glyphicon glyphicon-edit"></i>
           </button>
           <button type="button" title="{{'delete' | translate}}" class="btn btn-default btn-xs" data-target="#myModal" data-toggle="modal" ng-click="sendId(questionaire.id)">
             <i class="glyphicon glyphicon-trash"></i>
           </button>
           <button type="button" title="{{'copy_link' | translate}}" class="btn btn-default btn-xs" ngclipboard data-clipboard-text="{{domain}}/questionaire/frontend/index.html?questionaireId={{questionaire.id}}">
             <i class="glyphicon  glyphicon-link"></i>
           </button>
           <button type="button" title="{{'download_answer' | translate}}" class="btn btn-default btn-xs" ng-click="download(questionaire.id)">
             <i class="glyphicon glyphicon-download"></i>
           </button>
           <!-- <a class="btn btn-default btn-xs" href="http://localhost/questionaire/backend/controllers/question/answer_files/answer1.xlsx" title="下载答案">
             <i class="glyphicon glyphicon-download"></i>
           </a> -->
         </td>
      </tr>
   </tbody>
  </table>
  <div style="text-align:right;">
    <uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-links="true" rotate="false" num-pages="numPages" items-per-page="pageSize">
    </uib-pagination>
    <!-- <pre>Page: {{bigCurrentPage}} / {{numPages}}</pre> -->
  </div>
</div>

