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
        <li><a href="#/list"><i class="glyphicon glyphicon-th-list"></i> {{ 'questionaire_list' | translate}}</a></li>
        <li><a href="#/create"><i class="glyphicon glyphicon-file"></i> {{ 'create_questionaire' | translate}}</a></li>
        <li><a href='#/' ng-click="logout()"><i class="glyphicon glyphicon-off"></i> {{'log_out' | translate}}</a></li>
        <li><a>{{ 'welcome' | translate}}, <?php session_start(); $name = $_SESSION['username'] == '' ? '<script>window.localStorage.setItem("isLogin", "false");window.location.href = "#/";</script>' : $_SESSION['username']; echo $name; ?></a></li>
      </ul>
    </div>
  </nav>
</header>

<div class="modal fade" id="questionModal" tabindex="-1" role="dialog" 
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
            {{ 'confirm_delete_question_tip' | translate}}
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ 'cancel' | translate}}</button>
            <button type="button" class="btn btn-danger" ng-click="deleteQuestion(deleteQuestionId)">{{ 'confirm' | translate}}</button>
         </div>
      </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->

<div class="modal fade" id="optionModal" tabindex="-1" role="dialog" 
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
           {{ 'confirm_delete_option_tip' | translate}}
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ 'cancel' | translate}}</button>
            <button type="button" class="btn btn-danger" ng-click="deleteQuestionOption(deleteQuestionOptionId)">{{ 'confirm' | translate}}</button>
         </div>
      </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->

<form class="top-div" style="top:100px;" novalidate>
  <div class="form-group compo">
    <label for="subject">{{'questionaire_subject' | translate}}:</label>
    <input type="text" class="form-control" id="subject" ng-model="questionaire.subject" required autofocus />
  </div>

  <div class="form-group compo" ng-if="currentRole == 'system_admin'">
    <label for="brand">{{'questionaire_brand' | translate}}:</label>
    <input type="text" class="form-control" id="brand" ng-model="questionaire.brand" />
  </div>

  <div class="form-group compo">
    <label>{{'is_collect_privacy' | translate}}:</label>
    <input class="unchecked" ng-class='{"checked": questionaire.isProvicy}' type="checkbox" ng-model="questionaire.isProvicy" />
  </div>

  <div ng-show="questionaire.isProvicy">
    <div class="form-group compo" style="margin-bottom: 15px;">
      <label for="description">{{'questionaire_description' | translate}}:</label>
      <div class="ueditor" config="config" ready="ready" style="width:100%;height:500px;" ng-model="questionaire.description"></div>
    </div>

    <div class="form-group compo">
      <label for="agree_fst">
        {{'agree_des_one' | translate}}: &nbsp;&nbsp;{{'is_required_agree' | translate}}
        <input class="unchecked" ng-class='{"checked": questionaire.required_fst}' type="checkbox" ng-model="questionaire.required_fst" />
      </label> 
      <input type="text" class="form-control" id="agree_fst" ng-model="questionaire.agree_fst" />
    </div>

    <div class="form-group compo" style="margin-bottom: 15px;">
      <label for="agree_snd">
        {{'agree_des_two' | translate}}: &nbsp;&nbsp;{{'is_required_agree' | translate}}
        <input class="unchecked" ng-class='{"checked": questionaire.required_snd}' type="checkbox" ng-model="questionaire.required_snd" />
      </label> 
      <input type="text" class="form-control" id="agree_snd" ng-model="questionaire.agree_snd" />
    </div>
  </div>

  <div style="text-align:right;">
    <button type="button" class="btn btn-success btn-sm" style="margin-right:30px;" ng-disabled="isSubmit" ng-click="saveQuestionaire(questionaire.id)">{{'save_questionaire' | translate}}</button>
  </div>

  <div class="form-group compo">
    <div ng-repeat="question in questionaire.questions" ng-init="outerIndex = $index">
      <label for="question">{{'questionNo' | translate : {questionNo : outerIndex + 1} }}:</label>
      <div class="ueditor" config="config" ready="ready" style="width:100%;height:200px;" ng-model="question.title"></div>
      <br/>
      <span>{{'is_single_choice' | translate}}:</span>
      <input class="unchecked" ng-class='{"checked": question.isSingle}' type="checkbox" ng-model="question.isSingle" ng-click="returnOrigin(question.isSingle, question.options)"/>
      <br/><br/>
      <span>{{'is_set_skip_index_for_option' | translate}}:</span>
      <input class="unchecked" ng-class='{"checked": question.isSetSkip}' type="checkbox" ng-model="question.isSetSkip" ng-click="switchIsSetSkip(question)" />
      <br/>
      <div ng-if="question.isSetSkip && !question.isSingle">
        <div style="margin-left:20px;">
          <span>{{'group_one' | translate}}</span>
          <input type="number" class="form-control" ng-model="question.group.gp1" />
          <span>{{'group_two' | translate}}</span>
          <input type="number" class="form-control" ng-model="question.group.gp2" />
        </div>
      </div>
      <div style="text-align:right;">
        <button class="btn btn-danger btn-sm" style="margin-right:30px;margin-top:28px;" data-target="#questionModal" data-toggle="modal" ng-click="sendQuestionId(question.id)">{{'delete_question_index' | translate : {index : outerIndex + 1} }}</button>      
        <button class="btn btn-success btn-sm" style="margin-top:30px;" ng-disabled="isSubmit" ng-click="saveQuestion(question.id, question, outerIndex + 1)">{{'save_question_title' | translate : {index : outerIndex + 1} }}</button>      
      </div>
      <br/>
      <br/>
      <label>{{'option' | translate}}:</label>
      <div ng-repeat="item in question.options">
        <span>{{'option_content' | translate}}:</span>
        <textarea class="form-control" rows="5" ng-disabled="item.isCustomized" ng-model="item.content"></textarea>
        <span>{{'is_continue_next' | translate}}:</span>
        <input class="unchecked" ng-class='{"checked": item.isHasNext}' type="checkbox" ng-model="item.isHasNext" />
        <br/>
        <span>{{'is_allow_customize_content' | translate}}:</span>
        <input class="unchecked" ng-class='{"checked": item.isCustomized}' type="checkbox" ng-model="item.isCustomized" />
        <br/>
        <div ng-if="question.isSetSkip && !question.isSingle">
          <span>{{'is_skip_group_one' | translate}}:</span>
          <input class="unchecked" ng-class='{"checked": item.isSkipOne}' type="checkbox" ng-model="item.isSkipOne" ng-click="switchSkipIndex(item, question.group)" />
          ({{'skip_index_modify_tip' | translate}})
        </div>
        <div ng-if="question.isSetSkip && question.isSingle">
          <span>{{'skip_index' | translate}}:</span>
          ({{'skip_index_modify_tip' | translate}})
          <input type="number" class="form-control" ng-model="item.skipIndex" />
        </div>
        <div style="text-align:right;margin-top: 10px;">
          <button class="btn btn-danger btn-sm" style="margin-right:30px;" data-target="#optionModal" data-toggle="modal" ng-click="sendQuestionOptionId(item.id)">{{'delete_option_index' | translate : {questionIndex : outerIndex + 1, optionIndex : $index + 1} }}</button>      
          <button class="btn btn-success btn-sm" ng-disabled="isSubmit" ng-click="saveQuestionOption(item.id, item)">{{'save_option_index' | translate : {questionIndex : outerIndex + 1, optionIndex : $index + 1} }}</button>      
        </div>
        <br/><br/>
      </div>
      <br/><br/><br/>

      <!--show add options-->
      <div ng-repeat="option in question.addOptions">
        <textarea class="form-control" rows="5" ng-disabled="option.isCustomized" ng-model="option.content"></textarea>
        <span>{{'is_continue_next' | translate}}:</span>
        <input class="unchecked" ng-class='{"checked": option.isHasNext}' type="checkbox" ng-model="option.isHasNext" />
        <br/>
        <span>{{'is_allow_customize_content' | translate}}:</span>
        <input class="unchecked" ng-class='{"checked": option.isCustomized}' type="checkbox" ng-model="option.isCustomized" />
        <br/>
        <div ng-if="question.isSetSkip && !question.isSingle">
          <span>{{'is_skip_group_one' | translate}}:</span>
          <input class="unchecked" ng-class='{"checked": option.isSkipOne}' type="checkbox" ng-model="option.isSkipOne" ng-click="switchSkipIndex(option, question.group)" />
        </div>
        <div ng-if="question.isSetSkip && question.isSingle">
          <span>{{'skip_index' | translate}}:</span>
          <input type="number" class="form-control" ng-model="option.skipIndex" />
        </div>
        <div style="text-align:right;margin-top: 10px;">
          <button class="btn btn-danger btn-xs" ng-click="deleteOption(question.addOptions, $index, question)">{{'delete_option' | translate}}</button>
        </div>
        <br/><br/><br/>
      </div>
      <div style="text-align:right;" ng-if="question.addOptions.length > 0">
        <button class="btn btn-success btn-sm" ng-disabled="isSubmit" ng-click="saveAddQuestionOptions(question, outerIndex + 1)">{{'save_add_option' | translate : {questionIndex : outerIndex + 1} }}</button>      
      </div>
      <br/><br/><br/>

      <div style="text-align:left;">
        <button class="btn btn-info btn-sm" style="margin-top:-160px;" ng-click="addOption(question)">{{'add_option' | translate}}</button>      
      </div>
    </div>

    <!--add questions-->
    <div ng-repeat="question in questionaire.addQuestions">
      <label for="question">{{'questionNo' | translate : {questionNo : questionaire.questions.length + $index + 1} }}:</label>
      <div class="ueditor" config="config" ready="ready" style="width:100%;height:200px;" ng-model="question.title"></div>
      <br/>
      <span>{{'is_single_choice' | translate}}:</span>
      <input class="unchecked" ng-class='{"checked": question.isSingle}' type="checkbox" ng-model="question.isSingle" ng-click="returnOrigin(question.isSingle, question.options)"/>
      <br/><br/>
      <span>{{'is_set_skip_index_for_option' | translate}}:</span>
      <input class="unchecked" ng-class='{"checked": question.isSetSkip}' type="checkbox" ng-model="question.isSetSkip" ng-click="switchIsSetSkipAdd(question)" />
      <br/>
      <div ng-if="question.isSetSkip && !question.isSingle">
        <div style="margin-left:20px;">
          <span>{{'group_one' | translate}}</span>
          <input type="text" class="form-control" ng-model="question.group.gp1" />
          <span>{{'group_two' | translate}}</span>
          <input type="text" class="form-control" ng-model="question.group.gp2" />
        </div>
      </div>
      <div style="text-align:right;">
        <button class="btn btn-danger btn-sm" style="margin-top:28px;" ng-click="deleteQuestionAdd($index)">{{'delete_question_index' | translate : {index : questionaire.questions.length + $index + 1} }}</button>      
      </div>
      <br/>
      <br/>
      <label>{{'option' | translate}}:</label>
      <div ng-repeat="item in question.options">
        <span>{{'option_content' | translate}}:</span>
        <textarea class="form-control" rows="5" ng-disabled="item.isCustomized" ng-model="item.content"></textarea>
        <span>{{'is_continue_next' | translate}}:</span>
        <input class="unchecked" ng-class='{"checked": item.isHasNext}' type="checkbox" ng-model="item.isHasNext" />
        <br/>
        <span>{{'is_allow_customize_content' | translate}}:</span>
        <input class="unchecked" ng-class='{"checked": item.isCustomized}' type="checkbox" ng-model="item.isCustomized" />
        <br/>
        <div ng-if="question.isSetSkip && !question.isSingle">
          <span>{{'is_skip_group_one' | translate}}:</span>
          <input class="unchecked" ng-class='{"checked": item.isSkipOne}' type="checkbox" ng-model="item.isSkipOne" ng-click="switchSkipIndex(item, question.group)" />
        </div>
        <div ng-if="question.isSetSkip && question.isSingle">
          <span>{{'skip_index' | translate}}:</span>
          <input type="number" class="form-control" ng-model="item.skipIndex" />
        </div>
        <div style="text-align:right;margin-top: 10px;">
          <button class="btn btn-danger btn-sm" ng-click="deleteOptionAdd(question.options, $index)">{{'delete_option' | translate}}</button>      
        </div>
        <br/><br/>
      </div>
      <br/><br/><br/>
      <div style="text-align:left;">
        <button class="btn btn-info btn-sm" style="margin-top:-160px;" ng-click="addOptionAddQuestion(question)">{{'add_option' | translate}}</button>      
      </div>
    </div>

  </div>
  <div style="text-align:left;margin-left:3%">
    <button class="btn btn-default" ng-click="cancel()">{{'return' | translate}}</button>
  </div>
  <div style="text-align:right;margin-top:-30px;margin-right: 22%;margin-bottom: 20px;">
    <button class="btn btn-info btn-sm" ng-click="addQuestion(questionaire)">{{'add_question' | translate}}</button>
  </div>
  <div style="text-align:right;margin-top:-50px;margin-right: 2%;">
    <button class="btn btn-success btn-sm" ng-disabled="isSubmit" ng-if="questionaire.addQuestions.length > 0" ng-click="saveAddQuestion(questionaire)">{{'save_add_question' | translate}}</button>      
  </div>
</form>

