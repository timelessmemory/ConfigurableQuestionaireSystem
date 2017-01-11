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
        <a class="navbar-brand">Questionaire Backend Configuration Management</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#/list"><i class="glyphicon glyphicon-th-list"></i> Questionaire List</a></li>
        <li><a href='javascript:void(0);' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> Logout</a></li>
        <li><a>Welcome, <?php session_start(); $name = $_SESSION['username'] == '' ? '<script>window.localStorage.setItem("isLogin", "false");window.location.href = "#/";</script>' : $_SESSION['username']; echo $name; ?></a></li>
      </ul>
    </div>
  </nav>
</header>

<form class="top-div" style="top:100px;" ng-show="showHead" novalidate>
  <div class="form-group compo">
    <label for="subject">问卷主题:</label>
    <input type="text" class="form-control" id="subject" ng-model="questionaire.subject" required autofocus />
  </div>

  <div class="form-group compo" ng-if="currentRole == 'system_admin'">
    <label for="brand">品牌:</label>
    <input type="text" class="form-control" id="brand" ng-model="questionaire.brand" />
  </div>

  <div class="form-group compo">
    <label>是否收集隐私:</label>
    <input class="unchecked" ng-class='{"checked": questionaire.isProvicy}' type="checkbox" ng-model="questionaire.isProvicy" />
  </div>

  <div ng-show="questionaire.isProvicy">
    <div class="form-group compo">
      <label for="description">问卷说明:</label>
      <div class="ueditor" config="config" style="width:100%;height:500px;" ng-model="questionaire.description"></div>
    </div>

    <div class="form-group compo">
      <label for="agree_fst">
        同意说明1: &nbsp;&nbsp;是否必须同意
        <input class="unchecked" ng-class='{"checked": questionaire.required_fst}' type="checkbox" ng-model="questionaire.required_fst" />
      </label> 
      <input type="text" class="form-control" id="agree_fst" ng-model="questionaire.agree_fst" />
    </div>

    <div class="form-group compo" style="margin-bottom: 15px;">
      <label for="agree_snd">
        同意说明2: &nbsp;&nbsp;是否必须同意
        <input class="unchecked" ng-class='{"checked": questionaire.required_snd}' type="checkbox" ng-model="questionaire.required_snd" />
      </label> 
      <input type="text" class="form-control" id="agree_snd" ng-model="questionaire.agree_snd" />
    </div>
  </div>

  <div style="text-align:right;margin-bottom:20px;">
    <button type="submit" class="btn btn-success" style="margin-right:30px;" ng-click="createQuestion()">确定</button>
  </div>
</form>

<form class="top-div" ng-if="!showHead" style="top:100px;" novalidate>
  <div class="form-group compo">
    <label for="question">问题{{currentIndex + 1}}:</label>
    <div class="ueditor" config="config" ready="ready" style="width:100%;height:200px;" ng-model="questions[currentIndex].title"></div>
    <br/>
    <span>是否单选:</span>
    <input class="unchecked" ng-class='{"checked": questions[currentIndex].isSingle}' type="checkbox" ng-model="questions[currentIndex].isSingle" ng-click="returnOrigin(questions[currentIndex].isSingle, questions[currentIndex].options)" />
    <br/><br/>
    <span>是否为选项设置跳题索引:</span>
    <input class="unchecked" ng-class='{"checked": questions[currentIndex].isSetSkip}' type="checkbox" ng-model="questions[currentIndex].isSetSkip" ng-click="switchIsSetSkip(questions[currentIndex])" />
    <br/>
    <div ng-if="questions[currentIndex].isSetSkip && !questions[currentIndex].isSingle">
      <div style="margin-left:20px;">
        <span>Group1索引</span>
        <input type="number" class="form-control" ng-model="questions[currentIndex].group.gp1" />
        <span>Group2索引</span>
        <input type="number" class="form-control" ng-model="questions[currentIndex].group.gp2" />
      </div>
    </div>
    <br/>
    <br/>
    <label>选项:</label>
    <div ng-repeat="item in questions[currentIndex].options">
      <span>选项内容{{$index + 1}}:</span>
      <textarea class="form-control" rows="5" ng-disabled="item.isCustOmized" ng-model="item.content"></textarea>
      <span>选择此选项是否让被调查者继续回答下一题:</span>
      <input class="unchecked" ng-class='{"checked": item.isNext}' type="checkbox" ng-model="item.isNext" />
      <br/>
      <span>此选项是否由被调查者自行输入:</span>
      <input class="unchecked" ng-class='{"checked": item.isCustOmized}' type="checkbox" ng-model="item.isCustOmized" />
      <br/>
      <div ng-if="questions[currentIndex].isSetSkip && !questions[currentIndex].isSingle">
        <span>是否跳题至Group1:</span>
        <input class="unchecked" ng-class='{"checked": item.isSkipOne}' type="checkbox" ng-model="item.isSkipOne" ng-click="switchSkipIndex(item, questions[currentIndex].group)" />
      </div>
      <div ng-if="questions[currentIndex].isSetSkip && questions[currentIndex].isSingle">
        <span>跳题索引:</span>
        <input type="number" class="form-control" ng-model="item.skipIndex" />
      </div>
      <div style="text-align:right;margin-top: 16px;">
        <button class="btn btn-danger btn-xs" ng-click="deleteOption(questions[currentIndex].options, $index)">删除该选项</button>
      </div>
      <br/><br/><br/>
    </div>
    <button class="btn btn-success btn-xs" style="margin-right:30px;" ng-click="addOption()">继续添加选项</button>
    <div style="text-align:right;margin-top: -20px;">
      <button class="btn btn-danger btn-xs" style="margin-right:30px;" ng-click="deleteQuestion()">删除问题</button>
      <button ng-show="currentIndex > 0" class="btn btn-info btn-xs" style="margin-left:4%;" ng-click="lastQues()">上一题</button>
      <button class="btn btn-info btn-xs" style="margin-right:4%;margin-left: 4%;" ng-click="nextQues()">下一题</button>
      <button class="btn btn-primary btn-xs" ng-click="finishCreate()" ng-disabled="isSubmit">完成创建</button>
    </div>
  </div>
</form>