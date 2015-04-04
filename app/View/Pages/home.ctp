<!-- curriculums view -->
<?php 
 /*SPECS:
    The view shows the list of curriculums as icons with labels.
    When the user clicks on an icon, the curriculum menu page is shown.
    The top bar has a search box. When the user enters a search term and clicks
    on the search button, only the curriculums corresponding to the search term are shown.
    When the user clicks on the favorites button a keyword ":favorites" is added at the end of the search term
    When the user clicks on the 'Show Menu' button a toolbar is presented.
    If the user is connected, then the user bar (User Menu) is shown, else
    	the default home bar (Home Menu) is shown*/
?>
<div id="curriculums" data-dojo-type="edu/wohaoo/mobile/CurriculumsView"
	selected="true"
	data-dojo-props="serviceUrl:'/curriculums/?q=${q}&l=${l}'">
	<h1 dojoType="dojox/mobile/Heading" fixed="top">
		<!-- the refresh button -->
		<div dojoType="dojox.mobile.ToolBarButton" 
		        icon="mblDomButtonAppHomeMenu" 
		        class="fontawesome showHomeMenuButton pull-left" 
		        id="showHomeMenuButton"></div>
		<div dojoType="dojox.mobile.ToolBarButton" 
		        icon="mblDomButtonAppFavorites" 
		        class="showFavoritesButton pull-right"
		        id="showFavoritesButton" ></div>
		<div class="pull-right text-align-left" style="display: inline-block;"> 
			<div role="search">
				  <div  class="form-search js-search-form"  id="global-nav-search">
					<label class="visuallyhidden" for="search-query">Search query</label>
					<input data-dojo-type="dojox/mobile/TextBox" class="search-input" type="text" id="search-query" 
						    data-dojo-props='placeholder:__t("Search")' name="q" autocomplete="off" spellcheck="false" 
						    aria-autocomplete="list" aria-haspopup="true" aria-controls="typeahead-dropdown-1">
					<span class="search-icon js-search-action">
						  <button type="submit" class="icon nav-search" tabindex="-1">
							<span class="visuallyhidden">Search</span>
						  </button>
					</span>
					<input disabled="disabled" class="search-input search-hinting-input" 
						    type="text" id="search-query-hint" autocomplete="off" spellcheck="false">
				  </div>
			</div>
		</div> 
	</h1>
	<ul data-dojo-type="dojox.mobile.TabBar" class="mblHidden homeMenu" data-dojo-props='fill: "auto"'
	       id="proHomeMenu">
		<li data-dojo-type="dojox/mobile/TabBarButton" class='tabBarButton fontawesome' 
		      data-dojo-props='moveTo: "login", label: __t("Login"), icon:"mblDomButtonLogin"'></li>
		<li data-dojo-type="dojox/mobile/TabBarButton" class='tabBarButton fontawesome' 
		      data-dojo-props='moveTo: "signup", label:__t("Join"), icon:"mblDomButtonRegister"'></li>
	</ul>
	<ul data-dojo-type="dojox.mobile.TabBar" class="mblHidden userMenu" data-dojo-props='fill: "auto"'
	       id="proUserMenu">
		<li data-dojo-type="dojox/mobile/TabBarButton" class='tabBarButton fontawesome' 
		      data-dojo-props='moveTo: "settings", label:__t("Settings"), icon:"mblDomButtonSettings"'></li>
		<li data-dojo-type="dojox/mobile/TabBarButton" class='tabBarButton fontawesome' 
		      data-dojo-props='moveTo: "logout", label:__t("Logout"), icon:"mblDomButtonLogout"'></li>
	</ul>
	<ul dojoType="dojox.mobile.IconContainer" 
	      class="itemList" 
	      data-dojo-props='moveTo:"curriculumPage", transition:"slide"'></ul>
</div>

<!-- curriculum page -->
<?php /* SPECS:
	The view shows a four button menubar (2 cols and 2 rows): info, documents, videos, and audios.
	When the user clicks on the info button, the curriculum info view is shown.
	When the user clicks on the documents button, the curriculum documents view is shown.
	When the user clicks on the videos button, the curriculum videos view is shown.
	When the user clicks on the audios button, the curriculum audios view is shown.
	At the bottom of the view there is a toolbar containing only one button: the "Modules" button.
	When the user clicks on the "Modules" button the list of modules of the current curriculum is shown.*/
?>
<div id="curriculumPage" 
        data-dojo-type="edu/wohaoo/mobile/ItemMenuPageView"
        data-dojo-props="serviceUrl: '/curriculums/view/${dataId}', itemPrefix: 'cur-' ">
	<h1 dojoType="dojox/mobile/Heading" data-dojo-props='back:__t("Home"), moveTo:"curriculums"' fixed="top">
		<div class="header">
		</div>
		<div dojoType="dojox.mobile.ToolBarButton" 
						icon="mblDomButtonAddFavorite" 
						class="addFavoriteButton pull-right"></div>
	</h1>
	<ul dojoType="dojox.mobile.IconMenu" style="width:274px;height:210px;margin:20px;" data-dojo-props='cols:2'>
		<li dojoType="dojox/mobile/IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "curriculumInfo", label:__t("info"), icon:"mblDomButtonInfo"'></li>
		<li dojoType="dojox.mobile.IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "curriculumDocuments", label:__t("documents"), icon:"mblDomButtonDocuments"'></li>
		<li dojoType="dojox.mobile.IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "curriculumVideos", label:__t("videos"), icon:"mblDomButtonVideos"'></li>
		<li dojoType="dojox.mobile.IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "curriculumAudios", label:__t("sounds"), icon:"mblDomButtonAudios"'></li>
	</ul>
	<div data-dojo-type="dojox.mobile.Heading">
		<span data-dojo-type="dojox.mobile.ToolBarButton" class='nextButton' 
			defaultColor="mblColorBlue" selColor="mblColorPink" 
			data-dojo-props='label:__t("Start"), moveTo:"modules", arrow:"right"' style="float:right;"></span>
	</div><br>
</div>


<!-- curriculum info -->
<?php /*SPECS:
	The view shows the list of attributes of the current curriculum.
	When the user clicks on the back button, the curriculum menu page is shown.*/
?>
<div id="curriculumInfo" data-dojo-type="edu/wohaoo/mobile/ItemInfoView"
	data-dojo-props="serviceUrl: '/curriculums/info/${dataId}', itemPrefix: 'cur-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"curriculumPage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- curriculum videos -->
<?php /* SPECS:
	The view shows the list of videos of the current curriculum. One video under the other.
	When the user clicks on the back button, the curriculum menu page is shown.*/
?>
<div id="curriculumVideos" data-dojo-type="edu/wohaoo/mobile/ItemVideoView"
	data-dojo-props="serviceUrl: '/curriculums/videos/${dataId}', itemPrefix: 'cur-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"curriculumPage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- curriculum audios -->
<?php /*SPECS:
	The view shows the list of audios of the current curriculum. One audio under the other.
	When the user clicks on the back button, the curriculum menu page is shown.*/
?>
<div id="curriculumAudios" data-dojo-type="edu/wohaoo/mobile/ItemAudioView"
	data-dojo-props="serviceUrl: '/curriculums/audios/${dataId}', itemPrefix: 'cur-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"curriculumPage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- curriculum documents -->
<?php /*SPECS:
	The view shows the list of documents of the current curriculum. One document under the other.
	When the user clicks on the back button, the curriculum menu page is shown.*/
?>
<div id="curriculumDocuments" data-dojo-type="edu/wohaoo/mobile/ItemDocumentView"
	data-dojo-props="serviceUrl: '/curriculums/documents/${dataId}', itemPrefix: 'cur-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"curriculumPage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- dlg_login_register -->
<div id="dlg_login_register"
		data-dojo-type="dojox/mobile/SimpleDialog"
		data-dojo-mixins="dojox.mobile._ContentPaneMixin"
		data-dojo-props='href:"/js/wohaooedu/lib/edu/wohaoo/mobile/templates/login_register.html"'></div>

<!-- dlg_join_curriculum -->
<div id="dlg_join_curriculum"
		data-dojo-type="dojox/mobile/SimpleDialog"
		data-dojo-mixins="dojox.mobile._ContentPaneMixin"
		data-dojo-props='href:"/js/wohaooedu/lib/edu/wohaoo/mobile/templates/join_curriculum.html"'></div>


<!-- modules -->
<?php /*SPECS
	The view shows the list of modules of a given curriculum.
	When the user clicks on the back button, the curriculum menu page is shown.*/
?>
<div id="modules" data-dojo-type="edu/wohaoo/mobile/ModulesView"
	data-dojo-props="serviceUrl:'/modules/index/${dataId}', itemPrefix: 'cur-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"curriculumPage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.RoundRectList"  data-dojo-props='select:"none"' class="itemList"></ul>
</div>


<!-- module page -->
<?php /*SPECS:
	The view shows a four button menubar (2 cols and 2 rows): info, documents, videos, and audios.
	When the user clicks on the info button, the module info view is shown.
	When the user clicks on the documents button, the module documents view is shown.
	When the user clicks on the videos button, the module videos view is shown.
	When the user clicks on the audios button, the module audios view is shown.
	At the bottom of the view there is a toolbar containing only one button: the "Test Yourself" button.
	When the user clicks on the "Test Yourself" button the exams of the current module is loaded.*/
?>
<div id="modulePage" 
        data-dojo-type="edu/wohaoo/mobile/ItemMenuPageView"
        data-dojo-props="serviceUrl: '/modules/view/${dataId}', itemPrefix: 'mod-'">
	<h1 dojoType="dojox/mobile/Heading" data-dojo-props='back:__t("Modules"), moveTo:"modules"' fixed="top">
		<div class="header">
		</div>
	</h1>
	<ul dojoType="dojox.mobile.IconMenu" style="width:274px;height:210px;margin:20px;" data-dojo-props='cols:2'>
		<li dojoType="dojox/mobile/IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "moduleInfo", label:__t("info"), icon:"mblDomButtonProInfo"'></li>
		<li dojoType="dojox.mobile.IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "moduleDocuments", label:__t("documents"), icon:"mblDomButtonDocuments"'></li>
		<li dojoType="dojox.mobile.IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "moduleVideos", label:__t("videos"), icon:"mblDomButtonVideos"'></li>
		<li dojoType="dojox.mobile.IconMenuItem" class='iconMenu' 
		      data-dojo-props='moveTo: "moduleAudios", label:__t("sounds"), icon:"mblDomButtonAudios"'></li>
	</ul>
	<div data-dojo-type="dojox.mobile.Heading">
		<span data-dojo-type="dojox.mobile.ToolBarButton" class='nextButton' 
			defaultColor="mblColorBlue" selColor="mblColorPink" 
			data-dojo-props='label:__t("Test Yourself"), moveTo: "exam", arrow:"right"' style="float:right;"></span>
	</div>
</div>

<!-- module info -->
<?php /*SPECS:
	The view shows the list of attributes of the current module.
	When the user clicks on the back button, the module menu page is shown.*/
?>
<div id="moduleInfo" data-dojo-type="edu/wohaoo/mobile/ItemInfoView"
	data-dojo-props="serviceUrl: '/modules/info/${dataId}', itemPrefix: 'mod-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"modulePage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- module videos -->
<?php /*SPECS:
	The view shows the list of videos of the current module. One video under the other.
	When the user clicks on the back button, the module menu page is shown.*/
?>
<div id="moduleVideos" data-dojo-type="edu/wohaoo/mobile/ItemVideoView"
	data-dojo-props="serviceUrl: '/modules/videos/${dataId}', itemPrefix: 'mod-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"modulePage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- module audios -->
<?php /* SPECS:
	The view shows the list of audios of the current module. One audio under the other.
	When the user clicks on the back button, the module menu page is shown.*/
?>
<div id="moduleAudios" data-dojo-type="edu/wohaoo/mobile/ItemAudioView"
	data-dojo-props="serviceUrl: '/modules/audios/${dataId}', itemPrefix: 'mod-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"modulePage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- module documents -->
<?php /*SPECS
	The view shows the list of modules of a given module.
	When the user clicks on the back button, the module menu page is shown.*/
?>
<div id="moduleDocuments" data-dojo-type="edu/wohaoo/mobile/ItemDocumentView"
	data-dojo-props="serviceUrl: '/modules/documents/${dataId}', itemPrefix: 'mod-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Menu"), moveTo:"modulePage"' fixed="top">
		<div class="header"></div>
	</h1>
	<ul dojoType="dojox.mobile.EdgeToEdgeList" class="itemList"></ul>
</div>

<!-- exam -->
<?php /*
	The view shows the exam of the given module.
	The server will check if the user has already completed the exam.
	If the user has already completed the exam, the view shows the users score along with the exam comment (passed, failed).
		The user is then asked wether he/she would want to retake the exam. If yes, a new exam session is created and
		the first question of the exam is runned.
	If the user has not already  already completed the exam, the first question of the exam session is returned and
	the list of possible answers (options) is displayed.
	When the user clicks on the "Next Question" Button, the view is refreshed.*/
?>
<div id="exam" data-dojo-type="edu/wohaoo/mobile/ExamView"
	data-dojo-props="serviceUrl: '/modules/exam/${dataId}/${sessionId}/${forceNew}?q=${questionId}&a=${optionId}', itemPrefix: 'mod-'">
	<h1 class="heading" dojoType="dojox/mobile/Heading" 
	       data-dojo-props='back: __t("Module"), moveTo:"modulePage"' fixed="top">
		<div class="header"></div>
	</h1>
	<h2 data-dojo-type="dojox.mobile.EdgeToEdgeCategory" class="questionCategory"></h2>
	<ul dojoType="dojox.mobile.RoundRectList" data-dojo-props='select:"single"'  class="itemList"></ul>
	<div data-dojo-type="dojox.mobile.RoundRect" class="mblHidden examDetails" data-dojo-props='shadow:true'>
		<table style="width:100%">
			<tr>
				<td><span>Exam completed: <span class="examScore bold"></span>&nbsp;(<span class="examComment bold"></span>)</span></td>
				<td style="text-align:right">
					<button class="mblRedButton examRetakeButton" 
							data-dojo-type="dojox.mobile.Button">Retake Exam</button></td>
			</tr>
		</table>
	</div>
	<div data-dojo-type="dojox.mobile.Heading">
		<span data-dojo-type="dojox.mobile.ToolBarButton" class='examNextButton' 
			defaultColor="mblColorBlue" selColor="mblColorPink" 
			data-dojo-props='label:"Next Question", moveTo: "exam", arrow:"right"' style="float:right;"></span>
	</div>
</div>

<!-- login View -->
<?php /*SPECS
	The view shows a login form (email/telephone number, password). When the user submits the form,
		if successful, the user is redirected to the view indicated by the redirectTo property,
		else, the same form is shown again to the user.*/
?>
<div id="login" 
        data-dojo-type="edu/wohaoo/mobile/LoginView"
        data-dojo-props="serviceUrl: '/users/login/', redirectTo: 'curriculums'">
        <h1 dojoType="dojox/mobile/Heading" data-dojo-props='back:__t("Home"), moveTo:"curriculums"' fixed="top">
		<div class="header"></div>
	</h1>
</div>

<!-- logout View -->
<?php /*SPECS
	The view shows a logout progress bar, then the user is redirected to the view indicated by the redirectTo property.*/
?>
<div id="logout" 
        data-dojo-type="edu/wohaoo/mobile/LogoutView"
        data-dojo-props="serviceUrl: '/users/logout/', redirectTo: 'curriculums'">
        <h1 dojoType="dojox/mobile/Heading" data-dojo-props='back:__t("Home"), moveTo:"curriculums"' fixed="top">
		<div class="header"></div>
	</h1>
</div>

<!-- signup View -->
<?php /*SPECS
	The view shows a registration form (first_name, last_name, birth date, telephone number, email, password). 
		When the user submits the form,
		if successful, the user is redirected to the view indicated by the redirectTo property,
		else, the same form is shown again to the user.*/
?>
<div id="signup" 
        data-dojo-type="edu/wohaoo/mobile/SignupView"
        data-dojo-props="serviceUrl: '/users/signup/', redirectTo: 'curriculums'">
        <h1 dojoType="dojox/mobile/Heading" data-dojo-props='back:__t("Home"), moveTo:"curriculums"' fixed="top">
		<div class="header"></div>
	</h1>
</div>

<!-- enroll View -->
<?php /*SPECS
	The view shows an enrollment form (transaction id). 
		When the user submits the form,
		if successful, the user is redirected to the view indicated by the redirectTo property,
		else, the same form is shown again to the user.*/
?>
<div id="enroll" 
        data-dojo-type="edu/wohaoo/mobile/EnrollView"
        data-dojo-props="serviceUrl: '/users/enroll', redirectTo: 'modules'">
        <h1 dojoType="dojox/mobile/Heading" data-dojo-props='back:__t("Home"), moveTo:"modules"' fixed="top">
		<div class="header"></div>
	</h1>
</div>

<!-- settings View -->
<?php /*SPECS
	this view enables the user to setup personal prefrerences:
		-first name
		-last name
		-birth date
		-language*/
?>
<div id="settings" 
        data-dojo-type="edu/wohaoo/mobile/SettingsView"
        data-dojo-props="serviceUrl: '/users/settings/', itemPrefix: 'usr-', redirectTo: 'curriculums'">
        <h1 dojoType="dojox/mobile/Heading" data-dojo-props='back:__t("Home"), moveTo:"curriculums"' fixed="top">
		<div class="header"></div>
	</h1>
</div>

<div id="dlg_message" data-dojo-type="edu/wohaoo/mobile/MessageDialog">
	<div class="mblSimpleDialogTitle">Information</div>
	<div class="mblSimpleDialogText">This is a sample message.</div>
	<button data-dojo-type="dojox.mobile.Button" class="mblSimpleDialogButton" style="width:100px;" onclick="hide('dlg_message')">OK</button>
</div>

<div id="dlg_confirm" data-dojo-type="edu/wohaoo/mobile/ConfirmDialog">
	<div class="mblSimpleDialogTitle">Rain Alert</div>
	<div class="mblSimpleDialogText">Do you have an umbrella?</div>
	<button data-dojo-type="dojox.mobile.Button" class="mblSimpleDialogButton noButton" onclick="hide('dlg_confirm')">No</button>
	<button data-dojo-type="dojox.mobile.Button" class="mblSimpleDialogButton yesButton mblBlueButton" onclick="hide('dlg_confirm')">Yes</button>
</div>

<div id="dlg_person_data" 
	data-dojo-type="edu/wohaoo/mobile/PersonDataView">
</div>

<div id="dlg_language_select" 
	data-dojo-props="serviceUrl: '/langs/autocomplete?use_codes=1&viewClass=Json'"
	data-dojo-type="edu/wohaoo/mobile/LanguageDialogView">
</div>