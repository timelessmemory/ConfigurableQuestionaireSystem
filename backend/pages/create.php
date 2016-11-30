<div class="alert alert-warning" id="hintDiv">
   <a class="close">
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
        <li><a href="#/list"><i class="glyphicon glyphicon-th-list"></i> Questionaire List</a></li>
        <li><a href='javascript:void(0);' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        <li><a>Welcome, <?php session_start(); echo $_SESSION['username']; ?></a></li>
      </ul>
    </div>
  </nav>
</header>

<form class="top-div" name="form" ng-show="showHead" novalidate>
  <div class="form-group compo">
    <label for="subject">问卷主题:</label>
    <input type="text" class="form-control" id="subject" ng-model="questionaire.subject" required autofocus />
  </div>
  <div class="form-group compo">
    <label for="description">问卷说明:</label>
    <div>
        <script id="editor" type="text/plain" style="width:100%;height:500px;"></script>
    </div>
  </div>
  <div style="text-align:right;">
    <button type="submit" class="btn btn-success" style="margin-right:30px;" ng-click="createQuestion()">确定</button>
  </div>
</form>

<form ng-if="!showHead" name="form" novalidate>
  <div class="form-group compo">
    <label for="question">问题{{currentIndex + 1}}:(如需删除问题请完成创建后进入编辑页面进行操作)</label>
    <input type="text" class="form-control" id="question" ng-model="questions[currentIndex].title" required autofocus />
    <br/>
    <span>是否单选:</span>
    <input class="unchecked checked" id="single" type="checkbox" ng-model="questions[currentIndex].isSingle" ng-click="switchCheckBox($event, questions[currentIndex].isSingle)" />
    <br/>
    <br/>
    <label>选项:</label>
    <div ng-repeat="item in questions[currentIndex].options">
      <span>选项内容:</span>
      <textarea class="form-control" rows="5" ng-disabled="item.isCustOmized" ng-model="item.content"></textarea>
      <span>选择此选项是否让被调查者继续回答下一题:</span>
      <input class="unchecked checked" type="checkbox" ng-model="item.isNext" ng-click="switchCheckBox($event, item.isNext)" />
      <br/>
      <span>此选项是否由被调查者自行输入:</span>
      <input class="unchecked" type="checkbox" ng-model="item.isCustOmized" ng-click="switchCheckBox($event, item.isCustOmized)" />
      <br/><br/><br/>
    </div>
    <button class="btn btn-success btn-xs" style="margin-right:30px;" ng-click="addOption()">继续添加选项</button>
    <button ng-show="currentIndex > 0" class="btn btn-info btn-xs" style="margin-right:30px;" ng-click="lastQues()">上一题</button>
    <button class="btn btn-info btn-xs" style="margin-right:30px;" ng-click="nextQues()">下一题</button>
    <button class="btn btn-primary btn-xs" style="margin-right:30px;" ng-click="finishCreate()" ng-disabled="isSubmit">完成创建</button>
  </div>
</form>