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
        <li><a href="#/list"><i class="glyphicon glyphicon-th-list"></i> Questionaire List</a></li>
        <li><a href="#/create"><i class="glyphicon glyphicon-file"></i> Create Questionaire</a></li>
        <li><a href='#/' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        <li><a>Welcome, <?php session_start(); echo $_SESSION['username']; ?></a></li>
      </ul>
    </div>
  </nav>
</header>

<form class="top-div" style="top:100px;" novalidate>
  <div class="form-group compo">
    <label for="subject">问卷主题:</label>
    <input type="text" class="form-control" id="subject" ng-model="questionaire.subject" required autofocus />
  </div>
  <div class="form-group compo" style="margin-bottom: 15px;">
    <label for="description">问卷说明:</label>
    <div class="ueditor" config="config" ready="ready" style="width:100%;height:500px;" ng-model="questionaire.description"></div>
  </div>
  <div style="text-align:right;">
    <button type="button" class="btn btn-success btn-sm" style="margin-right:30px;" ng-click="saveQuestionaire(questionaire.id)">保存问卷信息</button>
  </div>

  <div class="form-group compo">
    <div ng-repeat="question in questionaire.questions" ng-init="outerIndex = $index">
      <label for="question">问题{{outerIndex + 1}}:</label>
      <div class="ueditor" config="config" ready="ready" style="width:100%;height:200px;" ng-model="question.title"></div>
      <br/>
      <span>是否单选:</span>
      <input class="unchecked" ng-class='{"checked": question.isSingle}' type="checkbox" ng-model="question.isSingle" />
      <br/><br/>
      <span>是否为选项设置跳题索引:</span>
      <input class="unchecked" ng-class='{"checked": question.isSetSkip}' type="checkbox" ng-model="question.isSetSkip" ng-click="switchIsSetSkip(question)" />
      <br/>
      <div ng-if="question.isSetSkip">
        <div style="margin-left:20px;">
          <span>Group1索引</span>
          <input type="text" class="form-control" ng-model="question.group.gp1" />
          <span>Group2索引</span>
          <input type="text" class="form-control" ng-model="question.group.gp2" />
        </div>
      </div>
      <div style="text-align:right;">
        <button class="btn btn-danger btn-sm" style="margin-right:30px;margin-top:28px;" ng-click="deleteQuestion(question.id)">删除问题{{outerIndex + 1}}</button>      
        <button class="btn btn-success btn-sm" style="margin-top:30px;" ng-click="saveQuestion(question.id)">保存问题{{outerIndex + 1}}题干的修改</button>      
      </div>
      <br/>
      <br/>
      <label>选项:</label>
      <div ng-repeat="item in question.options">
        <span>选项内容:</span>
        <textarea class="form-control" rows="5" ng-disabled="item.isCustomized" ng-model="item.content"></textarea>
        <span>选择此选项是否让被调查者继续回答下一题:</span>
        <input class="unchecked" ng-class='{"checked": item.isHasNext}' type="checkbox" ng-model="item.isHasNext" />
        <br/>
        <span>此选项是否由被调查者自行输入:</span>
        <input class="unchecked" ng-class='{"checked": item.isCustomized}' type="checkbox" ng-model="item.isCustomized" />
        <br/>
        <div ng-if="question.isSetSkip">
          <span>是否跳题至Group1:</span>
          <input class="unchecked" ng-class='{"checked": item.isSkipOne}' type="checkbox" ng-model="item.isSkipOne" ng-click="switchSkipIndex(item, question.group)" />
        </div>
        <div style="text-align:right;">
          <button class="btn btn-danger btn-sm" style="margin-right:30px;" ng-click="deleteQuestionOption(item.id)">删除问题{{outerIndex + 1}}选项{{$index + 1}}</button>      
          <button class="btn btn-success btn-sm" ng-click="saveQuestionOption(item.id)">保存问题{{outerIndex + 1}}选项{{$index + 1}}的修改</button>      
        </div>
        <br/><br/>
      </div>
      <br/><br/><br/>

      <!--show add options-->
      <div ng-repeat="option in question.addOptions">
        <textarea class="form-control" rows="5" ng-disabled="option.isCustomized" ng-model="option.content"></textarea>
        <span>选择此选项是否让被调查者继续回答下一题:</span>
        <input class="unchecked" ng-class='{"checked": option.isHasNext}' type="checkbox" ng-model="option.isHasNext" />
        <br/>
        <span>此选项是否由被调查者自行输入:</span>
        <input class="unchecked" ng-class='{"checked": option.isCustomized}' type="checkbox" ng-model="option.isCustomized" />
        <br/>
        <div ng-if="question.isSetSkip">
          <span>是否跳题至Group1:</span>
          <input class="unchecked" ng-class='{"checked": option.isSkipOne}' type="checkbox" ng-model="option.isSkipOne" ng-click="switchSkipIndex(option, question.group)" />
        </div>
        <br/><br/><br/>
      </div>
      <div style="text-align:right;" ng-if="question.addOptions.length > 0">
        <button class="btn btn-success btn-sm" ng-click="saveAddQuestionOptions(question)">保存问题{{outerIndex + 1}}添加的选项</button>      
      </div>
      <br/><br/><br/>

      <div style="text-align:left;">
        <button class="btn btn-info btn-sm" style="margin-top:-160px;" ng-click="addOption(question)">添加选项</button>      
      </div>
    </div>
  </div>
  <div style="text-align:left;margin-left:3%">
    <button class="btn btn-default" ng-click="cancel()">返回</button>
  </div>
  <div style="text-align:right;margin-top:-30px;margin-right: 2%;margin-bottom: 20px;">
    <button class="btn btn-info btn-sm" ng-click="addQuestion()">添加问题</button>
  </div>
</form>

