var profile = (function(){ //to run this profile go to the ROOt dir and type ROOT>dojo-src/util/buildScripts/build.sh --profile myapp.profile.js
    return {
        basePath: "./dojo-src", //relative to where the profile file is located
        releaseDir: "../wohaooedu",
        releaseName: "lib",
        action: "release",
        layerOptimize: "closure",
        optimize: "closure",
        cssOptimize: "comments",
        mini: true,
        stripConsole: "warn",
        selectorEngine: "lite",
        cssOptimize: "comments",
 
        defaultConfig: {
            hasCache:{
                "dojo-built": 1,
                "dojo-loader": 1,
                "dom": 1,
                "host-browser": 1,
                "config-selectorEngine": "lite"
            },
            async: 1
        },
 
        staticHasFeatures: {
            "config-deferredInstrumentation": 0,
            "config-dojo-loader-catches": 0,
            "config-tlmSiblingOfDojo": 0,
            "dojo-amd-factory-scan": 0,
            "dojo-combo-api": 0,
            "dojo-config-api": 1,
            "dojo-config-require": 0,
            "dojo-debug-messages": 0,
            "dojo-dom-ready-api": 1,
            "dojo-firebug": 0,
            "dojo-guarantee-console": 1,
            "dojo-has-api": 1,
            "dojo-inject-api": 1,
            "dojo-loader": 1,
            "dojo-log-api": 0,
            "dojo-modulePaths": 0,
            "dojo-moduleUrl": 0,
            "dojo-publish-privates": 0,
            "dojo-requirejs-api": 0,
            "dojo-sniff": 1,
            "dojo-sync-loader": 0,
            "dojo-test-sniff": 0,
            "dojo-timeout-api": 0,
            "dojo-trace-api": 0,
            "dojo-undef-api": 0,
            "dojo-v1x-i18n-Api": 1,
            "dom": 1,
            "host-browser": 1,
            "extend-dojo": 1
        },
 
        packages:[{
            name: "dojo",
            location: "dojo"
        },{
            name: "dijit",
            location: "dijit"
        },{
            name: "dojox",
            location: "dojox"
        },{
            name: "edu",
            location: "edu"
        }],
 
        layers: {
            "dojo/dojo": {
                include: [ "dojo/dojo","dojo/loadInit",
        	"dijit/dijit",
		 "dojox/mobile/parser",
		 "dojo/query", "dojox/mobile/IconMenu",
		 "dojox/mobile/IconMenuItem",
		 "dojox/mobile",
		 "dojox/mobile/TabBar",
		 "dojox/mobile/Heading",
		 "dojox/mobile/ToolBarButton",
		 "dojox/mobile/TabBarButton",
		 "dojox/mobile/IconMenuItem"],
                customBase: true,
                boot: true
            },
            "edu/wohaoo": {
                include: [
                	"edu/wohaoo/mobile/_AppViewMixin",
			"edu/wohaoo/mobile/_T9nMixin",
			"edu/wohaoo/mobile/AppListView",
			"edu/wohaoo/mobile/ConfirmDialog",
			"edu/wohaoo/mobile/CurriculumsView",
			"edu/wohaoo/mobile/EnrollView",
			"edu/wohaoo/mobile/ExamView",
			"edu/wohaoo/mobile/ItemAudioView",
			"edu/wohaoo/mobile/ItemDocumentView",
			"edu/wohaoo/mobile/ItemInfoView",
			"edu/wohaoo/mobile/ItemMenuPageView",
			"edu/wohaoo/mobile/ItemPageView",
			"edu/wohaoo/mobile/ItemVideoView",
			"edu/wohaoo/mobile/LanguageDialogView",
			"edu/wohaoo/mobile/LoginView",
			"edu/wohaoo/mobile/LogoutView",
			"edu/wohaoo/mobile/MessageDialog",
			"edu/wohaoo/mobile/ModulesView",
			"edu/wohaoo/mobile/PersonDataView",
			"edu/wohaoo/mobile/SettingsView",
			"edu/wohaoo/mobile/SignupView"
                ]
            }
        }
    };
})();