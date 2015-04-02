var profile = (function(){ //to run this profile go to the ROOt dir and type ROOT>dojo-src/util/buildScripts/build.sh --profile myapp.profile.js
    return {
        basePath: "./dojo-src", //relative to where the profile file is located
        releaseDir: "../footnation",
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
            name: "fn",
            location: "fn"
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
            "fn/mobile": {
                include: [ "fn/dojox/mobile/HeadingI18n",
		 "fn/dojox/mobile/IconMenuItemI18n",
		 "fn/mobile/ProfessionalsView",
		 "fn/mobile/ProMenuPageView",
		 "fn/mobile/ProfessionalInfoView",
		 "fn/mobile/ProfessionalActivityView",
		 "fn/mobile/ProfessionalStatsView",
		 "fn/mobile/ProfessionalNewsView",
		 "fn/mobile/ProSettingsView",
		 "fn/mobile/ClubsView",
		 "fn/mobile/ClubMenuPageView",
		 "fn/mobile/ClubInfoView",
		 "fn/mobile/ClubActivityView",
		 "fn/mobile/ClubStatsView",
		 "fn/mobile/ClubNewsView",
		 "fn/mobile/ClubSettingsView",
		  "fn/mobile/RingtonePicker",
		 "fn/mobile/LoginView"]
            }
        }
    };
})();