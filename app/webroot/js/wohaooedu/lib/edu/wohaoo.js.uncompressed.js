require({cache:{
'edu/wohaoo/mobile/_AppViewMixin':function(){
String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, number) {
        return typeof args[number] != 'undefined'
      ? args[number].toString()
      : match
    });
};

define([
   "dojo/query!css3", "dojo/on", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/registry", "dojox/mobile/Overlay", "dojo/i18n!./nls/Errors",
      "dojo/i18n!./nls/Messages", "dojo/domReady!"
], function(dojoQuery, on, dojoConnect, declare, _WidgetBase, domConstruct, dijitRegistry, 
	MobileOverlay, sysErrors, sysMessages) {
	return declare([_WidgetBase],{
		dataParams: null,
		serviceUrl: null,
		errors: sysErrors,
		messages: sysMessages,
		
		_setServiceUrlAttr: function(/*String*/ url){
		 	 if(typeof Sys !== 'undefined'){
		 	 	var httpScheme = Sys.getHttpScheme();
		 	 	var httpHost = Sys.getHttpHost();
		 	 	if(httpScheme && httpHost){
		 	 		url = httpScheme + "://" + httpHost  + url;
		 	 	}
		 	 }
			 this.serviceUrl = url;
		 },
		query: {},
		dataParams: [],
		dataId: false,
		busy: false,
		alert: function(str){
			if(typeof Sys !== 'undefined')
				Sys.message(str);
			else
				alert(str);
		},
		
		animate: function(){},
		
		postCreate: function(){
			var self = this;
			on(this.domNode, 'busy', function(e){
				console.debug("busy" + e.state);
				self.busy = e.state;
				var overlay = dijitRegistry.byId(this.id + "_overlay");
				if(self.busy && overlay){
					overlay.show();
					console.debug("showing overlay...");
				}else
				if(overlay){
					overlay.hide();
					console.debug("hiding overlay...");
				}
			});
			this.inherited(arguments);
		},
		
		processDataParams: function(){
			if(this.dataParams && this.dataParams.length > 0){
				var hasParams = this.dataUrl.indexOf("?") >= 0;
				if(hasParams){
					this.dataUrl = this.dataUrl + "&";
				}else{
					this.dataUrl = this.dataUrl + "?";
				}
				for(var iParam in this.dataParams){
					this.dataUrl += this.dataParams[iParam] + "&";
				}
			}
		},
		
		startup: function(){
			this.inherited(arguments);
			
			new MobileOverlay({
				"id": this.id + "_overlay",
				"class": 'overlay',
				"innerHTML": "<div class='waiting'><img src='/img/loading.gif'></div>"
			}).placeAt(this.domNode,"last");
			this.refresh();
		},
		
		moveTo: function(from, to, transition){
			if(typeof transition === 'undefined')
				transition = 'slide';
			dijitRegistry.byId(from).performTransition(to,1,transition);
		},
		
		startBusy: function(/*String*/ msg){
			on.emit(this.domNode, "busy", {message: msg, state: true});
		},
		
		endBusy: function(/*String*/ msg){
			on.emit(this.domNode, "busy", {message: msg, state: false});
		},
		
		refresh: function(){
		},
		
		createListItem: function(itemClass, id, caption, itemMoveTo, itemTransition){
		},
		
		//replaces url with full path url
		replaceUrl: function(url, pattern, replacement){
			return url.replace(pattern, replacement);
		},
		
		// Pushes data into a template - primitive
		substitute: function(template, obj) {
			return template.replace(/\$\{([^\s\:\}]+)(?:\:([^\s\:\}]+))?\}/g, function(match,key){
				if(typeof obj === 'function')
					return obj.call(this, key);
				return obj[key];
			});
		}
	
	});
});
},
'dojox/mobile/Overlay':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/sniff",
	"dojo/_base/window",
	"dojo/dom-class",
	"dojo/dom-geometry",
	"dojo/dom-style",
	"dojo/window",
	"dijit/_WidgetBase",
	"dojo/_base/array",
	"dijit/registry",
	"dojo/touch",
	"./viewRegistry",
	"./_css3"
], function(declare, lang, has, win, domClass, domGeometry, domStyle, windowUtils, WidgetBase, array, registry, touch, viewRegistry, css3){

	return declare("dojox.mobile.Overlay", WidgetBase, {
		// summary:
		//		A non-templated widget that animates up from the bottom, 
		//		overlaying the current content.

		// baseClass: String
		//		The name of the CSS class of this widget.
		baseClass: "mblOverlay mblOverlayHidden",

		buildRendering: function(){
			this.inherited(arguments);
			if(!this.containerNode){
				// set containerNode so that getChildren() works
				this.containerNode = this.domNode;
			}
		},

		_reposition: function(){
			// summary:
			//		Position the overlay at the bottom
			// tags:
			//		private
			var popupPos = domGeometry.position(this.domNode);
			var vp = windowUtils.getBox();
			// search for the scrollable parent if any 
			var scrollableParent = viewRegistry.getEnclosingScrollable(this.domNode);
			// update vp scroll position if the overlay is inside a scrollable
		 	if(scrollableParent){
		 		vp.t -= scrollableParent.getPos().y;
		 	}
		 	// reposition if needed 
		 	if((popupPos.y+popupPos.h) != vp.h // TODO: should be a has() test for position:fixed not scrolling
				|| (domStyle.get(this.domNode, 'position') != 'absolute' && has('android') < 3)){ // android 2.x supports position:fixed but child transforms don't persist
				popupPos.y = vp.t + vp.h - popupPos.h;
				domStyle.set(this.domNode, { position: "absolute", top: popupPos.y + "px", bottom: "auto" });
			}
			return popupPos;
		},

		show: function(/*DomNode?*/aroundNode){
			// summary:
			//		Scroll the overlay up into view
			array.forEach(registry.findWidgets(this.domNode), function(w){
				if(w && w.height == "auto" && typeof w.resize == "function"){
					w.resize();
				}
			});
			var popupPos = this._reposition();
			if(aroundNode){
				var aroundPos = domGeometry.position(aroundNode);
				if(popupPos.y < aroundPos.y){ // if the aroundNode is under the popup, try to scroll it up
					// TODO: if this widget has a scrollable parent, use its scrollTo method to make sure the aroundNode is visible?
					win.global.scrollBy(0, aroundPos.y + aroundPos.h - popupPos.y);
					this._reposition();
				}
			}
			var _domNode = this.domNode;
			domClass.replace(_domNode, ["mblCoverv", "mblIn"], ["mblOverlayHidden", "mblRevealv", "mblOut", "mblReverse", "mblTransition"]);
			this.defer(function(){
				var handler = this.connect(_domNode, css3.name("transitionEnd"), function(){
					this.disconnect(handler);
					domClass.remove(_domNode, ["mblCoverv", "mblIn", "mblTransition"]);
					this._reposition();
				});
				domClass.add(_domNode, "mblTransition");
			}, 100);
			var skipReposition = false;

			this._moveHandle = this.connect(win.doc.documentElement, touch.move,
				function(){
					skipReposition = true;
				}
			);
			this._repositionTimer = setInterval(lang.hitch(this, function(){
				if(skipReposition){ // don't reposition if busy
					skipReposition = false;
					return;
				}
				this._reposition();
			}), 50); // yield a short time to allow for consolidation for better CPU throughput
			return popupPos;
		},

		hide: function(){
			// summary:
			//		Scroll the overlay down and then make it invisible
			var _domNode = this.domNode;
			if(this._moveHandle){
				this.disconnect(this._moveHandle);
				this._moveHandle = null;
				clearInterval(this._repositionTimer);
				this._repositionTimer = null;
			}
			if(has("css3-animations")){
				domClass.replace(_domNode, ["mblRevealv", "mblOut", "mblReverse"], ["mblCoverv", "mblIn", "mblOverlayHidden", "mblTransition"]);
				this.defer(function(){
					var handler = this.connect(_domNode, css3.name("transitionEnd"), function(){
						this.disconnect(handler);
						domClass.replace(_domNode, ["mblOverlayHidden"], ["mblRevealv", "mblOut", "mblReverse", "mblTransition"]);
					});
					domClass.add(_domNode, "mblTransition");
				}, 100);
			}else{
				domClass.replace(_domNode, ["mblOverlayHidden"], ["mblCoverv", "mblIn", "mblRevealv", "mblOut", "mblReverse"]);
			}
		},

		onBlur: function(/*Event*/e){
			return false; // touching outside the overlay area does not call hide()
		}
	});
});

},
'dojo/i18n':function(){
define(["./_base/kernel", "require", "./has", "./_base/array", "./_base/config", "./_base/lang", "./_base/xhr", "./json", "module"],
	function(dojo, require, has, array, config, lang, xhr, json, module){

	// module:
	//		dojo/i18n

	has.add("dojo-preload-i18n-Api",
		// if true, define the preload localizations machinery
		1
	);

	 1 || has.add("dojo-v1x-i18n-Api",
		// if true, define the v1.x i18n functions
		1
	);

	var
		thisModule = dojo.i18n =
			{
				// summary:
				//		This module implements the dojo/i18n! plugin and the v1.6- i18n API
				// description:
				//		We choose to include our own plugin to leverage functionality already contained in dojo
				//		and thereby reduce the size of the plugin compared to various loader implementations. Also, this
				//		allows foreign AMD loaders to be used without their plugins.
			},

		nlsRe =
			// regexp for reconstructing the master bundle name from parts of the regexp match
			// nlsRe.exec("foo/bar/baz/nls/en-ca/foo") gives:
			// ["foo/bar/baz/nls/en-ca/foo", "foo/bar/baz/nls/", "/", "/", "en-ca", "foo"]
			// nlsRe.exec("foo/bar/baz/nls/foo") gives:
			// ["foo/bar/baz/nls/foo", "foo/bar/baz/nls/", "/", "/", "foo", ""]
			// so, if match[5] is blank, it means this is the top bundle definition.
			// courtesy of http://requirejs.org
			/(^.*(^|\/)nls)(\/|$)([^\/]*)\/?([^\/]*)/,

		getAvailableLocales = function(
			root,
			locale,
			bundlePath,
			bundleName
		){
			// summary:
			//		return a vector of module ids containing all available locales with respect to the target locale
			//		For example, assuming:
			//
			//		- the root bundle indicates specific bundles for "fr" and "fr-ca",
			//		-  bundlePath is "myPackage/nls"
			//		- bundleName is "myBundle"
			//
			//		Then a locale argument of "fr-ca" would return
			//
			//			["myPackage/nls/myBundle", "myPackage/nls/fr/myBundle", "myPackage/nls/fr-ca/myBundle"]
			//
			//		Notice that bundles are returned least-specific to most-specific, starting with the root.
			//
			//		If root===false indicates we're working with a pre-AMD i18n bundle that doesn't tell about the available locales;
			//		therefore, assume everything is available and get 404 errors that indicate a particular localization is not available

			for(var result = [bundlePath + bundleName], localeParts = locale.split("-"), current = "", i = 0; i<localeParts.length; i++){
				current += (current ? "-" : "") + localeParts[i];
				if(!root || root[current]){
					result.push(bundlePath + current + "/" + bundleName);
					result.specificity = current;
				}
			}
			return result;
		},

		cache = {},

		getBundleName = function(moduleName, bundleName, locale){
			locale = locale ? locale.toLowerCase() : dojo.locale;
			moduleName = moduleName.replace(/\./g, "/");
			bundleName = bundleName.replace(/\./g, "/");
			return (/root/i.test(locale)) ?
				(moduleName + "/nls/" + bundleName) :
				(moduleName + "/nls/" + locale + "/" + bundleName);
		},

		getL10nName = dojo.getL10nName = function(moduleName, bundleName, locale){
			return moduleName = module.id + "!" + getBundleName(moduleName, bundleName, locale);
		},

		doLoad = function(require, bundlePathAndName, bundlePath, bundleName, locale, load){
			// summary:
			//		get the root bundle which instructs which other bundles are required to construct the localized bundle
			require([bundlePathAndName], function(root){
				var current = lang.clone(root.root || root.ROOT),// 1.6 built bundle defined ROOT
					availableLocales = getAvailableLocales(!root._v1x && root, locale, bundlePath, bundleName);
				require(availableLocales, function(){
					for (var i = 1; i<availableLocales.length; i++){
						current = lang.mixin(lang.clone(current), arguments[i]);
					}
					// target may not have been resolve (e.g., maybe only "fr" exists when "fr-ca" was requested)
					var target = bundlePathAndName + "/" + locale;
					cache[target] = current;
					current.$locale = availableLocales.specificity;
					load();
				});
			});
		},

		normalize = function(id, toAbsMid){
			// summary:
			//		id may be relative.
			//		preload has form `*preload*<path>/nls/<module>*<flattened locales>` and
			//		therefore never looks like a relative
			return /^\./.test(id) ? toAbsMid(id) : id;
		},

		getLocalesToLoad = function(targetLocale){
			var list = config.extraLocale || [];
			list = lang.isArray(list) ? list : [list];
			list.push(targetLocale);
			return list;
		},

		load = function(id, require, load){
			// summary:
			//		id is in one of the following formats
			//
			//		1. <path>/nls/<bundle>
			//			=> load the bundle, localized to config.locale; load all bundles localized to
			//			config.extraLocale (if any); return the loaded bundle localized to config.locale.
			//
			//		2. <path>/nls/<locale>/<bundle>
			//			=> load then return the bundle localized to <locale>
			//
			//		3. *preload*<path>/nls/<module>*<JSON array of available locales>
			//			=> for config.locale and all config.extraLocale, load all bundles found
			//			in the best-matching bundle rollup. A value of 1 is returned, which
			//			is meaningless other than to say the plugin is executing the requested
			//			preloads
			//
			//		In cases 1 and 2, <path> is always normalized to an absolute module id upon entry; see
			//		normalize. In case 3, it <path> is assumed to be absolute; this is arranged by the builder.
			//
			//		To load a bundle means to insert the bundle into the plugin's cache and publish the bundle
			//		value to the loader. Given <path>, <bundle>, and a particular <locale>, the cache key
			//
			//			<path>/nls/<bundle>/<locale>
			//
			//		will hold the value. Similarly, then plugin will publish this value to the loader by
			//
			//			define("<path>/nls/<bundle>/<locale>", <bundle-value>);
			//
			//		Given this algorithm, other machinery can provide fast load paths be preplacing
			//		values in the plugin's cache, which is public. When a load is demanded the
			//		cache is inspected before starting any loading. Explicitly placing values in the plugin
			//		cache is an advanced/experimental feature that should not be needed; use at your own risk.
			//
			//		For the normal AMD algorithm, the root bundle is loaded first, which instructs the
			//		plugin what additional localized bundles are required for a particular locale. These
			//		additional locales are loaded and a mix of the root and each progressively-specific
			//		locale is returned. For example:
			//
			//		1. The client demands "dojo/i18n!some/path/nls/someBundle
			//
			//		2. The loader demands load(some/path/nls/someBundle)
			//
			//		3. This plugin require's "some/path/nls/someBundle", which is the root bundle.
			//
			//		4. Assuming config.locale is "ab-cd-ef" and the root bundle indicates that localizations
			//		are available for "ab" and "ab-cd-ef" (note the missing "ab-cd", then the plugin
			//		requires "some/path/nls/ab/someBundle" and "some/path/nls/ab-cd-ef/someBundle"
			//
			//		5. Upon receiving all required bundles, the plugin constructs the value of the bundle
			//		ab-cd-ef as...
			//
			//				mixin(mixin(mixin({}, require("some/path/nls/someBundle"),
			//		  			require("some/path/nls/ab/someBundle")),
			//					require("some/path/nls/ab-cd-ef/someBundle"));
			//
			//		This value is inserted into the cache and published to the loader at the
			//		key/module-id some/path/nls/someBundle/ab-cd-ef.
			//
			//		The special preload signature (case 3) instructs the plugin to stop servicing all normal requests
			//		(further preload requests will be serviced) until all ongoing preloading has completed.
			//
			//		The preload signature instructs the plugin that a special rollup module is available that contains
			//		one or more flattened, localized bundles. The JSON array of available locales indicates which locales
			//		are available. Here is an example:
			//
			//			*preload*some/path/nls/someModule*["root", "ab", "ab-cd-ef"]
			//
			//		This indicates the following rollup modules are available:
			//
			//			some/path/nls/someModule_ROOT
			//			some/path/nls/someModule_ab
			//			some/path/nls/someModule_ab-cd-ef
			//
			//		Each of these modules is a normal AMD module that contains one or more flattened bundles in a hash.
			//		For example, assume someModule contained the bundles some/bundle/path/someBundle and
			//		some/bundle/path/someOtherBundle, then some/path/nls/someModule_ab would be expressed as follows:
			//
			//			define({
			//				some/bundle/path/someBundle:<value of someBundle, flattened with respect to locale ab>,
			//				some/bundle/path/someOtherBundle:<value of someOtherBundle, flattened with respect to locale ab>,
			//			});
			//
			//		E.g., given this design, preloading for locale=="ab" can execute the following algorithm:
			//
			//			require(["some/path/nls/someModule_ab"], function(rollup){
			//				for(var p in rollup){
			//					var id = p + "/ab",
			//					cache[id] = rollup[p];
			//					define(id, rollup[p]);
			//				}
			//			});
			//
			//		Similarly, if "ab-cd" is requested, the algorithm can determine that "ab" is the best available and
			//		load accordingly.
			//
			//		The builder will write such rollups for every layer if a non-empty localeList  profile property is
			//		provided. Further, the builder will include the following cache entry in the cache associated with
			//		any layer.
			//
			//			"*now":function(r){r(['dojo/i18n!*preload*<path>/nls/<module>*<JSON array of available locales>']);}
			//
			//		The *now special cache module instructs the loader to apply the provided function to context-require
			//		with respect to the particular layer being defined. This causes the plugin to hold all normal service
			//		requests until all preloading is complete.
			//
			//		Notice that this algorithm is rarely better than the standard AMD load algorithm. Consider the normal case
			//		where the target locale has a single segment and a layer depends on a single bundle:
			//
			//		Without Preloads:
			//
			//		1. Layer loads root bundle.
			//		2. bundle is demanded; plugin loads single localized bundle.
			//
			//		With Preloads:
			//
			//		1. Layer causes preloading of target bundle.
			//		2. bundle is demanded; service is delayed until preloading complete; bundle is returned.
			//
			//		In each case a single transaction is required to load the target bundle. In cases where multiple bundles
			//		are required and/or the locale has multiple segments, preloads still requires a single transaction whereas
			//		the normal path requires an additional transaction for each additional bundle/locale-segment. However all
			//		of these additional transactions can be done concurrently. Owing to this analysis, the entire preloading
			//		algorithm can be discard during a build by setting the has feature dojo-preload-i18n-Api to false.

			if(has("dojo-preload-i18n-Api")){
				var split = id.split("*"),
					preloadDemand = split[1] == "preload";
				if(preloadDemand){
					if(!cache[id]){
						// use cache[id] to prevent multiple preloads of the same preload; this shouldn't happen, but
						// who knows what over-aggressive human optimizers may attempt
						cache[id] = 1;
						preloadL10n(split[2], json.parse(split[3]), 1, require);
					}
					// don't stall the loader!
					load(1);
				}
				if(preloadDemand || waitForPreloads(id, require, load)){
					return;
				}
			}

			var match = nlsRe.exec(id),
				bundlePath = match[1] + "/",
				bundleName = match[5] || match[4],
				bundlePathAndName = bundlePath + bundleName,
				localeSpecified = (match[5] && match[4]),
				targetLocale =	localeSpecified || dojo.locale || "",
				loadTarget = bundlePathAndName + "/" + targetLocale,
				loadList = localeSpecified ? [targetLocale] : getLocalesToLoad(targetLocale),
				remaining = loadList.length,
				finish = function(){
					if(!--remaining){
						load(lang.delegate(cache[loadTarget]));
					}
				};
			array.forEach(loadList, function(locale){
				var target = bundlePathAndName + "/" + locale;
				if(has("dojo-preload-i18n-Api")){
					checkForLegacyModules(target);
				}
				if(!cache[target]){
					doLoad(require, bundlePathAndName, bundlePath, bundleName, locale, finish);
				}else{
					finish();
				}
			});
		};

	if(has("dojo-unit-tests")){
		var unitTests = thisModule.unitTests = [];
	}

	if(has("dojo-preload-i18n-Api") ||  1 ){
		var normalizeLocale = thisModule.normalizeLocale = function(locale){
				var result = locale ? locale.toLowerCase() : dojo.locale;
				return result == "root" ? "ROOT" : result;
			},

			isXd = function(mid, contextRequire){
				return ( 0  &&  1 ) ?
					contextRequire.isXdUrl(require.toUrl(mid + ".js")) :
					true;
			},

			preloading = 0,

			preloadWaitQueue = [],

			preloadL10n = thisModule._preloadLocalizations = function(/*String*/bundlePrefix, /*Array*/localesGenerated, /*boolean?*/ guaranteedAmdFormat, /*function?*/ contextRequire){
				// summary:
				//		Load available flattened resource bundles associated with a particular module for dojo/locale and all dojo/config.extraLocale (if any)
				// description:
				//		Only called by built layer files. The entire locale hierarchy is loaded. For example,
				//		if locale=="ab-cd", then ROOT, "ab", and "ab-cd" are loaded. This is different than v1.6-
				//		in that the v1.6- would only load ab-cd...which was *always* flattened.
				//
				//		If guaranteedAmdFormat is true, then the module can be loaded with require thereby circumventing the detection algorithm
				//		and the extra possible extra transaction.

				// If this function is called from legacy code, then guaranteedAmdFormat and contextRequire will be undefined. Since the function
				// needs a require in order to resolve module ids, fall back to the context-require associated with this dojo/i18n module, which
				// itself may have been mapped.
				contextRequire = contextRequire || require;

				function doRequire(mid, callback){
					if(isXd(mid, contextRequire) || guaranteedAmdFormat){
						contextRequire([mid], callback);
					}else{
						syncRequire([mid], callback, contextRequire);
					}
				}

				function forEachLocale(locale, func){
					// given locale= "ab-cd-ef", calls func on "ab-cd-ef", "ab-cd", "ab", "ROOT"; stops calling the first time func returns truthy
					var parts = locale.split("-");
					while(parts.length){
						if(func(parts.join("-"))){
							return;
						}
						parts.pop();
					}
					func("ROOT");
				}

				function preload(locale){
					locale = normalizeLocale(locale);
					forEachLocale(locale, function(loc){
						if(array.indexOf(localesGenerated, loc)>=0){
							var mid = bundlePrefix.replace(/\./g, "/")+"_"+loc;
							preloading++;
							doRequire(mid, function(rollup){
								for(var p in rollup){
									cache[require.toAbsMid(p) + "/" + loc] = rollup[p];
								}
								--preloading;
								while(!preloading && preloadWaitQueue.length){
									load.apply(null, preloadWaitQueue.shift());
								}
							});
							return true;
						}
						return false;
					});
				}

				preload();
				array.forEach(dojo.config.extraLocale, preload);
			},

			waitForPreloads = function(id, require, load){
				if(preloading){
					preloadWaitQueue.push([id, require, load]);
				}
				return preloading;
			},

			checkForLegacyModules = function()
				{};
	}

	if( 1 ){
		// this code path assumes the dojo loader and won't work with a standard AMD loader
		var amdValue = {},
			evalBundle =
				// use the function ctor to keep the minifiers away (also come close to global scope, but this is secondary)
				new Function(
					"__bundle",				   // the bundle to evalutate
					"__checkForLegacyModules", // a function that checks if __bundle defined __mid in the global space
					"__mid",				   // the mid that __bundle is intended to define
					"__amdValue",

					// returns one of:
					//		1 => the bundle was an AMD bundle
					//		a legacy bundle object that is the value of __mid
					//		instance of Error => could not figure out how to evaluate bundle

					  // used to detect when __bundle calls define
					  "var define = function(mid, factory){define.called = 1; __amdValue.result = factory || mid;},"
					+ "	   require = function(){define.called = 1;};"

					+ "try{"
					+		"define.called = 0;"
					+		"eval(__bundle);"
					+		"if(define.called==1)"
								// bundle called define; therefore signal it's an AMD bundle
					+			"return __amdValue;"

					+		"if((__checkForLegacyModules = __checkForLegacyModules(__mid)))"
								// bundle was probably a v1.6- built NLS flattened NLS bundle that defined __mid in the global space
					+			"return __checkForLegacyModules;"

					+ "}catch(e){}"
					// evaulating the bundle was *neither* an AMD *nor* a legacy flattened bundle
					// either way, re-eval *after* surrounding with parentheses

					+ "try{"
					+		"return eval('('+__bundle+')');"
					+ "}catch(e){"
					+		"return e;"
					+ "}"
				),

			syncRequire = function(deps, callback, require){
				var results = [];
				array.forEach(deps, function(mid){
					var url = require.toUrl(mid + ".js");

					function load(text){
						var result = evalBundle(text, checkForLegacyModules, mid, amdValue);
						if(result===amdValue){
							// the bundle was an AMD module; re-inject it through the normal AMD path
							// we gotta do this since it could be an anonymous module and simply evaluating
							// the text here won't provide the loader with the context to know what
							// module is being defined()'d. With browser caching, this should be free; further
							// this entire code path can be circumvented by using the AMD format to begin with
							results.push(cache[url] = amdValue.result);
						}else{
							if(result instanceof Error){
								console.error("failed to evaluate i18n bundle; url=" + url, result);
								result = {};
							}
							// nls/<locale>/<bundle-name> indicates not the root.
							results.push(cache[url] = (/nls\/[^\/]+\/[^\/]+$/.test(url) ? result : {root:result, _v1x:1}));
						}
					}

					if(cache[url]){
						results.push(cache[url]);
					}else{
						var bundle = require.syncLoadNls(mid);
						// don't need to check for legacy since syncLoadNls returns a module if the module
						// (1) was already loaded, or (2) was in the cache. In case 1, if syncRequire is called
						// from getLocalization --> load, then load will have called checkForLegacyModules() before
						// calling syncRequire; if syncRequire is called from preloadLocalizations, then we
						// don't care about checkForLegacyModules() because that will be done when a particular
						// bundle is actually demanded. In case 2, checkForLegacyModules() is never relevant
						// because cached modules are always v1.7+ built modules.
						if(bundle){
							results.push(bundle);
						}else{
							if(!xhr){
								try{
									require.getText(url, true, load);
								}catch(e){
									results.push(cache[url] = {});
								}
							}else{
								xhr.get({
									url:url,
									sync:true,
									load:load,
									error:function(){
										results.push(cache[url] = {});
									}
								});
							}
						}
					}
				});
				callback && callback.apply(null, results);
			};

		checkForLegacyModules = function(target){
			// legacy code may have already loaded [e.g] the raw bundle x/y/z at x.y.z; when true, push into the cache
			for(var result, names = target.split("/"), object = dojo.global[names[0]], i = 1; object && i<names.length-1; object = object[names[i++]]){}
			if(object){
				result = object[names[i]];
				if(!result){
					// fallback for incorrect bundle build of 1.6
					result = object[names[i].replace(/-/g,"_")];
				}
				if(result){
					cache[target] = result;
				}
			}
			return result;
		};

		thisModule.getLocalization = function(moduleName, bundleName, locale){
			var result,
				l10nName = getBundleName(moduleName, bundleName, locale);
			load(
				l10nName,

				// isXd() and syncRequire() need a context-require in order to resolve the mid with respect to a reference module.
				// Since this legacy function does not have the concept of a reference module, resolve with respect to this
				// dojo/i18n module, which, itself may have been mapped.
				(!isXd(l10nName, require) ? function(deps, callback){ syncRequire(deps, callback, require); } : require),

				function(result_){ result = result_; }
			);
			return result;
		};

		if(has("dojo-unit-tests")){
			unitTests.push(function(doh){
				doh.register("tests.i18n.unit", function(t){
					var check;

					check = evalBundle("{prop:1}", checkForLegacyModules, "nonsense", amdValue);
					t.is({prop:1}, check); t.is(undefined, check[1]);

					check = evalBundle("({prop:1})", checkForLegacyModules, "nonsense", amdValue);
					t.is({prop:1}, check); t.is(undefined, check[1]);

					check = evalBundle("{'prop-x':1}", checkForLegacyModules, "nonsense", amdValue);
					t.is({'prop-x':1}, check); t.is(undefined, check[1]);

					check = evalBundle("({'prop-x':1})", checkForLegacyModules, "nonsense", amdValue);
					t.is({'prop-x':1}, check); t.is(undefined, check[1]);

					check = evalBundle("define({'prop-x':1})", checkForLegacyModules, "nonsense", amdValue);
					t.is(amdValue, check); t.is({'prop-x':1}, amdValue.result);

					check = evalBundle("define('some/module', {'prop-x':1})", checkForLegacyModules, "nonsense", amdValue);
					t.is(amdValue, check); t.is({'prop-x':1}, amdValue.result);

					check = evalBundle("this is total nonsense and should throw an error", checkForLegacyModules, "nonsense", amdValue);
					t.is(check instanceof Error, true);
				});
			});
		}
	}

	return lang.mixin(thisModule, {
		dynamic:true,
		normalize:normalize,
		load:load,
		cache:cache,
		getL10nName: getL10nName
	});
});

},
'dojo/_base/xhr':function(){
define([
	"./kernel",
	"./sniff",
	"require",
	"../io-query",
	/*===== "./declare", =====*/
	"../dom",
	"../dom-form",
	"./Deferred",
	"./config",
	"./json",
	"./lang",
	"./array",
	"../on",
	"../aspect",
	"../request/watch",
	"../request/xhr",
	"../request/util"
], function(dojo, has, require, ioq, /*===== declare, =====*/ dom, domForm, Deferred, config, json, lang, array, on, aspect, watch, _xhr, util){
	// module:
	//		dojo/_base/xhr

	/*=====
	dojo._xhrObj = function(){
		// summary:
		//		does the work of portably generating a new XMLHTTPRequest object.
	};
	=====*/
	dojo._xhrObj = _xhr._create;

	var cfg = dojo.config;

	// mix in io-query and dom-form
	dojo.objectToQuery = ioq.objectToQuery;
	dojo.queryToObject = ioq.queryToObject;
	dojo.fieldToObject = domForm.fieldToObject;
	dojo.formToObject = domForm.toObject;
	dojo.formToQuery = domForm.toQuery;
	dojo.formToJson = domForm.toJson;

	// need to block async callbacks from snatching this thread as the result
	// of an async callback might call another sync XHR, this hangs khtml forever
	// must checked by watchInFlight()

	dojo._blockAsync = false;

	// MOW: remove dojo._contentHandlers alias in 2.0
	var handlers = dojo._contentHandlers = dojo.contentHandlers = {
		// summary:
		//		A map of available XHR transport handle types. Name matches the
		//		`handleAs` attribute passed to XHR calls.
		// description:
		//		A map of available XHR transport handle types. Name matches the
		//		`handleAs` attribute passed to XHR calls. Each contentHandler is
		//		called, passing the xhr object for manipulation. The return value
		//		from the contentHandler will be passed to the `load` or `handle`
		//		functions defined in the original xhr call.
		// example:
		//		Creating a custom content-handler:
		//	|	xhr.contentHandlers.makeCaps = function(xhr){
		//	|		return xhr.responseText.toUpperCase();
		//	|	}
		//	|	// and later:
		//	|	dojo.xhrGet({
		//	|		url:"foo.txt",
		//	|		handleAs:"makeCaps",
		//	|		load: function(data){ /* data is a toUpper version of foo.txt */ }
		//	|	});

		"text": function(xhr){
			// summary:
			//		A contentHandler which simply returns the plaintext response data
			return xhr.responseText;
		},
		"json": function(xhr){
			// summary:
			//		A contentHandler which returns a JavaScript object created from the response data
			return json.fromJson(xhr.responseText || null);
		},
		"json-comment-filtered": function(xhr){
			// summary:
			//		A contentHandler which expects comment-filtered JSON.
			// description:
			//		A contentHandler which expects comment-filtered JSON.
			//		the json-comment-filtered option was implemented to prevent
			//		"JavaScript Hijacking", but it is less secure than standard JSON. Use
			//		standard JSON instead. JSON prefixing can be used to subvert hijacking.
			//
			//		Will throw a notice suggesting to use application/json mimetype, as
			//		json-commenting can introduce security issues. To decrease the chances of hijacking,
			//		use the standard `json` contentHandler, and prefix your "JSON" with: {}&&
			//
			//		use djConfig.useCommentedJson = true to turn off the notice
			if(!config.useCommentedJson){
				console.warn("Consider using the standard mimetype:application/json."
					+ " json-commenting can introduce security issues. To"
					+ " decrease the chances of hijacking, use the standard the 'json' handler and"
					+ " prefix your json with: {}&&\n"
					+ "Use djConfig.useCommentedJson=true to turn off this message.");
			}

			var value = xhr.responseText;
			var cStartIdx = value.indexOf("\/*");
			var cEndIdx = value.lastIndexOf("*\/");
			if(cStartIdx == -1 || cEndIdx == -1){
				throw new Error("JSON was not comment filtered");
			}
			return json.fromJson(value.substring(cStartIdx+2, cEndIdx));
		},
		"javascript": function(xhr){
			// summary:
			//		A contentHandler which evaluates the response data, expecting it to be valid JavaScript

			// FIXME: try Moz and IE specific eval variants?
			return dojo.eval(xhr.responseText);
		},
		"xml": function(xhr){
			// summary:
			//		A contentHandler returning an XML Document parsed from the response data
			var result = xhr.responseXML;

			if(result && has("dom-qsa2.1") && !result.querySelectorAll && has("dom-parser")){
				// http://bugs.dojotoolkit.org/ticket/15631
				// IE9 supports a CSS3 querySelectorAll implementation, but the DOM implementation 
				// returned by IE9 xhr.responseXML does not. Manually create the XML DOM to gain 
				// the fuller-featured implementation and avoid bugs caused by the inconsistency
				result = new DOMParser().parseFromString(xhr.responseText, "application/xml");
			}

			if(has("ie")){
				if((!result || !result.documentElement)){
					//WARNING: this branch used by the xml handling in dojo.io.iframe,
					//so be sure to test dojo.io.iframe if making changes below.
					var ms = function(n){ return "MSXML" + n + ".DOMDocument"; };
					var dp = ["Microsoft.XMLDOM", ms(6), ms(4), ms(3), ms(2)];
					array.some(dp, function(p){
						try{
							var dom = new ActiveXObject(p);
							dom.async = false;
							dom.loadXML(xhr.responseText);
							result = dom;
						}catch(e){ return false; }
						return true;
					});
				}
			}
			return result; // DOMDocument
		},
		"json-comment-optional": function(xhr){
			// summary:
			//		A contentHandler which checks the presence of comment-filtered JSON and
			//		alternates between the `json` and `json-comment-filtered` contentHandlers.
			if(xhr.responseText && /^[^{\[]*\/\*/.test(xhr.responseText)){
				return handlers["json-comment-filtered"](xhr);
			}else{
				return handlers["json"](xhr);
			}
		}
	};

	/*=====

	// kwargs function parameter definitions.   Assigning to dojo namespace rather than making them local variables
	// because they are used by dojo/io modules too

	dojo.__IoArgs = declare(null, {
		// url: String
		//		URL to server endpoint.
		// content: Object?
		//		Contains properties with string values. These
		//		properties will be serialized as name1=value2 and
		//		passed in the request.
		// timeout: Integer?
		//		Milliseconds to wait for the response. If this time
		//		passes, the then error callbacks are called.
		// form: DOMNode?
		//		DOM node for a form. Used to extract the form values
		//		and send to the server.
		// preventCache: Boolean?
		//		Default is false. If true, then a
		//		"dojo.preventCache" parameter is sent in the request
		//		with a value that changes with each request
		//		(timestamp). Useful only with GET-type requests.
		// handleAs: String?
		//		Acceptable values depend on the type of IO
		//		transport (see specific IO calls for more information).
		// rawBody: String?
		//		Sets the raw body for an HTTP request. If this is used, then the content
		//		property is ignored. This is mostly useful for HTTP methods that have
		//		a body to their requests, like PUT or POST. This property can be used instead
		//		of postData and putData for dojo/_base/xhr.rawXhrPost and dojo/_base/xhr.rawXhrPut respectively.
		// ioPublish: Boolean?
		//		Set this explicitly to false to prevent publishing of topics related to
		//		IO operations. Otherwise, if djConfig.ioPublish is set to true, topics
		//		will be published via dojo/topic.publish() for different phases of an IO operation.
		//		See dojo/main.__IoPublish for a list of topics that are published.

		load: function(response, ioArgs){
			// summary:
			//		This function will be
			//		called on a successful HTTP response code.
	 		// ioArgs: dojo/main.__IoCallbackArgs
			//		Provides additional information about the request.
			// response: Object
			//		The response in the format as defined with handleAs.
		},

		error: function(response, ioArgs){
			// summary:
			//		This function will
			//		be called when the request fails due to a network or server error, the url
			//		is invalid, etc. It will also be called if the load or handle callback throws an
			//		exception, unless djConfig.debugAtAllCosts is true.	 This allows deployed applications
			//		to continue to run even when a logic error happens in the callback, while making
			//		it easier to troubleshoot while in debug mode.
			// ioArgs: dojo/main.__IoCallbackArgs
			//		Provides additional information about the request.
			// response: Object
			//		The response in the format as defined with handleAs.
		},

		handle: function(loadOrError, response, ioArgs){
			// summary:
	 		//		This function will
	 		//		be called at the end of every request, whether or not an error occurs.
			// loadOrError: String
			//		Provides a string that tells you whether this function
			//		was called because of success (load) or failure (error).
			// response: Object
			//		The response in the format as defined with handleAs.
			// ioArgs: dojo/main.__IoCallbackArgs
			//		Provides additional information about the request.
		}
	});

	dojo.__IoCallbackArgs = declare(null, {
		// args: Object
		//		the original object argument to the IO call.
		// xhr: XMLHttpRequest
		//		For XMLHttpRequest calls only, the
		//		XMLHttpRequest object that was used for the
		//		request.
		// url: String
		//		The final URL used for the call. Many times it
		//		will be different than the original args.url
		//		value.
		// query: String
		//		For non-GET requests, the
		//		name1=value1&name2=value2 parameters sent up in
		//		the request.
		// handleAs: String
		//		The final indicator on how the response will be
		//		handled.
		// id: String
		//		For dojo/io/script calls only, the internal
		//		script ID used for the request.
		// canDelete: Boolean
		//		For dojo/io/script calls only, indicates
		//		whether the script tag that represents the
		//		request can be deleted after callbacks have
		//		been called. Used internally to know when
		//		cleanup can happen on JSONP-type requests.
		// json: Object
		//		For dojo/io/script calls only: holds the JSON
		//		response for JSONP-type requests. Used
		//		internally to hold on to the JSON responses.
		//		You should not need to access it directly --
		//		the same object should be passed to the success
		//		callbacks directly.
	});

	dojo.__IoPublish = declare(null, {
		// summary:
		//		This is a list of IO topics that can be published
		//		if djConfig.ioPublish is set to true. IO topics can be
		//		published for any Input/Output, network operation. So,
		//		dojo.xhr, dojo.io.script and dojo.io.iframe can all
		//		trigger these topics to be published.
		// start: String
		//		"/dojo/io/start" is sent when there are no outstanding IO
		//		requests, and a new IO request is started. No arguments
		//		are passed with this topic.
		// send: String
		//		"/dojo/io/send" is sent whenever a new IO request is started.
		//		It passes the dojo.Deferred for the request with the topic.
		// load: String
		//		"/dojo/io/load" is sent whenever an IO request has loaded
		//		successfully. It passes the response and the dojo.Deferred
		//		for the request with the topic.
		// error: String
		//		"/dojo/io/error" is sent whenever an IO request has errored.
		//		It passes the error and the dojo.Deferred
		//		for the request with the topic.
		// done: String
		//		"/dojo/io/done" is sent whenever an IO request has completed,
		//		either by loading or by erroring. It passes the error and
		//		the dojo.Deferred for the request with the topic.
		// stop: String
		//		"/dojo/io/stop" is sent when all outstanding IO requests have
		//		finished. No arguments are passed with this topic.
	});
	=====*/


	dojo._ioSetArgs = function(/*dojo/main.__IoArgs*/args,
			/*Function*/canceller,
			/*Function*/okHandler,
			/*Function*/errHandler){
		// summary:
		//		sets up the Deferred and ioArgs property on the Deferred so it
		//		can be used in an io call.
		// args:
		//		The args object passed into the public io call. Recognized properties on
		//		the args object are:
		// canceller:
		//		The canceller function used for the Deferred object. The function
		//		will receive one argument, the Deferred object that is related to the
		//		canceller.
		// okHandler:
		//		The first OK callback to be registered with Deferred. It has the opportunity
		//		to transform the OK response. It will receive one argument -- the Deferred
		//		object returned from this function.
		// errHandler:
		//		The first error callback to be registered with Deferred. It has the opportunity
		//		to do cleanup on an error. It will receive two arguments: error (the
		//		Error object) and dfd, the Deferred object returned from this function.

		var ioArgs = {args: args, url: args.url};

		//Get values from form if requested.
		var formObject = null;
		if(args.form){
			var form = dom.byId(args.form);
			//IE requires going through getAttributeNode instead of just getAttribute in some form cases,
			//so use it for all. See #2844
			var actnNode = form.getAttributeNode("action");
			ioArgs.url = ioArgs.url || (actnNode ? actnNode.value : null);
			formObject = domForm.toObject(form);
		}

		// set up the query params
		var miArgs = [{}];

		if(formObject){
			// potentially over-ride url-provided params w/ form values
			miArgs.push(formObject);
		}
		if(args.content){
			// stuff in content over-rides what's set by form
			miArgs.push(args.content);
		}
		if(args.preventCache){
			miArgs.push({"dojo.preventCache": new Date().valueOf()});
		}
		ioArgs.query = ioq.objectToQuery(lang.mixin.apply(null, miArgs));

		// .. and the real work of getting the deferred in order, etc.
		ioArgs.handleAs = args.handleAs || "text";
		var d = new Deferred(function(dfd){
			dfd.canceled = true;
			canceller && canceller(dfd);

			var err = dfd.ioArgs.error;
			if(!err){
				err = new Error("request cancelled");
				err.dojoType="cancel";
				dfd.ioArgs.error = err;
			}
			return err;
		});
		d.addCallback(okHandler);

		//Support specifying load, error and handle callback functions from the args.
		//For those callbacks, the "this" object will be the args object.
		//The callbacks will get the deferred result value as the
		//first argument and the ioArgs object as the second argument.
		var ld = args.load;
		if(ld && lang.isFunction(ld)){
			d.addCallback(function(value){
				return ld.call(args, value, ioArgs);
			});
		}
		var err = args.error;
		if(err && lang.isFunction(err)){
			d.addErrback(function(value){
				return err.call(args, value, ioArgs);
			});
		}
		var handle = args.handle;
		if(handle && lang.isFunction(handle)){
			d.addBoth(function(value){
				return handle.call(args, value, ioArgs);
			});
		}

		// Attach error handler last (not including topic publishing)
		// to catch any errors that may have been generated from load
		// or handle functions.
		d.addErrback(function(error){
			return errHandler(error, d);
		});

		//Plug in topic publishing, if dojo.publish is loaded.
		if(cfg.ioPublish && dojo.publish && ioArgs.args.ioPublish !== false){
			d.addCallbacks(
				function(res){
					dojo.publish("/dojo/io/load", [d, res]);
					return res;
				},
				function(res){
					dojo.publish("/dojo/io/error", [d, res]);
					return res;
				}
			);
			d.addBoth(function(res){
				dojo.publish("/dojo/io/done", [d, res]);
				return res;
			});
		}

		d.ioArgs = ioArgs;

		// FIXME: need to wire up the xhr object's abort method to something
		// analogous in the Deferred
		return d;
	};

	var _deferredOk = function(/*Deferred*/dfd){
		// summary:
		//		okHandler function for dojo._ioSetArgs call.

		var ret = handlers[dfd.ioArgs.handleAs](dfd.ioArgs.xhr);
		return ret === undefined ? null : ret;
	};
	var _deferError = function(/*Error*/error, /*Deferred*/dfd){
		// summary:
		//		errHandler function for dojo._ioSetArgs call.

		if(!dfd.ioArgs.args.failOk){
			console.error(error);
		}
		return error;
	};

	//Use a separate count for knowing if we are starting/stopping io calls.
	var _checkPubCount = function(dfd){
		if(_pubCount <= 0){
			_pubCount = 0;
			if(cfg.ioPublish && dojo.publish && (!dfd || dfd && dfd.ioArgs.args.ioPublish !== false)){
				dojo.publish("/dojo/io/stop");
			}
		}
	};

	var _pubCount = 0;
	aspect.after(watch, "_onAction", function(){
		_pubCount -= 1;
	});
	aspect.after(watch, "_onInFlight", _checkPubCount);

	dojo._ioCancelAll = watch.cancelAll;
	/*=====
	dojo._ioCancelAll = function(){
		// summary:
		//		Cancels all pending IO requests, regardless of IO type
		//		(xhr, script, iframe).
	};
	=====*/

	dojo._ioNotifyStart = function(/*Deferred*/dfd){
		// summary:
		//		If dojo.publish is available, publish topics
		//		about the start of a request queue and/or the
		//		the beginning of request.
		//
		//		Used by IO transports. An IO transport should
		//		call this method before making the network connection.
		if(cfg.ioPublish && dojo.publish && dfd.ioArgs.args.ioPublish !== false){
			if(!_pubCount){
				dojo.publish("/dojo/io/start");
			}
			_pubCount += 1;
			dojo.publish("/dojo/io/send", [dfd]);
		}
	};

	dojo._ioWatch = function(dfd, validCheck, ioCheck, resHandle){
		// summary:
		//		Watches the io request represented by dfd to see if it completes.
		// dfd: Deferred
		//		The Deferred object to watch.
		// validCheck: Function
		//		Function used to check if the IO request is still valid. Gets the dfd
		//		object as its only argument.
		// ioCheck: Function
		//		Function used to check if basic IO call worked. Gets the dfd
		//		object as its only argument.
		// resHandle: Function
		//		Function used to process response. Gets the dfd
		//		object as its only argument.

		var args = dfd.ioArgs.options = dfd.ioArgs.args;
		lang.mixin(dfd, {
			response: dfd.ioArgs,
			isValid: function(response){
				return validCheck(dfd);
			},
			isReady: function(response){
				return ioCheck(dfd);
			},
			handleResponse: function(response){
				return resHandle(dfd);
			}
		});
		watch(dfd);

		_checkPubCount(dfd);
	};

	var _defaultContentType = "application/x-www-form-urlencoded";

	dojo._ioAddQueryToUrl = function(/*dojo.__IoCallbackArgs*/ioArgs){
		// summary:
		//		Adds query params discovered by the io deferred construction to the URL.
		//		Only use this for operations which are fundamentally GET-type operations.
		if(ioArgs.query.length){
			ioArgs.url += (ioArgs.url.indexOf("?") == -1 ? "?" : "&") + ioArgs.query;
			ioArgs.query = null;
		}
	};

	/*=====
	dojo.__XhrArgs = declare(dojo.__IoArgs, {
		// summary:
		//		In addition to the properties listed for the dojo._IoArgs type,
		//		the following properties are allowed for dojo.xhr* methods.
		// handleAs: String?
		//		Acceptable values are: text (default), json, json-comment-optional,
		//		json-comment-filtered, javascript, xml. See `dojo/_base/xhr.contentHandlers`
	 	// sync: Boolean?
		//		false is default. Indicates whether the request should
		//		be a synchronous (blocking) request.
		// headers: Object?
		//		Additional HTTP headers to send in the request.
		// failOk: Boolean?
		//		false is default. Indicates whether a request should be
		//		allowed to fail (and therefore no console error message in
		//		the event of a failure)
		// contentType: String|Boolean
		//		"application/x-www-form-urlencoded" is default. Set to false to
		//		prevent a Content-Type header from being sent, or to a string
		//		to send a different Content-Type.
	 });
	=====*/

	dojo.xhr = function(/*String*/ method, /*dojo.__XhrArgs*/ args, /*Boolean?*/ hasBody){
		// summary:
		//		Deprecated.   Use dojo/request instead.
		// description:
		//		Sends an HTTP request with the given method.
		//		See also dojo.xhrGet(), xhrPost(), xhrPut() and dojo.xhrDelete() for shortcuts
		//		for those HTTP methods. There are also methods for "raw" PUT and POST methods
		//		via dojo.rawXhrPut() and dojo.rawXhrPost() respectively.
		// method:
		//		HTTP method to be used, such as GET, POST, PUT, DELETE. Should be uppercase.
		// hasBody:
		//		If the request has an HTTP body, then pass true for hasBody.

		var rDfd;
		//Make the Deferred object for this xhr request.
		var dfd = dojo._ioSetArgs(args, function(dfd){
			rDfd && rDfd.cancel();
		}, _deferredOk, _deferError);
		var ioArgs = dfd.ioArgs;

		//Allow for specifying the HTTP body completely.
		if("postData" in args){
			ioArgs.query = args.postData;
		}else if("putData" in args){
			ioArgs.query = args.putData;
		}else if("rawBody" in args){
			ioArgs.query = args.rawBody;
		}else if((arguments.length > 2 && !hasBody) || "POST|PUT".indexOf(method.toUpperCase()) === -1){
			//Check for hasBody being passed. If no hasBody,
			//then only append query string if not a POST or PUT request.
			dojo._ioAddQueryToUrl(ioArgs);
		}

		var options = {
			method: method,
			handleAs: "text",
			timeout: args.timeout,
			withCredentials: args.withCredentials,
			ioArgs: ioArgs
		};

		if(typeof args.headers !== 'undefined'){
			options.headers = args.headers;
		}
		if(typeof args.contentType !== 'undefined'){
			if(!options.headers){
				options.headers = {};
			}
			options.headers['Content-Type'] = args.contentType;
		}
		if(typeof ioArgs.query !== 'undefined'){
			options.data = ioArgs.query;
		}
		if(typeof args.sync !== 'undefined'){
			options.sync = args.sync;
		}

		dojo._ioNotifyStart(dfd);
		try{
			rDfd = _xhr(ioArgs.url, options, true);
		}catch(e){
			// If XHR creation fails, dojo/request/xhr throws
			// When this happens, cancel the deferred
			dfd.cancel();
			return dfd;
		}

		// sync ioArgs
		dfd.ioArgs.xhr = rDfd.response.xhr;

		rDfd.then(function(){
			dfd.resolve(dfd);
		}).otherwise(function(error){
			ioArgs.error = error;
			if(error.response){
				error.status = error.response.status;
				error.responseText = error.response.text;
				error.xhr = error.response.xhr;
			}
			dfd.reject(error);
		});
		return dfd; // dojo/_base/Deferred
	};

	dojo.xhrGet = function(/*dojo.__XhrArgs*/ args){
		// summary:
		//		Sends an HTTP GET request to the server.
		return dojo.xhr("GET", args); // dojo/_base/Deferred
	};

	dojo.rawXhrPost = dojo.xhrPost = function(/*dojo.__XhrArgs*/ args){
		// summary:
		//		Sends an HTTP POST request to the server. In addition to the properties
		//		listed for the dojo.__XhrArgs type, the following property is allowed:
		// postData:
		//		String. Send raw data in the body of the POST request.
		return dojo.xhr("POST", args, true); // dojo/_base/Deferred
	};

	dojo.rawXhrPut = dojo.xhrPut = function(/*dojo.__XhrArgs*/ args){
		// summary:
		//		Sends an HTTP PUT request to the server. In addition to the properties
		//		listed for the dojo.__XhrArgs type, the following property is allowed:
		// putData:
		//		String. Send raw data in the body of the PUT request.
		return dojo.xhr("PUT", args, true); // dojo/_base/Deferred
	};

	dojo.xhrDelete = function(/*dojo.__XhrArgs*/ args){
		// summary:
		//		Sends an HTTP DELETE request to the server.
		return dojo.xhr("DELETE", args); // dojo/_base/Deferred
	};

	/*
	dojo.wrapForm = function(formNode){
		// summary:
		//		A replacement for FormBind, but not implemented yet.

		// FIXME: need to think harder about what extensions to this we might
		// want. What should we allow folks to do w/ this? What events to
		// set/send?
		throw new Error("dojo.wrapForm not yet implemented");
	}
	*/

	dojo._isDocumentOk = function(x){
		return util.checkStatus(x.status);
	};

	dojo._getText = function(url){
		var result;
		dojo.xhrGet({url:url, sync:true, load:function(text){
			result = text;
		}});
		return result;
	};

	// Add aliases for static functions to dojo.xhr since dojo.xhr is what's returned from this module
	lang.mixin(dojo.xhr, {
		_xhrObj: dojo._xhrObj,
		fieldToObject: domForm.fieldToObject,
		formToObject: domForm.toObject,
		objectToQuery: ioq.objectToQuery,
		formToQuery: domForm.toQuery,
		formToJson: domForm.toJson,
		queryToObject: ioq.queryToObject,
		contentHandlers: handlers,
		_ioSetArgs: dojo._ioSetArgs,
		_ioCancelAll: dojo._ioCancelAll,
		_ioNotifyStart: dojo._ioNotifyStart,
		_ioWatch: dojo._ioWatch,
		_ioAddQueryToUrl: dojo._ioAddQueryToUrl,
		_isDocumentOk: dojo._isDocumentOk,
		_getText: dojo._getText,
		get: dojo.xhrGet,
		post: dojo.xhrPost,
		put: dojo.xhrPut,
		del: dojo.xhrDelete	// because "delete" is a reserved word
	});

	return dojo.xhr;
});

},
'dojo/io-query':function(){
define(["./_base/lang"], function(lang){

// module:
//		dojo/io-query

var backstop = {};

return {
// summary:
//		This module defines query string processing functions.

	objectToQuery: function objectToQuery(/*Object*/ map){
		// summary:
        //		takes a name/value mapping object and returns a string representing
        //		a URL-encoded version of that object.
        // example:
        //		this object:
        //
        //	|	{
        //	|		blah: "blah",
        //	|		multi: [
        //	|			"thud",
        //	|			"thonk"
        //	|		]
        //	|	};
        //
        //		yields the following query string:
        //
        //	|	"blah=blah&multi=thud&multi=thonk"

        // FIXME: need to implement encodeAscii!!
        var enc = encodeURIComponent, pairs = [];
        for(var name in map){
            var value = map[name];
            if(value != backstop[name]){
                var assign = enc(name) + "=";
                if(lang.isArray(value)){
                    for(var i = 0, l = value.length; i < l; ++i){
                        pairs.push(assign + enc(value[i]));
                    }
                }else{
                    pairs.push(assign + enc(value));
                }
            }
        }
        return pairs.join("&"); // String
    },

	queryToObject: function queryToObject(/*String*/ str){
        // summary:
        //		Create an object representing a de-serialized query section of a
        //		URL. Query keys with multiple values are returned in an array.
        //
        // example:
        //		This string:
        //
        //	|		"foo=bar&foo=baz&thinger=%20spaces%20=blah&zonk=blarg&"
        //
        //		results in this object structure:
        //
        //	|		{
        //	|			foo: [ "bar", "baz" ],
        //	|			thinger: " spaces =blah",
        //	|			zonk: "blarg"
        //	|		}
        //
        //		Note that spaces and other urlencoded entities are correctly
        //		handled.

        // FIXME: should we grab the URL string if we're not passed one?
        var dec = decodeURIComponent, qp = str.split("&"), ret = {}, name, val;
        for(var i = 0, l = qp.length, item; i < l; ++i){
            item = qp[i];
            if(item.length){
                var s = item.indexOf("=");
                if(s < 0){
                    name = dec(item);
                    val = "";
                }else{
                    name = dec(item.slice(0, s));
                    val  = dec(item.slice(s + 1));
                }
                if(typeof ret[name] == "string"){ // inline'd type check
                    ret[name] = [ret[name]];
                }

                if(lang.isArray(ret[name])){
                    ret[name].push(val);
                }else{
                    ret[name] = val;
                }
            }
        }
        return ret; // Object
    }
};
});
},
'dojo/dom-form':function(){
define(["./_base/lang", "./dom", "./io-query", "./json"], function(lang, dom, ioq, json){
	// module:
	//		dojo/dom-form

    function setValue(/*Object*/ obj, /*String*/ name, /*String*/ value){
        // summary:
        //		For the named property in object, set the value. If a value
        //		already exists and it is a string, convert the value to be an
        //		array of values.

        // Skip it if there is no value
        if(value === null){
            return;
        }

        var val = obj[name];
        if(typeof val == "string"){ // inline'd type check
            obj[name] = [val, value];
        }else if(lang.isArray(val)){
            val.push(value);
        }else{
            obj[name] = value;
        }
    }

	var exclude = "file|submit|image|reset|button";

	var form = {
		// summary:
		//		This module defines form-processing functions.

		fieldToObject: function fieldToObject(/*DOMNode|String*/ inputNode){
			// summary:
			//		Serialize a form field to a JavaScript object.
			// description:
			//		Returns the value encoded in a form field as
			//		as a string or an array of strings. Disabled form elements
			//		and unchecked radio and checkboxes are skipped.	Multi-select
			//		elements are returned as an array of string values.
			// inputNode: DOMNode|String
			// returns: Object

			var ret = null;
			inputNode = dom.byId(inputNode);
			if(inputNode){
				var _in = inputNode.name, type = (inputNode.type || "").toLowerCase();
				if(_in && type && !inputNode.disabled){
					if(type == "radio" || type == "checkbox"){
						if(inputNode.checked){
							ret = inputNode.value;
						}
					}else if(inputNode.multiple){
						ret = [];
						var nodes = [inputNode.firstChild];
						while(nodes.length){
							for(var node = nodes.pop(); node; node = node.nextSibling){
								if(node.nodeType == 1 && node.tagName.toLowerCase() == "option"){
									if(node.selected){
										ret.push(node.value);
									}
								}else{
									if(node.nextSibling){
										nodes.push(node.nextSibling);
									}
									if(node.firstChild){
										nodes.push(node.firstChild);
									}
									break;
								}
							}
						}
					}else{
						ret = inputNode.value;
					}
				}
			}
			return ret; // Object
		},

		toObject: function formToObject(/*DOMNode|String*/ formNode){
			// summary:
			//		Serialize a form node to a JavaScript object.
			// description:
			//		Returns the values encoded in an HTML form as
			//		string properties in an object which it then returns. Disabled form
			//		elements, buttons, and other non-value form elements are skipped.
			//		Multi-select elements are returned as an array of string values.
			// formNode: DOMNode|String
			// example:
			//		This form:
			//		|	<form id="test_form">
			//		|		<input type="text" name="blah" value="blah">
			//		|		<input type="text" name="no_value" value="blah" disabled>
			//		|		<input type="button" name="no_value2" value="blah">
			//		|		<select type="select" multiple name="multi" size="5">
			//		|			<option value="blah">blah</option>
			//		|			<option value="thud" selected>thud</option>
			//		|			<option value="thonk" selected>thonk</option>
			//		|		</select>
			//		|	</form>
			//
			//		yields this object structure as the result of a call to
			//		formToObject():
			//
			//		|	{
			//		|		blah: "blah",
			//		|		multi: [
			//		|			"thud",
			//		|			"thonk"
			//		|		]
			//		|	};

			var ret = {}, elems = dom.byId(formNode).elements;
			for(var i = 0, l = elems.length; i < l; ++i){
				var item = elems[i], _in = item.name, type = (item.type || "").toLowerCase();
				if(_in && type && exclude.indexOf(type) < 0 && !item.disabled){
					setValue(ret, _in, form.fieldToObject(item));
					if(type == "image"){
						ret[_in + ".x"] = ret[_in + ".y"] = ret[_in].x = ret[_in].y = 0;
					}
				}
			}
			return ret; // Object
		},

		toQuery: function formToQuery(/*DOMNode|String*/ formNode){
			// summary:
			//		Returns a URL-encoded string representing the form passed as either a
			//		node or string ID identifying the form to serialize
			// formNode: DOMNode|String
			// returns: String

			return ioq.objectToQuery(form.toObject(formNode)); // String
		},

		toJson: function formToJson(/*DOMNode|String*/ formNode, /*Boolean?*/ prettyPrint){
			// summary:
			//		Create a serialized JSON string from a form node or string
			//		ID identifying the form to serialize
			// formNode: DOMNode|String
			// prettyPrint: Boolean?
			// returns: String

			return json.stringify(form.toObject(formNode), null, prettyPrint ? 4 : 0); // String
		}
	};

    return form;
});

},
'dojo/_base/json':function(){
define(["./kernel", "../json"], function(dojo, json){

// module:
//		dojo/_base/json

/*=====
return {
	// summary:
	//		This module defines the dojo JSON API.
};
=====*/

dojo.fromJson = function(/*String*/ js){
	// summary:
	//		Parses a JavaScript expression and returns a JavaScript value.
	// description:
	//		Throws for invalid JavaScript expressions. It does not use a strict JSON parser. It
	//		always delegates to eval(). The content passed to this method must therefore come
	//		from a trusted source.
	//		It is recommend that you use dojo/json's parse function for an
	//		implementation uses the (faster) native JSON parse when available.
	// js:
	//		a string literal of a JavaScript expression, for instance:
	//		`'{ "foo": [ "bar", 1, { "baz": "thud" } ] }'`

	return eval("(" + js + ")"); // Object
};

/*=====
dojo._escapeString = function(){
	// summary:
	//		Adds escape sequences for non-visual characters, double quote and
	//		backslash and surrounds with double quotes to form a valid string
	//		literal.
};
=====*/
dojo._escapeString = json.stringify; // just delegate to json.stringify

dojo.toJsonIndentStr = "\t";
dojo.toJson = function(/*Object*/ it, /*Boolean?*/ prettyPrint){
	// summary:
	//		Returns a [JSON](http://json.org) serialization of an object.
	// description:
	//		Returns a [JSON](http://json.org) serialization of an object.
	//		Note that this doesn't check for infinite recursion, so don't do that!
	//		It is recommend that you use dojo/json's stringify function for an lighter
	//		and faster implementation that matches the native JSON API and uses the
	//		native JSON serializer when available.
	// it:
	//		an object to be serialized. Objects may define their own
	//		serialization via a special "__json__" or "json" function
	//		property. If a specialized serializer has been defined, it will
	//		be used as a fallback.
	//		Note that in 1.6, toJson would serialize undefined, but this no longer supported
	//		since it is not supported by native JSON serializer.
	// prettyPrint:
	//		if true, we indent objects and arrays to make the output prettier.
	//		The variable `dojo.toJsonIndentStr` is used as the indent string --
	//		to use something other than the default (tab), change that variable
	//		before calling dojo.toJson().
	//		Note that if native JSON support is available, it will be used for serialization,
	//		and native implementations vary on the exact spacing used in pretty printing.
	// returns:
	//		A JSON string serialization of the passed-in object.
	// example:
	//		simple serialization of a trivial object
	//		|	var jsonStr = dojo.toJson({ howdy: "stranger!", isStrange: true });
	//		|	doh.is('{"howdy":"stranger!","isStrange":true}', jsonStr);
	// example:
	//		a custom serializer for an objects of a particular class:
	//		|	dojo.declare("Furby", null, {
	//		|		furbies: "are strange",
	//		|		furbyCount: 10,
	//		|		__json__: function(){
	//		|		},
	//		|	});

	// use dojo/json
	return json.stringify(it, function(key, value){
		if(value){
			var tf = value.__json__||value.json;
			if(typeof tf == "function"){
				return tf.call(value);
			}
		}
		return value;
	}, prettyPrint && dojo.toJsonIndentStr);	// String
};

return dojo;
});

},
'dojo/request/watch':function(){
define([
	'./util',
	'../errors/RequestTimeoutError',
	'../errors/CancelError',
	'../_base/array',
	'../_base/window',
	'../has!host-browser?dom-addeventlistener?:../on:'
], function(util, RequestTimeoutError, CancelError, array, win, on){
	// avoid setting a timer per request. It degrades performance on IE
	// something fierece if we don't use unified loops.
	var _inFlightIntvl = null,
		_inFlight = [];

	function watchInFlight(){
		// summary:
		//		internal method that checks each inflight XMLHttpRequest to see
		//		if it has completed or if the timeout situation applies.

		var now = +(new Date);

		// we need manual loop because we often modify _inFlight (and therefore 'i') while iterating
		for(var i = 0, dfd; i < _inFlight.length && (dfd = _inFlight[i]); i++){
			var response = dfd.response,
				options = response.options;
			if((dfd.isCanceled && dfd.isCanceled()) || (dfd.isValid && !dfd.isValid(response))){
				_inFlight.splice(i--, 1);
				watch._onAction && watch._onAction();
			}else if(dfd.isReady && dfd.isReady(response)){
				_inFlight.splice(i--, 1);
				dfd.handleResponse(response);
				watch._onAction && watch._onAction();
			}else if(dfd.startTime){
				// did we timeout?
				if(dfd.startTime + (options.timeout || 0) < now){
					_inFlight.splice(i--, 1);
					// Cancel the request so the io module can do appropriate cleanup.
					dfd.cancel(new RequestTimeoutError('Timeout exceeded', response));
					watch._onAction && watch._onAction();
				}
			}
		}

		watch._onInFlight && watch._onInFlight(dfd);

		if(!_inFlight.length){
			clearInterval(_inFlightIntvl);
			_inFlightIntvl = null;
		}
	}

	function watch(dfd){
		// summary:
		//		Watches the io request represented by dfd to see if it completes.
		// dfd: Deferred
		//		The Deferred object to watch.
		// response: Object
		//		The object used as the value of the request promise.
		// validCheck: Function
		//		Function used to check if the IO request is still valid. Gets the dfd
		//		object as its only argument.
		// ioCheck: Function
		//		Function used to check if basic IO call worked. Gets the dfd
		//		object as its only argument.
		// resHandle: Function
		//		Function used to process response. Gets the dfd
		//		object as its only argument.
		if(dfd.response.options.timeout){
			dfd.startTime = +(new Date);
		}

		if(dfd.isFulfilled()){
			// bail out if the deferred is already fulfilled
			return;
		}

		_inFlight.push(dfd);
		if(!_inFlightIntvl){
			_inFlightIntvl = setInterval(watchInFlight, 50);
		}

		// handle sync requests separately from async:
		// http://bugs.dojotoolkit.org/ticket/8467
		if(dfd.response.options.sync){
			watchInFlight();
		}
	}

	watch.cancelAll = function cancelAll(){
		// summary:
		//		Cancels all pending IO requests, regardless of IO type
		try{
			array.forEach(_inFlight, function(dfd){
				try{
					dfd.cancel(new CancelError('All requests canceled.'));
				}catch(e){}
			});
		}catch(e){}
	};

	if(win && on && win.doc.attachEvent){
		// Automatically call cancel all io calls on unload in IE
		// http://bugs.dojotoolkit.org/ticket/2357
		on(win.global, 'unload', function(){
			watch.cancelAll();
		});
	}

	return watch;
});

},
'dojo/request/util':function(){
define([
	'exports',
	'../errors/RequestError',
	'../errors/CancelError',
	'../Deferred',
	'../io-query',
	'../_base/array',
	'../_base/lang',
	'../promise/Promise'
], function(exports, RequestError, CancelError, Deferred, ioQuery, array, lang, Promise){
	exports.deepCopy = function deepCopy(target, source){
		for(var name in source){
			var tval = target[name],
				sval = source[name];
			if(tval !== sval){
				if(tval && typeof tval === 'object' && sval && typeof sval === 'object'){
					exports.deepCopy(tval, sval);
				}else{
					target[name] = sval;
				}
			}
		}
		return target;
	};

	exports.deepCreate = function deepCreate(source, properties){
		properties = properties || {};
		var target = lang.delegate(source),
			name, value;

		for(name in source){
			value = source[name];

			if(value && typeof value === 'object'){
				target[name] = exports.deepCreate(value, properties[name]);
			}
		}
		return exports.deepCopy(target, properties);
	};

	var freeze = Object.freeze || function(obj){ return obj; };
	function okHandler(response){
		return freeze(response);
	}
	function dataHandler (response) {
		return response.data || response.text;
	}

	exports.deferred = function deferred(response, cancel, isValid, isReady, handleResponse, last){
		var def = new Deferred(function(reason){
			cancel && cancel(def, response);

			if(!reason || !(reason instanceof RequestError) && !(reason instanceof CancelError)){
				return new CancelError('Request canceled', response);
			}
			return reason;
		});

		def.response = response;
		def.isValid = isValid;
		def.isReady = isReady;
		def.handleResponse = handleResponse;

		function errHandler(error){
			error.response = response;
			throw error;
		}
		var responsePromise = def.then(okHandler).otherwise(errHandler);

		if(exports.notify){
			responsePromise.then(
				lang.hitch(exports.notify, 'emit', 'load'),
				lang.hitch(exports.notify, 'emit', 'error')
			);
		}

		var dataPromise = responsePromise.then(dataHandler);

		// http://bugs.dojotoolkit.org/ticket/16794
		// The following works around a leak in IE9 through the
		// prototype using lang.delegate on dataPromise and
		// assigning the result a property with a reference to
		// responsePromise.
		var promise = new Promise();
		for (var prop in dataPromise) {
			if (dataPromise.hasOwnProperty(prop)) {
				promise[prop] = dataPromise[prop];
			}
		}
		promise.response = responsePromise;
		freeze(promise);
		// End leak fix


		if(last){
			def.then(function(response){
				last.call(def, response);
			}, function(error){
				last.call(def, response, error);
			});
		}

		def.promise = promise;
		def.then = promise.then;

		return def;
	};

	exports.addCommonMethods = function addCommonMethods(provider, methods){
		array.forEach(methods||['GET', 'POST', 'PUT', 'DELETE'], function(method){
			provider[(method === 'DELETE' ? 'DEL' : method).toLowerCase()] = function(url, options){
				options = lang.delegate(options||{});
				options.method = method;
				return provider(url, options);
			};
		});
	};

	exports.parseArgs = function parseArgs(url, options, skipData){
		var data = options.data,
			query = options.query;
		
		if(data && !skipData){
			if(typeof data === 'object'){
				options.data = ioQuery.objectToQuery(data);
			}
		}

		if(query){
			if(typeof query === 'object'){
				query = ioQuery.objectToQuery(query);
			}
			if(options.preventCache){
				query += (query ? '&' : '') + 'request.preventCache=' + (+(new Date));
			}
		}else if(options.preventCache){
			query = 'request.preventCache=' + (+(new Date));
		}

		if(url && query){
			url += (~url.indexOf('?') ? '&' : '?') + query;
		}

		return {
			url: url,
			options: options,
			getHeader: function(headerName){ return null; }
		};
	};

	exports.checkStatus = function(stat){
		stat = stat || 0;
		return (stat >= 200 && stat < 300) || // allow any 2XX response code
			stat === 304 ||                 // or, get it out of the cache
			stat === 1223 ||                // or, Internet Explorer mangled the status code
			!stat;                         // or, we're Titanium/browser chrome/chrome extension requesting a local file
	};
});

},
'dojo/errors/RequestError':function(){
define(['./create'], function(create){
	// module:
	//		dojo/errors/RequestError

	/*=====
	 return function(){
		 // summary:
		 //		TODOC
	 };
	 =====*/

	return create("RequestError", function(message, response){
		this.response = response;
	});
});

},
'dojo/errors/RequestTimeoutError':function(){
define(['./create', './RequestError'], function(create, RequestError){
	// module:
	//		dojo/errors/RequestTimeoutError

	/*=====
	 return function(){
		 // summary:
		 //		TODOC
	 };
	 =====*/

	return create("RequestTimeoutError", null, RequestError, {
		dojoType: "timeout"
	});
});

},
'dojo/request/xhr':function(){
define([
	'../errors/RequestError',
	'./watch',
	'./handlers',
	'./util',
	'../has'/*=====,
	'../request',
	'../_base/declare' =====*/
], function(RequestError, watch, handlers, util, has/*=====, request, declare =====*/){
	has.add('native-xhr', function(){
		// if true, the environment has a native XHR implementation
		return typeof XMLHttpRequest !== 'undefined';
	});
	has.add('dojo-force-activex-xhr', function(){
		return has('activex') && !document.addEventListener && window.location.protocol === 'file:';
	});

	has.add('native-xhr2', function(){
		if(!has('native-xhr')){ return; }
		var x = new XMLHttpRequest();
		return typeof x['addEventListener'] !== 'undefined' &&
			(typeof opera === 'undefined' || typeof x['upload'] !== 'undefined');
	});

	has.add('native-formdata', function(){
		// if true, the environment has a native FormData implementation
		return typeof FormData === 'function';
	});

	function handleResponse(response, error){
		var _xhr = response.xhr;
		response.status = response.xhr.status;
		response.text = _xhr.responseText;

		if(response.options.handleAs === 'xml'){
			response.data = _xhr.responseXML;
		}

		if(!error){
			try{
				handlers(response);
			}catch(e){
				error = e;
			}
		}

		if(error){
			this.reject(error);
		}else if(util.checkStatus(_xhr.status)){
			this.resolve(response);
		}else{
			error = new RequestError('Unable to load ' + response.url + ' status: ' + _xhr.status, response);

			this.reject(error);
		}
	}

	var isValid, isReady, addListeners, cancel;
	if(has('native-xhr2')){
		// Any platform with XHR2 will only use the watch mechanism for timeout.

		isValid = function(response){
			// summary:
			//		Check to see if the request should be taken out of the watch queue
			return !this.isFulfilled();
		};
		cancel = function(dfd, response){
			// summary:
			//		Canceler for deferred
			response.xhr.abort();
		};
		addListeners = function(_xhr, dfd, response){
			// summary:
			//		Adds event listeners to the XMLHttpRequest object
			function onLoad(evt){
				dfd.handleResponse(response);
			}
			function onError(evt){
				var _xhr = evt.target;
				var error = new RequestError('Unable to load ' + response.url + ' status: ' + _xhr.status, response);
				dfd.handleResponse(response, error);
			}

			function onProgress(evt){
				if(evt.lengthComputable){
					response.loaded = evt.loaded;
					response.total = evt.total;
					dfd.progress(response);
				}
			}

			_xhr.addEventListener('load', onLoad, false);
			_xhr.addEventListener('error', onError, false);
			_xhr.addEventListener('progress', onProgress, false);

			return function(){
				_xhr.removeEventListener('load', onLoad, false);
				_xhr.removeEventListener('error', onError, false);
				_xhr.removeEventListener('progress', onProgress, false);
				_xhr = null;
			};
		};
	}else{
		isValid = function(response){
			return response.xhr.readyState; //boolean
		};
		isReady = function(response){
			return 4 === response.xhr.readyState; //boolean
		};
		cancel = function(dfd, response){
			// summary:
			//		canceller function for util.deferred call.
			var xhr = response.xhr;
			var _at = typeof xhr.abort;
			if(_at === 'function' || _at === 'object' || _at === 'unknown'){
				xhr.abort();
			}
		};
	}

	function getHeader(headerName){
		return this.xhr.getResponseHeader(headerName);
	}

	var undefined,
		defaultOptions = {
			data: null,
			query: null,
			sync: false,
			method: 'GET'
		};
	function xhr(url, options, returnDeferred){
		var response = util.parseArgs(
			url,
			util.deepCreate(defaultOptions, options),
			has('native-formdata') && options && options.data && options.data instanceof FormData
		);
		url = response.url;
		options = response.options;

		var remover,
			last = function(){
				remover && remover();
			};

		//Make the Deferred object for this xhr request.
		var dfd = util.deferred(
			response,
			cancel,
			isValid,
			isReady,
			handleResponse,
			last
		);
		var _xhr = response.xhr = xhr._create();

		if(!_xhr){
			// If XHR factory somehow returns nothings,
			// cancel the deferred.
			dfd.cancel(new RequestError('XHR was not created'));
			return returnDeferred ? dfd : dfd.promise;
		}

		response.getHeader = getHeader;

		if(addListeners){
			remover = addListeners(_xhr, dfd, response);
		}

		var data = options.data,
			async = !options.sync,
			method = options.method;

		try{
			// IE6 won't let you call apply() on the native function.
			_xhr.open(method, url, async, options.user || undefined, options.password || undefined);

			if(options.withCredentials){
				_xhr.withCredentials = options.withCredentials;
			}

			var headers = options.headers,
				contentType = 'application/x-www-form-urlencoded';
			if(headers){
				for(var hdr in headers){
					if(hdr.toLowerCase() === 'content-type'){
						contentType = headers[hdr];
					}else if(headers[hdr]){
						//Only add header if it has a value. This allows for instance, skipping
						//insertion of X-Requested-With by specifying empty value.
						_xhr.setRequestHeader(hdr, headers[hdr]);
					}
				}
			}

			if(contentType && contentType !== false){
				_xhr.setRequestHeader('Content-Type', contentType);
			}
			if(!headers || !('X-Requested-With' in headers)){
				_xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			}

			if(util.notify){
				util.notify.emit('send', response, dfd.promise.cancel);
			}
			_xhr.send(data);
		}catch(e){
			dfd.reject(e);
		}

		watch(dfd);
		_xhr = null;

		return returnDeferred ? dfd : dfd.promise;
	}

	/*=====
	xhr = function(url, options){
		// summary:
		//		Sends a request using XMLHttpRequest with the given URL and options.
		// url: String
		//		URL to request
		// options: dojo/request/xhr.__Options?
		//		Options for the request.
		// returns: dojo/request.__Promise
	};
	xhr.__BaseOptions = declare(request.__BaseOptions, {
		// sync: Boolean?
		//		Whether to make a synchronous request or not. Default
		//		is `false` (asynchronous).
		// data: String|Object|FormData?
		//		Data to transfer. This is ignored for GET and DELETE
		//		requests.
		// headers: Object?
		//		Headers to use for the request.
		// user: String?
		//		Username to use during the request.
		// password: String?
		//		Password to use during the request.
		// withCredentials: Boolean?
		//		For cross-site requests, whether to send credentials
		//		or not.
	});
	xhr.__MethodOptions = declare(null, {
		// method: String?
		//		The HTTP method to use to make the request. Must be
		//		uppercase. Default is `"GET"`.
	});
	xhr.__Options = declare([xhr.__BaseOptions, xhr.__MethodOptions]);

	xhr.get = function(url, options){
		// summary:
		//		Send an HTTP GET request using XMLHttpRequest with the given URL and options.
		// url: String
		//		URL to request
		// options: dojo/request/xhr.__BaseOptions?
		//		Options for the request.
		// returns: dojo/request.__Promise
	};
	xhr.post = function(url, options){
		// summary:
		//		Send an HTTP POST request using XMLHttpRequest with the given URL and options.
		// url: String
		//		URL to request
		// options: dojo/request/xhr.__BaseOptions?
		//		Options for the request.
		// returns: dojo/request.__Promise
	};
	xhr.put = function(url, options){
		// summary:
		//		Send an HTTP PUT request using XMLHttpRequest with the given URL and options.
		// url: String
		//		URL to request
		// options: dojo/request/xhr.__BaseOptions?
		//		Options for the request.
		// returns: dojo/request.__Promise
	};
	xhr.del = function(url, options){
		// summary:
		//		Send an HTTP DELETE request using XMLHttpRequest with the given URL and options.
		// url: String
		//		URL to request
		// options: dojo/request/xhr.__BaseOptions?
		//		Options for the request.
		// returns: dojo/request.__Promise
	};
	=====*/
	xhr._create = function(){
		// summary:
		//		does the work of portably generating a new XMLHTTPRequest object.
		throw new Error('XMLHTTP not available');
	};
	if(has('native-xhr') && !has('dojo-force-activex-xhr')){
		xhr._create = function(){
			return new XMLHttpRequest();
		};
	}else if(has('activex')){
		try{
			new ActiveXObject('Msxml2.XMLHTTP');
			xhr._create = function(){
				return new ActiveXObject('Msxml2.XMLHTTP');
			};
		}catch(e){
			try{
				new ActiveXObject('Microsoft.XMLHTTP');
				xhr._create = function(){
					return new ActiveXObject('Microsoft.XMLHTTP');
				};
			}catch(e){}
		}
	}

	util.addCommonMethods(xhr);

	return xhr;
});

},
'dojo/request/handlers':function(){
define([
	'../json',
	'../_base/kernel',
	'../_base/array',
	'../has',
	'../selector/_loader' // only included for has() qsa tests
], function(JSON, kernel, array, has){
	has.add('activex', typeof ActiveXObject !== 'undefined');
	has.add('dom-parser', function(global){
		return 'DOMParser' in global;
	});

	var handleXML;
	if(has('activex')){
		// GUIDs obtained from http://msdn.microsoft.com/en-us/library/ms757837(VS.85).aspx
		var dp = [
			'Msxml2.DOMDocument.6.0',
			'Msxml2.DOMDocument.4.0',
			'MSXML2.DOMDocument.3.0',
			'MSXML.DOMDocument' // 2.0
		];

		handleXML = function(response){
			var result = response.data;

			if(result && has('dom-qsa2.1') && !result.querySelectorAll && has('dom-parser')){
				// http://bugs.dojotoolkit.org/ticket/15631
				// IE9 supports a CSS3 querySelectorAll implementation, but the DOM implementation 
				// returned by IE9 xhr.responseXML does not. Manually create the XML DOM to gain 
				// the fuller-featured implementation and avoid bugs caused by the inconsistency
				result = new DOMParser().parseFromString(response.text, 'application/xml');
			}

			if(!result || !result.documentElement){
				var text = response.text;
				array.some(dp, function(p){
					try{
						var dom = new ActiveXObject(p);
						dom.async = false;
						dom.loadXML(text);
						result = dom;
					}catch(e){ return false; }
					return true;
				});
			}

			return result;
		};
	}

	var handlers = {
		'javascript': function(response){
			return kernel.eval(response.text || '');
		},
		'json': function(response){
			return JSON.parse(response.text || null);
		},
		'xml': handleXML
	};

	function handle(response){
		var handler = handlers[response.options.handleAs];

		response.data = handler ? handler(response) : (response.data || response.text);

		return response;
	}

	handle.register = function(name, handler){
		handlers[name] = handler;
	};

	return handle;
});

},
'edu/wohaoo/mobile/_T9nMixin':function(){
/** SPECS
	Translation mixin
 */
define([
     "dojo/request", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dijit/registry",  
     "dojo/domReady!"
], function(dojoRequest, declare, _WidgetBase, 
	 dijitRegistry, messages) {
	 
	var _T9nMixin = declare([_WidgetBase],{
		ServiceUrl: "/translations/translate/",
		
		postCreate: function(){
			
			var self = this;
			
			var httpScheme = Sys.getHttpScheme();
			var httpHost = Sys.getHttpHost();
			if(httpScheme && httpHost){
				this.ServiceUrl = httpScheme + "://" + httpHost  + this.ServiceUrl;
			}
			this.inherited(arguments);
		},
		
		translate : function(trans_lang, orig_text, orig_lang){
			
			if(!orig_lang || typeof orig_lang === 'undefined')
				orig_lang = 'en';
			if(!trans_lang || typeof trans_lang === 'undefined')
				trans_lang = dojo.locale;
			if(orig_lang ===  trans_lang)
				return orig_text;
			var self = this;
			var translation = orig_text;
			
			if(typeof TRANSLATIONS_DB === 'object'  && typeof CryptoJS === 'object'){
				var guid = CryptoJS.MD5(orig_text);
				if(typeof TRANSLATIONS_DB[guid] === 'object'  && typeof TRANSLATIONS_DB[guid][trans_lang]  !== 'undefined'){
					translation = TRANSLATIONS_DB[guid][trans_lang];
					return translation;
				}
			}
			
			// The parameters to pass to xhrGet, the url, how to handle it, and the callbacks.
			var xhrArgs = {
				url: this.ServiceUrl,
				sync: true,
				handleAs: "json",
				preventCache: false,
				postData:  {
					 "data[Translation][t9n_orig_lang]":  orig_lang,
					"data[Translation][t9n_orig_text]": orig_text,
					"data[Translation][t9n_trans_lang]": trans_lang
				},
				load: function(data){
					
					 if(!data.trans_text){
						translation = orig_text;
					}
					else{
						translation =  data.trans_text;
					}
				},
				error: function(error){
				}
			};

			// Call the asynchronous xhrGet
			var deferred = dojo.xhrPost(xhrArgs);
			return translation;
		}
	});
	_T9nMixin.prototype._S = new _T9nMixin();
	window.__t = function(orig_text, trans_lang, orig_lang){
		return _T9nMixin.prototype.translate.call(_T9nMixin.prototype._S, trans_lang, orig_text, orig_lang);
	};
	return _T9nMixin;
});
},
'edu/wohaoo/mobile/AppListView':function(){
define([
     "dojo/_base/array", "dojo/on", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare", 
      "dojox/mobile/ScrollableView",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/registry", "edu/wohaoo/mobile/_AppViewMixin",
     "dojo/data/ItemFileReadStore", "dojo/domReady!"
], function(dojoArray, on, dojoQuery, dojoConnect, declare, MobileScrollableView, 
                _WidgetBase, domConstruct,  dijitRegistry,
		_AppViewMixin, ItemFileReadStore) {
	return declare([MobileScrollableView, _AppViewMixin],{
		//no Scrollable Cover
		noCover: true,
		
		// Create a template string for items:
		itemTemplateString: '',

		listNode: null,
		
		itemClass: '',
		
		_store: null,
		
		dataId: null,
		
		q: "",
		
		l: "",
		
		minId: 0,
		maxId: 0,
		
		dataUrl: null,
		
		autoScroll: true,
		
		postCreate: function(){
			this.inherited(arguments);
			
			var self = this;
			on(this, 'beforescroll', function (evt){
				self.__beforescroll(evt);
			});
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.domNode).forEach(function(element){
				element.style.display = 'none';
			});
		},
		
		__beforescroll: function(evt){
			if(!this.autoScroll)
				return false;
			if(!evt.afterBottom && !evt.beforeTop)
				return false;
			if(evt.afterBottomHeight > 40)
				return false;
			if(evt.beforeTopHeight > 40)
				return false;
			if(this.busy)
				return false;
			var up = false, down = false;
			up = evt.beforeTopHeight > 0;
			down = evt.afterBottomHeight > 0;
			console.debug("up=" + up + ",down=" + down);
			this.dataParams = [];
			if(down && this.maxId > 0){
				this.dataParams[this.dataParams.length] = ['max_id=' + this.maxId];
			}else
			if(up && this.minId > 0){
				this.dataParams[this.dataParams.length] = ['min_id=' + this.minId];
			}
			console.debug("refresh:minId=" + this.minId + ",maxId=" + this.maxId);
			if(this.dataParams.length > 0){
				this.refresh();
			}
			return true;
		},
		
		updateItemList: function(){
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			this.processDataParams();
			this._store = new ItemFileReadStore({url: this.dataUrl});
			
			if(!this._store || this._store === null || this.itemTemplateString === null || this.itemTemplateString === '')
				return;
			//self MUST be defined right next to the fetch call!!!
			var self = this;
			self._store.fetch({query: this.query, 
			onBegin: function(){
				self.hideItems();
				self.minId = 0;
				self.maxId = 0;
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					var iStore = storeItem._S; //get the item store
					var id = iStore.getValue(storeItem, "uniqueid");
					var uniqueid = iStore.getValue(storeItem, "uniqueid");
					var caption = iStore.getValue(storeItem, "name");
					
					// Create a new list item, inject into list
					var item = self.createListItem(self.itemClass,
						id,
						caption,
						self.itemMoveTo,
						self.itemTransition);
					
					if(!item || item === null)
						throw new Exception(__t("New Item is Null"));
					
					var icon = iStore.getValue(storeItem, "icon");
					if(icon.indexOf("//") === -1){
						var httpScheme = Sys.getHttpScheme();
						var httpHost = Sys.getHttpHost();
						if(httpScheme && httpHost){
							icon = httpScheme + "://" + httpHost  + icon;
						}
					}
					storeItem.icon = icon;
					item.set('icon', icon);
					
					item.placeAt(self.listNode,"last");
					
					// Update the list item's content using our template for items
					self.updateItemContent(item, self.itemTemplateString, storeItem);
					
					if(typeof item.moveTo !== 'undefined'){
						if(item.domNode._clickEvent)
							dojoConnect.disconnect(item.domNode._clickEvent);
						item.domNode._clickEvent = dojoConnect.connect(item.domNode, 'click', function(evt){
							var itemWidget = dijitRegistry.byNode(this);
							var moveToWidget = dijitRegistry.byId(itemWidget.moveTo);
							moveToWidget.set('dataId', itemWidget.dataId);
							moveToWidget.refresh();
							self.moveTo(self.id, item.moveTo, itemWidget.transition);
						});
					}
					
					//uniaueid === ranking
					console.debug("uniqueid=" + uniqueid);
					if(uniqueid > self.maxId)
						self.maxId = uniqueid;
					if(!self.minId || uniqueid < self.minId)
						self.minId = uniqueid;
					console.debug("minId=" + self.minId + ",maxId=" + self.maxId);
				});
			}, onError: function(error){
				self.alert(self.errors.err102);//Internal client error
				console.debug("error=" + error);
				throw error;
			}});
		},
		
		updateItemContent: function(item, itemTemplateString, storeItem){
			item.labelNode.innerHTML = this.substitute(self.itemTemplateString, storeItem);
		},
		
		refresh: function(){
			this.updateItemList();
			this.inherited(arguments);
		}
	});
});
},
'dojox/mobile/ScrollableView':function(){
define([
	"dojo/_base/array",
	"dojo/_base/declare",
	"dojo/dom-class",
	"dojo/dom-construct",
	"dijit/registry",	// registry.byNode
	"./View",
	"./_ScrollableMixin"
], function(array, declare, domClass, domConstruct, registry, View, ScrollableMixin){

	// module:
	//		dojox/mobile/ScrollableView

	return declare("dojox.mobile.ScrollableView", [View, ScrollableMixin], {
		// summary:
		//		A container that has a touch scrolling capability.
		// description:
		//		ScrollableView is a subclass of View (dojox/mobile/View).
		//		Unlike the base View class, ScrollableView's domNode always stays
		//		at the top of the screen and its height is "100%" of the screen.
		//		Inside this fixed domNode, the containerNode scrolls. The browser's
		//		default scrolling behavior is disabled, and the scrolling mechanism is
		//		reimplemented in JavaScript. Thus the user does not need to use the
		//		two-finger operation to scroll the inner DIV (containerNode).
		//		The main purpose of this widget is to realize fixed-positioned header
		//		and/or footer bars.

		// scrollableParams: Object
		//		Parameters for dojox/mobile/scrollable.init().
		scrollableParams: null,

		// keepScrollPos: Boolean
		//		Overrides dojox/mobile/View/keepScrollPos.
		keepScrollPos: false,

		constructor: function(){
			// summary:
			//		Creates a new instance of the class.
			this.scrollableParams = {noResize: true};
		},

		buildRendering: function(){
			this.inherited(arguments);
			domClass.add(this.domNode, "mblScrollableView");
			this.domNode.style.overflow = "hidden";
			this.domNode.style.top = "0px";
			this.containerNode = domConstruct.create("div",
				{className:"mblScrollableViewContainer"}, this.domNode);
			this.containerNode.style.position = "absolute";
			this.containerNode.style.top = "0px"; // view bar is relative
			if(this.scrollDir === "v"){
				this.containerNode.style.width = "100%";
			}
		},

		startup: function(){
			if(this._started){ return; }
			// user can initialize the app footers using a value for fixedFooter (we keep this value for non regression of existing apps)
			if(this.fixedFooter && !this.isLocalFooter){
				this._fixedAppFooter = this.fixedFooter;
				this.fixedFooter = "";
			}
			this.reparent();
			this.inherited(arguments);
		},

		resize: function(){
			// summary:
			//		Calls resize() of each child widget.
			this.inherited(arguments); // scrollable#resize() will be called
			array.forEach(this.getChildren(), function(child){
				if(child.resize){ child.resize(); }
			});
			this._dim = this.getDim(); // update dimension cache
			if(this._conn){
				// if a resize happens during a scroll, update the scrollbar
				this.resetScrollBar();
			}
		},

		isTopLevel: function(/*Event*/e){
			// summary:
			//		Returns true if this is a top-level widget.
			//		Overrides dojox/mobile/scrollable.isTopLevel.
			var parent = this.getParent && this.getParent();
			return (!parent || !parent.resize); // top level widget
		},

		addFixedBar: function(/*Widget*/widget){
			// summary:
			//		Adds a view local fixed bar to this widget.
			// description:
			//		This method can be used to programmatically add a view local
			//		fixed bar to ScrollableView. The bar is appended to this
			//		widget's domNode. The addChild API cannot be used for this
			//		purpose, because it adds the given widget to containerNode.
			var c = widget.domNode;
			var fixed = this.checkFixedBar(c, true);
			if(!fixed){ return; }
			// Fixed bar has to be added to domNode, not containerNode.
			this.domNode.appendChild(c);
			if(fixed === "top"){
				this.fixedHeaderHeight = c.offsetHeight;
				this.isLocalHeader = true;
			}else if(fixed === "bottom"){
				this.fixedFooterHeight = c.offsetHeight;
				this.isLocalFooter = true;
				c.style.bottom = "0px";
			}
			this.resize();
		},

		reparent: function(){
			// summary:
			//		Moves all the children, except header and footer, to
			//		containerNode.
			var i, idx, len, c;
			for(i = 0, idx = 0, len = this.domNode.childNodes.length; i < len; i++){
				c = this.domNode.childNodes[idx];
				// search for view-specific header or footer
				if(c === this.containerNode || this.checkFixedBar(c, true)){
					idx++;
					continue;
				}
				this.containerNode.appendChild(this.domNode.removeChild(c));
			}
		},

		onAfterTransitionIn: function(moveTo, dir, transition, context, method){
			// summary:
			//		Overrides View.onAfterTransitionIn to flash the scroll bar
			//		after performing a view transition.
			this.flashScrollBar();
		},

		getChildren: function(){
			// summary:
			//		Overrides _WidgetBase.getChildren to add local fixed bars,
			//		which are not under containerNode, to the children array.
			var children = this.inherited(arguments);
			var fixedWidget;
			if(this.fixedHeader && this.fixedHeader.parentNode === this.domNode){
				fixedWidget = registry.byNode(this.fixedHeader);
				if(fixedWidget){
					children.push(fixedWidget);
				}
			}
			if(this.fixedFooter && this.fixedFooter.parentNode === this.domNode){
				fixedWidget = registry.byNode(this.fixedFooter);
				if(fixedWidget){
					children.push(fixedWidget);
				}
			}
			return children;
		},

		_addTransitionPaddingTop: function(/*String|Integer*/ value){
			// add padding top to the view in order to get alignment during the transition
			this.domNode.style.paddingTop = value + "px";
			this.containerNode.style.paddingTop = value + "px";
		},

		_removeTransitionPaddingTop: function(){
			// remove padding top from the view after the transition
			this.domNode.style.paddingTop = "";
			this.containerNode.style.paddingTop = "";
		}

	});
});

},
'dojox/mobile/_ScrollableMixin':function(){
define([
	"dojo/_base/kernel",
	"dojo/_base/config",
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/_base/window",
	"dojo/dom",
	"dojo/dom-class",
	"dijit/registry",	// registry.byNode
	"./scrollable"
], function(dojo, config, declare, lang, win, dom, domClass, registry, Scrollable){
	// module:
	//		dojox/mobile/_ScrollableMixin

	var cls = declare("dojox.mobile._ScrollableMixin", Scrollable, {
		// summary:
		//		Mixin for widgets to have a touch scrolling capability.
	
		// fixedHeader: String
		//		Id of the fixed header.
		fixedHeader: "",

		// fixedFooter: String
		//		Id of the fixed footer.
		fixedFooter: "",

		_fixedAppFooter: "",

		// scrollableParams: Object
		//		Parameters for dojox/mobile/scrollable.init().
		scrollableParams: null,

		// allowNestedScrolls: Boolean
		//		Flag to allow scrolling in nested containers, e.g. to allow ScrollableView in a SwapView.
		allowNestedScrolls: true,

		// appBars: Boolean
		//		Enables the search for application-specific bars (header or footer).
		appBars: true, 

		constructor: function(){
			// summary:
			//		Creates a new instance of the class.
			// tags:
			//		private
			this.scrollableParams = {};
		},

		destroy: function(){
			this.cleanup();
			this.inherited(arguments);
		},

		startup: function(){
			if(this._started){ return; }
			if(this._fixedAppFooter){
				this._fixedAppFooter = dom.byId(this._fixedAppFooter);
			}
			this.findAppBars();
			var node, params = this.scrollableParams;
			if(this.fixedHeader){
				node = dom.byId(this.fixedHeader);
				if(node.parentNode == this.domNode){ // local footer
					this.isLocalHeader = true;
				}
				params.fixedHeaderHeight = node.offsetHeight;
			}
			if(this.fixedFooter){
				node = dom.byId(this.fixedFooter);
				if(node.parentNode == this.domNode){ // local footer
					this.isLocalFooter = true;
					node.style.bottom = "0px";
				}
				params.fixedFooterHeight = node.offsetHeight;
			}
			this.scrollType = this.scrollType || config["mblScrollableScrollType"] || 0;
			this.init(params);
			if(this.allowNestedScrolls){
				for(var p = this.getParent(); p; p = p.getParent()){
					if(p && p.scrollableParams){
						this.dirLock = true;
						p.dirLock = true;
						break;
					}
				}
			}
			// subscribe to afterResizeAll to scroll the focused input field into view
			// so as not to break layout on orientation changes while keyboard is shown (#14991)
			this._resizeHandle = this.subscribe("/dojox/mobile/afterResizeAll", function(){
				if(this.domNode.style.display === 'none'){ return; }
				var elem = win.doc.activeElement;
				if(this.isFormElement(elem) && dom.isDescendant(elem, this.containerNode)){
					this.scrollIntoView(elem);
				}
			});
			this.inherited(arguments);
		},

		findAppBars: function(){
			// summary:
			//		Search for application-specific header or footer.
			if(!this.appBars){ return; }
			var i, len, c;
			for(i = 0, len = win.body().childNodes.length; i < len; i++){
				c = win.body().childNodes[i];
				this.checkFixedBar(c, false);
			}
			if(this.domNode.parentNode){
				for(i = 0, len = this.domNode.parentNode.childNodes.length; i < len; i++){
					c = this.domNode.parentNode.childNodes[i];
					this.checkFixedBar(c, false);
				}
			}
			this.fixedFooterHeight = this.fixedFooter ? this.fixedFooter.offsetHeight : 0;
		},

		checkFixedBar: function(/*DomNode*/node, /*Boolean*/local){
			// summary:
			//		Checks if the given node is a fixed bar or not.
			if(node.nodeType === 1){
				var fixed = node.getAttribute("fixed") // TODO: Remove the non-HTML5-compliant attribute in 2.0
					|| node.getAttribute("data-mobile-fixed")
					|| (registry.byNode(node) && registry.byNode(node).fixed);
				if(fixed === "top"){
					domClass.add(node, "mblFixedHeaderBar");
					if(local){
						node.style.top = "0px";
						this.fixedHeader = node;
					}
					return fixed;
				}else if(fixed === "bottom"){
					domClass.add(node, "mblFixedBottomBar");
					if(local){
						this.fixedFooter = node;
					}else{
						this._fixedAppFooter = node;
					}
					return fixed;
				}
			}
			return null;
		}
	});
	return cls;
});

},
'dojox/mobile/scrollable':function(){
define([
	"dojo/_base/kernel",
	"dojo/_base/connect",
	"dojo/_base/event",
	"dojo/_base/lang",
	"dojo/_base/window",
	"dojo/dom-class",
	"dojo/dom-construct",
	"dojo/dom-style",
	"dojo/dom-geometry",
	"dojo/touch",
	"./sniff",
	"./_css3",
	"./_maskUtils"
], function(dojo, connect, event, lang, win, domClass, domConstruct, domStyle,
			domGeom, touch, has, css3, maskUtils){

	// module:
	//		dojox/mobile/scrollable

	// TODO: rename to Scrollable.js (capital S) for 2.0

	// TODO: shouldn't be referencing this dojox/mobile variable, would be better to require the mobile.js module
	var dm = lang.getObject("dojox.mobile", true);

	// feature detection
	has.add("translate3d", function(){
		if(has("css3-animations")){
			var elem = win.doc.createElement("div");
			elem.style[css3.name("transform")] = "translate3d(0px,1px,0px)";
			win.doc.documentElement.appendChild(elem);
			var v = win.doc.defaultView.getComputedStyle(elem, '')[css3.name("transform", true)];
			var hasTranslate3d = v && v.indexOf("matrix") === 0;
			win.doc.documentElement.removeChild(elem);
			return hasTranslate3d;
		}
	});

	var Scrollable = function(){
		// summary:
		//		Mixin for enabling touch scrolling capability.
		// description:
		//		Mixin for enabling touch scrolling capability.
		//		Mobile WebKit browsers do not allow scrolling inner DIVs. (For instance,
		//		on iOS you need the two-finger operation to scroll them.)
		//		That means you cannot have fixed-positioned header/footer bars.
		//		To solve this issue, this module disables the browsers default scrolling
		//		behavior, and rebuilds its own scrolling machinery by handling touch
		//		events. In this module, this.domNode has height "100%" and is fixed to
		//		the window, and this.containerNode scrolls. If you place a bar outside
		//		of this.containerNode, then it will be fixed-positioned while
		//		this.containerNode is scrollable.
		//
		//		This module has the following features:
		//
		//		- Scrolls inner DIVs vertically, horizontally, or both.
		//		- Vertical and horizontal scroll bars.
		//		- Flashes the scroll bars when a view is shown.
		//		- Simulates the flick operation using animation.
		//		- Respects header/footer bars if any.
	};

	lang.extend(Scrollable, {
		// fixedHeaderHeight: Number
		//		height of a fixed header
		fixedHeaderHeight: 0,

		// fixedFooterHeight: Number
		//		height of a fixed footer
		fixedFooterHeight: 0,

		// isLocalFooter: Boolean
		//		footer is view-local (as opposed to application-wide)
		isLocalFooter: false,

		// scrollBar: Boolean
		//		show scroll bar or not
		scrollBar: true,

		// scrollDir: String
		//		v: vertical, h: horizontal, vh: both, f: flip
		scrollDir: "v",

		// weight: Number
		//		frictional drag
		weight: 0.6,

		// fadeScrollBar: Boolean
		fadeScrollBar: true,

		// disableFlashScrollBar: Boolean
		disableFlashScrollBar: false,

		// threshold: Number
		//		drag threshold value in pixels
		threshold: 4,

		// constraint: Boolean
		//		bounce back to the content area
		constraint: true,

		// touchNode: DOMNode
		//		a node that will have touch event handlers
		touchNode: null,

		// propagatable: Boolean
		//		let touchstart event propagate up
		propagatable: true,

		// dirLock: Boolean
		//		disable the move handler if scroll starts in the unexpected direction
		dirLock: false,

		// height: String
		//		explicitly specified height of this widget (ex. "300px")
		height: "",

		// scrollType: Number
		//		- 1: use (-webkit-)transform:translate3d(x,y,z) style, use (-webkit-)animation for slide animation
		//		- 2: use top/left style,
		//		- 3: use (-webkit-)transform:translate3d(x,y,z) style, use (-webkit-)transition for slide animation
		//		- 0: use default value (3 for Android, iOS6+, and BlackBerry; otherwise 1)
		scrollType: 0,
		
		// _parentPadBorderExtentsBottom: [private] Number
		//		For Tooltip.js.
		_parentPadBorderExtentsBottom: 0,

		// _moved: [private] Boolean
		//		Flag that signals if the user have moved in (one of) the scroll
		//		direction(s) since touch start (a move under the threshold is ignored).
		_moved: false,

		init: function(/*Object?*/params){
			// summary:
			//		Initialize according to the given params.
			// description:
			//		Mixes in the given params into this instance. At least domNode
			//		and containerNode have to be given.
			//		Starts listening to the touchstart events.
			//		Calls resize(), if this widget is a top level widget.
			if(params){
				for(var p in params){
					if(params.hasOwnProperty(p)){
						this[p] = ((p == "domNode" || p == "containerNode") && typeof params[p] == "string") ?
							win.doc.getElementById(params[p]) : params[p]; // mix-in params
					}
				}
			}
			// prevent browser scrolling on IE10 (evt.preventDefault() is not enough)
			if(typeof this.domNode.style.msTouchAction != "undefined"){
				this.domNode.style.msTouchAction = "none";
			}
			this.touchNode = this.touchNode || this.containerNode;
			this._v = (this.scrollDir.indexOf("v") != -1); // vertical scrolling
			this._h = (this.scrollDir.indexOf("h") != -1); // horizontal scrolling
			this._f = (this.scrollDir == "f"); // flipping views

			this._ch = []; // connect handlers
			this._ch.push(connect.connect(this.touchNode, touch.press, this, "onTouchStart"));
			if(has("css3-animations")){
				// flag for whether to use -webkit-transform:translate3d(x,y,z) or top/left style.
				// top/left style works fine as a workaround for input fields auto-scrolling issue,
				// so use top/left in case of Android by default.
				this._useTopLeft = this.scrollType ? this.scrollType === 2 : false;
				// Flag for using webkit transition on transform, instead of animation + keyframes.
				// (keyframes create a slight delay before the slide animation...)
				if(!this._useTopLeft){
					this._useTransformTransition = 
						this.scrollType ? this.scrollType === 3 : has("ios") >= 6 || has("android") || has("bb");
				}
				if(!this._useTopLeft){
					if(this._useTransformTransition){
						this._ch.push(connect.connect(this.domNode, css3.name("transitionEnd"), this, "onFlickAnimationEnd"));
						this._ch.push(connect.connect(this.domNode, css3.name("transitionStart"), this, "onFlickAnimationStart"));
					}else{
						this._ch.push(connect.connect(this.domNode, css3.name("animationEnd"), this, "onFlickAnimationEnd"));
						this._ch.push(connect.connect(this.domNode, css3.name("animationStart"), this, "onFlickAnimationStart"));
	
						// Creation of keyframes takes a little time. If they are created
						// in a lazy manner, a slight delay is noticeable when you start
						// scrolling for the first time. This is to create keyframes up front.
						for(var i = 0; i < 3; i++){
							this.setKeyframes(null, null, i);
						}
					}
					if(has("translate3d")){ // workaround for flicker issue on iPhone and Android 3.x/4.0
						domStyle.set(this.containerNode, css3.name("transform"), "translate3d(0,0,0)");
					}
				}else{
					this._ch.push(connect.connect(this.domNode, css3.name("transitionEnd"), this, "onFlickAnimationEnd"));
					this._ch.push(connect.connect(this.domNode, css3.name("transitionStart"), this, "onFlickAnimationStart"));
				}
			}

			this._speed = {x:0, y:0};
			this._appFooterHeight = 0;
			if(this.isTopLevel() && !this.noResize){
				this.resize();
			}
			var _this = this;
			setTimeout(function(){ 
				// Why not using widget.defer() instead of setTimeout()? Because this module
				// is not always mixed into a widget (ex. dojox/mobile/_ComboBoxMenu), and adding 
				// a check to call either defer or setTimeout has been considered overkill.
				_this.flashScrollBar();
			}, 600);
			
			// #16363: while navigating among input field using TAB (desktop keyboard) or 
			// NEXT (mobile soft keyboard), domNode.scrollTop gets modified (this holds even 
			// if the text widget has selectOnFocus at false, that is even if dijit's _FormWidgetMixin._onFocus 
			// does not trigger a global scrollIntoView). This messes up ScrollableView's own 
			// scrolling machinery. To avoid this misbehavior:
			if(win.global.addEventListener){ // all supported browsers but IE8
				// (for IE8, using attachEvent is not a solution, because it only works in bubbling phase)
				this._onScroll = function(e){
					if(!_this.domNode || _this.domNode.style.display === 'none'){ return; }
					var scrollTop = _this.domNode.scrollTop;
					var scrollLeft = _this.domNode.scrollLeft; 
					var pos;
					if(scrollTop > 0 || scrollLeft > 0){ 
						pos = _this.getPos(); 
						// Reset to zero while compensating using our own scroll: 
						_this.domNode.scrollLeft = 0; 
						_this.domNode.scrollTop = 0; 
						_this.scrollTo({x: pos.x - scrollLeft, y: pos.y - scrollTop}); // no animation 
					}
				};
				win.global.addEventListener("scroll", this._onScroll, true);
			}
			// #17062: Ensure auto-scroll when navigating focusable fields
			if(!this.disableTouchScroll && this.domNode.addEventListener){
				this._onFocusScroll = function(e){
					if(!_this.domNode || _this.domNode.style.display === 'none'){ return; }
					var node = win.doc.activeElement;
					var nodeRect, scrollableRect;
					if(node){
						nodeRect = node.getBoundingClientRect();
						scrollableRect = _this.domNode.getBoundingClientRect();
						if(nodeRect.height < _this.getDim().d.h){
							// do not call scrollIntoView for elements with a height
							// larger than the height of scrollable's content display
							// area (it would be ergonomically harmful).
							
							if(nodeRect.top < (scrollableRect.top + _this.fixedHeaderHeight)){
								// scrolling towards top (to bring into the visible area an element
								// located above it).
								_this.scrollIntoView(node, true);
							}else if((nodeRect.top + nodeRect.height) > 
								(scrollableRect.top + scrollableRect.height - _this.fixedFooterHeight)){
								// scrolling towards bottom (to bring into the visible area an element
								// located below it).
								_this.scrollIntoView(node, false);
							} // else do nothing (the focused node is already visible)
						}
					}
				};
				this.domNode.addEventListener("focus", this._onFocusScroll, true);
			}
		},

		isTopLevel: function(){
			// summary:
			//		Returns true if this is a top-level widget.
			// description:
			//		Subclass may want to override.
			return true;
		},

		cleanup: function(){
			// summary:
			//		Uninitialize the module.
			if(this._ch){
				for(var i = 0; i < this._ch.length; i++){
					connect.disconnect(this._ch[i]);
				}
				this._ch = null;
			}
			if(this._onScroll && win.global.removeEventListener){ // all supported browsers but IE8
				win.global.removeEventListener("scroll", this._onScroll, true);
				this._onScroll = null;
			}
			
			if(this._onFocusScroll && this.domNode.removeEventListener){
				this.domNode.removeEventListener("focus", this._onFocusScroll, true);
				this._onFocusScroll = null;
			} 
		},

		findDisp: function(/*DomNode*/node){
			// summary:
			//		Finds the currently displayed view node from my sibling nodes.
			if(!node.parentNode){ return null; }

			// the given node is the first candidate
			if(node.nodeType === 1 && domClass.contains(node, "mblSwapView") && node.style.display !== "none"){
				return node;
			}

			var nodes = node.parentNode.childNodes;
			for(var i = 0; i < nodes.length; i++){
				var n = nodes[i];
				if(n.nodeType === 1 && domClass.contains(n, "mblView") && n.style.display !== "none"){
					return n;
				}
			}
			return node;
		},

		getScreenSize: function(){
			// summary:
			//		Returns the dimensions of the browser window.
			return {
				h: win.global.innerHeight||win.doc.documentElement.clientHeight||win.doc.documentElement.offsetHeight,
				w: win.global.innerWidth||win.doc.documentElement.clientWidth||win.doc.documentElement.offsetWidth
			};
		},

		resize: function(e){
			// summary:
			//		Adjusts the height of the widget.
			// description:
			//		If the height property is 'inherit', the height is inherited
			//		from its offset parent. If 'auto', the content height, which
			//		could be smaller than the entire screen height, is used. If an
			//		explicit height value (ex. "300px"), it is used as the new
			//		height. If nothing is specified as the height property, from the
			//		current top position of the widget to the bottom of the screen
			//		will be the new height.

			// moved from init() to support dynamically added fixed bars
			this._appFooterHeight = (this._fixedAppFooter) ? this._fixedAppFooter.offsetHeight : 0;
			if(this.isLocalHeader){
				this.containerNode.style.marginTop = this.fixedHeaderHeight + "px";
			}

			// Get the top position. Same as dojo.position(node, true).y
			var top = 0;
			for(var n = this.domNode; n && n.tagName != "BODY"; n = n.offsetParent){
				n = this.findDisp(n); // find the first displayed view node
				if(!n){ break; }
				top += n.offsetTop + domGeom.getBorderExtents(n).h;
			}

			// adjust the height of this view
			var	h,
				screenHeight = this.getScreenSize().h,
				dh = screenHeight - top - this._appFooterHeight; // default height
			if(this.height === "inherit"){
				if(this.domNode.offsetParent){
					h = domGeom.getContentBox(this.domNode.offsetParent).h - domGeom.getBorderExtents(this.domNode).h + "px";
				}
			}else if(this.height === "auto"){
				var parent = this.domNode.offsetParent;
				if(parent){
					this.domNode.style.height = "0px";
					var	parentRect = parent.getBoundingClientRect(),
						scrollableRect = this.domNode.getBoundingClientRect(),
						contentBottom = parentRect.bottom - this._appFooterHeight - this._parentPadBorderExtentsBottom;
					if(scrollableRect.bottom >= contentBottom){ // use entire screen
						dh = screenHeight - (scrollableRect.top - parentRect.top) - this._appFooterHeight - this._parentPadBorderExtentsBottom;
					}else{ // stretch to fill predefined area
						dh = contentBottom - scrollableRect.bottom;
					}
				}
				// content could be smaller than entire screen height
				var contentHeight = Math.max(this.domNode.scrollHeight, this.containerNode.scrollHeight);
				h = (contentHeight ? Math.min(contentHeight, dh) : dh) + "px";
			}else if(this.height){
				h = this.height;
			}
			if(!h){
				h = dh + "px";
			}
			if(h.charAt(0) !== "-" && // to ensure that h is not negative (e.g. "-10px")
				h !== "default"){
				this.domNode.style.height = h;
			}

			if(!this._conn){
				// to ensure that the view is within a scrolling area when resized.
				this.onTouchEnd();
			}
		},

		onFlickAnimationStart: function(e){
			event.stop(e);
		},

		onFlickAnimationEnd: function(e){
			if(has("ios")){
				this._keepInputCaretInActiveElement();
			}
			if(e){
				var an = e.animationName;
				if(an && an.indexOf("scrollableViewScroll2") === -1){
					if(an.indexOf("scrollableViewScroll0") !== -1){ // scrollBarV
						if(this._scrollBarNodeV){ domClass.remove(this._scrollBarNodeV, "mblScrollableScrollTo0"); }
					}else if(an.indexOf("scrollableViewScroll1") !== -1){ // scrollBarH
						if(this._scrollBarNodeH){ domClass.remove(this._scrollBarNodeH, "mblScrollableScrollTo1"); }
					}else{ // fade or others
						if(this._scrollBarNodeV){ this._scrollBarNodeV.className = ""; }
						if(this._scrollBarNodeH){ this._scrollBarNodeH.className = ""; }
					}
					return;
				}
				if(this._useTransformTransition || this._useTopLeft){
					var n = e.target;
					if(n === this._scrollBarV || n === this._scrollBarH){
						var cls = "mblScrollableScrollTo" + (n === this._scrollBarV ? "0" : "1");
						if(domClass.contains(n, cls)){
							domClass.remove(n, cls);
						}else{
							n.className = "";
						}
						return;
					}
				}
				if(e.srcElement){
					event.stop(e);
				}
			}
			this.stopAnimation();
			if(this._bounce){
				var _this = this;
				var bounce = _this._bounce;
				setTimeout(function(){
					_this.slideTo(bounce, 0.3, "ease-out");
				}, 0);
				_this._bounce = undefined;
			}else{
				this.hideScrollBar();
				this.removeCover();
			}
		},

		isFormElement: function(/*DOMNode*/node){
			// summary:
			//		Returns true if the given node is a form control.
			if(node && node.nodeType !== 1){ node = node.parentNode; }
			if(!node || node.nodeType !== 1){ return false; }
			var t = node.tagName;
			return (t === "SELECT" || t === "INPUT" || t === "TEXTAREA" || t === "BUTTON");
		},

		onTouchStart: function(e){
			// summary:
			//		User-defined function to handle touchStart events.
			if(this.disableTouchScroll){ return; }
			if(this._conn && (new Date()).getTime() - this.startTime < 500){
				return; // ignore successive onTouchStart calls
			}
			if(!this._conn){
				this._conn = [];
				this._conn.push(connect.connect(win.doc, touch.move, this, "onTouchMove"));
				this._conn.push(connect.connect(win.doc, touch.release, this, "onTouchEnd"));
			}

			this._aborted = false;
			if(domClass.contains(this.containerNode, "mblScrollableScrollTo2")){
				this.abort();
			}else{ // reset scrollbar class especially for reseting fade-out animation
				if(this._scrollBarNodeV){ this._scrollBarNodeV.className = ""; }
				if(this._scrollBarNodeH){ this._scrollBarNodeH.className = ""; }
			}
			this.touchStartX = e.touches ? e.touches[0].pageX : e.clientX;
			this.touchStartY = e.touches ? e.touches[0].pageY : e.clientY;
			this.startTime = (new Date()).getTime();
			this.startPos = this.getPos();
			this._dim = this.getDim();
			this._time = [0];
			this._posX = [this.touchStartX];
			this._posY = [this.touchStartY];
			this._locked = false;
			this._moved = false;

			if(!this.isFormElement(e.target)){
				this.propagatable ? e.preventDefault() : event.stop(e);
			}
		},

		onTouchMove: function(e){
			// summary:
			//		User-defined function to handle touchMove events.
			if(this._locked){ return; }
			var x = e.touches ? e.touches[0].pageX : e.clientX;
			var y = e.touches ? e.touches[0].pageY : e.clientY;
			var dx = x - this.touchStartX;
			var dy = y - this.touchStartY;
			var to = {x:this.startPos.x + dx, y:this.startPos.y + dy};
			var dim = this._dim;

			dx = Math.abs(dx);
			dy = Math.abs(dy);
			if(this._time.length == 1){ // the first TouchMove after TouchStart
				if(this.dirLock){
					if(this._v && !this._h && dx >= this.threshold && dx >= dy ||
						(this._h || this._f) && !this._v && dy >= this.threshold && dy >= dx){
						this._locked = true;
						return;
					}
				}
				if(this._v && this._h){ // scrollDir="hv"
					if(dy < this.threshold &&
						dx < this.threshold){
						return;
					}
				}else{
					if(this._v && dy < this.threshold ||
						(this._h || this._f) && dx < this.threshold){
						return;
					}
				}
				this._moved = true;
				this.addCover();
				this.showScrollBar();
			}

			var weight = this.weight;
			if(this._v && this.constraint){
				if(to.y > 0){ // content is below the screen area
					to.y = Math.round(to.y * weight);
				}else if(to.y < -dim.o.h){ // content is above the screen area
					if(dim.c.h < dim.d.h){ // content is shorter than display
						to.y = Math.round(to.y * weight);
					}else{
						to.y = -dim.o.h - Math.round((-dim.o.h - to.y) * weight);
					}
				}
			}
			if((this._h || this._f) && this.constraint){
				if(to.x > 0){
					to.x = Math.round(to.x * weight);
				}else if(to.x < -dim.o.w){
					if(dim.c.w < dim.d.w){
						to.x = Math.round(to.x * weight);
					}else{
						to.x = -dim.o.w - Math.round((-dim.o.w - to.x) * weight);
					}
				}
			}
			this.scrollTo(to);

			var max = 10;
			var n = this._time.length; // # of samples
			if(n >= 2){
				this._moved = true;
				// Check the direction of the finger move.
				// If the direction has been changed, discard the old data.
				var d0, d1;
				if(this._v && !this._h){
					d0 = this._posY[n - 1] - this._posY[n - 2];
					d1 = y - this._posY[n - 1];
				}else if(!this._v && this._h){
					d0 = this._posX[n - 1] - this._posX[n - 2];
					d1 = x - this._posX[n - 1];
				}
				if(d0 * d1 < 0){ // direction changed
					// leave only the latest data
					this._time = [this._time[n - 1]];
					this._posX = [this._posX[n - 1]];
					this._posY = [this._posY[n - 1]];
					n = 1;
				}
			}
			if(n == max){
				this._time.shift();
				this._posX.shift();
				this._posY.shift();
			}
			this._time.push((new Date()).getTime() - this.startTime);
			this._posX.push(x);
			this._posY.push(y);
		},

		_keepInputCaretInActiveElement: function(){
			var activeElement = win.doc.activeElement;
			var initialValue;
			if(activeElement && (activeElement.tagName == "INPUT" || activeElement.tagName == "TEXTAREA")){
				initialValue = activeElement.value;
				if(activeElement.type == "number" || activeElement.type == "week"){
					if(initialValue){
						activeElement.value = activeElement.value + 1;
					}else{
						activeElement.value = (activeElement.type == "week") ? "2013-W10" : 1;
					}
					activeElement.value = initialValue;
				}else{
					activeElement.value = activeElement.value + " ";
					activeElement.value = initialValue;
				}
			}
		},

		onTouchEnd: function(/*Event*/e){
			// summary:
			//		User-defined function to handle touchEnd events.
			if(this._locked){ return; }
			var speed = this._speed = {x:0, y:0};
			var dim = this._dim;
			var pos = this.getPos();
			var to = {}; // destination
			if(e){
				if(!this._conn){ return; } // if we get onTouchEnd without onTouchStart, ignore it.
				for(var i = 0; i < this._conn.length; i++){
					connect.disconnect(this._conn[i]);
				}
				this._conn = null;

				var clicked = false;
				if(!this._aborted && !this._moved){
					clicked = true;
				}
				if(clicked){ // clicked, not dragged or flicked
					this.hideScrollBar();
					this.removeCover();
					// need to send a synthetic click?
					if(has("touch") && has("clicks-prevented") && !this.isFormElement(e.target)){
						var elem = e.target;
						if(elem.nodeType != 1){
							elem = elem.parentNode;
						}
						setTimeout(function(){
							dm._sendClick(elem, e);
						});
					}
					return;
				}
				speed = this._speed = this.getSpeed();
			}else{
				if(pos.x == 0 && pos.y == 0){ return; } // initializing
				dim = this.getDim();
			}

			if(this._v){
				to.y = pos.y + speed.y;
			}
			if(this._h || this._f){
				to.x = pos.x + speed.x;
			}

			if(this.adjustDestination(to, pos, dim) === false){ return; }
			if(this.constraint){
				if(this.scrollDir == "v" && dim.c.h < dim.d.h){ // content is shorter than display
					this.slideTo({y:0}, 0.3, "ease-out"); // go back to the top
					return;
				}else if(this.scrollDir == "h" && dim.c.w < dim.d.w){ // content is narrower than display
					this.slideTo({x:0}, 0.3, "ease-out"); // go back to the left
					return;
				}else if(this._v && this._h && dim.c.h < dim.d.h && dim.c.w < dim.d.w){
					this.slideTo({x:0, y:0}, 0.3, "ease-out"); // go back to the top-left
					return;
				}
			}

			var duration, easing = "ease-out";
			var bounce = {};
			if(this._v && this.constraint){
				if(to.y > 0){ // going down. bounce back to the top.
					if(pos.y > 0){ // started from below the screen area. return quickly.
						duration = 0.3;
						to.y = 0;
					}else{
						to.y = Math.min(to.y, 20);
						easing = "linear";
						bounce.y = 0;
					}
				}else if(-speed.y > dim.o.h - (-pos.y)){ // going up. bounce back to the bottom.
					if(pos.y < -dim.o.h){ // started from above the screen top. return quickly.
						duration = 0.3;
						to.y = dim.c.h <= dim.d.h ? 0 : -dim.o.h; // if shorter, move to 0
					}else{
						to.y = Math.max(to.y, -dim.o.h - 20);
						easing = "linear";
						bounce.y = -dim.o.h;
					}
				}
			}
			if((this._h || this._f) && this.constraint){
				if(to.x > 0){ // going right. bounce back to the left.
					if(pos.x > 0){ // started from right of the screen area. return quickly.
						duration = 0.3;
						to.x = 0;
					}else{
						to.x = Math.min(to.x, 20);
						easing = "linear";
						bounce.x = 0;
					}
				}else if(-speed.x > dim.o.w - (-pos.x)){ // going left. bounce back to the right.
					if(pos.x < -dim.o.w){ // started from left of the screen top. return quickly.
						duration = 0.3;
						to.x = dim.c.w <= dim.d.w ? 0 : -dim.o.w; // if narrower, move to 0
					}else{
						to.x = Math.max(to.x, -dim.o.w - 20);
						easing = "linear";
						bounce.x = -dim.o.w;
					}
				}
			}
			this._bounce = (bounce.x !== undefined || bounce.y !== undefined) ? bounce : undefined;

			if(duration === undefined){
				var distance, velocity;
				if(this._v && this._h){
					velocity = Math.sqrt(speed.x*speed.x + speed.y*speed.y);
					distance = Math.sqrt(Math.pow(to.y - pos.y, 2) + Math.pow(to.x - pos.x, 2));
				}else if(this._v){
					velocity = speed.y;
					distance = to.y - pos.y;
				}else if(this._h){
					velocity = speed.x;
					distance = to.x - pos.x;
				}
				if(distance === 0 && !e){ return; } // #13154
				duration = velocity !== 0 ? Math.abs(distance / velocity) : 0.01; // time = distance / velocity
			}
			this.slideTo(to, duration, easing);
		},

		adjustDestination: function(/*Object*/to, /*Object*/pos, /*Object*/dim){
			// summary:
			//		A stub function to be overridden by subclasses.
			// description:
			//		This function is called from onTouchEnd(). The purpose is to give its
			//		subclasses a chance to adjust the destination position. If this
			//		function returns false, onTouchEnd() returns immediately without
			//		performing scroll.
			// to:
			//		The destination position. An object with x and y.
			// pos:
			//		The current position. An object with x and y.
			// dim:
			//		Dimension information returned by getDim().			

			// subclass may want to implement
			return true; // Boolean
		},

		abort: function(){
			// summary:
			//		Aborts scrolling.
			// description:
			//		This function stops the scrolling animation that is currently
			//		running. It is called when the user touches the screen while
			//		scrolling.
			this._aborted = true;
			this.scrollTo(this.getPos());
			this.stopAnimation();
		},

		stopAnimation: function(){
			// summary:
			//		Stops the currently running animation.
			domClass.remove(this.containerNode, "mblScrollableScrollTo2");
			if(this._scrollBarV){
				this._scrollBarV.className = "";
			}
			if(this._scrollBarH){
				this._scrollBarH.className = "";
			}
			if(this._useTransformTransition || this._useTopLeft){
				this.containerNode.style[css3.name("transition")] = "";
				if(this._scrollBarV) { this._scrollBarV.style[css3.name("transition")] = ""; }
				if(this._scrollBarH) { this._scrollBarH.style[css3.name("transition")] = ""; }
			}
		},

		scrollIntoView: function(/*DOMNode*/node, /*Boolean?*/alignWithTop, /*Number?*/duration){
			// summary:
			//		Scrolls the pane until the searching node is in the view.
			// node:
			//		A DOM node to be searched for view.
			// alignWithTop:
			//		If true, aligns the node at the top of the pane.
			//		If false, aligns the node at the bottom of the pane.
			// duration:
			//		Duration of scrolling in seconds. (ex. 0.3)
			//		If not specified, scrolls without animation.
			// description:
			//		Just like the scrollIntoView method of DOM elements, this
			//		function causes the given node to scroll into view, aligning it
			//		either at the top or bottom of the pane.

			if(!this._v){ return; } // cannot scroll vertically

			var c = this.containerNode,
				h = this.getDim().d.h, // the height of ScrollableView's content display area
				top = 0;

			// Get the top position of node relative to containerNode
			for(var n = node; n !== c; n = n.offsetParent){
				if(!n || n.tagName === "BODY"){ return; } // exit if node is not a child of scrollableView
				top += n.offsetTop;
			}
			// Calculate scroll destination position
			var y = alignWithTop ? Math.max(h - c.offsetHeight, -top) : Math.min(0, h - top - node.offsetHeight);

			// Scroll to destination position
			(duration && typeof duration === "number") ? 
				this.slideTo({y: y}, duration, "ease-out") : this.scrollTo({y: y});
		},

		getSpeed: function(){
			// summary:
			//		Returns an object that indicates the scrolling speed.
			// description:
			//		From the position and elapsed time information, calculates the
			//		scrolling speed, and returns an object with x and y.
			var x = 0, y = 0, n = this._time.length;
			// if the user holds the mouse or finger more than 0.5 sec, do not move.
			if(n >= 2 && (new Date()).getTime() - this.startTime - this._time[n - 1] < 500){
				var dy = this._posY[n - (n > 3 ? 2 : 1)] - this._posY[(n - 6) >= 0 ? n - 6 : 0];
				var dx = this._posX[n - (n > 3 ? 2 : 1)] - this._posX[(n - 6) >= 0 ? n - 6 : 0];
				var dt = this._time[n - (n > 3 ? 2 : 1)] - this._time[(n - 6) >= 0 ? n - 6 : 0];
				y = this.calcSpeed(dy, dt);
				x = this.calcSpeed(dx, dt);
			}
			return {x:x, y:y};
		},

		calcSpeed: function(/*Number*/distance, /*Number*/time){
			// summary:
			//		Calculate the speed given the distance and time.
			return Math.round(distance / time * 100) * 4;
		},

		scrollTo: function(/*Object*/to, /*Boolean?*/doNotMoveScrollBar, /*DomNode?*/node){
			// summary:
			//		Scrolls to the given position immediately without animation.
			// to:
			//		The destination position. An object with x and y.
			//		ex. {x:0, y:-5}
			// doNotMoveScrollBar:
			//		If true, the scroll bar will not be updated. If not specified,
			//		it will be updated.
			// node:
			//		A DOM node to scroll. If not specified, defaults to
			//		this.containerNode.

			// scroll events
			var scrollEvent, beforeTopHeight, afterBottomHeight;
			var doScroll = true;
			if(!this._aborted && this._conn){ // No scroll event if the call to scrollTo comes from abort or onTouchEnd
				if(!this._dim){
					this._dim = this.getDim();
				}
				beforeTopHeight = (to.y > 0)?to.y:0;
				afterBottomHeight = (this._dim.o.h + to.y < 0)?-1 * (this._dim.o.h + to.y):0;
				scrollEvent = {bubbles: false,
						cancelable: false,
						x: to.x,
						y: to.y,
						beforeTop: beforeTopHeight > 0,
						beforeTopHeight: beforeTopHeight,
						afterBottom: afterBottomHeight > 0,
						afterBottomHeight: afterBottomHeight};
				// before scroll event
				doScroll = this.onBeforeScroll(scrollEvent);
			}
			
			if(doScroll){
				var s = (node || this.containerNode).style;
				if(has("css3-animations")){
					if(!this._useTopLeft){
						if(this._useTransformTransition){
							s[css3.name("transition")] = "";	
						}
						s[css3.name("transform")] = this.makeTranslateStr(to);
					}else{
						s[css3.name("transition")] = "";
						if(this._v){
							s.top = to.y + "px";
						}
						if(this._h || this._f){
							s.left = to.x + "px";
						}
					}
				}else{
					if(this._v){
						s.top = to.y + "px";
					}
					if(this._h || this._f){
						s.left = to.x + "px";
					}
				}
				if(has("ios")){
					this._keepInputCaretInActiveElement();
				}
				if(!doNotMoveScrollBar){
					this.scrollScrollBarTo(this.calcScrollBarPos(to));
				}
				if(scrollEvent){
					// After scroll event
					this.onAfterScroll(scrollEvent);
				}
			}
		},

		onBeforeScroll: function(/*Event*/e){
			// e: Event
			//		the scroll event, that contains the following attributes:
			//		x (x coordinate of the scroll destination),
			//		y (y coordinate of the scroll destination),
			//		beforeTop (a boolean that is true if the scroll detination is before the top of the scrollable),
			//		beforeTopHeight (the number of pixels before the top of the scrollable for the scroll destination),
			//		afterBottom (a boolean that is true if the scroll destination is after the bottom of the scrollable),
			//		afterBottomHeight (the number of pixels after the bottom of the scrollable for the scroll destination)
			// summary:
			//		called before a scroll is initiated. If this method returns false,
			//		the scroll is canceled.
			// tags:
			//		callback
			return true;
		},

		onAfterScroll: function(/*Event*/e){
			// e: Event
			//		the scroll event, that contains the following attributes:
			//		x (x coordinate of the scroll destination),
			//		y (y coordinate of the scroll destination),
			//		beforeTop (a boolean that is true if the scroll detination is before the top of the scrollable),
			//		beforeTopHeight (the number of pixels before the top of the scrollable for the scroll destination),
			//		afterBottom (a boolean that is true if the scroll destination is after the bottom of the scrollable),
			//		afterBottomHeight (the number of pixels after the bottom of the scrollable for the scroll destination)
			// summary:
			//		called after a scroll has been performed.
			// tags:
			//		callback
		},
		
		slideTo: function(/*Object*/to, /*Number*/duration, /*String*/easing){
			// summary:
			//		Scrolls to the given position with the slide animation.
			// to:
			//		The scroll destination position. An object with x and/or y.
			//		ex. {x:0, y:-5}, {y:-29}, etc.
			// duration:
			//		Duration of scrolling in seconds. (ex. 0.3)
			// easing:
			//		The name of easing effect which webkit supports.
			//		"ease", "linear", "ease-in", "ease-out", etc.

			this._runSlideAnimation(this.getPos(), to, duration, easing, this.containerNode, 2);
			this.slideScrollBarTo(to, duration, easing);
		},

		makeTranslateStr: function(/*Object*/to){
			// summary:
			//		Constructs a string value that is passed to the -webkit-transform property.
			// to:
			//		The destination position. An object with x and/or y.
			// description:
			//		Return value example: "translate3d(0px,-8px,0px)"

			var y = this._v && typeof to.y == "number" ? to.y+"px" : "0px";
			var x = (this._h||this._f) && typeof to.x == "number" ? to.x+"px" : "0px";
			return has("translate3d") ?
					"translate3d("+x+","+y+",0px)" : "translate("+x+","+y+")";
		},

		getPos: function(){
			// summary:
			//		Gets the top position in the midst of animation.
			if(has("css3-animations")){
				var s = win.doc.defaultView.getComputedStyle(this.containerNode, '');
				if(!this._useTopLeft){
					var m = s[css3.name("transform")];
					if(m && m.indexOf("matrix") === 0){
						var arr = m.split(/[,\s\)]+/);
						// IE10 returns a matrix3d
						var i = m.indexOf("matrix3d") === 0 ? 12 : 4;
						return {y:arr[i+1] - 0, x:arr[i] - 0};
					}
					return {x:0, y:0};
				}else{
					return {x:parseInt(s.left) || 0, y:parseInt(s.top) || 0};
				}
			}else{
				// this.containerNode.offsetTop does not work here,
				// because it adds the height of the top margin.
				var y = parseInt(this.containerNode.style.top) || 0;
				return {y:y, x:this.containerNode.offsetLeft};
			}
		},

		getDim: function(){
			// summary:
			//		Returns various internal dimensional information needed for calculation.

			var d = {};
			// content width/height
			d.c = {h:this.containerNode.offsetHeight, w:this.containerNode.offsetWidth};

			// view width/height
			d.v = {h:this.domNode.offsetHeight + this._appFooterHeight, w:this.domNode.offsetWidth};

			// display width/height
			d.d = {h:d.v.h - this.fixedHeaderHeight - this.fixedFooterHeight - this._appFooterHeight, w:d.v.w};

			// overflowed width/height
			d.o = {h:d.c.h - d.v.h + this.fixedHeaderHeight + this.fixedFooterHeight + this._appFooterHeight, w:d.c.w - d.v.w};
			return d;
		},

		showScrollBar: function(){
			// summary:
			//		Shows the scroll bar.
			// description:
			//		This function creates the scroll bar instance if it does not
			//		exist yet, and calls resetScrollBar() to reset its length and
			//		position.

			if(!this.scrollBar){ return; }

			var dim = this._dim;
			if(this.scrollDir == "v" && dim.c.h <= dim.d.h){ return; }
			if(this.scrollDir == "h" && dim.c.w <= dim.d.w){ return; }
			if(this._v && this._h && dim.c.h <= dim.d.h && dim.c.w <= dim.d.w){ return; }

			var createBar = function(self, dir){
				var bar = self["_scrollBarNode" + dir];
				if(!bar){
					var wrapper = domConstruct.create("div", null, self.domNode);
					var props = { position: "absolute", overflow: "hidden" };
					if(dir == "V"){
						props.right = "2px";
						props.width = "5px";
					}else{
						props.bottom = (self.isLocalFooter ? self.fixedFooterHeight : 0) + 2 + "px";
						props.height = "5px";
					}
					domStyle.set(wrapper, props);
					wrapper.className = "mblScrollBarWrapper";
					self["_scrollBarWrapper"+dir] = wrapper;

					bar = domConstruct.create("div", null, wrapper);
					domStyle.set(bar, css3.add({
						opacity: 0.6,
						position: "absolute",
						backgroundColor: "#606060",
						fontSize: "1px",
						MozBorderRadius: "2px",
						zIndex: 2147483647 // max of signed 32-bit integer
					}, {
						borderRadius: "2px",
						transformOrigin: "0 0"
					}));
					domStyle.set(bar, dir == "V" ? {width: "5px"} : {height: "5px"});
					self["_scrollBarNode" + dir] = bar;
				}
				return bar;
			};
			if(this._v && !this._scrollBarV){
				this._scrollBarV = createBar(this, "V");
			}
			if(this._h && !this._scrollBarH){
				this._scrollBarH = createBar(this, "H");
			}
			this.resetScrollBar();
		},

		hideScrollBar: function(){
			// summary:
			//		Hides the scroll bar.
			// description:
			//		If the fadeScrollBar property is true, hides the scroll bar with
			//		the fade animation.

			if(this.fadeScrollBar && has("css3-animations")){
				if(!dm._fadeRule){
					var node = domConstruct.create("style", null, win.doc.getElementsByTagName("head")[0]);
					node.textContent =
						".mblScrollableFadeScrollBar{"+
						"  " + css3.name("animation-duration", true) + ": 1s;"+
						"  " + css3.name("animation-name", true) + ": scrollableViewFadeScrollBar;}"+
						"@" + css3.name("keyframes", true) + " scrollableViewFadeScrollBar{"+
						"  from { opacity: 0.6; }"+
						"  to { opacity: 0; }}";
					dm._fadeRule = node.sheet.cssRules[1];
				}
			}
			if(!this.scrollBar){ return; }
			var f = function(bar, self){
				domStyle.set(bar, css3.add({
					opacity: 0
				}, {
					animationDuration: ""
				}));
				// do not use fade animation in case of using top/left on Android
				// since it causes screen flicker during adress bar's fading out
				if(!(self._useTopLeft && has('android'))){
					bar.className = "mblScrollableFadeScrollBar";
				}
			};
			if(this._scrollBarV){
				f(this._scrollBarV, this);
				this._scrollBarV = null;
			}
			if(this._scrollBarH){
				f(this._scrollBarH, this);
				this._scrollBarH = null;
			}
		},

		calcScrollBarPos: function(/*Object*/to){
			// summary:
			//		Calculates the scroll bar position.
			// description:
			//		Given the scroll destination position, calculates the top and/or
			//		the left of the scroll bar(s). Returns an object with x and y.
			// to:
			//		The scroll destination position. An object with x and y.
			//		ex. {x:0, y:-5}			

			var pos = {};
			var dim = this._dim;
			var f = function(wrapperH, barH, t, d, c){
				var y = Math.round((d - barH - 8) / (d - c) * t);
				if(y < -barH + 5){
					y = -barH + 5;
				}
				if(y > wrapperH - 5){
					y = wrapperH - 5;
				}
				return y;
			};
			if(typeof to.y == "number" && this._scrollBarV){
				pos.y = f(this._scrollBarWrapperV.offsetHeight, this._scrollBarV.offsetHeight, to.y, dim.d.h, dim.c.h);
			}
			if(typeof to.x == "number" && this._scrollBarH){
				pos.x = f(this._scrollBarWrapperH.offsetWidth, this._scrollBarH.offsetWidth, to.x, dim.d.w, dim.c.w);
			}
			return pos;
		},

		scrollScrollBarTo: function(/*Object*/to){
			// summary:
			//		Moves the scroll bar(s) to the given position without animation.
			// to:
			//		The destination position. An object with x and/or y.
			//		ex. {x:2, y:5}, {y:20}, etc.

			if(!this.scrollBar){ return; }
			if(this._v && this._scrollBarV && typeof to.y == "number"){
				if(has("css3-animations")){
					if(!this._useTopLeft){
						if(this._useTransformTransition){
							this._scrollBarV.style[css3.name("transition")] = "";
						}
						this._scrollBarV.style[css3.name("transform")] = this.makeTranslateStr({y:to.y});
					}else{
						domStyle.set(this._scrollBarV, css3.add({
							top: to.y + "px"
						}, {
							transition: ""
						}));
					}
				}else{
					this._scrollBarV.style.top = to.y + "px";
				}
			}
			if(this._h && this._scrollBarH && typeof to.x == "number"){
				if(has("css3-animations")){
					if(!this._useTopLeft){
						if(this._useTransformTransition){
							this._scrollBarH.style[css3.name("transition")] = "";
						}
						this._scrollBarH.style[css3.name("transform")] = this.makeTranslateStr({x:to.x});
					}else{
						domStyle.set(this._scrollBarH, css3.add({
							left: to.x + "px"
						}, {
							transition: ""
						}));
					}
				}else{
					this._scrollBarH.style.left = to.x + "px";
				}
			}
		},

		slideScrollBarTo: function(/*Object*/to, /*Number*/duration, /*String*/easing){
			// summary:
			//		Moves the scroll bar(s) to the given position with the slide animation.
			// to:
			//		The destination position. An object with x and y.
			//		ex. {x:0, y:-5}
			// duration:
			//		Duration of the animation in seconds. (ex. 0.3)
			// easing:
			//		The name of easing effect which webkit supports.
			//		"ease", "linear", "ease-in", "ease-out", etc.

			if(!this.scrollBar){ return; }
			var fromPos = this.calcScrollBarPos(this.getPos());
			var toPos = this.calcScrollBarPos(to);
			if(this._v && this._scrollBarV){
				this._runSlideAnimation({y:fromPos.y}, {y:toPos.y}, duration, easing, this._scrollBarV, 0);
			}
			if(this._h && this._scrollBarH){
				this._runSlideAnimation({x:fromPos.x}, {x:toPos.x}, duration, easing, this._scrollBarH, 1);
			}
		},

		_runSlideAnimation: function(/*Object*/from, /*Object*/to, /*Number*/duration, /*String*/easing, /*DomNode*/node, /*Number*/idx){
			// tags:
			//		private
			
			// idx: 0:scrollbarV, 1:scrollbarH, 2:content
			if(has("css3-animations")){
				if(!this._useTopLeft){
					if(this._useTransformTransition){
						// for iOS6 (maybe others?): use -webkit-transform + -webkit-transition
						if(to.x === undefined){ to.x = from.x; }
						if(to.y === undefined){ to.y = from.y; }
						 // make sure we actually change the transform, otherwise no webkitTransitionEnd is fired.
						if(to.x !== from.x || to.y !== from.y){
							domStyle.set(node, css3.add({}, {
								transitionProperty: css3.name("transform"),
								transitionDuration: duration + "s",
								transitionTimingFunction: easing
							}));
							var t = this.makeTranslateStr(to);
							setTimeout(function(){ // setTimeout is needed to prevent webkitTransitionEnd not fired
								domStyle.set(node, css3.add({}, {
									transform: t
								}));
							}, 0);
							domClass.add(node, "mblScrollableScrollTo"+idx);
						} else {
							// transform not changed, just hide the scrollbar
							this.hideScrollBar();
							this.removeCover();
						}
					}else{
						// use -webkit-transform + -webkit-animation
						this.setKeyframes(from, to, idx);
						domStyle.set(node, css3.add({}, {
							animationDuration: duration + "s",
							animationTimingFunction: easing
						}));
						domClass.add(node, "mblScrollableScrollTo"+idx);
						if(idx == 2){
							this.scrollTo(to, true, node);
						}else{
							this.scrollScrollBarTo(to);
						}
					}
				}else{
					domStyle.set(node, css3.add({}, {
						transitionProperty: "top, left",
						transitionDuration: duration + "s",
						transitionTimingFunction: easing
					}));
					setTimeout(function(){ // setTimeout is needed to prevent webkitTransitionEnd not fired
						domStyle.set(node, {
							top: (to.y || 0) + "px",
							left: (to.x || 0) + "px"
						});
					}, 0);
					domClass.add(node, "mblScrollableScrollTo"+idx);
				}
			}else if(dojo.fx && dojo.fx.easing && duration){
				// If you want to support non-webkit browsers,
				// your application needs to load necessary modules as follows:
				//
				// | dojo.require("dojo.fx");
				// | dojo.require("dojo.fx.easing");
				//
				// This module itself does not make dependency on them.
				// TODO: for 2.0 the dojo global is going away. Use require("dojo/fx") and require("dojo/fx/easing") instead.
				var s = dojo.fx.slideTo({
					node: node,
					duration: duration*1000,
					left: to.x,
					top: to.y,
					easing: (easing == "ease-out") ? dojo.fx.easing.quadOut : dojo.fx.easing.linear
				}).play();
				if(idx == 2){
					connect.connect(s, "onEnd", this, "onFlickAnimationEnd");
				}
			}else{
				// directly jump to the destination without animation
				if(idx == 2){
					this.scrollTo(to, false, node);
					this.onFlickAnimationEnd();
				}else{
					this.scrollScrollBarTo(to);
				}
			}
		},

		resetScrollBar: function(){
			// summary:
			//		Resets the scroll bar length, position, etc.
			var f = function(wrapper, bar, d, c, hd, v){
				if(!bar){ return; }
				var props = {};
				props[v ? "top" : "left"] = hd + 4 + "px"; // +4 is for top or left margin
				var t = (d - 8) <= 0 ? 1 : d - 8;
				props[v ? "height" : "width"] = t + "px";
				domStyle.set(wrapper, props);
				var l = Math.round(d * d / c); // scroll bar length
				l = Math.min(Math.max(l - 8, 5), t); // -8 is for margin for both ends
				bar.style[v ? "height" : "width"] = l + "px";
				domStyle.set(bar, {"opacity": 0.6});
			};
			var dim = this.getDim();
			f(this._scrollBarWrapperV, this._scrollBarV, dim.d.h, dim.c.h, this.fixedHeaderHeight, true);
			f(this._scrollBarWrapperH, this._scrollBarH, dim.d.w, dim.c.w, 0);
			this.createMask();
		},

		createMask: function(){
			// summary:
			//		Creates a mask for a scroll bar edge.
			// description:
			//		This function creates a mask that hides corners of one scroll
			//		bar edge to make it round edge. The other side of the edge is
			//		always visible and round shaped with the border-radius style.
			if(!(has("webkit")||has("svg"))){ return; }
			//var ctx;
			if(this._scrollBarWrapperV){
				var h = this._scrollBarWrapperV.offsetHeight;
				maskUtils.createRoundMask(this._scrollBarWrapperV, 0, 0, 0, 0, 5, h, 2, 2, 0.5);
			}
			if(this._scrollBarWrapperH){
				var w = this._scrollBarWrapperH.offsetWidth;
				maskUtils.createRoundMask(this._scrollBarWrapperH, 0, 0, 0, 0, w, 5, 2, 2, 0.5);
			}
		},

		flashScrollBar: function(){
			// summary:
			//		Shows the scroll bar instantly.
			// description:
			//		This function shows the scroll bar, and then hides it 300ms
			//		later. This is used to show the scroll bar to the user for a
			//		short period of time when a hidden view is revealed.
			if(this.disableFlashScrollBar || !this.domNode){ return; }
			this._dim = this.getDim();
			if(this._dim.d.h <= 0){ return; } // dom is not ready
			this.showScrollBar();
			var _this = this;
			setTimeout(function(){
				_this.hideScrollBar();
			}, 300);
		},

		addCover: function(){
			// summary:
			//		Adds the transparent DIV cover.
			// description:
			//		The cover is to prevent DOM events from affecting the child
			//		widgets such as a list widget. Without the cover, for example,
			//		child widgets may receive a click event and respond to it
			//		unexpectedly when the user flicks the screen to scroll.
			//		Note that only the desktop browsers need the cover.

			if(!has('touch') && !this.noCover){
				if(!dm._cover){
					dm._cover = domConstruct.create("div", null, win.doc.body);
					dm._cover.className = "mblScrollableCover";
					domStyle.set(dm._cover, {
						backgroundColor: "#ffff00",
						opacity: 0,
						position: "absolute",
						top: "0px",
						left: "0px",
						width: "100%",
						height: "100%",
						zIndex: 2147483647 // max of signed 32-bit integer
					});
					this._ch.push(connect.connect(dm._cover, touch.press, this, "onTouchEnd"));
				}else{
					dm._cover.style.display = "";
				}
				this.setSelectable(dm._cover, false);
				this.setSelectable(this.domNode, false);
			}
		},

		removeCover: function(){
			// summary:
			//		Removes the transparent DIV cover.

			if(!has('touch') && dm._cover){
				dm._cover.style.display = "none";
				this.setSelectable(dm._cover, true);
				this.setSelectable(this.domNode, true);
			}
		},

		setKeyframes: function(/*Object*/from, /*Object*/to, /*Number*/idx){
			// summary:
			//		Programmatically sets key frames for the scroll animation.

			if(!dm._rule){
				dm._rule = [];
			}
			// idx: 0:scrollbarV, 1:scrollbarH, 2:content
			if(!dm._rule[idx]){
				var node = domConstruct.create("style", null, win.doc.getElementsByTagName("head")[0]);
				node.textContent =
					".mblScrollableScrollTo"+idx+"{" + css3.name("animation-name", true) + ": scrollableViewScroll"+idx+";}"+
					"@" + css3.name("keyframes", true) + " scrollableViewScroll"+idx+"{}";
				dm._rule[idx] = node.sheet.cssRules[1];
			}
			var rule = dm._rule[idx];
			if(rule){
				if(from){
					rule.deleteRule(has("webkit")?"from":0);
					(rule.insertRule||rule.appendRule).call(rule, "from { " + css3.name("transform", true) + ": "+this.makeTranslateStr(from)+"; }");
				}
				if(to){
					if(to.x === undefined){ to.x = from.x; }
					if(to.y === undefined){ to.y = from.y; }
					rule.deleteRule(has("webkit")?"to":1);
					(rule.insertRule||rule.appendRule).call(rule, "to { " + css3.name("transform", true) + ": "+this.makeTranslateStr(to)+"; }");
				}
			}
		},

		setSelectable: function(/*DomNode*/node, /*Boolean*/selectable){
			// summary:
			//		Sets the given node as selectable or unselectable.
			 
			// dojo.setSelectable has dependency on dojo.query. Redefine our own.
			node.style.KhtmlUserSelect = selectable ? "auto" : "none";
			node.style.MozUserSelect = selectable ? "" : "none";
			node.onselectstart = selectable ? null : function(){return false;};
			if(has("ie")){
				node.unselectable = selectable ? "" : "on";
				var nodes = node.getElementsByTagName("*");
				for(var i = 0; i < nodes.length; i++){
					nodes[i].unselectable = selectable ? "" : "on";
				}
			}
		}
	});

	lang.setObject("dojox.mobile.scrollable", Scrollable);

	return Scrollable;
});

},
'dojo/data/ItemFileReadStore':function(){
define(["../_base/kernel", "../_base/lang", "../_base/declare", "../_base/array", "../_base/xhr",
	"../Evented", "./util/filter", "./util/simpleFetch", "../date/stamp"
], function(kernel, lang, declare, array, xhr, Evented, filterUtil, simpleFetch, dateStamp){

// module:
//		dojo/data/ItemFileReadStore

var ItemFileReadStore = declare("dojo.data.ItemFileReadStore", [Evented],{
	// summary:
	//		The ItemFileReadStore implements the dojo/data/api/Read API and reads
	//		data from JSON files that have contents in this format --
	// |	{ items: [
	// |		{ name:'Kermit', color:'green', age:12, friends:['Gonzo', {_reference:{name:'Fozzie Bear'}}]},
	// |		{ name:'Fozzie Bear', wears:['hat', 'tie']},
	// |		{ name:'Miss Piggy', pets:'Foo-Foo'}
	// |	]}
	//		Note that it can also contain an 'identifier' property that specified which attribute on the items
	//		in the array of items that acts as the unique identifier for that item.

	constructor: function(/* Object */ keywordParameters){
		// summary:
		//		constructor
		// keywordParameters:
		//		{url: String} {data: jsonObject} {typeMap: object}
		//		The structure of the typeMap object is as follows:
		// |	{
		// |		type0: function || object,
		// |		type1: function || object,
		// |		...
		// |		typeN: function || object
		// |	}
		//		Where if it is a function, it is assumed to be an object constructor that takes the
		//		value of _value as the initialization parameters.  If it is an object, then it is assumed
		//		to be an object of general form:
		// |	{
		// |		type: function, //constructor.
		// |		deserialize:	function(value) //The function that parses the value and constructs the object defined by type appropriately.
		// |	}

		this._arrayOfAllItems = [];
		this._arrayOfTopLevelItems = [];
		this._loadFinished = false;
		this._jsonFileUrl = keywordParameters.url;
		this._ccUrl = keywordParameters.url;
		this.url = keywordParameters.url;
		this._jsonData = keywordParameters.data;
		this.data = null;
		this._datatypeMap = keywordParameters.typeMap || {};
		if(!this._datatypeMap['Date']){
			//If no default mapping for dates, then set this as default.
			//We use the dojo/date/stamp here because the ISO format is the 'dojo way'
			//of generically representing dates.
			this._datatypeMap['Date'] = {
				type: Date,
				deserialize: function(value){
					return dateStamp.fromISOString(value);
				}
			};
		}
		this._features = {'dojo.data.api.Read':true, 'dojo.data.api.Identity':true};
		this._itemsByIdentity = null;
		this._storeRefPropName = "_S"; // Default name for the store reference to attach to every item.
		this._itemNumPropName = "_0"; // Default Item Id for isItem to attach to every item.
		this._rootItemPropName = "_RI"; // Default Item Id for isItem to attach to every item.
		this._reverseRefMap = "_RRM"; // Default attribute for constructing a reverse reference map for use with reference integrity
		this._loadInProgress = false; //Got to track the initial load to prevent duelling loads of the dataset.
		this._queuedFetches = [];
		if(keywordParameters.urlPreventCache !== undefined){
			this.urlPreventCache = keywordParameters.urlPreventCache?true:false;
		}
		if(keywordParameters.hierarchical !== undefined){
			this.hierarchical = keywordParameters.hierarchical?true:false;
		}
		if(keywordParameters.clearOnClose){
			this.clearOnClose = true;
		}
		if("failOk" in keywordParameters){
			this.failOk = keywordParameters.failOk?true:false;
		}
	},

	url: "",	// use "" rather than undefined for the benefit of the parser (#3539)

	//Internal var, crossCheckUrl.  Used so that setting either url or _jsonFileUrl, can still trigger a reload
	//when clearOnClose and close is used.
	_ccUrl: "",

	data: null,	// define this so that the parser can populate it

	typeMap: null, //Define so parser can populate.

	// clearOnClose: Boolean
	//		Parameter to allow users to specify if a close call should force a reload or not.
	//		By default, it retains the old behavior of not clearing if close is called.  But
	//		if set true, the store will be reset to default state.  Note that by doing this,
	//		all item handles will become invalid and a new fetch must be issued.
	clearOnClose: false,

	// urlPreventCache: Boolean
	//		Parameter to allow specifying if preventCache should be passed to the xhrGet call or not when loading data from a url.
	//		Note this does not mean the store calls the server on each fetch, only that the data load has preventCache set as an option.
	//		Added for tracker: #6072
	urlPreventCache: false,

	// failOk: Boolean
	//		Parameter for specifying that it is OK for the xhrGet call to fail silently.
	failOk: false,

	// hierarchical: Boolean
	//		Parameter to indicate to process data from the url as hierarchical
	//		(data items can contain other data items in js form).  Default is true
	//		for backwards compatibility.  False means only root items are processed
	//		as items, all child objects outside of type-mapped objects and those in
	//		specific reference format, are left straight JS data objects.
	hierarchical: true,

	_assertIsItem: function(/* dojo/data/api/Item */ item){
		// summary:
		//		This function tests whether the item passed in is indeed an item in the store.
		// item:
		//		The item to test for being contained by the store.
		if(!this.isItem(item)){
			throw new Error(this.declaredClass + ": Invalid item argument.");
		}
	},

	_assertIsAttribute: function(/* attribute-name-string */ attribute){
		// summary:
		//		This function tests whether the item passed in is indeed a valid 'attribute' like type for the store.
		// attribute:
		//		The attribute to test for being contained by the store.
		if(typeof attribute !== "string"){
			throw new Error(this.declaredClass + ": Invalid attribute argument.");
		}
	},

	getValue: function(	/* dojo/data/api/Item */ item,
						   /* attribute-name-string */ attribute,
						   /* value? */ defaultValue){
		// summary:
		//		See dojo/data/api/Read.getValue()
		var values = this.getValues(item, attribute);
		return (values.length > 0)?values[0]:defaultValue; // mixed
	},

	getValues: function(/* dojo/data/api/Item */ item,
						/* attribute-name-string */ attribute){
		// summary:
		//		See dojo/data/api/Read.getValues()

		this._assertIsItem(item);
		this._assertIsAttribute(attribute);
		// Clone it before returning.  refs: #10474
		return (item[attribute] || []).slice(0); // Array
	},

	getAttributes: function(/* dojo/data/api/Item */ item){
		// summary:
		//		See dojo/data/api/Read.getAttributes()
		this._assertIsItem(item);
		var attributes = [];
		for(var key in item){
			// Save off only the real item attributes, not the special id marks for O(1) isItem.
			if((key !== this._storeRefPropName) && (key !== this._itemNumPropName) && (key !== this._rootItemPropName) && (key !== this._reverseRefMap)){
				attributes.push(key);
			}
		}
		return attributes; // Array
	},

	hasAttribute: function(	/* dojo/data/api/Item */ item,
							   /* attribute-name-string */ attribute){
		// summary:
		//		See dojo/data/api/Read.hasAttribute()
		this._assertIsItem(item);
		this._assertIsAttribute(attribute);
		return (attribute in item);
	},

	containsValue: function(/* dojo/data/api/Item */ item,
							/* attribute-name-string */ attribute,
							/* anything */ value){
		// summary:
		//		See dojo/data/api/Read.containsValue()
		var regexp = undefined;
		if(typeof value === "string"){
			regexp = filterUtil.patternToRegExp(value, false);
		}
		return this._containsValue(item, attribute, value, regexp); //boolean.
	},

	_containsValue: function(	/* dojo/data/api/Item */ item,
								 /* attribute-name-string */ attribute,
								 /* anything */ value,
								 /* RegExp?*/ regexp){
		// summary:
		//		Internal function for looking at the values contained by the item.
		// description:
		//		Internal function for looking at the values contained by the item.  This
		//		function allows for denoting if the comparison should be case sensitive for
		//		strings or not (for handling filtering cases where string case should not matter)
		// item:
		//		The data item to examine for attribute values.
		// attribute:
		//		The attribute to inspect.
		// value:
		//		The value to match.
		// regexp:
		//		Optional regular expression generated off value if value was of string type to handle wildcarding.
		//		If present and attribute values are string, then it can be used for comparison instead of 'value'
		return array.some(this.getValues(item, attribute), function(possibleValue){
			if(possibleValue !== null && !lang.isObject(possibleValue) && regexp){
				if(possibleValue.toString().match(regexp)){
					return true; // Boolean
				}
			}else if(value === possibleValue){
				return true; // Boolean
			}
		});
	},

	isItem: function(/* anything */ something){
		// summary:
		//		See dojo/data/api/Read.isItem()
		if(something && something[this._storeRefPropName] === this){
			if(this._arrayOfAllItems[something[this._itemNumPropName]] === something){
				return true;
			}
		}
		return false; // Boolean
	},

	isItemLoaded: function(/* anything */ something){
		// summary:
		//		See dojo/data/api/Read.isItemLoaded()
		return this.isItem(something); //boolean
	},

	loadItem: function(/* object */ keywordArgs){
		// summary:
		//		See dojo/data/api/Read.loadItem()
		this._assertIsItem(keywordArgs.item);
	},

	getFeatures: function(){
		// summary:
		//		See dojo/data/api/Read.getFeatures()
		return this._features; //Object
	},

	getLabel: function(/* dojo/data/api/Item */ item){
		// summary:
		//		See dojo/data/api/Read.getLabel()
		if(this._labelAttr && this.isItem(item)){
			return this.getValue(item,this._labelAttr); //String
		}
		return undefined; //undefined
	},

	getLabelAttributes: function(/* dojo/data/api/Item */ item){
		// summary:
		//		See dojo/data/api/Read.getLabelAttributes()
		if(this._labelAttr){
			return [this._labelAttr]; //array
		}
		return null; //null
	},

	filter: function(/* Object */ requestArgs, /* item[] */ arrayOfItems, /* Function */ findCallback){
		// summary:
		//		This method handles the basic filtering needs for ItemFile* based stores.
		var items = [],
			i, key;

		if(requestArgs.query){
			var value,
				ignoreCase = requestArgs.queryOptions ? requestArgs.queryOptions.ignoreCase : false;

			//See if there are any string values that can be regexp parsed first to avoid multiple regexp gens on the
			//same value for each item examined.  Much more efficient.
			var regexpList = {};
			for(key in requestArgs.query){
				value = requestArgs.query[key];
				if(typeof value === "string"){
					regexpList[key] = filterUtil.patternToRegExp(value, ignoreCase);
				}else if(value instanceof RegExp){
					regexpList[key] = value;
				}
			}
			for(i = 0; i < arrayOfItems.length; ++i){
				var match = true;
				var candidateItem = arrayOfItems[i];
				if(candidateItem === null){
					match = false;
				}else{
					for(key in requestArgs.query){
						value = requestArgs.query[key];
						if(!this._containsValue(candidateItem, key, value, regexpList[key])){
							match = false;
						}
					}
				}
				if(match){
					items.push(candidateItem);
				}
			}
			findCallback(items, requestArgs);
		}else{
			// We want a copy to pass back in case the parent wishes to sort the array.
			// We shouldn't allow resort of the internal list, so that multiple callers
			// can get lists and sort without affecting each other.  We also need to
			// filter out any null values that have been left as a result of deleteItem()
			// calls in ItemFileWriteStore.
			for(i = 0; i < arrayOfItems.length; ++i){
				var item = arrayOfItems[i];
				if(item !== null){
					items.push(item);
				}
			}
			findCallback(items, requestArgs);
		}
	},

	_fetchItems: function(	/* Object */ keywordArgs,
							  /* Function */ findCallback,
							  /* Function */ errorCallback){
		// summary:
		//		See dojo/data/util.simpleFetch.fetch()
		var self = this;

		if(this._loadFinished){
			this.filter(keywordArgs, this._getItemsArray(keywordArgs.queryOptions), findCallback);
		}else{
			//Do a check on the JsonFileUrl and crosscheck it.
			//If it doesn't match the cross-check, it needs to be updated
			//This allows for either url or _jsonFileUrl to he changed to
			//reset the store load location.  Done this way for backwards
			//compatibility.  People use _jsonFileUrl (even though officially
			//private.
			if(this._jsonFileUrl !== this._ccUrl){
				kernel.deprecated(this.declaredClass + ": ",
					"To change the url, set the url property of the store," +
						" not _jsonFileUrl.  _jsonFileUrl support will be removed in 2.0");
				this._ccUrl = this._jsonFileUrl;
				this.url = this._jsonFileUrl;
			}else if(this.url !== this._ccUrl){
				this._jsonFileUrl = this.url;
				this._ccUrl = this.url;
			}

			//See if there was any forced reset of data.
			if(this.data != null){
				this._jsonData = this.data;
				this.data = null;
			}

			if(this._jsonFileUrl){
				//If fetches come in before the loading has finished, but while
				//a load is in progress, we have to defer the fetching to be
				//invoked in the callback.
				if(this._loadInProgress){
					this._queuedFetches.push({args: keywordArgs, filter: lang.hitch(self, "filter"), findCallback: lang.hitch(self, findCallback)});
				}else{
					this._loadInProgress = true;
					var getArgs = {
						url: self._jsonFileUrl,
						handleAs: "json-comment-optional",
						preventCache: this.urlPreventCache,
						failOk: this.failOk
					};
					var getHandler = xhr.get(getArgs);
					getHandler.addCallback(function(data){
						try{
							self._getItemsFromLoadedData(data);
							self._loadFinished = true;
							self._loadInProgress = false;

							self.filter(keywordArgs, self._getItemsArray(keywordArgs.queryOptions), findCallback);
							self._handleQueuedFetches();
						}catch(e){
							self._loadFinished = true;
							self._loadInProgress = false;
							errorCallback(e, keywordArgs);
						}
					});
					getHandler.addErrback(function(error){
						self._loadInProgress = false;
						errorCallback(error, keywordArgs);
					});

					//Wire up the cancel to abort of the request
					//This call cancel on the deferred if it hasn't been called
					//yet and then will chain to the simple abort of the
					//simpleFetch keywordArgs
					var oldAbort = null;
					if(keywordArgs.abort){
						oldAbort = keywordArgs.abort;
					}
					keywordArgs.abort = function(){
						var df = getHandler;
						if(df && df.fired === -1){
							df.cancel();
							df = null;
						}
						if(oldAbort){
							oldAbort.call(keywordArgs);
						}
					};
				}
			}else if(this._jsonData){
				try{
					this._loadFinished = true;
					this._getItemsFromLoadedData(this._jsonData);
					this._jsonData = null;
					self.filter(keywordArgs, this._getItemsArray(keywordArgs.queryOptions), findCallback);
				}catch(e){
					errorCallback(e, keywordArgs);
				}
			}else{
				errorCallback(new Error(this.declaredClass + ": No JSON source data was provided as either URL or a nested Javascript object."), keywordArgs);
			}
		}
	},

	_handleQueuedFetches: function(){
		// summary:
		//		Internal function to execute delayed request in the store.
		
		//Execute any deferred fetches now.
		if(this._queuedFetches.length > 0){
			for(var i = 0; i < this._queuedFetches.length; i++){
				var fData = this._queuedFetches[i],
					delayedQuery = fData.args,
					delayedFilter = fData.filter,
					delayedFindCallback = fData.findCallback;
				if(delayedFilter){
					delayedFilter(delayedQuery, this._getItemsArray(delayedQuery.queryOptions), delayedFindCallback);
				}else{
					this.fetchItemByIdentity(delayedQuery);
				}
			}
			this._queuedFetches = [];
		}
	},

	_getItemsArray: function(/*object?*/queryOptions){
		// summary:
		//		Internal function to determine which list of items to search over.
		// queryOptions: The query options parameter, if any.
		if(queryOptions && queryOptions.deep){
			return this._arrayOfAllItems;
		}
		return this._arrayOfTopLevelItems;
	},

	close: function(/*dojo/data/api/Request|Object?*/ request){
		// summary:
		//		See dojo/data/api/Read.close()
		if(this.clearOnClose &&
			this._loadFinished &&
			!this._loadInProgress){
			//Reset all internalsback to default state.  This will force a reload
			//on next fetch.  This also checks that the data or url param was set
			//so that the store knows it can get data.  Without one of those being set,
			//the next fetch will trigger an error.

			if(((this._jsonFileUrl == "" || this._jsonFileUrl == null) &&
				(this.url == "" || this.url == null)
				) && this.data == null){
				console.debug(this.declaredClass + ": WARNING!  Data reload " +
					" information has not been provided." +
					"  Please set 'url' or 'data' to the appropriate value before" +
					" the next fetch");
			}
			this._arrayOfAllItems = [];
			this._arrayOfTopLevelItems = [];
			this._loadFinished = false;
			this._itemsByIdentity = null;
			this._loadInProgress = false;
			this._queuedFetches = [];
		}
	},

	_getItemsFromLoadedData: function(/* Object */ dataObject){
		// summary:
		//		Function to parse the loaded data into item format and build the internal items array.
		// description:
		//		Function to parse the loaded data into item format and build the internal items array.
		// dataObject:
		//		The JS data object containing the raw data to convery into item format.
		// returns: Array
		//		Array of items in store item format.

		// First, we define a couple little utility functions...
		var addingArrays = false,
			self = this;

		function valueIsAnItem(/* anything */ aValue){
			// summary:
			//		Given any sort of value that could be in the raw json data,
			//		return true if we should interpret the value as being an
			//		item itself, rather than a literal value or a reference.
			// example:
			// 	|	false == valueIsAnItem("Kermit");
			// 	|	false == valueIsAnItem(42);
			// 	|	false == valueIsAnItem(new Date());
			// 	|	false == valueIsAnItem({_type:'Date', _value:'1802-05-14'});
			// 	|	false == valueIsAnItem({_reference:'Kermit'});
			// 	|	true == valueIsAnItem({name:'Kermit', color:'green'});
			// 	|	true == valueIsAnItem({iggy:'pop'});
			// 	|	true == valueIsAnItem({foo:42});
			return (aValue !== null) &&
				(typeof aValue === "object") &&
				(!lang.isArray(aValue) || addingArrays) &&
				(!lang.isFunction(aValue)) &&
				(aValue.constructor == Object || lang.isArray(aValue)) &&
				(typeof aValue._reference === "undefined") &&
				(typeof aValue._type === "undefined") &&
				(typeof aValue._value === "undefined") &&
				self.hierarchical;
		}

		function addItemAndSubItemsToArrayOfAllItems(/* dojo/data/api/Item */ anItem){
			self._arrayOfAllItems.push(anItem);
			for(var attribute in anItem){
				var valueForAttribute = anItem[attribute];
				if(valueForAttribute){
					if(lang.isArray(valueForAttribute)){
						var valueArray = valueForAttribute;
						for(var k = 0; k < valueArray.length; ++k){
							var singleValue = valueArray[k];
							if(valueIsAnItem(singleValue)){
								addItemAndSubItemsToArrayOfAllItems(singleValue);
							}
						}
					}else{
						if(valueIsAnItem(valueForAttribute)){
							addItemAndSubItemsToArrayOfAllItems(valueForAttribute);
						}
					}
				}
			}
		}

		this._labelAttr = dataObject.label;

		// We need to do some transformations to convert the data structure
		// that we read from the file into a format that will be convenient
		// to work with in memory.

		// Step 1: Walk through the object hierarchy and build a list of all items
		var i,
			item;
		this._arrayOfAllItems = [];
		this._arrayOfTopLevelItems = dataObject.items;

		for(i = 0; i < this._arrayOfTopLevelItems.length; ++i){
			item = this._arrayOfTopLevelItems[i];
			if(lang.isArray(item)){
				addingArrays = true;
			}
			addItemAndSubItemsToArrayOfAllItems(item);
			item[this._rootItemPropName]=true;
		}

		// Step 2: Walk through all the attribute values of all the items,
		// and replace single values with arrays.  For example, we change this:
		//		{ name:'Miss Piggy', pets:'Foo-Foo'}
		// into this:
		//		{ name:['Miss Piggy'], pets:['Foo-Foo']}
		//
		// We also store the attribute names so we can validate our store
		// reference and item id special properties for the O(1) isItem
		var allAttributeNames = {},
			key;

		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i];
			for(key in item){
				if(key !== this._rootItemPropName){
					var value = item[key];
					if(value !== null){
						if(!lang.isArray(value)){
							item[key] = [value];
						}
					}else{
						item[key] = [null];
					}
				}
				allAttributeNames[key]=key;
			}
		}

		// Step 3: Build unique property names to use for the _storeRefPropName and _itemNumPropName
		// This should go really fast, it will generally never even run the loop.
		while(allAttributeNames[this._storeRefPropName]){
			this._storeRefPropName += "_";
		}
		while(allAttributeNames[this._itemNumPropName]){
			this._itemNumPropName += "_";
		}
		while(allAttributeNames[this._reverseRefMap]){
			this._reverseRefMap += "_";
		}

		// Step 4: Some data files specify an optional 'identifier', which is
		// the name of an attribute that holds the identity of each item.
		// If this data file specified an identifier attribute, then build a
		// hash table of items keyed by the identity of the items.
		var arrayOfValues;

		var identifier = dataObject.identifier;
		if(identifier){
			this._itemsByIdentity = {};
			this._features['dojo.data.api.Identity'] = identifier;
			for(i = 0; i < this._arrayOfAllItems.length; ++i){
				item = this._arrayOfAllItems[i];
				arrayOfValues = item[identifier];
				var identity = arrayOfValues[0];
				if(!Object.hasOwnProperty.call(this._itemsByIdentity, identity)){
					this._itemsByIdentity[identity] = item;
				}else{
					if(this._jsonFileUrl){
						throw new Error(this.declaredClass + ":  The json data as specified by: [" + this._jsonFileUrl + "] is malformed.  Items within the list have identifier: [" + identifier + "].  Value collided: [" + identity + "]");
					}else if(this._jsonData){
						throw new Error(this.declaredClass + ":  The json data provided by the creation arguments is malformed.  Items within the list have identifier: [" + identifier + "].  Value collided: [" + identity + "]");
					}
				}
			}
		}else{
			this._features['dojo.data.api.Identity'] = Number;
		}

		// Step 5: Walk through all the items, and set each item's properties
		// for _storeRefPropName and _itemNumPropName, so that store.isItem() will return true.
		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i];
			item[this._storeRefPropName] = this;
			item[this._itemNumPropName] = i;
		}

		// Step 6: We walk through all the attribute values of all the items,
		// looking for type/value literals and item-references.
		//
		// We replace item-references with pointers to items.  For example, we change:
		//		{ name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
		// into this:
		//		{ name:['Kermit'], friends:[miss_piggy] }
		// (where miss_piggy is the object representing the 'Miss Piggy' item).
		//
		// We replace type/value pairs with typed-literals.  For example, we change:
		//		{ name:['Nelson Mandela'], born:[{_type:'Date', _value:'1918-07-18'}] }
		// into this:
		//		{ name:['Kermit'], born:(new Date(1918, 6, 18)) }
		//
		// We also generate the associate map for all items for the O(1) isItem function.
		for(i = 0; i < this._arrayOfAllItems.length; ++i){
			item = this._arrayOfAllItems[i]; // example: { name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
			for(key in item){
				arrayOfValues = item[key]; // example: [{_reference:{name:'Miss Piggy'}}]
				for(var j = 0; j < arrayOfValues.length; ++j){
					value = arrayOfValues[j]; // example: {_reference:{name:'Miss Piggy'}}
					if(value !== null && typeof value == "object"){
						if(("_type" in value) && ("_value" in value)){
							var type = value._type; // examples: 'Date', 'Color', or 'ComplexNumber'
							var mappingObj = this._datatypeMap[type]; // examples: Date, dojo.Color, foo.math.ComplexNumber, {type: dojo.Color, deserialize(value){ return new dojo.Color(value)}}
							if(!mappingObj){
								throw new Error("dojo.data.ItemFileReadStore: in the typeMap constructor arg, no object class was specified for the datatype '" + type + "'");
							}else if(lang.isFunction(mappingObj)){
								arrayOfValues[j] = new mappingObj(value._value);
							}else if(lang.isFunction(mappingObj.deserialize)){
								arrayOfValues[j] = mappingObj.deserialize(value._value);
							}else{
								throw new Error("dojo.data.ItemFileReadStore: Value provided in typeMap was neither a constructor, nor a an object with a deserialize function");
							}
						}
						if(value._reference){
							var referenceDescription = value._reference; // example: {name:'Miss Piggy'}
							if(!lang.isObject(referenceDescription)){
								// example: 'Miss Piggy'
								// from an item like: { name:['Kermit'], friends:[{_reference:'Miss Piggy'}]}
								arrayOfValues[j] = this._getItemByIdentity(referenceDescription);
							}else{
								// example: {name:'Miss Piggy'}
								// from an item like: { name:['Kermit'], friends:[{_reference:{name:'Miss Piggy'}}] }
								for(var k = 0; k < this._arrayOfAllItems.length; ++k){
									var candidateItem = this._arrayOfAllItems[k],
										found = true;
									for(var refKey in referenceDescription){
										if(candidateItem[refKey] != referenceDescription[refKey]){
											found = false;
										}
									}
									if(found){
										arrayOfValues[j] = candidateItem;
									}
								}
							}
							if(this.referenceIntegrity){
								var refItem = arrayOfValues[j];
								if(this.isItem(refItem)){
									this._addReferenceToMap(refItem, item, key);
								}
							}
						}else if(this.isItem(value)){
							//It's a child item (not one referenced through _reference).
							//We need to treat this as a referenced item, so it can be cleaned up
							//in a write store easily.
							if(this.referenceIntegrity){
								this._addReferenceToMap(value, item, key);
							}
						}
					}
				}
			}
		}
	},

	_addReferenceToMap: function(/*item*/ refItem, /*item*/ parentItem, /*string*/ attribute){
		// summary:
		//		Method to add an reference map entry for an item and attribute.
		// description:
		//		Method to add an reference map entry for an item and attribute.
		// refItem:
		//		The item that is referenced.
		// parentItem:
		//		The item that holds the new reference to refItem.
		// attribute:
		//		The attribute on parentItem that contains the new reference.

		//Stub function, does nothing.  Real processing is in ItemFileWriteStore.
	},

	getIdentity: function(/* dojo/data/api/Item */ item){
		// summary:
		//		See dojo/data/api/Identity.getIdentity()
		var identifier = this._features['dojo.data.api.Identity'];
		if(identifier === Number){
			return item[this._itemNumPropName]; // Number
		}else{
			var arrayOfValues = item[identifier];
			if(arrayOfValues){
				return arrayOfValues[0]; // Object|String
			}
		}
		return null; // null
	},

	fetchItemByIdentity: function(/* Object */ keywordArgs){
		// summary:
		//		See dojo/data/api/Identity.fetchItemByIdentity()

		// Hasn't loaded yet, we have to trigger the load.
		var item,
			scope;
		if(!this._loadFinished){
			var self = this;
			//Do a check on the JsonFileUrl and crosscheck it.
			//If it doesn't match the cross-check, it needs to be updated
			//This allows for either url or _jsonFileUrl to he changed to
			//reset the store load location.  Done this way for backwards
			//compatibility.  People use _jsonFileUrl (even though officially
			//private.
			if(this._jsonFileUrl !== this._ccUrl){
				kernel.deprecated(this.declaredClass + ": ",
					"To change the url, set the url property of the store," +
						" not _jsonFileUrl.  _jsonFileUrl support will be removed in 2.0");
				this._ccUrl = this._jsonFileUrl;
				this.url = this._jsonFileUrl;
			}else if(this.url !== this._ccUrl){
				this._jsonFileUrl = this.url;
				this._ccUrl = this.url;
			}

			//See if there was any forced reset of data.
			if(this.data != null && this._jsonData == null){
				this._jsonData = this.data;
				this.data = null;
			}

			if(this._jsonFileUrl){

				if(this._loadInProgress){
					this._queuedFetches.push({args: keywordArgs});
				}else{
					this._loadInProgress = true;
					var getArgs = {
						url: self._jsonFileUrl,
						handleAs: "json-comment-optional",
						preventCache: this.urlPreventCache,
						failOk: this.failOk
					};
					var getHandler = xhr.get(getArgs);
					getHandler.addCallback(function(data){
						var scope = keywordArgs.scope?keywordArgs.scope:kernel.global;
						try{
							self._getItemsFromLoadedData(data);
							self._loadFinished = true;
							self._loadInProgress = false;
							item = self._getItemByIdentity(keywordArgs.identity);
							if(keywordArgs.onItem){
								keywordArgs.onItem.call(scope, item);
							}
							self._handleQueuedFetches();
						}catch(error){
							self._loadInProgress = false;
							if(keywordArgs.onError){
								keywordArgs.onError.call(scope, error);
							}
						}
					});
					getHandler.addErrback(function(error){
						self._loadInProgress = false;
						if(keywordArgs.onError){
							var scope = keywordArgs.scope?keywordArgs.scope:kernel.global;
							keywordArgs.onError.call(scope, error);
						}
					});
				}

			}else if(this._jsonData){
				// Passed in data, no need to xhr.
				self._getItemsFromLoadedData(self._jsonData);
				self._jsonData = null;
				self._loadFinished = true;
				item = self._getItemByIdentity(keywordArgs.identity);
				if(keywordArgs.onItem){
					scope = keywordArgs.scope?keywordArgs.scope:kernel.global;
					keywordArgs.onItem.call(scope, item);
				}
			}
		}else{
			// Already loaded.  We can just look it up and call back.
			item = this._getItemByIdentity(keywordArgs.identity);
			if(keywordArgs.onItem){
				scope = keywordArgs.scope?keywordArgs.scope:kernel.global;
				keywordArgs.onItem.call(scope, item);
			}
		}
	},

	_getItemByIdentity: function(/* Object */ identity){
		// summary:
		//		Internal function to look an item up by its identity map.
		var item = null;
		if(this._itemsByIdentity){
			// If this map is defined, we need to just try to get it.  If it fails
			// the item does not exist.
			if(Object.hasOwnProperty.call(this._itemsByIdentity, identity)){
				item = this._itemsByIdentity[identity];
			}
		}else if (Object.hasOwnProperty.call(this._arrayOfAllItems, identity)){
			item = this._arrayOfAllItems[identity];
		}
		if(item === undefined){
			item = null;
		}
		return item; // Object
	},

	getIdentityAttributes: function(/* dojo/data/api/Item */ item){
		// summary:
		//		See dojo/data/api/Identity.getIdentityAttributes()

		var identifier = this._features['dojo.data.api.Identity'];
		if(identifier === Number){
			// If (identifier === Number) it means getIdentity() just returns
			// an integer item-number for each item.  The dojo/data/api/Identity
			// spec says we need to return null if the identity is not composed
			// of attributes
			return null; // null
		}else{
			return [identifier]; // Array
		}
	},

	_forceLoad: function(){
		// summary:
		//		Internal function to force a load of the store if it hasn't occurred yet.  This is required
		//		for specific functions to work properly.
		var self = this;
		//Do a check on the JsonFileUrl and crosscheck it.
		//If it doesn't match the cross-check, it needs to be updated
		//This allows for either url or _jsonFileUrl to he changed to
		//reset the store load location.  Done this way for backwards
		//compatibility.  People use _jsonFileUrl (even though officially
		//private.
		if(this._jsonFileUrl !== this._ccUrl){
			kernel.deprecated(this.declaredClass + ": ",
				"To change the url, set the url property of the store," +
					" not _jsonFileUrl.  _jsonFileUrl support will be removed in 2.0");
			this._ccUrl = this._jsonFileUrl;
			this.url = this._jsonFileUrl;
		}else if(this.url !== this._ccUrl){
			this._jsonFileUrl = this.url;
			this._ccUrl = this.url;
		}

		//See if there was any forced reset of data.
		if(this.data != null){
			this._jsonData = this.data;
			this.data = null;
		}

		if(this._jsonFileUrl){
			var getArgs = {
				url: this._jsonFileUrl,
				handleAs: "json-comment-optional",
				preventCache: this.urlPreventCache,
				failOk: this.failOk,
				sync: true
			};
			var getHandler = xhr.get(getArgs);
			getHandler.addCallback(function(data){
				try{
					//Check to be sure there wasn't another load going on concurrently
					//So we don't clobber data that comes in on it.  If there is a load going on
					//then do not save this data.  It will potentially clobber current data.
					//We mainly wanted to sync/wait here.
					//TODO:  Revisit the loading scheme of this store to improve multi-initial
					//request handling.
					if(self._loadInProgress !== true && !self._loadFinished){
						self._getItemsFromLoadedData(data);
						self._loadFinished = true;
					}else if(self._loadInProgress){
						//Okay, we hit an error state we can't recover from.  A forced load occurred
						//while an async load was occurring.  Since we cannot block at this point, the best
						//that can be managed is to throw an error.
						throw new Error(this.declaredClass + ":  Unable to perform a synchronous load, an async load is in progress.");
					}
				}catch(e){
					console.log(e);
					throw e;
				}
			});
			getHandler.addErrback(function(error){
				throw error;
			});
		}else if(this._jsonData){
			self._getItemsFromLoadedData(self._jsonData);
			self._jsonData = null;
			self._loadFinished = true;
		}
	}
});
//Mix in the simple fetch implementation to this class.
lang.extend(ItemFileReadStore,simpleFetch);

return ItemFileReadStore;

});

},
'dojo/data/util/filter':function(){
define(["../../_base/lang"], function(lang){
	// module:
	//		dojo/data/util/filter
	// summary:
	//		TODOC

var filter = {};
lang.setObject("dojo.data.util.filter", filter);

filter.patternToRegExp = function(/*String*/pattern, /*boolean?*/ ignoreCase){
	// summary:
	//		Helper function to convert a simple pattern to a regular expression for matching.
	// description:
	//		Returns a regular expression object that conforms to the defined conversion rules.
	//		For example:
	//
	//		- ca*   -> /^ca.*$/
	//		- *ca*  -> /^.*ca.*$/
	//		- *c\*a*  -> /^.*c\*a.*$/
	//		- *c\*a?*  -> /^.*c\*a..*$/
	//
	//		and so on.
	// pattern: string
	//		A simple matching pattern to convert that follows basic rules:
	//
	//		- * Means match anything, so ca* means match anything starting with ca
	//		- ? Means match single character.  So, b?b will match to bob and bab, and so on.
	//		- \ is an escape character.  So for example, \* means do not treat * as a match, but literal character *.
	//
	//		To use a \ as a character in the string, it must be escaped.  So in the pattern it should be
	//		represented by \\ to be treated as an ordinary \ character instead of an escape.
	// ignoreCase:
	//		An optional flag to indicate if the pattern matching should be treated as case-sensitive or not when comparing
	//		By default, it is assumed case sensitive.

	var rxp = "^";
	var c = null;
	for(var i = 0; i < pattern.length; i++){
		c = pattern.charAt(i);
		switch(c){
			case '\\':
				rxp += c;
				i++;
				rxp += pattern.charAt(i);
				break;
			case '*':
				rxp += ".*"; break;
			case '?':
				rxp += "."; break;
			case '$':
			case '^':
			case '/':
			case '+':
			case '.':
			case '|':
			case '(':
			case ')':
			case '{':
			case '}':
			case '[':
			case ']':
				rxp += "\\"; //fallthrough
			default:
				rxp += c;
		}
	}
	rxp += "$";
	if(ignoreCase){
		return new RegExp(rxp,"mi"); //RegExp
	}else{
		return new RegExp(rxp,"m"); //RegExp
	}

};

return filter;
});

},
'dojo/data/util/simpleFetch':function(){
define(["../../_base/lang", "../../_base/kernel", "./sorter"],
  function(lang, kernel, sorter){
	// module:
	//		dojo/data/util/simpleFetch
	// summary:
	//		The simpleFetch mixin is designed to serve as a set of function(s) that can
	//		be mixed into other datastore implementations to accelerate their development.

var simpleFetch = {};
lang.setObject("dojo.data.util.simpleFetch", simpleFetch);

simpleFetch.errorHandler = function(/*Object*/ errorData, /*Object*/ requestObject){
	// summary:
	//		The error handler when there is an error fetching items.  This function should not be called
	//		directly and is used by simpleFetch.fetch().
	if(requestObject.onError){
		var scope = requestObject.scope || kernel.global;
		requestObject.onError.call(scope, errorData, requestObject);
	}
};

simpleFetch.fetchHandler = function(/*Array*/ items, /*Object*/ requestObject){
	// summary:
	//		The handler when items are sucessfully fetched.  This function should not be called directly
	//		and is used by simpleFetch.fetch().
	var oldAbortFunction = requestObject.abort || null,
		aborted = false,

		startIndex = requestObject.start?requestObject.start: 0,
		endIndex = (requestObject.count && (requestObject.count !== Infinity))?(startIndex + requestObject.count):items.length;

	requestObject.abort = function(){
		aborted = true;
		if(oldAbortFunction){
			oldAbortFunction.call(requestObject);
		}
	};

	var scope = requestObject.scope || kernel.global;
	if(!requestObject.store){
		requestObject.store = this;
	}
	if(requestObject.onBegin){
		requestObject.onBegin.call(scope, items.length, requestObject);
	}
	if(requestObject.sort){
		items.sort(sorter.createSortFunction(requestObject.sort, this));
	}
	if(requestObject.onItem){
		for(var i = startIndex; (i < items.length) && (i < endIndex); ++i){
			var item = items[i];
			if(!aborted){
				requestObject.onItem.call(scope, item, requestObject);
			}
		}
	}
	if(requestObject.onComplete && !aborted){
		var subset = null;
		if(!requestObject.onItem){
			subset = items.slice(startIndex, endIndex);
		}
		requestObject.onComplete.call(scope, subset, requestObject);
	}
};

simpleFetch.fetch = function(/* Object? */ request){
	// summary:
	//		The simpleFetch mixin is designed to serve as a set of function(s) that can
	//		be mixed into other datastore implementations to accelerate their development.
	// description:
	//		The simpleFetch mixin should work well for any datastore that can respond to a _fetchItems()
	//		call by returning an array of all the found items that matched the query.  The simpleFetch mixin
	//		is not designed to work for datastores that respond to a fetch() call by incrementally
	//		loading items, or sequentially loading partial batches of the result
	//		set.  For datastores that mixin simpleFetch, simpleFetch
	//		implements a fetch method that automatically handles eight of the fetch()
	//		arguments -- onBegin, onItem, onComplete, onError, start, count, sort and scope
	//		The class mixing in simpleFetch should not implement fetch(),
	//		but should instead implement a _fetchItems() method.  The _fetchItems()
	//		method takes three arguments, the keywordArgs object that was passed
	//		to fetch(), a callback function to be called when the result array is
	//		available, and an error callback to be called if something goes wrong.
	//		The _fetchItems() method should ignore any keywordArgs parameters for
	//		start, count, onBegin, onItem, onComplete, onError, sort, and scope.
	//		The _fetchItems() method needs to correctly handle any other keywordArgs
	//		parameters, including the query parameter and any optional parameters
	//		(such as includeChildren).  The _fetchItems() method should create an array of
	//		result items and pass it to the fetchHandler along with the original request object --
	//		or, the _fetchItems() method may, if it wants to, create an new request object
	//		with other specifics about the request that are specific to the datastore and pass
	//		that as the request object to the handler.
	//
	//		For more information on this specific function, see dojo/data/api/Read.fetch()
	//
	// request:
	//		The keywordArgs parameter may either be an instance of
	//		conforming to dojo/data/api/Request or may be a simple anonymous object
	//		that may contain any of the following:
	// |	{
	// |		query: query-object or query-string,
	// |		queryOptions: object,
	// |		onBegin: Function,
	// |		onItem: Function,
	// |		onComplete: Function,
	// |		onError: Function,
	// |		scope: object,
	// |		start: int
	// |		count: int
	// |		sort: array
	// |	}
	//		All implementations should accept keywordArgs objects with any of
	//		the 9 standard properties: query, onBegin, onItem, onComplete, onError
	//		scope, sort, start, and count.  Some implementations may accept additional
	//		properties in the keywordArgs object as valid parameters, such as
	//		{includeOutliers:true}.
	//
	//		####The *query* parameter
	//
	//		The query may be optional in some data store implementations.
	//		The dojo/data/api/Read API does not specify the syntax or semantics
	//		of the query itself -- each different data store implementation
	//		may have its own notion of what a query should look like.
	//		However, as of dojo 0.9, 1.0, and 1.1, all the provided datastores in dojo.data
	//		and dojox.data support an object structure query, where the object is a set of
	//		name/value parameters such as { attrFoo: valueBar, attrFoo1: valueBar1}.  Most of the
	//		dijit widgets, such as ComboBox assume this to be the case when working with a datastore
	//		when they dynamically update the query.  Therefore, for maximum compatibility with dijit
	//		widgets the recommended query parameter is a key/value object.  That does not mean that the
	//		the datastore may not take alternative query forms, such as a simple string, a Date, a number,
	//		or a mix of such.  Ultimately, The dojo/data/api/Read API is agnostic about what the query
	//		format.
	//
	//		Further note:  In general for query objects that accept strings as attribute
	//		value matches, the store should also support basic filtering capability, such as *
	//		(match any character) and ? (match single character).  An example query that is a query object
	//		would be like: { attrFoo: "value*"}.  Which generally means match all items where they have
	//		an attribute named attrFoo, with a value that starts with 'value'.
	//
	//		####The *queryOptions* parameter
	//
	//		The queryOptions parameter is an optional parameter used to specify options that may modify
	//		the query in some fashion, such as doing a case insensitive search, or doing a deep search
	//		where all items in a hierarchical representation of data are scanned instead of just the root
	//		items.  It currently defines two options that all datastores should attempt to honor if possible:
	// |	{
	// |		ignoreCase: boolean, // Whether or not the query should match case sensitively or not.  Default behaviour is false.
	// |		deep: boolean	// Whether or not a fetch should do a deep search of items and all child
	// |						// items instead of just root-level items in a datastore.  Default is false.
	// |	}
	//
	//		####The *onBegin* parameter.
	//
	//		function(size, request);
	//		If an onBegin callback function is provided, the callback function
	//		will be called just once, before the first onItem callback is called.
	//		The onBegin callback function will be passed two arguments, the
	//		the total number of items identified and the Request object.  If the total number is
	//		unknown, then size will be -1.  Note that size is not necessarily the size of the
	//		collection of items returned from the query, as the request may have specified to return only a
	//		subset of the total set of items through the use of the start and count parameters.
	//
	//		####The *onItem* parameter.
	//
	//		function(item, request);
	//
	//		If an onItem callback function is provided, the callback function
	//		will be called as each item in the result is received. The callback
	//		function will be passed two arguments: the item itself, and the
	//		Request object.
	//
	//		####The *onComplete* parameter.
	//
	//		function(items, request);
	//
	//		If an onComplete callback function is provided, the callback function
	//		will be called just once, after the last onItem callback is called.
	//		Note that if the onItem callback is not present, then onComplete will be passed
	//		an array containing all items which matched the query and the request object.
	//		If the onItem callback is present, then onComplete is called as:
	//		onComplete(null, request).
	//
	//		####The *onError* parameter.
	//
	//		function(errorData, request);
	//
	//		If an onError callback function is provided, the callback function
	//		will be called if there is any sort of error while attempting to
	//		execute the query.
	//		The onError callback function will be passed two arguments:
	//		an Error object and the Request object.
	//
	//		####The *scope* parameter.
	//
	//		If a scope object is provided, all of the callback functions (onItem,
	//		onComplete, onError, etc) will be invoked in the context of the scope
	//		object.  In the body of the callback function, the value of the "this"
	//		keyword will be the scope object.   If no scope object is provided,
	//		the callback functions will be called in the context of dojo.global().
	//		For example, onItem.call(scope, item, request) vs.
	//		onItem.call(dojo.global(), item, request)
	//
	//		####The *start* parameter.
	//
	//		If a start parameter is specified, this is a indication to the datastore to
	//		only start returning items once the start number of items have been located and
	//		skipped.  When this parameter is paired with 'count', the store should be able
	//		to page across queries with millions of hits by only returning subsets of the
	//		hits for each query
	//
	//		####The *count* parameter.
	//
	//		If a count parameter is specified, this is a indication to the datastore to
	//		only return up to that many items.  This allows a fetch call that may have
	//		millions of item matches to be paired down to something reasonable.
	//
	//		####The *sort* parameter.
	//
	//		If a sort parameter is specified, this is a indication to the datastore to
	//		sort the items in some manner before returning the items.  The array is an array of
	//		javascript objects that must conform to the following format to be applied to the
	//		fetching of items:
	// |	{
	// |		attribute: attribute || attribute-name-string,
	// |		descending: true|false;   // Optional.  Default is false.
	// |	}
	//		Note that when comparing attributes, if an item contains no value for the attribute
	//		(undefined), then it the default ascending sort logic should push it to the bottom
	//		of the list.  In the descending order case, it such items should appear at the top of the list.

	request = request || {};
	if(!request.store){
		request.store = this;
	}

	this._fetchItems(request, lang.hitch(this, "fetchHandler"), lang.hitch(this, "errorHandler"));
	return request;	// Object
};

return simpleFetch;
});

},
'dojo/data/util/sorter':function(){
define(["../../_base/lang"], function(lang){
	// module:
	//		dojo/data/util/sorter
	// summary:
	//		TODOC

var sorter = {};
lang.setObject("dojo.data.util.sorter", sorter);

sorter.basicComparator = function(	/*anything*/ a,
													/*anything*/ b){
	// summary:
	//		Basic comparison function that compares if an item is greater or less than another item
	// description:
	//		returns 1 if a > b, -1 if a < b, 0 if equal.
	//		'null' values (null, undefined) are treated as larger values so that they're pushed to the end of the list.
	//		And compared to each other, null is equivalent to undefined.

	//null is a problematic compare, so if null, we set to undefined.
	//Makes the check logic simple, compact, and consistent
	//And (null == undefined) === true, so the check later against null
	//works for undefined and is less bytes.
	var r = -1;
	if(a === null){
		a = undefined;
	}
	if(b === null){
		b = undefined;
	}
	if(a == b){
		r = 0;
	}else if(a > b || a == null){
		r = 1;
	}
	return r; //int {-1,0,1}
};

sorter.createSortFunction = function(	/* attributes[] */sortSpec, /*dojo/data/api/Read*/ store){
	// summary:
	//		Helper function to generate the sorting function based off the list of sort attributes.
	// description:
	//		The sort function creation will look for a property on the store called 'comparatorMap'.  If it exists
	//		it will look in the mapping for comparisons function for the attributes.  If one is found, it will
	//		use it instead of the basic comparator, which is typically used for strings, ints, booleans, and dates.
	//		Returns the sorting function for this particular list of attributes and sorting directions.
	// sortSpec:
	//		A JS object that array that defines out what attribute names to sort on and whether it should be descenting or asending.
	//		The objects should be formatted as follows:
	// |	{
	// |		attribute: "attributeName-string" || attribute,
	// |		descending: true|false;   // Default is false.
	// |	}
	// store:
	//		The datastore object to look up item values from.

	var sortFunctions=[];

	function createSortFunction(attr, dir, comp, s){
		//Passing in comp and s (comparator and store), makes this
		//function much faster.
		return function(itemA, itemB){
			var a = s.getValue(itemA, attr);
			var b = s.getValue(itemB, attr);
			return dir * comp(a,b); //int
		};
	}
	var sortAttribute;
	var map = store.comparatorMap;
	var bc = sorter.basicComparator;
	for(var i = 0; i < sortSpec.length; i++){
		sortAttribute = sortSpec[i];
		var attr = sortAttribute.attribute;
		if(attr){
			var dir = (sortAttribute.descending) ? -1 : 1;
			var comp = bc;
			if(map){
				if(typeof attr !== "string" && ("toString" in attr)){
					 attr = attr.toString();
				}
				comp = map[attr] || bc;
			}
			sortFunctions.push(createSortFunction(attr,
				dir, comp, store));
		}
	}
	return function(rowA, rowB){
		var i=0;
		while(i < sortFunctions.length){
			var ret = sortFunctions[i++](rowA, rowB);
			if(ret !== 0){
				return ret;//int
			}
		}
		return 0; //int
	}; // Function
};

return sorter;
});

},
'edu/wohaoo/mobile/ConfirmDialog':function(){
/** SPECS
	The view shows the exam of the given module.
	The server will check if the user has already completed the exam.
	If the user has already completed the exam, the view shows the users score along with the exam comment (passed, failed).
		The user is then asked wether he/she would want to retake the exam. If yes, a new exam session is created and
		the first question of the exam is runned.
	If the user has not already  already completed the exam, the first question of the exam session is returned and
	the list of possible answers (options) is displayed.
	When the user clicks on the "Next Question" Button, the view is refreshed.
 */
define([
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/MessageDialog", "dijit/registry",  "dojo/i18n!./nls/Messages",
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, MessageDialog, dijitRegistry, messages) {
	return declare([MessageDialog],{
		mblSimpleDialogTitleNode: null,
		mblSimpleDialogTextNode: null,
		mblSimpleDialogButtonNodeNo: null,
		mblSimpleDialogButtonNodeYes: null,
		
		messages: messages,
		 
		postCreate: function(){
			var self = this;
			
			//showFavoritesButton
			dojoQuery(".mblSimpleDialogTitle", this.domNode).forEach(function(element){
				self.mblSimpleDialogTitleNode = element;
				self.mblSimpleDialogTitleNode.innerHTML = self.messages.Information;
			});
			
			dojoQuery(".mblSimpleDialogText", this.domNode).forEach(function(element){
				self.mblSimpleDialogTextNode = element;
			});
			
			dojoQuery(".mblSimpleDialogButton.noButton", this.domNode).forEach(function(element){
				self.mblSimpleDialogButtonNodeNo = element;
				self.mblSimpleDialogButtonNodeNo.innerHTML = self.messages.No;
			});
			
			dojoQuery(".mblSimpleDialogButton.yesButton", this.domNode).forEach(function(element){
				self.mblSimpleDialogButtonNodeYes = element;
				self.mblSimpleDialogButtonNodeYes.innerHTML = self.messages.Yes;
			});
			
			this.inherited(arguments);
		},
		
		show: function(message, onOk){
			this.mblSimpleDialogTextNode.innerHTML = message;
			if(onOk){
				//ok  button
				if(this.mblSimpleDialogButtonNodeYes._clickEvent){
					this.mblSimpleDialogButtonNodeYes._clickEvent.remove();
					this.mblSimpleDialogButtonNodeYes._clickEvent = null;
				}
				var self = this;
				this.mblSimpleDialogButtonNodeYes._clickEvent = 
					dojoConnect.connect(this.mblSimpleDialogButtonNodeYes, 'click',  function(evt){
						onOk.call();
						self.mblSimpleDialogButtonNodeYes._clickEvent.remove();
						self.mblSimpleDialogButtonNodeYes._clickEvent = null;
					});
			}
			this.inherited(arguments);
		}
	});
});
},
'edu/wohaoo/mobile/MessageDialog':function(){
/** SPECS
	The view shows the exam of the given module.
	The server will check if the user has already completed the exam.
	If the user has already completed the exam, the view shows the users score along with the exam comment (passed, failed).
		The user is then asked wether he/she would want to retake the exam. If yes, a new exam session is created and
		the first question of the exam is runned.
	If the user has not already  already completed the exam, the first question of the exam session is returned and
	the list of possible answers (options) is displayed.
	When the user clicks on the "Next Question" Button, the view is refreshed.
 */
define([
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "dojox/mobile/SimpleDialog", "dijit/registry",  "dojo/i18n!./nls/Messages",
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, SimpleDialog, dijitRegistry, messages) {
	return declare([SimpleDialog],{
		mblSimpleDialogTitleNode: null,
		mblSimpleDialogTextNode: null,
		mblSimpleDialogButtonNode: null,
		
		messages: messages,
		 
		postCreate: function(){
			var self = this;
			
			//showFavoritesButton
			dojoQuery(".mblSimpleDialogTitle", this.domNode).forEach(function(element){
				self.mblSimpleDialogTitleNode = element;
				self.mblSimpleDialogTitleNode.innerHTML = self.messages.Information;
			});
			
			dojoQuery(".mblSimpleDialogText", this.domNode).forEach(function(element){
				self.mblSimpleDialogTextNode = element;
			});
			
			dojoQuery(".mblSimpleDialogButton", this.domNode).forEach(function(element){
				self.mblSimpleDialogButtonNode = element;
				self.mblSimpleDialogTitleNode.innerHTML = self.messages.OK;
			});
			
			this.inherited(arguments);
		},
		
		show: function(message){
			this.mblSimpleDialogTextNode.innerHTML = message;
			this.inherited(arguments);
		}
	});
});
},
'dojox/mobile/SimpleDialog':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/window",
	"dojo/dom-class",
	"dojo/dom-attr",
	"dojo/dom-construct",
	"dojo/on",
	"dojo/touch",
	"dijit/registry",
	"./Pane",
	"./iconUtils",
	"./sniff"
], function(declare, win, domClass, domAttr, domConstruct, on, touch, registry, Pane, iconUtils, has){
	// module:
	//		dojox/mobile/SimpleDialog

	return declare("dojox.mobile.SimpleDialog", Pane, {
		// summary:
		//		A dialog box for mobile.
		// description:
		//		SimpleDialog is a dialog box for mobile.
		//		When a SimpleDialog is created, it is initially hidden 
		//		(display="none"). To show the dialog box, you need to
		//		get a reference to the widget and to call its show() method.
		//
		//		The contents can be arbitrary HTML, text, or widgets. Note,
		//		however, that the widget is initially hidden. You need to be
		//		careful when you place in a SimpleDialog elements that cannot 
		//		be initialized in hidden state.
		//
		//		This widget has much less functionalities than dijit/Dialog, 
		//		but it has the advantage of a much smaller code size.

		// top: String
		//		The top edge position of the widget. If "auto", the widget is
		//		placed at the middle of the screen. Otherwise, the value
		//		(ex. "20px") is used as the top style of widget's domNode.
		top: "auto",

		// left: String
		//		The left edge position of the widget. If "auto", the widget is
		//		placed at the center of the screen. Otherwise, the value
		//		(ex. "20px") is used as the left style of widget's domNode.
		left: "auto",

		// modal: Boolean
		//		If true, a translucent cover is added over the entire page to
		//		prevent the user from interacting with elements on the page.
		modal: true,

		// closeButton: [const] Boolean
		//		If true, a button to close the dialog box is displayed at the
		//		top-right corner.
		//		Note that changing the value of the property after the widget
		//		creation has no effect.
		closeButton: false,

		// closeButtonClass: String
		//		A class name of a DOM button to be used as a close button.
		closeButtonClass: "mblDomButtonSilverCircleRedCross",

		// tabIndex: String
		//		Tabindex setting for the item so users can hit the tab key to
		//		focus on it.
		tabIndex: "0",
		
		// _setTabIndexAttr: [private] String
		//		Sets tabIndex to domNode.
		_setTabIndexAttr: "",

		/* internal properties */	
		
		// baseClass: String
		//		The name of the CSS class of this widget.
		baseClass: "mblSimpleDialog",
		
		// _cover: [private] Array
		//		Array for sharing the cover instances.
		_cover: [],

		buildRendering: function(){
			this.containerNode = domConstruct.create("div", {className:"mblSimpleDialogContainer"});
			if(this.srcNodeRef){
				// reparent
				for(var i = 0, len = this.srcNodeRef.childNodes.length; i < len; i++){
					this.containerNode.appendChild(this.srcNodeRef.removeChild(this.srcNodeRef.firstChild));
				}
			}
			this.inherited(arguments);
			domAttr.set(this.domNode, "role", "dialog");
			
			if(this.containerNode.getElementsByClassName){ //TODO: Do we need to support IE8 a11y?
	            var titleNode = this.containerNode.getElementsByClassName("mblSimpleDialogTitle")[0];
	            if (titleNode){
	            	titleNode.id = titleNode.id || registry.getUniqueId("dojo_mobile_mblSimpleDialogTitle");
	            	domAttr.set(this.domNode, "aria-labelledby", titleNode.id);
	            }
	            var textNode = this.containerNode.getElementsByClassName("mblSimpleDialogText")[0];
	            if (textNode){
	                textNode.id = textNode.id || registry.getUniqueId("dojo_mobile_mblSimpleDialogText");
	                domAttr.set(this.domNode, "aria-describedby", textNode.id);
	            }
			}
			domClass.add(this.domNode, "mblSimpleDialogDecoration");
			this.domNode.style.display = "none";
			this.domNode.appendChild(this.containerNode);
			if(this.closeButton){
				this.closeButtonNode = domConstruct.create("div", {
					className: "mblSimpleDialogCloseBtn "+this.closeButtonClass
				}, this.domNode);
				iconUtils.createDomButton(this.closeButtonNode);
				this.connect(this.closeButtonNode, "onclick", "_onCloseButtonClick");
			}
			this.connect(this.domNode, "onkeydown", "_onKeyDown"); // for desktop browsers
		},

		startup: function(){
			if(this._started){ return; }
			this.inherited(arguments);
			win.body().appendChild(this.domNode);
		},

		addCover: function(){
			// summary:
			//		Adds the transparent DIV cover.
			if(!this._cover[0]){
				this._cover[0] = domConstruct.create("div", {
					className: "mblSimpleDialogCover"
				}, win.body());
			}else{
				this._cover[0].style.display = "";
			}

			if(has("windows-theme")) {
				// Hack to prevent interaction with elements placed under cover div.
				this.own(on(this._cover[0], touch.press, function() {}));
			}
		},

		removeCover: function(){
			// summary:
			//		Removes the transparent DIV cover.
			this._cover[0].style.display = "none";
		},

		_onCloseButtonClick: function(e){
			// tags:
			//		private
			if(this.onCloseButtonClick(e) === false){ return; } // user's click action
			this.hide();
		},

		onCloseButtonClick: function(/*Event*/ /*===== e =====*/){
			// summary:
			//		User-defined function to handle clicks.
			// tags:
			//		callback
		},

		_onKeyDown: function(e){
			// tags:
			//		private
			if(e.keyCode == 27){ // ESC
				this.hide();
			}
		},

		refresh: function(){ // TODO: should we call refresh on resize?
			// summary:
			//		Refreshes the layout of the dialog.
			var n = this.domNode;
			var h;
			if(this.closeButton){
				var b = this.closeButtonNode;
				var s = Math.round(b.offsetHeight / 2);
				b.style.top = -s + "px";
				b.style.left = n.offsetWidth - s + "px";
			}
			if(this.top === "auto"){
				h = win.global.innerHeight || win.doc.documentElement.clientHeight;
				n.style.top = Math.round((h - n.offsetHeight) / 2) + "px";
			}else{
				n.style.top = this.top;
			}
			if(this.left === "auto"){
				h = win.global.innerWidth || win.doc.documentElement.clientWidth;
				n.style.left = Math.round((h - n.offsetWidth) / 2) + "px";
			}else{
				n.style.left = this.left;
			}
		},

		show: function(){
			// summary:
			//		Shows the dialog.
			if(this.domNode.style.display === ""){ return; }
			if(this.modal){
				this.addCover();
			}
			this.domNode.style.display = "";
			this.resize(); // #15628
			this.refresh();
			var diaglogButton;
			if(this.domNode.getElementsByClassName){
				diaglogButton = this.domNode.getElementsByClassName("mblSimpleDialogButton")[0];
			}
			var focusNode = diaglogButton || this.closeButtonNode || this.domNode; // Focus preference is: user supplied button, close button, entire dialog
			/// on Safari iOS the focus is not taken without a timeout
			this.defer(function(){ focusNode.focus();}, 1000);
		},

		hide: function(){
			// summary:
			//		Hides the dialog.
			if(this.domNode.style.display === "none"){ return; }
			this.domNode.style.display = "none";
			if(this.modal){
				this.removeCover();
			}
		}
	});
});

},
'edu/wohaoo/mobile/CurriculumsView':function(){
/**
 *The view shows the list of curriculums as icons with labels.
    When the user clicks on an icon, the curriculum menu page is shown
    The top bar has a search box. When the user enters a search term and clicks
    on the search button, only the curriculums corresponding to the search term are shown.
    When the user clicks on the favorites button a keyword ":favorites" is added at the end of the search term
    When the user clicks on the 'Show Menu' button a toolbar is presented.
    If the user is connected, then the user bar (User Menu) is shown, else
    	the default home bar (Home Menu) is shown
    */
define([
   "dojo/dom-class", "dojo/keys", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/AppListView", "dijit/registry", "dojox/mobile/IconItem",
     "dojo/domReady!"
], function(domClass, dojoKeys, dojoQuery, dojoConnect, declare, _WidgetBase, 
		domConstruct, AppListView, dijitRegistry, MobileIconItem) {
	return declare([AppListView],{
		loggedin: false,
		
		/**
		 storeItem => {
			model => 'curriculum',
			id,
			icon,
			code,
			lang,
			desc,
			name,
			value,
			created,
			module_count,
			course_id,
			course_created,
			course_graduated,
			course_module_count
		 }
		 */
		searchButtonNode: null,
		searchInputNode: null,
		showFavoritesButtonNode: null,
		showHomeMenuButtonNode: null,
		homeMenuNode: null,
		userMenuNode: null,
		
		postCreate: function(){
			this.itemTemplateString = '<div class="box"></div>';
			this.itemClass = 'curriculum';
			this.itemMoveTo = 'curriculumPage';
			this.itemTransition = 'slide';
			
			this.inherited(arguments);
		},
		
		startup: function(){
			var self = this;
			dojoQuery('button[type="submit"].icon', this.domNode).forEach(function(element){
				self.searchButtonNode = element;
			});
			
			dojoQuery("input#search-query", this.domNode).forEach(function(element){
				self.searchInputNode = element;
			});
			
			//showFavoritesButton
			dojoQuery("span.showFavoritesButton", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.showFavoritesButtonNode = widget.domNode;
			});
			
			//showHomeMenuButtonNode
			dojoQuery("span.showHomeMenuButton", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.showHomeMenuButtonNode = widget.domNode;
			});
			
			//homeMenu
			dojoQuery(".homeMenu", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.homeMenuNode = widget.domNode;
			});
			
			//userMenu
			dojoQuery(".userMenu", this.domNode).forEach(function(element){
				var widget = dijitRegistry.byNode(element);
				self.userMenuNode = widget.domNode;
			});
			
			this._attachEvents();
			this.inherited(arguments);
		},
		
		createListItem: function(itemClass, id, caption, itemMoveTo, itemTransition){
			var item = new MobileIconItem({
				"class": itemClass,
				"dataId": id,
				"moveTo": itemMoveTo,
				"rightText": "",
				"transition": itemTransition,
				'label': caption
			});
			return item;
		},
		
		updateItemContent: function(item, itemTemplateString, storeItem){
		},
		
		refresh: function(){
			//hide tad bars
			domClass.add(this.homeMenuNode, 'mblHidden');
			domClass.add(this.userMenuNode, 'mblHidden');
			if(this.searchInputNode)
				this.q = this.searchInputNode.value;
			this.inherited(arguments);
		},
		
		_attachEvents: function (){
			var self = this;
			if(self.searchInputNode._keyupEvent){
				self.searchInputNode._keyupEvent.remove();
				self.searchInputNode._keyupEvent = null;
			}
			self.searchInputNode._keyupEvent = dojoConnect.connect(self.searchInputNode, "keyup", function(e){
				if(e.keyCode === dojoKeys.ENTER){
					self.q = self.searchInputNode.value;
					if(self.q !== ''){
						domClass.add(self.searchButtonNode, 'nav-search-active');
						domClass.remove(self.searchButtonNode, 'nav-search');
					}else{
						domClass.remove(self.searchButtonNode, 'nav-search-active');
						domClass.add(self.searchButtonNode, 'nav-search');
					}
					self.refresh();
				}
			});
			
			//show clear button icon
			if(self.searchButtonNode._clickEvent){
				self.searchButtonNode._clickEvent.remove();
				self.searchButtonNode._clickEvent = null;
			}
			self.searchButtonNode._clickEvent = dojoConnect.connect(self.searchButtonNode, 'click', function(e){
				if(domClass.contains(self.searchButtonNode, "nav-search-active")){
					self.q = "";
					self.l = "";
					self.searchInputNode.value = "";
					self.refresh();
					domClass.remove(self.searchButtonNode, 'nav-search-active');
					domClass.add(self.searchButtonNode, 'nav-search');
				}else{
					self.q = self.searchInputNode.value;
					self.l = "";
					if(self.q !== ''){
						self.refresh();
						domClass.add(self.searchButtonNode, 'nav-search-active');
						domClass.remove(self.searchButtonNode, 'nav-search');
					}
				}
			});
			
			//showFavoritesButton
			if(self.showFavoritesButtonNode._clickEvent){
				self.showFavoritesButtonNode._clickEvent.remove();
				self.showFavoritesButtonNode._clickEvent = null;
			}
			
			self.showFavoritesButtonNode._clickEvent = 
			dojoConnect.connect(self.showFavoritesButtonNode, 'click', function(e){
				if(self.searchInputNode.value.indexOf(":favorites") < 0)
					self.searchInputNode.value = self.searchInputNode.value + ":favorites";
				
				var favorites = Sys.getCurriculumFavorites(); //restrict search to favorites list
				if(!favorites)
					favorites = "0"; //default
				self.l = favorites;
				
				if(!domClass.contains(self.searchButtonNode, "nav-search-active")){
					domClass.add(self.searchButtonNode, 'nav-search-active');
					domClass.remove(self.searchButtonNode, 'nav-search');
				}
				
				self.refresh();
			});
			
			//showHomeMenuButtonNode
			if(self.showHomeMenuButtonNode._clickEvent){
				self.showHomeMenuButtonNode._clickEvent.remove();
				self.showHomeMenuButtonNode._clickEvent = null;
			}
			self.showHomeMenuButtonNode._clickEvent = 
			dojoConnect.connect(self.showHomeMenuButtonNode, 'click', function(e){
				if(!Sys.getUser()){
					if(domClass.contains(self.homeMenuNode, "mblHidden")){
						domClass.remove(self.homeMenuNode, 'mblHidden');
					}else{
						domClass.add(self.homeMenuNode, 'mblHidden');
					}
				}else{
					//user is logged in
					if(domClass.contains(self.userMenuNode, "mblHidden")){
						domClass.remove(self.userMenuNode, 'mblHidden');
					}else{
						domClass.add(self.userMenuNode, 'mblHidden');
					}
				}
			});
			
			dojoQuery(".tabBarButton", this.domNode).forEach(function(element){
				var button = dijitRegistry.byNode(element);
				button.set('onClick', function(evt){
					var moveToWidget = dijitRegistry.byId(this.moveTo);
					moveToWidget.refresh();
					moveToWidget.animate();
					self.moveTo(self.id, this.moveTo, this.transition);
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
			
		}
	
	});
});
},
'dojox/mobile/IconItem':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/event",
	"dojo/_base/lang",
	"dojo/sniff",
	"dojo/_base/window",
	"dojo/dom-class",
	"dojo/dom-construct",
	"dojo/dom-geometry",
	"dojo/dom-style",
	"./_ItemBase",
	"./Badge",
	"./TransitionEvent",
	"./iconUtils",
	"./lazyLoadUtils",
	"./viewRegistry",
	"./_css3",
	"dojo/has!dojo-bidi?dojox/mobile/bidi/IconItem"
], function(declare, event, lang, has, win, domClass, domConstruct, domGeometry, domStyle, ItemBase, Badge, TransitionEvent, iconUtils, lazyLoadUtils, viewRegistry, css3, BidiIconItem){

	// module:
	//		dojox/mobile/IconItem

	var IconItem = declare(has("dojo-bidi") ? "dojox.mobile.NonBidiIconItem" : "dojox.mobile.IconItem", ItemBase, {
		// summary:
		//		An icon item widget.
		// description:
		//		IconItem represents an item that has an application component
		//		and its icon image. You can tap the icon to open the
		//		corresponding application component. You can also use the icon
		//		to move to a different view by specifying either of the moveTo,
		//		href or url parameters.

		// lazy: String
		//		If true, the content of the widget, which includes dojo markup,
		//		is instantiated lazily. That is, only when the widget is opened
		//		by the user, the required modules are loaded and the content
		//		widgets are instantiated.
		//		This option works both in the sync and async loader mode.
		lazy: false,

		// requires: String
		//		Comma-separated required module names to be lazily loaded. This
		//		property is effective only when lazy=true. All the modules
		//		specified with data-dojo-type and their depending modules are
		//		automatically loaded by the IconItem when it is opened.
		//		However, if you need other extra modules to be loaded, use this parameter.
		//		This option works both in the sync and async loader mode.
		requires: "",

		// timeout: String
		//		Duration of highlight in seconds.
		timeout: 10,

		// content: String
		//		An HTML fragment to embed as icon content.
		content: "",

		// badge: String
		//		A text to show in a badge (ex. "55").
		badge: "",

		// badgeClass: String
		//		A class name of a DOM button for a badge.
		badgeClass: "mblDomButtonRedBadge",

		// deletable: Boolean
		//		If true, you can delete this IconItem by clicking on the delete
		//		icon during edit mode.
		//		If false, the delete icon is not displayed during edit mode so
		//		that it cannot be deleted.
		deletable: true,

		// deleteIcon: String
		//		A delete icon to display at the top-left corner of the item
		//		during edit mode. The value can be either a path for an image
		//		file or a class name of a DOM button.
		deleteIcon: "",

		// tag: String
		//		A name of the HTML tag to create as domNode.
		tag: "li",

		/* internal properties */	
		// Note these are overrides for similar properties defined in _ItemBase.
		paramsToInherit: "transition,icon,deleteIcon,badgeClass,deleteIconTitle,deleteIconRole",
		baseClass: "mblIconItem",
		_selStartMethod: "touch",
		_selEndMethod: "none",

		destroy: function(){
			if(this.badgeObj){
				delete this.badgeObj;
			}
			this.inherited(arguments);
		},

		buildRendering: function(){
			this.domNode = this.srcNodeRef || domConstruct.create(this.tag);

			if(this.srcNodeRef){
				// reparent
				this._tmpNode = domConstruct.create("div");
				for(var i = 0, len = this.srcNodeRef.childNodes.length; i < len; i++){
					this._tmpNode.appendChild(this.srcNodeRef.firstChild);
				}
			}

			this.iconDivNode = domConstruct.create("div", {className:"mblIconArea"}, this.domNode);
			this.iconParentNode = domConstruct.create("div", {className:"mblIconAreaInner"}, this.iconDivNode);
			this.labelNode = domConstruct.create("span", {className:"mblIconAreaTitle"}, this.iconDivNode);

			this.inherited(arguments);
		},

		startup: function(){
			if(this._started){ return; }

			var p = this.getParent();
			require([p.iconItemPaneClass], lang.hitch(this, function(module){
				var w = this.paneWidget = new module(p.iconItemPaneProps);
				this.containerNode = w.containerNode;
				if(this._tmpNode){
					// reparent
					for(var i = 0, len = this._tmpNode.childNodes.length; i < len; i++){
						w.containerNode.appendChild(this._tmpNode.firstChild);
					}
					this._tmpNode = null;
				}
				p.paneContainerWidget.addChild(w, this.getIndexInParent());
				w.set("label", this.label);
				this._clickCloseHandle = this.connect(w.closeIconNode, "onclick", "_closeIconClicked");
				this._keydownCloseHandle = this.connect(w.closeIconNode, "onkeydown", "_closeIconClicked"); // for desktop browsers
			}));

			this.inherited(arguments);
			if(!this._isOnLine){
				this._isOnLine = true;
				// retry applying the attribute for which the custom setter delays the actual 
				// work until _isOnLine is true. 
				this.set("icon", this._pendingIcon !== undefined ? this._pendingIcon : this.icon);
				// Not needed anymore (this code executes only once per life cycle):
				delete this._pendingIcon; 
			}
			if(!this.icon && p.defaultIcon){
				this.set("icon", p.defaultIcon);
			}

			this._dragstartHandle = this.connect(this.domNode, "ondragstart", event.stop);
			this.connect(this.domNode, "onkeydown", "_onClick"); // for desktop browsers
		},

		highlight: function(/*Number?*/timeout){
			// summary:
			//		Shakes the icon 10 seconds.
			domClass.add(this.iconDivNode, "mblVibrate");
			timeout = (timeout !== undefined) ? timeout : this.timeout;
			if(timeout > 0){
				var _this = this;
				_this.defer(function(){
					_this.unhighlight();
				}, timeout*1000);
			}
		},

		unhighlight: function(){
			// summary:
			//		Stops shaking the icon.
			if(!has("ie") && has("trident") === 7){
				// Workaround on IE11: if just removing the style, the icon continues to shake
				domStyle.set(this.iconDivNode, "animation-name", "");
			}
			domClass.remove(this.iconDivNode, "mblVibrate");
		},

		isOpen: function(e){
			// summary:
			//		Returns true if the icon is open.
			return this.paneWidget.isOpen();
		},

		_onClick: function(e){
			// summary:
			//		Internal handler for click events.
			// tags:
			//		private
			if(this.getParent().isEditing || e && e.type === "keydown" && e.keyCode !== 13){ return; }
			if(this.onClick(e) === false){ return; } // user's click action
			this.defaultClickAction(e);
		},

		onClick: function(/*Event*/ /*===== e =====*/){
			// summary:
			//		User-defined function to handle clicks.
			// tags:
			//		callback
		},

		_onNewWindowOpened: function(e){
			// Override from _ItemBase
			this.set("selected", false);
		},

		_prepareForTransition: function(e, transOpts){
			// Override from _ItemBase
			if(transOpts){
				this.defer(function(d){
					this.set("selected", false);
				}, 1500);
				return true;
			}else{
				if(this.getParent().transition === "below" && this.isOpen()){
					this.close();
				}else{
					this.open(e);
				}
				return false;
			}
		},

		_closeIconClicked: function(e){
			// summary:
			//		Internal handler for click events.
			// tags:
			//		private
			if(e){
				if(e.type === "keydown" && e.keyCode !== 13){ return; }
				if(this.closeIconClicked(e) === false){ return; } // user's click action
				this.defer(function(d){ this._closeIconClicked(); });
				return;
			}
			this.close();
		},

		closeIconClicked: function(/*Event*/ /*===== e =====*/){
			// summary:
			//		User-defined function to handle clicks for the close icon.
			// tags:
			//		callback
		},

		open: function(e){
			// summary:
			//		Opens the icon content, or makes a transition.
			var parent = this.getParent(); // IconContainer
			if(this.transition === "below"){
				if(parent.single){
					parent.closeAll();
				}
				this._open_1();
			}else{
				parent._opening = this;
				if(parent.single){
					this.paneWidget.closeHeaderNode.style.display = "none";
					if(!this.isOpen()){
						parent.closeAll();
					}
					parent.appView._heading.set("label", this.label);
				}
				this.moveTo = parent.id + "_mblApplView";
				new TransitionEvent(this.domNode, this.getTransOpts(), e).dispatch();
			}
		},

		_open_1: function(){
			// summary:
			//		Opens the icon content for the 'below' transition.
			// tags:
			//		private
			this.paneWidget.show();
			this.unhighlight();
			if(this.lazy){
				lazyLoadUtils.instantiateLazyWidgets(this.containerNode, this.requires);
				this.lazy = false;
			}
			this.scrollIntoView(this.paneWidget.domNode);
			this.onOpen();
		},

		scrollIntoView: function(/*DomNode*/node){
			// summary:
			//		Scrolls until the given node is in the view.
			var s = viewRegistry.getEnclosingScrollable(node);
			if(s){ // this node is placed inside scrollable
				var dim = s.getDim();
				if(dim.c.h >= dim.d.h){ // #16306: only if the content is larger than the display area 
					s.scrollIntoView(node, true);
				}
			}else{
				win.global.scrollBy(0, domGeometry.position(node, false).y);
			}
		},

		close: function(/*Boolean?*/noAnimation){
			// summary:
			//		Closes the icon content.
			if(!this.isOpen()){ return; }
			this.set("selected", false);
			if(has("css3-animations") && !noAnimation){
				var contentNode = this.paneWidget.domNode;
				if(this.getParent().transition == "below"){
					domClass.add(contentNode, "mblCloseContent mblShrink");
					var nodePos = domGeometry.position(contentNode, true);
					var targetPos = domGeometry.position(this.domNode, true);
					var origin = (targetPos.x + targetPos.w/2 - nodePos.x) + "px " + (targetPos.y + targetPos.h/2 - nodePos.y) + "px";
					domStyle.set(contentNode, css3.add({}, { transformOrigin:origin }));
				}else{
					domClass.add(contentNode, "mblCloseContent mblShrink0");
				}
			}else{
				this.paneWidget.hide();
			}
			this.onClose();
		},

		onOpen: function(){
			// summary:
			//		Stub method to allow the application to connect.
		},

		onClose: function(){
			// summary:
			//		Stub method to allow the application to connect.
		},

		_setLabelAttr: function(/*String*/text){
			// tags:
			//		private
			this.label = text;
			var s = this._cv ? this._cv(text) : text;
			this.labelNode.innerHTML = s;
			if(this.paneWidget){
				this.paneWidget.set("label", text);
			}
		},

		_getBadgeAttr: function(){
			// tags:
			//		private
			return this.badgeObj ? this.badgeObj.getValue() : null;
		},

		_setBadgeAttr: function(/*String*/value){
			// tags:
			//		private
			if(!this.badgeObj){
				this.badgeObj = new Badge({fontSize:14, className:this.badgeClass});
				domStyle.set(this.badgeObj.domNode, {
					position: "absolute",
					top: "-2px",
					right: "2px"
				});
			}
			this.badgeObj.setValue(value);
			if(value){
				this.iconDivNode.appendChild(this.badgeObj.domNode);
			}else{
				this.iconDivNode.removeChild(this.badgeObj.domNode);
			}
		},

		_setDeleteIconAttr: function(icon){
			// tags:
			//		private
			if(!this.getParent()){ return; } // icon may be invalid because inheritParams is not called yet

			this._set("deleteIcon", icon);
			icon = this.deletable ? icon : "";
			this.deleteIconNode = iconUtils.setIcon(icon, this.deleteIconPos, this.deleteIconNode, 
					this.deleteIconTitle || this.alt, this.iconDivNode);
			if(this.deleteIconNode){
				domClass.add(this.deleteIconNode, "mblIconItemDeleteIcon");
				if(this.deleteIconRole){
					this.deleteIconNode.setAttribute("role", this.deleteIconRole);
				}
			}
		},

		_setContentAttr: function(/*String|DomNode*/data){
			// tags:
			//		private
			var root;
			if(!this.paneWidget){
				if(!this._tmpNode){
					this._tmpNode = domConstruct.create("div");
				}
				root = this._tmpNode;
			}else{
				root = this.paneWidget.containerNode;
			}

			if(typeof data === "object"){
				domConstruct.empty(root);
				root.appendChild(data);
			}else{
				root.innerHTML = data;
			}
		},

		_setSelectedAttr: function(/*Boolean*/selected){
			// summary:
			//		Makes this widget in the selected or unselected state.
			// tags:
			//		private
			this.inherited(arguments);
			domStyle.set(this.iconNode, "opacity",
						 selected ? this.getParent().pressedIconOpacity : 1);
		}
	});

	return has("dojo-bidi") ? declare("dojox.mobile.IconItem", [IconItem, BidiIconItem]) : IconItem;
});

},
'dojox/mobile/lazyLoadUtils':function(){
define([
	"dojo/_base/kernel",
	"dojo/_base/array",
	"dojo/_base/config",
	"dojo/_base/window",
	"dojo/_base/Deferred",
	"dojo/ready"
], function(dojo, array, config, win, Deferred, ready){

	// module:
	//		dojox/mobile/lazyLoadUtils

	var LazyLoadUtils = function(){
		// summary:
		//		Utilities to lazy-loading of Dojo widgets.

		this._lazyNodes = [];
		var _this = this;
		if(config.parseOnLoad){
			ready(90, function(){
				var lazyNodes = array.filter(win.body().getElementsByTagName("*"), // avoid use of dojo.query
					function(n){ return n.getAttribute("lazy") === "true" || (n.getAttribute("data-dojo-props")||"").match(/lazy\s*:\s*true/); });
				var i, j, nodes, s, n;
				for(i = 0; i < lazyNodes.length; i++){
					array.forEach(["dojoType", "data-dojo-type"], function(a){
						nodes = array.filter(lazyNodes[i].getElementsByTagName("*"),
											function(n){ return n.getAttribute(a); });
						for(j = 0; j < nodes.length; j++){
							n = nodes[j];
							n.setAttribute("__" + a, n.getAttribute(a));
							n.removeAttribute(a);
							_this._lazyNodes.push(n);
						}
					});
				}
			});
		}

		ready(function(){
			for(var i = 0; i < _this._lazyNodes.length; i++){ /* 1.8 */
				var n = _this._lazyNodes[i];
				array.forEach(["dojoType", "data-dojo-type"], function(a){
					if(n.getAttribute("__" + a)){
						n.setAttribute(a, n.getAttribute("__" + a));
						n.removeAttribute("__" + a);
					}
				});
			}
			delete _this._lazyNodes;

		});

		this.instantiateLazyWidgets = function(root, requires, callback){
			// summary:
			//		Instantiates dojo widgets under the root node.
			// description:
			//		Finds DOM nodes that have the dojoType or data-dojo-type attributes,
			//		requires the found Dojo modules, and runs the parser.
			var d = new Deferred();
			var req = requires ? requires.split(/,/) : [];
			var nodes = root.getElementsByTagName("*"); // avoid use of dojo.query
			var len = nodes.length;
			for(var i = 0; i < len; i++){
				var s = nodes[i].getAttribute("dojoType") || nodes[i].getAttribute("data-dojo-type");
				if(s){
					req.push(s);
					var m = nodes[i].getAttribute("data-dojo-mixins"),
						mixins = m ? m.split(/, */) : [];
					req = req.concat(mixins);
				}
			}
			if(req.length === 0){ return true; }

			if(dojo.require){
				array.forEach(req, function(c){
					dojo["require"](c);
				});
				dojo.parser.parse(root);
				if(callback){ callback(root); }
				return true;
			}else{
				req = array.map(req, function(s){ return s.replace(/\./g, "/"); });
				require(req, function(){
					dojo.parser.parse(root);
					if(callback){ callback(root); }
					d.resolve(true);
				});
			}
			return d;
		}	
	};

	// Return singleton.  (TODO: can we replace LazyLoadUtils class and singleton w/a simple hash of functions?)
	return new LazyLoadUtils();
});


},
'edu/wohaoo/mobile/EnrollView':function(){
define([
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/EnrollView.html",
    "dojo/i18n!./nls/EnrollView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
	
		dataId: 0,
		transactionCodeText: "",
		logoText: i18n.logo,
		descText: "",
		amountText: "",
		telephoneText: "",
		enrollText: i18n.enroll,
		delay: 1000,
		redirectTo: "",
		
		enrollPane: null,
		transactionCode: null,
		enrollButton: null,
		
		enrollMessage: null,
		amountInput: null,
		telephoneInput: null,
		transactionCode: null,
		
		//payment
		paymentDest: null,
		_setPaymentDestAttr: function(d){
			this.telephoneInput.domNode.placeholder = d;
		},
		paymentMessage: null,
		_setPaymentMessageAttr: function(m){
			this.enrollMessage.containerNode.innerHTML = m;
		}, 
		paymentAmount: null,
		_setPaymentAmountAttr: function(a){
			this.amountInput.domNode.placeholder = a;
		}, 
		
		paymentInput: null,
		_setPaymentInputAttr: function(d){
			this.transactionCode.domNode.placeholder = d;
		}, 
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.enrollButton.domNode, 'click', function(e){
				console.debug("enroll clicked");
				var transactionCode = self.transactionCode.get('value');
				self.__doEnroll(transactionCode);
				e.stopPropagation();
				e.preventDefault(); 
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doEnroll: function(transactionCode){
			var self = this;
			console.debug("creating new student enrollment");
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[Curriculum][id]":  self.dataId,
					"data[Payment][pay_transaction_id]":  transactionCode
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("enroll request returned");
				if(!response.studentid || !response.courseid ){
					console.debug("there was an error during enrollment:" + response.error);
					Sys.message(response.error);
				}
				else{
					console.debug("enrollment was successfull");
					var redirectToWidget = dijitRegistry.byId(self.redirectTo);
					redirectToWidget.refresh();
					redirectToWidget.animate();
					self.moveTo(self.id, self.redirectTo);
				}
			}, function(error){
				self.alert(self.errors.err101);
				console.debug("error occurred:" + error.toString());
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.enrollPane.domNode, 'mblHidden')){
				dojoClass.add(this.enrollPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.enrollPane.domNode, 'mblHidden')){
					dojoClass.remove(self.enrollPane.domNode, 'mblHidden');
				}
				
				dojoQuery(".company-logo", self.domNode).forEach(function(element){
					dojoClass.remove(element, 'center');
				});
			}, this.delay);
		},
		
		destroyDescendants: function(/*Boolean*/ preserveDom){
			this.inherited(arguments);
		},

		destroy: function(){
			this.inherited(arguments);
		}
	});

});
},
'dijit/_WidgetsInTemplateMixin':function(){
define([
	"dojo/_base/array", // forEach()
	"dojo/aspect", // after()
	"dojo/_base/declare", // declare()
	"dojo/_base/lang",	// hitch()
	"dojo/parser" // parse()
], function(array, aspect, declare, lang, parser){

	// module:
	//		dijit/_WidgetsInTemplateMixin

	return declare("dijit._WidgetsInTemplateMixin", null, {
		// summary:
		//		Mixin to supplement _TemplatedMixin when template contains widgets

		// _earlyTemplatedStartup: Boolean
		//		A fallback to preserve the 1.0 - 1.3 behavior of children in
		//		templates having their startup called before the parent widget
		//		fires postCreate. Defaults to 'false', causing child widgets to
		//		have their .startup() called immediately before a parent widget
		//		.startup(), but always after the parent .postCreate(). Set to
		//		'true' to re-enable to previous, arguably broken, behavior.
		_earlyTemplatedStartup: false,

		// widgetsInTemplate: [protected] Boolean
		//		Should we parse the template to find widgets that might be
		//		declared in markup inside it?  (Remove for 2.0 and assume true)
		widgetsInTemplate: true,

		// contextRequire: Function
		//		Used to provide a context require to the dojo/parser in order to be
		//		able to use relative MIDs (e.g. `./Widget`) in the widget's template.
		contextRequire: null,

		_beforeFillContent: function(){
			if(this.widgetsInTemplate){
				// Before copying over content, instantiate widgets in template
				var node = this.domNode;

				if(this.containerNode && !this.searchContainerNode){
					// Tell parse call below not to look for widgets inside of this.containerNode
					this.containerNode.stopParser = true;
				}

				parser.parse(node, {
					noStart: !this._earlyTemplatedStartup,
					template: true,
					inherited: {dir: this.dir, lang: this.lang, textDir: this.textDir},
					propsThis: this,	// so data-dojo-props of widgets in the template can reference "this" to refer to me
					contextRequire: this.contextRequire,
					scope: "dojo"	// even in multi-version mode templates use dojoType/data-dojo-type
				}).then(lang.hitch(this, function(widgets){
					this._startupWidgets = widgets;

					// _WidgetBase::destroy() will destroy any supporting widgets under this.domNode.
					// If we wanted to, we could call this.own() on anything in this._startupWidgets that was moved outside
					// of this.domNode (like Dialog, which is moved to <body>).

					// Hook up attach points and events for nodes that were converted to widgets
					for(var i = 0; i < widgets.length; i++){
						this._processTemplateNode(widgets[i], function(n,p){
							// callback to get a property of a widget
							return n[p];
						}, function(widget, type, callback){
							// callback to do data-dojo-attach-event to a widget
							if(type in widget){
								// back-compat, remove for 2.0
								return widget.connect(widget, type, callback);
							}else{
								// 1.x may never hit this branch, but it's the default for 2.0
								return widget.on(type, callback, true);
							}
						});
					}

					// Cleanup flag set above, just in case
					if(this.containerNode && this.containerNode.stopParser){
						delete this.containerNode.stopParser;
					}
				}));

				if(!this._startupWidgets){
					throw new Error(this.declaredClass + ": parser returned unfilled promise (probably waiting for module auto-load), " +
						"unsupported by _WidgetsInTemplateMixin.   Must pre-load all supporting widgets before instantiation.");
				}
			}
		},

		_processTemplateNode: function(/*DOMNode|Widget*/ baseNode, getAttrFunc, attachFunc){
			// Override _AttachMixin._processNode to skip DOMNodes with data-dojo-type set.   They are handled separately
			// in the _beforeFillContent() code above.

			if(getAttrFunc(baseNode, "dojoType") || getAttrFunc(baseNode, "data-dojo-type")){
				return true;
			}

			return this.inherited(arguments);
		},

		startup: function(){
			array.forEach(this._startupWidgets, function(w){
				if(w && !w._started && w.startup){
					w.startup();
				}
			});
			this._startupWidgets = null;
			this.inherited(arguments);
		}
	});
});

},
'dojox/mobile/ContentPane':function(){
define([
	"dojo/_base/declare",
	"./Container",
	"./_ContentPaneMixin"
], function(declare, Container, ContentPaneMixin){

	// module:
	//		dojox/mobile/ContentPane

	return declare("dojox.mobile.ContentPane", [Container, ContentPaneMixin], {
		// summary:
		//		A very simple content pane to embed an HTML fragment.
		// description:
		//		This widget embeds an HTML fragment and runs the parser. It has
		//		the ability to load external content using dojo/_base/xhr. onLoad()
		//		is called when parsing is done and the content is
		//		ready. Compared with dijit/layout/ContentPane, this widget
		//		provides only basic fuctionality, but it is much lighter.

		baseClass: "mblContentPane"
	});
});

},
'dojox/mobile/_ContentPaneMixin':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/Deferred",
	"dojo/_base/lang",
	"dojo/_base/window",
	"dojo/_base/xhr",
	"./_ExecScriptMixin",
	"./ProgressIndicator",
	"./lazyLoadUtils"
], function(declare, Deferred, lang, win, xhr, ExecScriptMixin, ProgressIndicator, lazyLoadUtils){

	// module:
	//		dojox/mobile/_ContentPaneMixin

	return declare("dojox.mobile._ContentPaneMixin", ExecScriptMixin, {
		// summary:
		//		Mixin for a very simple content pane to embed an HTML fragment.
		// description:
		//		By mixing this class into a widget, the widget can have the ability
		//		to embed an external HTML fragment and to run the parser.

		// href: String
		//		URL of the content to embed.
		href: "",

		// lazy: String
		//		If true, external content specified with the href property is
		//		not loaded at startup time. It can be loaded by calling load().
		lazy: false,

		// content: String
		//		An HTML fragment to embed.
		content: "",

		// parseOnLoad: Boolean
		//		If true, runs the parser when the load completes.
		parseOnLoad: true,

		// prog: Boolean
		//		If true, shows progress indicator while loading an HTML fragment
		//		specified by href.
		prog: true,

		// executeScripts: Boolean
		//		If true, executes scripts that is found in the content.
		executeScripts: true,

		constructor: function(){
			// summary:
			//		Creates a new instance of the class.
			// tags:
			//		private
			if(this.prog){
				this._p = ProgressIndicator.getInstance();
			}
		},

		loadHandler: function(/*String*/response){
			// summary:
			//		A handler called when load completes.
			this.set("content", response);
		},

		errorHandler: function(err){
			// summary:
			//		An error handler called when load fails.
			if(this._p){ this._p.stop(); }
		},

		load: function(){
			// summary:
			//		Loads external content specified with href.
			this.lazy = false;
			this.set("href", this.href);
		},

		onLoad: function(){
			// summary:
			//		Stub method to allow the application to connect to the
			//		loading of external content (see load()).
			//		Called when parsing is done and the content is ready.
			return true;
		},

		_setHrefAttr: function(/*String*/href){
			// tags:
			//		private
			if(this.lazy || !href || href === this._loaded){
				this.lazy = false;
				return null;
			}
			var p = this._p;
			if(p){
				win.body().appendChild(p.domNode);
				p.start();
			}
			this._set("href", href);
			this._loaded = href;
			return xhr.get({
				url: href,
				handleAs: "text",
				load: lang.hitch(this, "loadHandler"),
				error: lang.hitch(this, "errorHandler")
			});
		},

		_setContentAttr: function(/*String|DomNode*/data){
			// tags:
			//		private			
			this.destroyDescendants();
			if(typeof data === "object"){
				this.containerNode.appendChild(data);
			}else{
				if(this.executeScripts){
					data = this.execScript(data);
				}
				this.containerNode.innerHTML = data;
			}
			if(this.parseOnLoad){
				var _this = this;
				return Deferred.when(lazyLoadUtils.instantiateLazyWidgets(_this.containerNode), function(){
					if(_this._p){ _this._p.stop(); }
					return _this.onLoad();
				});
			}
			if(this._p){ this._p.stop(); }
			return this.onLoad();
		}
	});
});

},
'dojox/mobile/_ExecScriptMixin':function(){
define([
	"dojo/_base/kernel",
	"dojo/_base/declare",
	"dojo/_base/window",
	"dojo/dom-construct"
], function(kernel, declare, win, domConstruct){
	// module:
	//		dojox/mobile/_ExecScriptMixin

	return declare("dojox.mobile._ExecScriptMixin", null, {
		// summary:
		//		Mixin for providing script execution capability to content handlers.
		// description:
		//		This module defines the execScript method, which is called
		//		from an HTML content handler.

		execScript: function(/*String*/ html){
			// summary:
			//		Finds script tags and executes the script.
			// html: String
			//		The HTML input.
			// returns: String
			//		The given HTML text from which &lt;script&gt; blocks are removed.
			var s = html.replace(/\f/g, " ").replace(/<\/script>/g, "\f");
			s = s.replace(/<script [^>]*src=['"]([^'"]+)['"][^>]*>([^\f]*)\f/ig, function(ignore, path){
				domConstruct.create("script", {
					type: "text/javascript",
					src: path}, win.doc.getElementsByTagName("head")[0]);
				return "";
			});

			s = s.replace(/<script>([^\f]*)\f/ig, function(ignore, code){
				kernel.eval(code);
				return "";
			});

			return s;
		}
	});
});

},
'dojox/mobile/Button':function(){
define([
	"dojo/_base/array",
	"dojo/_base/declare",
	"dojo/dom-class",
	"dojo/dom-construct",
	"dijit/_WidgetBase",
	"dijit/form/_ButtonMixin",
	"dijit/form/_FormWidgetMixin",
	"dojo/has",
	"dojo/has!dojo-bidi?dojox/mobile/bidi/Button"
	],
	function(array, declare, domClass, domConstruct, WidgetBase, ButtonMixin, FormWidgetMixin, has, BidiButton){

	var Button = declare(has("dojo-bidi") ? "dojox.mobile.NonBidiButton" : "dojox.mobile.Button", [WidgetBase, FormWidgetMixin, ButtonMixin], {
		// summary:
		//		Non-templated BUTTON widget with a thin API wrapper for click 
		//		events and for setting the label.
		//
		//		Buttons can display a label, an icon, or both.
		//		A label should always be specified (through innerHTML) or the label
		//		attribute.  It can be hidden via showLabel=false.
		// example:
		//	|	<button data-dojo-type="dojox/mobile/Button" onClick="...">Hello world</button>

		// baseClass: String
		//		The name of the CSS class of this widget.
		baseClass: "mblButton",

		// _setTypeAttr: [private] Function 
		//		Overrides the automatic assignment of type to nodes, because it causes
		//		exception on IE. Instead, the type must be specified as this.type
		//		when the node is created, as part of the original DOM.
		_setTypeAttr: null,

		// duration: Number
		//		The duration of selection, in milliseconds, or -1 for no post-click CSS styling.
		duration: 1000,

		/*=====
		// label: String
		//		The label of the button.
		label: "",
		=====*/
		
		_onClick: function(e){
			// tags:
			//		private
			var ret = this.inherited(arguments);
			if(ret && this.duration >= 0){ // if its not a button with a state, then emulate press styles
				var button = this.focusNode || this.domNode;
				var newStateClasses = (this.baseClass+' '+this["class"]).split(" ");
				newStateClasses = array.map(newStateClasses, function(c){ return c+"Selected"; });
				domClass.add(button, newStateClasses);
				this.defer(function(){
					domClass.remove(button, newStateClasses);
				}, this.duration);
			}
			return ret;
		},

		isFocusable: function(){ 
			// Override of the method of dijit/_WidgetBase.
			return false; 
		},

		buildRendering: function(){
			if(!this.srcNodeRef){
				this.srcNodeRef = domConstruct.create("button", {"type": this.type});
			}else if(this._cv){
				var n = this.srcNodeRef.firstChild;
				if(n && n.nodeType === 3){
					n.nodeValue = this._cv(n.nodeValue);
				}
			}
			this.inherited(arguments);
			this.focusNode = this.domNode;
		},

		postCreate: function(){
			this.inherited(arguments);
			this.connect(this.domNode, "onclick", "_onClick");
		},

		_setLabelAttr: function(/*String*/ content){
			// tags:
			//		private
			this.inherited(arguments, [this._cv ? this._cv(content) : content]);
		}
	});

	return has("dojo-bidi") ? declare("dojox.mobile.Button", [Button, BidiButton]) : Button;
});

},
'dijit/form/_ButtonMixin':function(){
define([
	"dojo/_base/declare", // declare
	"dojo/dom", // dom.setSelectable
	"dojo/has",
	"../registry"        // registry.byNode
], function(declare, dom, has, registry){

	// module:
	//		dijit/form/_ButtonMixin

	var ButtonMixin = declare("dijit.form._ButtonMixin" + (has("dojo-bidi") ? "_NoBidi" : ""), null, {
		// summary:
		//		A mixin to add a thin standard API wrapper to a normal HTML button
		// description:
		//		A label should always be specified (through innerHTML) or the label attribute.
		//
		//		Attach points:
		//
		//		- focusNode (required): this node receives focus
		//		- valueNode (optional): this node's value gets submitted with FORM elements
		//		- containerNode (optional): this node gets the innerHTML assignment for label
		// example:
		// |	<button data-dojo-type="dijit/form/Button" onClick="...">Hello world</button>
		// example:
		// |	var button1 = new Button({label: "hello world", onClick: foo});
		// |	dojo.body().appendChild(button1.domNode);

		// label: HTML String
		//		Content to display in button.
		label: "",

		// type: [const] String
		//		Type of button (submit, reset, button, checkbox, radio)
		type: "button",

		__onClick: function(/*Event*/ e){
			// summary:
			//		Internal function to divert the real click onto the hidden INPUT that has a native default action associated with it
			// type:
			//		private
			e.stopPropagation();
			e.preventDefault();
			if(!this.disabled){
				// cannot use on.emit since button default actions won't occur
				this.valueNode.click(e);
			}
			return false;
		},

		_onClick: function(/*Event*/ e){
			// summary:
			//		Internal function to handle click actions
			if(this.disabled){
				e.stopPropagation();
				e.preventDefault();
				return false;
			}
			if(this.onClick(e) === false){
				e.preventDefault();
			}
			var cancelled = e.defaultPrevented;

			// Signal Form/Dialog to submit/close.  For 2.0, consider removing this code and instead making the Form/Dialog
			// listen for bubbled click events where evt.target.type == "submit" && !evt.defaultPrevented.
			if(!cancelled && this.type == "submit" && !(this.valueNode || this.focusNode).form){
				for(var node = this.domNode; node.parentNode; node = node.parentNode){
					var widget = registry.byNode(node);
					if(widget && typeof widget._onSubmit == "function"){
						widget._onSubmit(e);
						e.preventDefault(); // action has already occurred
						cancelled = true;
						break;
					}
				}
			}

			return !cancelled;
		},

		postCreate: function(){
			this.inherited(arguments);
			dom.setSelectable(this.focusNode, false);
		},

		onClick: function(/*Event*/ /*===== e =====*/){
			// summary:
			//		Callback for when button is clicked.
			//		If type="submit", return true to perform submit, or false to cancel it.
			// type:
			//		callback
			return true;		// Boolean
		},

		_setLabelAttr: function(/*String*/ content){
			// summary:
			//		Hook for set('label', ...) to work.
			// description:
			//		Set the label (text) of the button; takes an HTML string.
			this._set("label", content);
			var labelNode = this.containerNode || this.focusNode;
			labelNode.innerHTML = content;
		}
	});

	if(has("dojo-bidi")){
		ButtonMixin = declare("dijit.form._ButtonMixin", ButtonMixin, {
			_setLabelAttr: function(){
				this.inherited(arguments);
				var labelNode = this.containerNode || this.focusNode;
				this.applyTextDir(labelNode);
			}
		});
	}

	return ButtonMixin;
});

},
'dojox/mobile/TextBox':function(){
define([
	"dojo/_base/declare",
	"dojo/dom-construct",
	"dijit/_WidgetBase",
	"dijit/form/_FormValueMixin",
	"dijit/form/_TextBoxMixin",
	"dojo/has",
	"dojo/has!dojo-bidi?dojox/mobile/bidi/TextBox"
], function(declare, domConstruct, WidgetBase, FormValueMixin, TextBoxMixin, has, BidiTextBox){

	var TextBox = declare(has("dojo-bidi") ? "dojox.mobile.NonBidiTextBox" : "dojox.mobile.TextBox", [WidgetBase, FormValueMixin, TextBoxMixin],{
		// summary:
		//		A non-templated base class for textbox form inputs

		baseClass: "mblTextBox",

		// Override automatic assigning type --> node, it causes exception on IE8.
		// Instead, type must be specified as this.type when the node is created, as part of the original DOM
		_setTypeAttr: null,

		// Map widget attributes to DOMNode attributes.
		_setPlaceHolderAttr: function(/*String*/value){
			value = this._cv ? this._cv(value) : value;
			this._set("placeHolder", value);
			this.textbox.setAttribute("placeholder", value);
		},

		buildRendering: function(){
			if(!this.srcNodeRef){
				this.srcNodeRef = domConstruct.create("input", {"type":this.type});
			}
			this.inherited(arguments);
			this.textbox = this.focusNode = this.domNode;
		},

		postCreate: function(){
			this.inherited(arguments);
			this.connect(this.textbox, "onmouseup", function(){ this._mouseIsDown = false; });
			this.connect(this.textbox, "onmousedown", function(){ this._mouseIsDown = true; });
			this.connect(this.textbox, "onfocus", function(e){
				this._onFocus(this._mouseIsDown ? "mouse" : e);
				this._mouseIsDown = false;
			});
			this.connect(this.textbox, "onblur", "_onBlur");
		}
	});
	return has("dojo-bidi") ? declare("dojox.mobile.TextBox", [TextBox, BidiTextBox]) : TextBox;	
});

},
'dijit/form/_TextBoxMixin':function(){
define([
	"dojo/_base/array", // array.forEach
	"dojo/_base/declare", // declare
	"dojo/dom", // dom.byId
	"dojo/has",
	"dojo/keys", // keys.ALT keys.CAPS_LOCK keys.CTRL keys.META keys.SHIFT
	"dojo/_base/lang", // lang.mixin
	"dojo/on", // on
	"../main"    // for exporting dijit._setSelectionRange, dijit.selectInputText
], function(array, declare, dom, has, keys, lang, on, dijit){

	// module:
	//		dijit/form/_TextBoxMixin

	var _TextBoxMixin = declare("dijit.form._TextBoxMixin" + (has("dojo-bidi") ? "_NoBidi" : ""), null, {
		// summary:
		//		A mixin for textbox form input widgets

		// trim: Boolean
		//		Removes leading and trailing whitespace if true.  Default is false.
		trim: false,

		// uppercase: Boolean
		//		Converts all characters to uppercase if true.  Default is false.
		uppercase: false,

		// lowercase: Boolean
		//		Converts all characters to lowercase if true.  Default is false.
		lowercase: false,

		// propercase: Boolean
		//		Converts the first character of each word to uppercase if true.
		propercase: false,

		// maxLength: String
		//		HTML INPUT tag maxLength declaration.
		maxLength: "",

		// selectOnClick: [const] Boolean
		//		If true, all text will be selected when focused with mouse
		selectOnClick: false,

		// placeHolder: String
		//		Defines a hint to help users fill out the input field (as defined in HTML 5).
		//		This should only contain plain text (no html markup).
		placeHolder: "",

		_getValueAttr: function(){
			// summary:
			//		Hook so get('value') works as we like.
			// description:
			//		For `dijit/form/TextBox` this basically returns the value of the `<input>`.
			//
			//		For `dijit/form/MappedTextBox` subclasses, which have both
			//		a "displayed value" and a separate "submit value",
			//		This treats the "displayed value" as the master value, computing the
			//		submit value from it via this.parse().
			return this.parse(this.get('displayedValue'), this.constraints);
		},

		_setValueAttr: function(value, /*Boolean?*/ priorityChange, /*String?*/ formattedValue){
			// summary:
			//		Hook so set('value', ...) works.
			//
			// description:
			//		Sets the value of the widget to "value" which can be of
			//		any type as determined by the widget.
			//
			// value:
			//		The visual element value is also set to a corresponding,
			//		but not necessarily the same, value.
			//
			// formattedValue:
			//		If specified, used to set the visual element value,
			//		otherwise a computed visual value is used.
			//
			// priorityChange:
			//		If true, an onChange event is fired immediately instead of
			//		waiting for the next blur event.

			var filteredValue;
			if(value !== undefined){
				// TODO: this is calling filter() on both the display value and the actual value.
				// I added a comment to the filter() definition about this, but it should be changed.
				filteredValue = this.filter(value);
				if(typeof formattedValue != "string"){
					if(filteredValue !== null && ((typeof filteredValue != "number") || !isNaN(filteredValue))){
						formattedValue = this.filter(this.format(filteredValue, this.constraints));
					}else{
						formattedValue = '';
					}
				}
			}
			if(formattedValue != null /* and !undefined */ && ((typeof formattedValue) != "number" || !isNaN(formattedValue)) && this.textbox.value != formattedValue){
				this.textbox.value = formattedValue;
				this._set("displayedValue", this.get("displayedValue"));
			}

			this.inherited(arguments, [filteredValue, priorityChange]);
		},

		// displayedValue: String
		//		For subclasses like ComboBox where the displayed value
		//		(ex: Kentucky) and the serialized value (ex: KY) are different,
		//		this represents the displayed value.
		//
		//		Setting 'displayedValue' through set('displayedValue', ...)
		//		updates 'value', and vice-versa.  Otherwise 'value' is updated
		//		from 'displayedValue' periodically, like onBlur etc.
		//
		//		TODO: move declaration to MappedTextBox?
		//		Problem is that ComboBox references displayedValue,
		//		for benefit of FilteringSelect.
		displayedValue: "",

		_getDisplayedValueAttr: function(){
			// summary:
			//		Hook so get('displayedValue') works.
			// description:
			//		Returns the displayed value (what the user sees on the screen),
			//		after filtering (ie, trimming spaces etc.).
			//
			//		For some subclasses of TextBox (like ComboBox), the displayed value
			//		is different from the serialized value that's actually
			//		sent to the server (see `dijit/form/ValidationTextBox.serialize()`)

			// TODO: maybe we should update this.displayedValue on every keystroke so that we don't need
			// this method
			// TODO: this isn't really the displayed value when the user is typing
			return this.filter(this.textbox.value);
		},

		_setDisplayedValueAttr: function(/*String*/ value){
			// summary:
			//		Hook so set('displayedValue', ...) works.
			// description:
			//		Sets the value of the visual element to the string "value".
			//		The widget value is also set to a corresponding,
			//		but not necessarily the same, value.

			if(value == null /* or undefined */){
				value = ''
			}
			else if(typeof value != "string"){
				value = String(value)
			}

			this.textbox.value = value;

			// sets the serialized value to something corresponding to specified displayedValue
			// (if possible), and also updates the textbox.value, for example converting "123"
			// to "123.00"
			this._setValueAttr(this.get('value'), undefined);

			this._set("displayedValue", this.get('displayedValue'));
		},

		format: function(value /*=====, constraints =====*/){
			// summary:
			//		Replaceable function to convert a value to a properly formatted string.
			// value: String
			// constraints: Object
			// tags:
			//		protected extension
			return value == null /* or undefined */ ? "" : (value.toString ? value.toString() : value);
		},

		parse: function(value /*=====, constraints =====*/){
			// summary:
			//		Replaceable function to convert a formatted string to a value
			// value: String
			// constraints: Object
			// tags:
			//		protected extension

			return value;	// String
		},

		_refreshState: function(){
			// summary:
			//		After the user types some characters, etc., this method is
			//		called to check the field for validity etc.  The base method
			//		in `dijit/form/TextBox` does nothing, but subclasses override.
			// tags:
			//		protected
		},

		 onInput: function(/*===== event =====*/){
			 // summary:
			 //		Connect to this function to receive notifications of various user data-input events.
			 //		Return false to cancel the event and prevent it from being processed.
			 // event:
			 //		keydown | keypress | cut | paste | input
			 // tags:
			 //		callback
		 },

		__skipInputEvent: false,
		_onInput: function(/*Event*/ evt){
			// summary:
			//		Called AFTER the input event has happened

			this._processInput(evt);

			if(this.intermediateChanges){
				// allow the key to post to the widget input box
				this.defer(function(){
					this._handleOnChange(this.get('value'), false);
				});
			}
		},

		_processInput: function(/*Event*/ evt){
			// summary:
			//		Default action handler for user input events

			this._refreshState();

			// In case someone is watch()'ing for changes to displayedValue
			this._set("displayedValue", this.get("displayedValue"));
		},

		postCreate: function(){
			// setting the value here is needed since value="" in the template causes "undefined"
			// and setting in the DOM (instead of the JS object) helps with form reset actions
			this.textbox.setAttribute("value", this.textbox.value); // DOM and JS values should be the same

			this.inherited(arguments);

			// normalize input events to reduce spurious event processing
			//	onkeydown: do not forward modifier keys
			//		       set charOrCode to numeric keycode
			//	onkeypress: do not forward numeric charOrCode keys (already sent through onkeydown)
			//	onpaste & oncut: set charOrCode to 229 (IME)
			//	oninput: if primary event not already processed, set charOrCode to 229 (IME), else do not forward
			var handleEvent = function(e){
				var charOrCode;
				if(e.type == "keydown"){
					charOrCode = e.keyCode;
					switch(charOrCode){ // ignore state keys
						case keys.SHIFT:
						case keys.ALT:
						case keys.CTRL:
						case keys.META:
						case keys.CAPS_LOCK:
						case keys.NUM_LOCK:
						case keys.SCROLL_LOCK:
							return;
					}
					if(!e.ctrlKey && !e.metaKey && !e.altKey){ // no modifiers
						switch(charOrCode){ // ignore location keys
							case keys.NUMPAD_0:
							case keys.NUMPAD_1:
							case keys.NUMPAD_2:
							case keys.NUMPAD_3:
							case keys.NUMPAD_4:
							case keys.NUMPAD_5:
							case keys.NUMPAD_6:
							case keys.NUMPAD_7:
							case keys.NUMPAD_8:
							case keys.NUMPAD_9:
							case keys.NUMPAD_MULTIPLY:
							case keys.NUMPAD_PLUS:
							case keys.NUMPAD_ENTER:
							case keys.NUMPAD_MINUS:
							case keys.NUMPAD_PERIOD:
							case keys.NUMPAD_DIVIDE:
								return;
						}
						if((charOrCode >= 65 && charOrCode <= 90) || (charOrCode >= 48 && charOrCode <= 57) || charOrCode == keys.SPACE){
							return; // keypress will handle simple non-modified printable keys
						}
						var named = false;
						for(var i in keys){
							if(keys[i] === e.keyCode){
								named = true;
								break;
							}
						}
						if(!named){
							return;
						} // only allow named ones through
					}
				}
				charOrCode = e.charCode >= 32 ? String.fromCharCode(e.charCode) : e.charCode;
				if(!charOrCode){
					charOrCode = (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 48 && e.keyCode <= 57) || e.keyCode == keys.SPACE ? String.fromCharCode(e.keyCode) : e.keyCode;
				}
				if(!charOrCode){
					charOrCode = 229; // IME
				}
				if(e.type == "keypress"){
					if(typeof charOrCode != "string"){
						return;
					}
					if((charOrCode >= 'a' && charOrCode <= 'z') || (charOrCode >= 'A' && charOrCode <= 'Z') || (charOrCode >= '0' && charOrCode <= '9') || (charOrCode === ' ')){
						if(e.ctrlKey || e.metaKey || e.altKey){
							return;
						} // can only be stopped reliably in keydown
					}
				}
				if(e.type == "input"){
					if(this.__skipInputEvent){ // duplicate event
						this.__skipInputEvent = false;
						return;
					}
				}else{
					this.__skipInputEvent = true;
				}
				// create fake event to set charOrCode and to know if preventDefault() was called
				var faux = { faux: true }, attr;
				for(attr in e){
					if(attr != "layerX" && attr != "layerY"){ // prevent WebKit warnings
						var v = e[attr];
						if(typeof v != "function" && typeof v != "undefined"){
							faux[attr] = v;
						}
					}
				}
				lang.mixin(faux, {
					charOrCode: charOrCode,
					_wasConsumed: false,
					preventDefault: function(){
						faux._wasConsumed = true;
						e.preventDefault();
					},
					stopPropagation: function(){
						e.stopPropagation();
					}
				});
				// give web page author a chance to consume the event
				//console.log(faux.type + ', charOrCode = (' + (typeof charOrCode) + ') ' + charOrCode + ', ctrl ' + !!faux.ctrlKey + ', alt ' + !!faux.altKey + ', meta ' + !!faux.metaKey + ', shift ' + !!faux.shiftKey);
				if(this.onInput(faux) === false){ // return false means stop
					faux.preventDefault();
					faux.stopPropagation();
				}
				if(faux._wasConsumed){
					return;
				} // if preventDefault was called
				this.defer(function(){
					this._onInput(faux);
				}); // widget notification after key has posted
				if(e.type == "keypress"){
					e.stopPropagation(); // don't allow parents to stop printables from being typed
				}
			};
			this.own(on(this.textbox, "keydown, keypress, paste, cut, input, compositionend", lang.hitch(this, handleEvent)));
		},

		_blankValue: '', // if the textbox is blank, what value should be reported
		filter: function(val){
			// summary:
			//		Auto-corrections (such as trimming) that are applied to textbox
			//		value on blur or form submit.
			// description:
			//		For MappedTextBox subclasses, this is called twice
			//
			//		- once with the display value
			//		- once the value as set/returned by set('value', ...)
			//
			//		and get('value'), ex: a Number for NumberTextBox.
			//
			//		In the latter case it does corrections like converting null to NaN.  In
			//		the former case the NumberTextBox.filter() method calls this.inherited()
			//		to execute standard trimming code in TextBox.filter().
			//
			//		TODO: break this into two methods in 2.0
			//
			// tags:
			//		protected extension
			if(val === null){
				return this._blankValue;
			}
			if(typeof val != "string"){
				return val;
			}
			if(this.trim){
				val = lang.trim(val);
			}
			if(this.uppercase){
				val = val.toUpperCase();
			}
			if(this.lowercase){
				val = val.toLowerCase();
			}
			if(this.propercase){
				val = val.replace(/[^\s]+/g, function(word){
					return word.substring(0, 1).toUpperCase() + word.substring(1);
				});
			}
			return val;
		},

		_setBlurValue: function(){
			// Format the displayed value, for example (for NumberTextBox) convert 1.4 to 1.400,
			// or (for CurrencyTextBox) 2.50 to $2.50

			this._setValueAttr(this.get('value'), true);
		},

		_onBlur: function(e){
			if(this.disabled){
				return;
			}
			this._setBlurValue();
			this.inherited(arguments);
		},

		_isTextSelected: function(){
			return this.textbox.selectionStart != this.textbox.selectionEnd;
		},

		_onFocus: function(/*String*/ by){
			if(this.disabled || this.readOnly){
				return;
			}

			// Select all text on focus via click if nothing already selected.
			// Since mouse-up will clear the selection, need to defer selection until after mouse-up.
			// Don't do anything on focus by tabbing into the widget since there's no associated mouse-up event.
			if(this.selectOnClick && by == "mouse"){
				// Use on.once() to only select all text on first click only; otherwise users would have no way to clear
				// the selection.
				this._selectOnClickHandle = on.once(this.domNode, "mouseup, touchend", lang.hitch(this, function(evt){
					// Check if the user selected some text manually (mouse-down, mouse-move, mouse-up)
					// and if not, then select all the text
					if(!this._isTextSelected()){
						_TextBoxMixin.selectInputText(this.textbox);
					}
				}));
				this.own(this._selectOnClickHandle);

				// in case the mouseup never comes
				this.defer(function(){
					if(this._selectOnClickHandle){
						this._selectOnClickHandle.remove();
						this._selectOnClickHandle = null;
					}
				}, 500); // if mouseup not received soon, then treat it as some gesture
			}
			// call this.inherited() before refreshState(), since this.inherited() will possibly scroll the viewport
			// (to scroll the TextBox into view), which will affect how _refreshState() positions the tooltip
			this.inherited(arguments);

			this._refreshState();
		},

		reset: function(){
			// Overrides `dijit/_FormWidget/reset()`.
			// Additionally resets the displayed textbox value to ''
			this.textbox.value = '';
			this.inherited(arguments);
		}
	});

	if(has("dojo-bidi")){
		_TextBoxMixin = declare("dijit.form._TextBoxMixin", _TextBoxMixin, {
			_setValueAttr: function(){
				this.inherited(arguments);
				this.applyTextDir(this.focusNode);
			},
			_setDisplayedValueAttr: function(){
				this.inherited(arguments);
				this.applyTextDir(this.focusNode);
			},
			_onInput: function(){
				this.applyTextDir(this.focusNode);
				this.inherited(arguments);
			}
		});
	}

	_TextBoxMixin._setSelectionRange = dijit._setSelectionRange = function(/*DomNode*/ element, /*Number?*/ start, /*Number?*/ stop){
		if(element.setSelectionRange){
			element.setSelectionRange(start, stop);
		}
	};

	_TextBoxMixin.selectInputText = dijit.selectInputText = function(/*DomNode*/ element, /*Number?*/ start, /*Number?*/ stop){
		// summary:
		//		Select text in the input element argument, from start (default 0), to stop (default end).

		// TODO: use functions in _editor/selection.js?
		element = dom.byId(element);
		if(isNaN(start)){
			start = 0;
		}
		if(isNaN(stop)){
			stop = element.value ? element.value.length : 0;
		}
		try{
			element.focus();
			_TextBoxMixin._setSelectionRange(element, start, stop);
		}catch(e){ /* squelch random errors (esp. on IE) from unexpected focus changes or DOM nodes being hidden */
		}
	};

	return _TextBoxMixin;
});

},
'edu/wohaoo/mobile/ExamView':function(){
/** SPECS
	The view shows the exam of the given module.
	The server will check if the user has already completed the exam.
	If the user has already completed the exam, the view shows the users score along with the exam comment (passed, failed).
		The user is then asked wether he/she would want to retake the exam. If yes, a new exam session is created and
		the first question of the exam is runned.
	If the user has not already  already completed the exam, the first question of the exam session is returned and
	the list of possible answers (options) is displayed.
	When the user clicks on the "Next Question" Button, the view is refreshed.
 */
define([
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem", "dijit/registry",
    "dojo/i18n!./nls/Messages",
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem, dijitRegistry, messages) {
	return declare([ItemPageView],{
		listNode: null,
		sessionId:  0,
		questionId: 0,
		optionId: 0,
		forceNew: 0,
		completed: false,
		messages: messages,
		
		/**
		 examItem => {
			model => 'exam',
			id,
			score,
			passed,
			completed
		 }
		 */
		 
		 /**
		 questionItem => {
			model => 'question',
			id,
			text
		 }
		 */
		 
		  /**
		 optionItem => {
			model => 'option',
			id,
			text
		 }
		 */
		questionNode: null,
		examDetailsNode: null,
		examScoreNode: null,
		examCommentNode: null,
		examNextButtonNode: null,
		examRetakeButtonNode: null,
		 
		postCreate: function(){
			this.itemTemplateString =  '';
			this.itemClass = 'question';
			var self = this;
			
			//showFavoritesButton
			dojoQuery(".questionCategory", this.domNode).forEach(function(element){
				self.questionNode = element;
			});
			
			dojoQuery(".examNextButton", this.domNode).forEach(function(element){
				self.examNextButtonNode = element;
			});
			
			dojoQuery(".examRetakeButton", this.domNode).forEach(function(element){
				self.examRetakeButtonNode = element;
			});
			
			dojoQuery(".examDetails", this.domNode).forEach(function(element){
				self.examDetailsNode = element;
			});
			
			
			dojoQuery("span.examScore", self.examDetailsNode ).forEach(function(element){
				self.examScoreNode = element;
			});
			
			
			dojoQuery("span.examComment", self.examDetailsNode ).forEach(function(element){
				self.examCommentNode = element;
			});
			
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			var self = this;
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
			
			this.query = {id: "*", model: "exam"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(examItems){
				dojoArray.forEach(examItems, function(examItem, index){
					self.sessionId = self._store.getValue(examItem, "uniqueid");
					var text = self._store.getValue(examItem, "text");
					self.completed = self._store.getValue(examItem, "completed");
					if(self.completed){
						if(domClass.contains(self.examDetailsNode, "mblHidden")){
							var score = self._store.getValue(examItem, "score");
							self.examScoreNode.innerText = score.toString();
							var passed = self._store.getValue(examItem, "passed");
							if(passed)
								self.examCommentNode.innerText = "Passed";
							else
								self.examCommentNode.innerText = "Failed";
							domClass.remove(self.examDetailsNode, 'mblHidden');
						}
					}else{
						if(!domClass.contains(self.examDetailsNode, "mblHidden")){
							domClass.add(self.examDetailsNode, 'mblHidden');
						}
					}
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			this.query = {id: "*", model: "question"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(questionItems){
				dojoArray.forEach(questionItems, function(questionItem, index){
					self.questionId = self._store.getValue(questionItem, "uniqueid");
					var text = self._store.getValue(questionItem, "text");
					self.questionNode.innerHTML = text;
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			
			this.query = {id: "*", model: "option"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(optionItems){
				dojoArray.forEach(optionItems, function(optionItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(optionItem, "uniqueid"),
						'variableHeight': true
					});
					
					item.placeAt(self.listNode,"first");
					
					var text = self._store.getValue(optionItem, "text");
					
					item.labelNode.innerHTML = text;
					
					if(item._checkEvent){
						item._checkEvent.remove();
						item._checkEvent = null;
					}
					item._checkEvent = dojoConnect.connect(item.domNode, 'click', 
						function(e){
							var itemWidget = dijitRegistry.byNode(this);
							self.optionId = itemWidget.dataId;
						});
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			//next question button
			if(self.examNextButtonNode._clickEvent){
				self.examNextButtonNode._clickEvent.remove();
				self.examNextButtonNode._clickEvent = null;
			}
			self.examNextButtonNode._clickEvent = dojoConnect.connect(self.examNextButtonNode, 'click', function(e){
				if(!self.completed){
					self.refresh();
				}else{
					dijitRegistry.byId('dlg_message').show(self.messages.EXAM_IS_COMPLETED);
				}
			});
			
			//retake  button
			if(self.examRetakeButtonNode._clickEvent){
				self.examRetakeButtonNode._clickEvent.remove();
				self.examRetakeButtonNode._clickEvent = null;
			}
			self.examRetakeButtonNode._clickEvent = dojoConnect.connect(self.examRetakeButtonNode, 'click', function(e){
				dijitRegistry.byId('dlg_confirm').show(self.messages.CONFIRM_EXAM_RETAKE, function(){
								self.forceNew = 1;
								self.refresh();
								});
			});
		}
	
	});
});
},
'edu/wohaoo/mobile/ItemPageView':function(){
define([
    "dojo/query!css3", "dojo/_base/connect", "dojo/on", "dojo/_base/declare",  "dojox/mobile/ScrollableView",
     "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/_AppViewMixin", "dijit/registry", "dojo/data/ItemFileReadStore",
     "dojo/domReady!"
], function(dojoQuery, dojoConnect, on, declare, MobileScrollableView, _WidgetBase, domConstruct, _AppViewMixin, 
	dijitRegistry, ItemFileReadStore) {
	return declare([MobileScrollableView, _AppViewMixin],{
		headerNode: null,
		itemHeaderTemplateString:  '<img width=32 height=32 src="${icon}" alt="${name}" class="headerImage pull-left" />' + 
				'<div class="headerDetails"><div class="name">${short_name}</div>'  +
				'</div>',
		dataUrl:null,
		itemModel: '',
		itemPrefix: "",
		
		postCreate: function(){
			this.inherited(arguments);
		},
		
		startup: function(){
			this.inherited(arguments);
			/*var itemId = Sys.getActiveItemId();
			if(itemId){
				this.dataId = itemId;
			}*/
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.domNode).forEach(function(element){
				element.style.display = 'none';
			});
		},
		
		/**
		 storeItem => {
			model => 'curriculum' || 'module',
			id,
			icon,
			code,
			name,
			short_name
		 }
		 */
		refresh: function (){
			this.inherited(arguments);
			
			if(!this.dataId)
				return;
			var self = this;
			this.query = {id: this.itemPrefix + this.dataId.toString()};
			dojoQuery(".header", this.domNode).forEach(function(element){
				self.headerNode = element;
			});
			
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			this.processDataParams();
			
			this._store = new ItemFileReadStore({url: this.dataUrl, hierarchical: true});
			
			if(!this._store || this._store === null || this.itemHeaderTemplateString === null || this.itemHeaderTemplateString === '')
				return;
			
			this._store.fetch({query: this.query, queryOptions: {deep: false}, onComplete: function(storeItems){
				for(var i in storeItems){
					var storeItem = storeItems[i];
					var icon = self._store.getValue(storeItem, "icon");
					if(icon && icon.indexOf("://") === -1){
						var httpScheme = Sys.getHttpScheme();
						var httpHost = Sys.getHttpHost();
						if(httpScheme && httpHost){
							icon = httpScheme + "://" + httpHost  + icon;
						}
					}
					storeItem.icon = icon;
					
					// Update the list item's content using our template for items
					self.headerNode.innerHTML = self.substitute(self.itemHeaderTemplateString, storeItem);
					
					self.itemName = self._store.getValue(storeItem, 'name');
					
					self.itemModel = self._store.getValue(storeItem, 'model');
				}
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug("error=" + error);
			}});
		}
	
	});
});
},
'edu/wohaoo/mobile/ItemAudioView':function(){
/**
 *SPECS:
	The view shows the list of audios of the current curriculum. One audio under the other.
	When the user clicks on the back button, the curriculum menu page is shown.
 */
define([
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
    "dojox/mobile/compat", "dojox/mobile/Audio",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem, MobileCompat, MobileAudio) {
	return declare([ItemPageView],{
		listNode: null,
		
		/**
		 storeItem => {
			model => 'audio',
			id,
			mp3,
			ogg,
			wav,
			embed,
			text
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '<source src="${mp3}" type="audio/mpeg">' +
		'<source src="${ogg}" type="audio/ogg">' +
		'<source src="${wav}" type="audio/wav">' +
		'<p>${text}</p>';
			this.itemClass = 'audio';
			
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "audio"};
			var self = this;
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
			
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(storeItem, "uniqueid"),
						'variableHeight': true
					}).placeAt(self.listNode,"first");
					
					var embed = self._store.getValue(storeItem, "embed");
					
					if(!embed){
						var audioItem = new MobileAudio({}).placeAt(item.domNode, 'first');
				
						// Update the audio item's content using our template for items
						audioItem.domNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
					}else{
						item.domNode.innerHTML = embed;
					}
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		}
	
	});
});
},
'dojox/mobile/compat':function(){
define([
	"dojo/_base/lang",
	"dojo/sniff"
], function(lang, has){
	// module:
	//		dojox/mobile/compat

	var dm = lang.getObject("dojox.mobile", true);
	// TODO: Use feature detection instead, but this would require a major rewrite of _compat
	// to detect each feature and plug the corresponding compat code if needed.
	// Currently the compat code is a workaround for too many different things to be able to
	// decide based on feature detection. So for now we just disable _compat on the mobile browsers
	// that are known to support enough CSS3: all webkit-based browsers, IE10 (Windows [Phone] 8) and IE11+.
	if(!(has("webkit") || has("ie") === 10) || (!has("ie") && has("trident") > 6)){
		var s = "dojox/mobile/_compat"; // assign to a variable so as not to be picked up by the build tool
		require([s]);
	}
	
	/*=====
	return {
		// summary:
		//		CSS3 compatibility module.
		// description:
		//		This module provides to dojox/mobile support for some of the CSS3 features 
		//		in non-CSS3 browsers, such as IE or Firefox.
		//		If you require this module, when running in a non-CSS3 browser it directly 
		//		replaces some of the methods of	dojox/mobile classes, without any subclassing. 
		//		This way, HTML pages remain the same regardless of whether this compatibility 
		//		module is used or not.
		//
		//		Example of usage: 
		//		|	require([
		//		|		"dojox/mobile",
		//		|		"dojox/mobile/compat",
		//		|		...
		//		|	], function(...){
		//		|		...
		//		|	});
		//
		//		This module also loads compatibility CSS files, which have a -compat.css
		//		suffix. You can use either the `<link>` tag or `@import` to load theme
		//		CSS files. Then, this module searches for the loaded CSS files and loads
		//		compatibility CSS files. For example, if you load dojox/mobile/themes/iphone/iphone.css
		//		in a page, this module automatically loads dojox/mobile/themes/iphone/iphone-compat.css.
		//		If you explicitly load iphone-compat.css with `<link>` or `@import`,
		//		this module will not load again the already loaded file.
		//
		//		Note that, by default, compatibility CSS files are only loaded for CSS files located
		//		in a directory containing a "mobile/themes" path. For that, a matching is done using 
		//		the default pattern	"/\/mobile\/themes\/.*\.css$/". If a custom theme is not located 
		//		in a directory containing this path, the data-dojo-config needs to specify a custom 
		//		pattern using the "mblLoadCompatPattern" configuration parameter, for instance:
		// |	data-dojo-config="mblLoadCompatPattern: /\/mycustomtheme\/.*\.css$/"
	};
	=====*/
	return dm;
});

},
'dojox/mobile/Audio':function(){
define([
	"dojo/_base/declare",
	"dojo/dom-construct",
	"dojo/sniff",
	"dijit/_Contained",
	"dijit/_WidgetBase"
], function(declare, domConstruct, has, Contained, WidgetBase){
	// module:
	//		dojox/mobile/Audio

	return declare("dojox.mobile.Audio", [WidgetBase, Contained], {
		// summary:
		//		A thin wrapper around the HTML5 `<audio>` element.
		// description:
		//		dojox/mobile/Audio is a widget which plays audio. If all sources cannot 
		//		be played (typically, in desktop browsers that do not support `<audio>`), 
		//		dojox/mobile/Audio automatically replaces `<audio>` with `<embed>`, such 
		//		that the browser tries to play it with a suitable plug-in.
		
		// source: [const] Array
		//		An array of src and type,
		//		ex. [{src:"a.mp3", type:"audio/mpeg"}, {src:"a.ogg", type:"audio/ogg"}, ...].
		//		The src gives the path of the media resource. The type gives the
		//		type of the media resource.
		//		Note that changing the value of the property after the widget
		//		creation has no effect.
		source: null,

		// width: [const] String
		//		The width of the embed element. 
		//		Note that changing the value of the property after the widget
		//		creation has no effect.
		width: "200px",

		// height: [const] String
		//		The height of the embed element.
		//		Note that changing the value of the property after the widget
		//		creation has no effect.
		height: "15px",

		// _playable: [private] Boolean
		//		Internal flag.
		_playable: false,
		
		// _tag: [private] String
		//		The name of the tag ("audio").
		_tag: "audio",

		constructor: function(){
			// summary:
			//		Creates a new instance of the class.
			this.source = [];
		},

		buildRendering: function(){
			this.domNode = this.srcNodeRef || domConstruct.create(this._tag);
		},

		_getEmbedRegExp: function(){
			// tags:
			//		private
			return has('ff') ? /audio\/mpeg/i :
				   has('ie') ? /audio\/wav/i :
				   null;
		},

		startup: function(){
			if(this._started){ return; }
			this.inherited(arguments);
			var i;
		 	if(this.domNode.canPlayType){
				if(this.source.length > 0){
					for(i = 0, len = this.source.length; i < len; i++){
						domConstruct.create("source", {src:this.source[i].src, type:this.source[i].type}, this.domNode);
						this._playable = this._playable || !!this.domNode.canPlayType(this.source[i].type);
					}
				}else{
					for(i = 0, len = this.domNode.childNodes.length; i < len; i++){
						var n = this.domNode.childNodes[i];
						if(n.nodeType === 1 && n.nodeName === "SOURCE"){
							this.source.push({src:n.src, type:n.type});
							this._playable = this._playable || !!this.domNode.canPlayType(n.type);
						}
					}
				}
			}
			has.add("mobile-embed-audio-video-support", true);	//It should move to staticHasFeatures
		 	if(has("mobile-embed-audio-video-support")){
				if(!this._playable){
					for(i = 0, len = this.source.length, re = this._getEmbedRegExp(); i < len; i++){
					 	if(this.source[i].type.match(re)){
							var node = domConstruct.create("embed", {
								src: this.source[0].src,
								type: this.source[0].type,
								width: this.width,
								height: this.height
							});
							this.domNode.parentNode.replaceChild(node, this.domNode);
							this.domNode = node;
							this._playable = true;
							break;
						}
					}
				}
			}
		}

	});
});

},
'edu/wohaoo/mobile/ItemDocumentView':function(){
/**
 *SPECS:
	The view shows the list of documents of the current curriculum. One document under the other.
	When the user clicks on the back button, the curriculum menu page is shown.
  */
define([
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem) {
	return declare([ItemPageView],{
		listNode: null,
		
		/**
		 storeItem => {
			model => 'document',
			source
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '<iframe id="viewer" src ="${source}" ' +
    	     			'width="320" height="240"' + 
    	     			'allowfullscreen webkitallowfullscreen></iframe>';
			this.itemClass = 'audio';
			
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "document"};
			var self = this;
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
			
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(storeItem, "uniqueid"),
						'variableHeight': true
					}).placeAt(self.listNode,"first");
					
					var pdf = self._store.getValue(storeItem, "pdf");
					
					var embed = self._store.getValue(storeItem, "embed");

					if(pdf){
						 storeItem.source = '/ViewerJS/#' + pdf;
						// Update the audio item's content using our template for items
						item.labelNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
					}else{				
						// Update the audio item's content using our template for items
						item.labelNode.innerHTML = embed;
					}
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		}
	
	});
});
},
'edu/wohaoo/mobile/ItemInfoView':function(){
/**
 *<!-- SPECS:
	The view shows the list of attributes of the current [item].
	When the user clicks on the back button, the [item] menu page is shown.
*/
define([
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem) {
	return declare([ItemPageView],{
		listNode: null,
		postCreate: function(){
			this.itemTemplateString = '<div class="attributeName">${name}</div>' +
				'<div class="attributeValue">${value}</div>' ;
			this.itemClass = 'attribute';
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "attribute"};
			var self = this;
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
			/**
			 storeItem => {
			 	model => 'attribute',
			 	id,
			 	name,
			 	value
			 }
			 */
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(storeItem, "uniqueid"),
						'variableHeight': true
					}).placeAt(self.listNode,"first");
				
					// Update the list item's content using our template for items
					item.labelNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		}
	
	});
});
},
'edu/wohaoo/mobile/ItemMenuPageView':function(){
/**
 * SPECS:
	The view shows a four button menubar (2 cols and 2 rows): info, documents, videos, and audios.
	When the user clicks on the info button, the [item] info view is shown.
	When the user clicks on the documents button, the [item]  documents view is shown.
	When the user clicks on the videos button, the [item]  videos view is shown.
	When the user clicks on the audios button, the [item]  audios view is shown.
	At the bottom of the view there is a toolbar containing only one button: the "List" (modules, exams) button.
	When the user clicks on the "List" button the list of modules of the current [item]  is shown.
 
 */
define([
     "dojo/dom-class", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/registry",  "edu/wohaoo/mobile/ItemPageView", "dojo/i18n!./nls/Messages",
     "dojo/domReady!"
], function(domClass, dojoQuery, dojoConnect, declare, _WidgetBase, domConstruct, dijitRegistry, 
		ItemPageView, sysMessages) {
	return declare([ItemPageView],{
		addFavoriteButton: null,
		sysMessages: sysMessages,
		
		postCreate: function(){
			this.inherited(arguments);
		},
		refresh: function (){
			this.inherited(arguments);
			var self = this;
			dojoQuery(".iconMenu", this.domNode).forEach(function(element){
				if(element._clickEvent)
					dojoConnect.disconnect(element._clickEvent);
				element._clickEvent = dojoConnect.connect(element, "click", function(evt){
					var menuItem = dijitRegistry.byNode(this);
					var moveToWidget = dijitRegistry.byId(menuItem.moveTo);
					moveToWidget.set('dataId', self.dataId);
					moveToWidget.set('model', self.itemModel);
					moveToWidget.refresh();
					self.moveTo(self.id, menuItem.moveTo, menuItem.transition);
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
			
			dojoQuery(".nextButton", this.domNode).forEach(function(element){
				if(element._clickEvent)
					dojoConnect.disconnect(element._clickEvent);
				element._clickEvent = dojoConnect.connect(element, "click", function(evt){
					var menuItem = dijitRegistry.byNode(this);
					var moveToWidget = dijitRegistry.byId(menuItem.moveTo);
					moveToWidget.set('dataId', self.dataId);
					moveToWidget.refresh();
					self.moveTo(self.id, menuItem.moveTo, menuItem.transition);
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
			
			//addFavoriteButton
			dojoQuery(".addFavoriteButton", this.domNode).forEach(function(button){
				if(!self.itemModel || self.itemModel !== 'curriculum') return;
				
				self.addFavoriteButton = button;
				
				var favoriteStr =  "";
				favoriteStr = Sys.getCurriculumFavorites();	
				
				var dataId = self.dataId;
				var regex = dataId + '($|\,)';
				var is_favorite = favoriteStr.match(regex);
				if(is_favorite){
					domClass.add(self.addFavoriteButton, 'favorite');
				}else
				if(domClass.contains(self.addFavoriteButton, "favorite")){
					domClass.remove(self.addFavoriteButton, 'favorite');
				}
				
				if(self.addFavoriteButton._clickEvent)
					self.addFavoriteButton._clickEvent.remove();
				self.addFavoriteButton._clickEvent = null;
				self.addFavoriteButton._clickEvent = dojoConnect.connect(self.addFavoriteButton, "click", function(evt){
					var favoriteStr = Sys.getCurriculumFavorites();
					var favorites = favoriteStr.split(',');
					var notempty= function(elt){ return elt !== "";};
					favorites = favorites.filter(notempty);
					if(domClass.contains(self.addFavoriteButton, "favorite")){
						domClass.remove(self.addFavoriteButton, 'favorite');
						//remove current curriculum from favorites list;
						var notself = function(elt){ return elt !== self.dataId.toString();};
						favorites = favorites.filter(notself);
						favoriteStr = favorites.join(',');
						Sys.setCurriculumFavorites(favoriteStr);
						Sys.message(self.sysMessages.FavoriteRemoved.format(self.itemName));
					}else{
						domClass.add(self.addFavoriteButton, 'favorite');
						//Set curriculum favorite date
						Sys.setCurriculumFavoriteDate(dataId.toString(), new Date().getTime().toString());
						//add current curriculum from favorites list;
						favorites[favorites.length] = self.dataId.toString();
						favoriteStr = favorites.join(',');
						Sys.setCurriculumFavorites(favoriteStr);
						Sys.message(self.sysMessages.FavoriteAdded.format(self.itemName));
					}
					evt.stopPropagation();
					evt.preventDefault();
				});
			});
		}
	
	});
});
},
'edu/wohaoo/mobile/ItemVideoView':function(){
/**
	SPECS:
	The view shows the list of videos of the current curriculum. One video under the other.
	When the user clicks on the back button, the curriculum menu page is shown.
   */
define([
    "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
    "dojox/mobile/compat", "dojox/mobile/Video",
     "dojo/domReady!"
], function(dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem, MobileCompat, MobileVideo) {
	return declare([ItemPageView],{
		listNode: null,
		
		/**
		 storeItem => {
			model => 'video',
			id,
			mp4,
			ogv,
			webm,
			embed,
			text
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '<source src="${mp4}" type="video/mp4">'+
		'<source src="${ogv}" type="video/ogg">'+
		'<source src="${webm}" type="video/webm">'+
		'<p>${text}</p>';
			this.itemClass = 'video';
			
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			this.query = {id: "*", model: "video"};
			var self = this;
			
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
			
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(storeItem, "uniqueid"),
						'variableHeight': true
					}).placeAt(self.listNode,"first");
					
					var embed = self._store.getValue(storeItem, "embed");
					if(!embed){
						var videoItem = new MobileVideo({
							"width":  320,
							"height" : 240
						}).placeAt(item.domNode, 'first');
						// Update the video item's content using our template for items
						videoItem.domNode.innerHTML = self.substitute(self.itemTemplateString, storeItem);
					}else{
						item.domNode.innerHTML = embed;
					}
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		}
	
	});
});
},
'dojox/mobile/Video':function(){
define([
	"dojo/_base/declare",
	"dojo/sniff",
	"./Audio"
], function(declare, has, Audio){
	// module:
	//		dojox/mobile/Video

	return declare("dojox.mobile.Video", Audio, {
		// summary:
		//		A thin wrapper around the HTML5 `<video>` element.
		// description:
		//		dojox/mobile/Video is a widget which plays video. If all sources cannot 
		//		be played (typically, in desktop browsers that do not support `<video>`), 
		//		dojox/mobile/Video automatically replaces `<video>` with `<embed>`, such 
		//		that the browser tries to play it with a suitable plug-in.
		
		// width: [const] String
		//		The width of the embed element.
		//		Note that changing the value of the property after the widget
		//		creation has no effect.
		width: "200px",

		// height: [const] String
		//		The height of the embed element.
		//		Note that changing the value of the property after the widget
		//		creation has no effect.
		height: "150px",

		// _tag: [private] String
		//		The name of the tag ("video").
		_tag: "video",

		_getEmbedRegExp: function(){
			return has("ff") ? /video\/mp4/i :
				   has("ie") >= 9 ? /video\/webm/i :
				   //has("safari") ? /video\/webm/i : //Google is gooing to provide webm plugin for safari
				   null;
		}
	});
});

},
'edu/wohaoo/mobile/LanguageDialogView':function(){
/** SPECS
	this view enables the user to setup personal prefrerences:
		-first name
		-last name
		-birth date
		-language
 */
define([
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/_AppViewMixin",  "dojox/mobile/SimpleDialog", "dijit/registry",  
    "dojo/i18n!./nls/Messages",   "dojo/text!./templates/LanguageDialogView.html", "dojo/data/ItemFileReadStore",
    "dijit/_TemplatedMixin",  "dijit/_WidgetsInTemplateMixin", "dojox/mobile/ListItem",
    "dojox/mobile/EdgeToEdgeList", "dojox/mobile/Button" ,
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, _AppViewMixin, SimpleDialog, dijitRegistry, messages, Template, ItemFileReadStore,
	_TemplatedMixin, _WidgetsInTemplateMixin, MobileListItem) {
	return declare([SimpleDialog, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin],{
		templateString: Template,
		messages: messages,
		okButton: null,
		optionsList: null,
		serviceUrl: "",
		
		LanguageDialogTitle: messages.LANGUAGE_SELECTION,
		OKButton: messages.OK,
		
		messages: messages,
		
		callback: null,
		
		languageCode: null,
		
		languageDictionary: null,
		
		langItems: null,
		
		_setLanguageCodeAttr: function(v){
			if(v!== null && v){
				this._set('LanguageCode', v);
				this.langItems[v].set('checked', 'true');
			}else{
				this._set('LanguageCode', '');
			}
		},
		
		map: function(code){
			if(code !== null && code)
				return this.languageDictionary[code];
			return '';
		},
		 
		postCreate: function(){
			var self = this;
			this.itemTemplateString =  "";
			this.itemClass = 'langItem';
			
			this.languageDictionary = [];
			
			this.inherited(arguments);
			
			if(this.okButton){
				if(this.okButton.domNode._clickEvent)
					dojoConnect.disconnect(this.okButton.domNode._clickEvent);
				this.okButton.domNode._clickEvent = dojoConnect.connect(this.okButton.domNode, 'click', function(evt){
					self.callback.call(self, self.LanguageCode);
					self.hide();
				});
			}
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.optionsList.domNode).forEach(function(element){
				element.style.display = 'none';
			});
		},
		
		/**
		 storeItem => {
			model => 'language',
			id,
			code,
			name
		 }
		 */
		refresh: function (){
			var self = this;
			this.query = {id: "*", model: "language"};
			
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			
			this._store = new ItemFileReadStore({url: this.dataUrl, hierarchical: true});
			
			if(!this._store || this._store === null )
				return;
			
			this._store.fetch({query: this.query, queryOptions: {deep: false}, 
				onBegin: function(){
					self.hideItems();
				},
				onComplete: function(storeItems){
				self.langItems = [];
				
				for(var i in storeItems){
					var storeItem = storeItems[i];
					var code = self._store.getValue(storeItem, "code");
					var name = self._store.getValue(storeItem, "name");
					
					self.languageDictionary[code] = name;
					
					var item = new MobileListItem({
								"code": code,
								'label': name,
								'class' : self.itemClass
							});
							
					self.langItems[code] = item;
					
					if(code === self.LanguageCode)
						item.set('checked', 'true');
					
					item.placeAt(self.optionsList.domNode,"last");
					
					storeItem.code = code;
					
					// Update the list item's content using our template for items
					//item.labelNode.innerHTML = item.labelNode.innerHTML+
					//		 self.substitute(self.itemTemplateString, storeItem);
							 
					
					if(item.domNode._clickEvent)
						dojoConnect.disconnect(item.domNode._clickEvent);
					item.domNode._clickEvent = dojoConnect.connect(item.domNode, 'click', function(evt){
						var itemWidget = dijitRegistry.byNode(this);
						self.LanguageCode = itemWidget.get('code');
					});
					
				}
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug("error=" + error);
			}});
			SimpleDialog.prototype.refresh.call(this, arguments);
		},
		
		show: function(cb){
			this.callback = cb;
			this.inherited(arguments);
		}
	});
});
},
'edu/wohaoo/mobile/LoginView':function(){
define([
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/LoginView.html",
    "dojo/i18n!./nls/LoginView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
		telephoneText: i18n.telephone,
		passwordText: i18n.password,
		logoText: __t("WOHAOO Education"),
		loginText: i18n.login,
		delay: 1500,
		redirectTo: "",
		
		loginPane: null,
		telephone: null,
		password: null,
		loginButton: null,
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.loginButton.domNode, 'click', function(e){
				console.debug("login clicked");
				var telephone = self.telephone.get('value');
				var password = self.password.get('value');
				self.__doLogin(telephone, password);
				e.stopPropagation();
				e.preventDefault(); 
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doLogin: function(telephone, password){
			var self = this;
			console.debug("authenticating user against url:" + this.serviceUrl);
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[User][usr_telephone]":  telephone,
					"data[User][usr_password]": password
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("auth request returned");
				if(!response.userid){
					console.debug("there was an error during login:" + response.toString());
					Sys.message(response.error);
				}
				else{
					console.debug("login was successfull");
					Sys.setUser(response.userid);
					var redirectToWidget = dijitRegistry.byId(self.redirectTo);
					redirectToWidget.refresh();
					self.moveTo(self.id, self.redirectTo);
				}
			}, function(error){
				self.alert(self.errors.err101);
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.loginPane.domNode, 'mblHidden')){
				dojoClass.add(this.loginPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.loginPane.domNode, 'mblHidden')){
					dojoClass.remove(self.loginPane.domNode, 'mblHidden');
				}
				
				dojoQuery(".company-logo", self.domNode).forEach(function(element){
					dojoClass.remove(element, 'center');
				});
			}, this.delay);
		},
		
		destroyDescendants: function(/*Boolean*/ preserveDom){
			this.inherited(arguments);
		},

		destroy: function(){
			this.inherited(arguments);
		}
	});

});
},
'edu/wohaoo/mobile/LogoutView':function(){
define([
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/LogoutView.html",
    "dojo/i18n!./nls/LogoutView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
		logoText: "WOHAOO Education",
		logoutText: i18n.logout,
		delay: 100,
		redirectTo: "",
		
		logoutPane: null,
		logoutButton: null,
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.logoutButton.domNode, 'click', function(e){
				console.debug("logout clicked");
				var userid = Sys.getUser();
				self.__doLogout(userid);
				e.stopPropagation();
				e.preventDefault(); 
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doLogout: function(userid){
			var self = this;
			console.debug("logging user out");
			
			function logOut(){
				Sys.setUser("");
				var redirectToWidget = dijitRegistry.byId(self.redirectTo);
				redirectToWidget.refresh();
				self.moveTo(self.id, self.redirectTo);
			}
			
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[User][id]":  userid
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("logout request returned");
				if(response.error){
					console.debug("there was an error during  logout:" + response.error);
					Sys.message(response.error);
				}
				else{
					console.debug("logout was successfull");
					logOut();
				}
			}, function(error){
				self.alert(self.errors.err101);
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.logoutPane.domNode, 'mblHidden')){
				dojoClass.add(this.logoutPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.logoutPane.domNode, 'mblHidden')){
					dojoClass.remove(self.logoutPane.domNode, 'mblHidden');
				}
				
				dojoQuery(".company-logo", self.domNode).forEach(function(element){
					dojoClass.remove(element, 'center');
				});
			}, this.delay);
		},
		
		destroyDescendants: function(/*Boolean*/ preserveDom){
			this.inherited(arguments);
		},

		destroy: function(){
			this.inherited(arguments);
		}
	});

});
},
'edu/wohaoo/mobile/ModulesView':function(){
/**
 * SPECS
	The view shows the list of modules of a given curriculum.
	When the user clicks on the back button, the curriculum menu page is shown.
 */
define([
    "dojo/_base/array", "dijit/registry",  "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/ItemPageView", "dojox/mobile/ListItem",
     "dojo/domReady!"
], function(dojoArray, dijitRegistry, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, ItemPageView, MobileListItem) {
	return declare([ItemPageView],{
		listNode: null,
		
		courseId: null,
		paymentDest: null,
		paymentAmount: null,
		paymentMessage: null,
		paymentInput: null,
		
		/**
		 storeItem => {
			model => 'module',
			id,
			icon,
			name,
			passed
		 }
		 */
		 
		postCreate: function(){
			this.itemTemplateString =  '';
			this.itemClass = 'module';
			this.itemMoveTo = "modulePage";
			this.itemTransition = 'slide';
			this.inherited(arguments);
		},
		
		refresh: function (){
			this.inherited(arguments);
			if(!this._store || this._store === null)
				return;
			var self = this;
			dojoQuery(".itemList", this.domNode).forEach(function(element){
				self.listNode = element;
			});
				
			this.query = {id: this.itemPrefix + this.dataId, model: "curriculum"};
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(curItems){
				dojoArray.forEach(curItems, function(curItem, index){
					self.courseId = self._store.getValue(curItem, "course_id");
					self.paymentDest = self._store.getValue(curItem, "payment_dest");
					self.paymentAmount = self._store.getValue(curItem, "enroll_fees");
					self.paymentMessage = self._store.getValue(curItem, "payment_message");
					self.paymentInput = self._store.getValue(curItem, "payment_input");
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
			
			this.query = {id: "*", model: "module"};
			
			this._store.fetch({query: this.query, queryOptions: {deep: true}, 
			onBegin: function(){
				self.hideItems();
			},
			onComplete: function(storeItems){
				dojoArray.forEach(storeItems, function(storeItem, index){
					// Create a new list item, inject into list
					var item = new MobileListItem({
						"class": self.itemClass,
						"dataId": self._store.getValue(storeItem, "uniqueid"),
						'variableHeight': true
					});
					
					var passed = self._store.getValue(storeItem, "passed");
					item.passed = 0;
					
					if(Sys.getUser()){
						if(passed)
							item.set('checked', 'true');
						item.passed = passed;
					}
					
					item.previous = self._store.getValue(storeItem, "previous");
					item.previous_passed = self._store.getValue(storeItem, "previous_passed");
					
					item.placeAt(self.listNode,"last");
					
					var name = self._store.getValue(storeItem, "name");
					
					item.labelNode.innerText = name;
					
					if(item.domNode._clickEvent)
						dojoConnect.disconnect(item.domNode._clickEvent);
					item.domNode._clickEvent = dojoConnect.connect(item.domNode, 'click', function(evt){
						var user = Sys.getUser();
						var itemWidget = dijitRegistry.byNode(this);
						if(!user){
							evt.stopPropagation();
							evt.preventDefault();
							dijitRegistry.byId('dlg_login_register').show();
							return;
						}else{
							if(!self.courseId){
								evt.stopPropagation();
								evt.preventDefault();
								
								dijitRegistry.byId('enroll').set('paymentDest', self.paymentDest);
								dijitRegistry.byId('enroll').set('paymentMessage', self.paymentMessage);
								dijitRegistry.byId('enroll').set('paymentAmount', self.paymentAmount);
								dijitRegistry.byId('enroll').set('paymentInput', self.paymentInput);
								
								dijitRegistry.byId('dlg_join_curriculum').show();
							}else{
								if(itemWidget.passed){
									//module is already pased
									//don't show it
									dijitRegistry.byId('dlg_message').show(self.messages.MODULE_IS_COMPLETED);
								}else{
									debugger;
									if(itemWidget.previous && !itemWidget.previous_passed){
										dijitRegistry.byId('dlg_message').show(
												self.messages.PREVIOUS_MODULE_MUST_BE_COMPLETED.format(
												itemWidget.previous) );
									}else{
										var moveToWidget = dijitRegistry.byId(self.itemMoveTo);
										moveToWidget.set('dataId', itemWidget.dataId);
										moveToWidget.refresh();
										self.moveTo(self.id, self.itemMoveTo, self.itemTransition);
									}
								}
							}
						}
					});
				});
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug('error=' + error.toString());
			}});
		},
		
		showView: function(dialog, viewName, transition){
			dijitRegistry.byId(dialog).hide();
			var moveToWidget = dijitRegistry.byId(viewName);
			moveToWidget.set('redirectTo', this.id);
			moveToWidget.set('dataId', this.dataId);
			moveToWidget.animate();
			this.moveTo(this.id, viewName, transition);
		}
	});
});
},
'edu/wohaoo/mobile/PersonDataView':function(){
/** SPECS
	this view enables the user to setup personal prefrerences:
		-first name
		-last name
		-birth date
 */
define([
    "dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct", "dijit/_TemplatedMixin",  "dijit/_WidgetsInTemplateMixin", "edu/wohaoo/mobile/_AppViewMixin", 
     "dojox/mobile/SimpleDialog", "dijit/registry",  
    "dojo/i18n!./nls/Messages",   "dojo/text!./templates/PersonDataView.html", "dojo/data/ItemFileReadStore",
    "dojo/date/stamp", "dojox/mobile/Heading",
    "dojox/mobile/ToolBarButton" , "dojox/mobile/DatePicker" , 
    "dojox/mobile/Button",  "dojox/mobile/TextBox","dojox/mobile/Opener",
     "dojo/domReady!"
], function(domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin, SimpleDialog, 
	dijitRegistry, messages, template, ItemFileReadStore, stamp, MobileHeading,
	MobileToolBarButton, MobileDatePicker) {
	return declare([SimpleDialog, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin],{
		templateString: template,
		messages: messages,
		SettingsHeaderText: messages.PERSONAL_DETAILS,
		FirstNameLabel: messages.FIRST_NAME,
		LastNameLabel: messages.LAST_NAME,
		BirthDateLabel:  messages.BIRTH_DATE,
		OKButton: messages.OK,
		CancelButton: messages.CANCEL,
		
		FirstNameInput: null,
		LastNameInput: null,
		BirthDateInput: null,
		BirthDatePicker: null,
		BirthDateOpener: null,
		datePicker: null,
		okButton: null,
		cancelButton: null,
		serviceUrl: "",
		
		messages: messages,
		
		callback: null,
		
		FisrtName: null,
		LastName: null,
		BirthDate: null,
		
		_setFirstNameAttr: function(n){
			this._set('FirstName', n);
			this.FirstNameInput.set('value', n);
		},
		_setLastNameAttr: function(n){
			this._set('LastName', n);
			this.LastNameInput.set('value',  n);
		},
		
		_setBirthDateAttr: function(d){
			if(d !== null){
				this._set('BirthDate', d);
				this.BirthDateInput.set('value', d);
			}else
				this._set('BirthDate', '');
		},
		 
		postCreate: function(){
			var self = this;
			this.itemTemplateString =  "";
			
			this.inherited(arguments);
			
			//this.datePicker.set("value", stamp.toISOString(new Date(), {selector: "date"}));
			if(this.BirthDateInput){
				if(this.BirthDateInput.domNode._clickEvent)
					dojoConnect.disconnect(this.BirthDateInput.domNode._clickEvent);
				this.BirthDateInput.domNode._clickEvent = dojoConnect.connect(this.BirthDateInput.domNode, 'click', function(toto){
					self.onShowBirthDatePicker(toto);
				});
			}
		},
		
		hideItems: function(){
			dojoQuery("." + this.itemClass, this.domNode).forEach(function(element){
				element.style.display = 'none';
			});
		},
		
		/**
		 storeItem => {
			model => 'language',
			id,
			code,
			name
		 }
		 */
		refresh: function (){
			SimpleDialog.prototype.refresh.call(this, arguments);
		},
		
		onShowBirthDatePicker: function(evt){
			var self = this;
			domConstruct.empty(this.BirthDateOpener.containerNode);
			
			var heading = MobileHeading({
				'label': __t("Date Picker")
			}).placeAt(this.BirthDateOpener.containerNode, 'first');
			
			var button1 = new MobileToolBarButton({
				"label" : __t('Done'),
				"onClick":  function(e){
					self.onBirthDateDone();
				},
				'class':"mblColorBlue",
				'style' : 'position:absolute;width:45px;right:0;'
			}).placeAt(heading.domNode, 'last');
			
			var button2 = new MobileToolBarButton({
				"label" : __t('Cancel'),
				"onClick": function(e){
					self.onBirthDateCancel();
				},
				'class':"mblColorBlue",
				'style' : "position:absolute;width:45px;left:0;"
			}).placeAt(heading.domNode, 'last');
			
			this.datePicker = new MobileDatePicker({
				value: '2000-01-01'
			}).placeAt(this.BirthDateOpener.containerNode, 'last');
			
			this.BirthDateOpener.startup();
			this.BirthDateOpener.show(this.BirthDateInput.domNode, ['below-centered','above-centered','after','before']);
		},
		
		onBirthDateShow: function(){
			this.datePicker.set('value', this.BirthDateInput.get('value'));
		},
		onBirthDateHide: function(node, v){
			if(!v)
				return;
		},
		onBirthDateDone: function(){
			this.BirthDateOpener.hide(true);
			this.set('BirthDate', this.datePicker.get("value"));
		},
		onBirthDateCancel: function(){
			this.BirthDateOpener.hide(false);
		},
		onOk: function(){
			this.callback.call(this, this.FirstName, this.LastName, this.BirthDate);
			this.hide();
		},
		
		onCancel: function(){
			this.hide();
		},
		
		show: function(cb){
			this.callback = cb;
			this.inherited(arguments);
		}
	});
});
},
'dojox/mobile/DatePicker':function(){
define([
	"dojo/_base/lang",
	"./_PickerChooser!DatePicker"
], function(lang, DatePicker){

	// module:
	//		dojox/mobile/DatePicker

	// TODO: need to list all the properties/methods in the interface provided by
	// SpinWheelDatePicker / ValuePickerDatePicker
		
	/*=====
	return function(){
		// summary:
		//		A wrapper widget around SpinWheelDatePicker or ValuePickerDatePicker.
		//		It should be used with the automatic theme loader, dojox/mobile/deviceTheme.
		//		Returns ValuePickerDatePicker when the current theme is "android" or "holodark".
		//		Returns SpinWheelDatePicker otherwise.
	};
	=====*/
	return lang.setObject("dojox.mobile.DatePicker", DatePicker);
});

},
'dojox/mobile/_PickerChooser':function(){
define([
	"dojo/_base/lang",
	"dojo/_base/window"
], function(lang, win){

	// module:
	//		dojox/mobile/_PickerChooser

	return{
		// summary:
		//		This widget chooses a picker class according to the current theme.
		//		Imports ValuePicker-based date/time picker when the current theme is "android".
		//		Imports SpinWheel-based date/time picker otherwise.

		load: function (id, parentRequire, loaded){
			// summary:
			//		Imports a picker class according to the current theme.
			var dm = win.global._no_dojo_dm || lang.getObject("dojox.mobile", true);
			parentRequire([(dm.currentTheme === "android" || dm.currentTheme === "holodark" ? "./ValuePicker" : "./SpinWheel") + id], loaded);
		}
	};
});

},
'dojox/mobile/Opener':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/Deferred",
	"dojo/_base/lang",
	"dojo/_base/window",
	"dojo/dom-class",
	"dojo/dom-construct",
	"dojo/dom-style",
	"dojo/dom-geometry",
	"./Tooltip",
	"./Overlay",
	"./lazyLoadUtils"
], function(declare, Deferred, lang, win, domClass, domConstruct, domStyle, domGeometry, Tooltip, Overlay, lazyLoadUtils){

	var isOverlay = domClass.contains(win.doc.documentElement, "dj_phone");
	
	var cls = declare("dojox.mobile.Opener", isOverlay ? Overlay : Tooltip, {
		// summary:
		//		A non-templated popup widget that will use either Tooltip or 
		//		Overlay depending on screen size.

		// lazy: String
		//		If true, the content of the widget, which includes dojo markup,
		//		is instantiated lazily. That is, only when the widget is opened
		//		by the user, the required modules are loaded and the content
		//		widgets are instantiated.
		lazy: false,

		// requires: String
		//		Comma-separated required module names to be lazily loaded. This
		//		is effective only when lazy=true. All the modules specified with
		//		dojoType and their depending modules are automatically loaded
		//		when the widget is opened. However, if you need other extra
		//		modules to be loaded, use this parameter.
		requires: "",

		buildRendering: function(){
			this.inherited(arguments);
			this.cover = domConstruct.create('div', {
				onclick: lang.hitch(this, '_onBlur'), 'class': 'mblOpenerUnderlay',
				style: { position: isOverlay ? 'absolute' : 'fixed', backgroundColor:'transparent', overflow:'hidden', zIndex:'-1' }
			}, this.domNode, 'first');
		},

		onShow: function(/*DomNode*/node){},
		onHide: function(/*DomNode*/node, /*Anything*/v){},

		show: function(node, positions){
			if(this.lazy){
				this.lazy = false;
				var _this = this;
				return Deferred.when(lazyLoadUtils.instantiateLazyWidgets(this.domNode, this.requires), function(){
					return _this.show(node, positions);
				});
			}
			this.node = node;
			this.onShow(node);
			domStyle.set(this.cover, { top:'0px', left:'0px', width:'0px', height:'0px' }); // move cover temporarily to calculate domNode vertical position correctly
			this._resizeCover(domGeometry.position(this.domNode, false)); // must be before this.inherited(arguments) for Tooltip sizing
			return this.inherited(arguments);
		},

		hide: function(/*Anything*/ val){
			this.inherited(arguments);
			this.onHide(this.node, val);
		},
		
		_reposition: function(){
			// tags:
			//		private
			var popupPos = this.inherited(arguments);
			this._resizeCover(popupPos);
			return popupPos;
		},

		_resizeCover: function(popupPos){
			// tags:
			//		private
			if(isOverlay){
				if(parseInt(domStyle.get(this.cover, 'top')) != -popupPos.y || parseInt(domStyle.get(this.cover, 'height')) != popupPos.y){
					var x = Math.max(popupPos.x, 0); // correct onorientationchange values
					domStyle.set(this.cover, { top:-popupPos.y+'px', left:-x+'px', width:popupPos.w+x+'px', height:popupPos.y+'px' });
				}
			}else{
				domStyle.set(this.cover, { 
					width:Math.max(win.doc.documentElement.scrollWidth || win.body().scrollWidth || win.doc.documentElement.clientWidth)+'px', 
					height:Math.max(win.doc.documentElement.scrollHeight || win.body().scrollHeight || win.doc.documentElement.clientHeight)+'px' 
				});
			}			
		},

		_onBlur: function(e){
			// tags:
			//		private
			var ret = this.onBlur(e);
			if(ret !== false){ // only exactly false prevents hide()
				this.hide(e);
			}
			return ret;
		}
	});
	cls.prototype.baseClass += " mblOpener"; // add to either mblOverlay or mblTooltip
	return cls;
});

},
'dojox/mobile/Tooltip':function(){
define([
	"dojo/_base/array", // array.forEach
	"dijit/registry",
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/dom-class",
	"dojo/dom-construct",
	"dojo/dom-geometry",
	"dojo/dom-style",
	"dijit/place",
	"dijit/_WidgetBase",
	"dojo/has",
	"dojo/has!dojo-bidi?dojox/mobile/bidi/Tooltip"
], function(array, registry, declare, lang, domClass, domConstruct, domGeometry, domStyle, place, WidgetBase, has, BidiTooltip){

	var Tooltip = declare(has("dojo-bidi") ? "dojox.mobile.NonBidiTooltip" : "dojox.mobile.Tooltip", WidgetBase, {
		// summary:
		//		A non-templated popup bubble widget

		baseClass: "mblTooltip mblTooltipHidden",

		buildRendering: function(){
			// create the helper nodes here in case the user overwrote domNode.innerHTML
			this.inherited(arguments);
			this.anchor = domConstruct.create("div", {"class":"mblTooltipAnchor"}, this.domNode, "first");
			this.arrow = domConstruct.create("div", {"class":"mblTooltipArrow"}, this.anchor);
			this.innerArrow = domConstruct.create("div", {"class":"mblTooltipInnerArrow"}, this.anchor);
			if(!this.containerNode){
				// set containerNode so that getChildren() works
				this.containerNode = this.domNode;
			}
		},

		show: function(/*DomNode*/ aroundNode, /*Array*/positions){
			// summary:
			//		Pop up the tooltip and point to aroundNode using the best position
			// positions:
			//		Ordered list of positions to try matching up.
			//
			//		- before-centered: places drop down before the aroundNode
			//		- after-centered: places drop down after the aroundNode
			//		- above-centered: drop down goes above aroundNode
			//		- below-centered: drop down goes below aroundNode

			var domNode = this.domNode;
			var connectorClasses = {
				"MRM": "mblTooltipAfter",
				"MLM": "mblTooltipBefore",
				"BMT": "mblTooltipBelow",
				"TMB": "mblTooltipAbove",
				"BLT": "mblTooltipBelow",
				"TLB": "mblTooltipAbove",
				"BRT": "mblTooltipBelow",
				"TRB": "mblTooltipAbove",
				"TLT": "mblTooltipBefore",
				"TRT": "mblTooltipAfter",
				"BRB": "mblTooltipAfter",
				"BLB": "mblTooltipBefore"
			};
			domClass.remove(domNode, ["mblTooltipAfter","mblTooltipBefore","mblTooltipBelow","mblTooltipAbove"]);
			array.forEach(registry.findWidgets(domNode), function(widget){
				if(widget.height == "auto" && typeof widget.resize == "function"){
					if(!widget._parentPadBorderExtentsBottom){
						widget._parentPadBorderExtentsBottom = domGeometry.getPadBorderExtents(domNode).b;
					}
					widget.resize();
				}
			});
			// Convert before/after to before-centered/after-centered for compatibility
			// TODO remove this 1.7->1.8 compatibility code in 2.0
			if(positions){
				positions = array.map(positions, function(pos){
					return {after: "after-centered", before: "before-centered"}[pos] || pos;
				});
			}
			var best = place.around(domNode, aroundNode, positions || ["below-centered", "above-centered", "after-centered", "before-centered"], this.isLeftToRight());
			var connectorClass = connectorClasses[best.corner + best.aroundCorner.charAt(0)] || "";
			domClass.add(domNode, connectorClass);
			var pos = domGeometry.position(aroundNode, true);
			domStyle.set(this.anchor, (connectorClass == "mblTooltipAbove" || connectorClass == "mblTooltipBelow")
				? { top: "", left: Math.max(0, pos.x - best.x + (pos.w >> 1) - (this.arrow.offsetWidth >> 1)) + "px" }
				: { left: "", top: Math.max(0, pos.y - best.y + (pos.h >> 1) - (this.arrow.offsetHeight >> 1)) + "px" }
			);
			domClass.replace(domNode, "mblTooltipVisible", "mblTooltipHidden");
			this.resize = lang.hitch(this, "show", aroundNode, positions); // orientation changes
			return best;
		},

		hide: function(){
			// summary:
			//		Pop down the tooltip
			this.resize = undefined;
			domClass.replace(this.domNode, "mblTooltipHidden", "mblTooltipVisible");
		},

		onBlur: function(/*Event*/e){
			return true; // touching outside the overlay area does call hide() by default
		},

		destroy: function(){
			if(this.anchor){
				this.anchor.removeChild(this.innerArrow);
				this.anchor.removeChild(this.arrow);
				this.domNode.removeChild(this.anchor);
				this.anchor = this.arrow = this.innerArrow = undefined;
			}
			this.inherited(arguments);
		}
	});
	
	return has("dojo-bidi") ? declare("dojox.mobile.Tooltip", [Tooltip, BidiTooltip]) : Tooltip;		
});

},
'edu/wohaoo/mobile/SettingsView':function(){
/** SPECS
	this view enables the user to setup personal prefrerences:
		-first name
		-last name
		-birth date
		-language
 */
define([
    "dojo/request","dojo/dom-class", "dojo/_base/array", "dojo/query!css3", "dojo/_base/connect", "dojo/_base/declare",  "dijit/_WidgetBase", 
    "dojo/dom-construct",  "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", "dijit/registry",  
    "dijit/_TemplatedMixin",  "dijit/_WidgetsInTemplateMixin",
    "dojo/i18n!./nls/Messages",   "dojo/text!./templates/SettingsView.html","dojo/data/ItemFileReadStore",
     "dojo/domReady!"
], function(dojoRequest, domClass, dojoArray, dojoQuery, dojoConnect, declare, _WidgetBase, 
	domConstruct, _AppViewMixin, MobileScrollableView, dijitRegistry, 
	_TemplatedMixin, _WidgetsInTemplateMixin, messages, SettingsViewTemplate, ItemFileReadStore) {
	return declare([MobileScrollableView, _TemplatedMixin, _WidgetsInTemplateMixin, _AppViewMixin],{
		templateString: SettingsViewTemplate,
		messages: messages,
		personalDataItem: null,
		languageItem: null,
		
		messages: messages,
		
		FirstName: "",
		LastName: "",
		BirthDate: '',
		LanguageCode: '',
		
		_setFirstNameAttr : function(n){
			this._set('FirstName', n);
			this.updatePersonDetails();
		},
		
		_setLastNameAttr : function(n){
			this._set('LastName', n);
			this.updatePersonDetails();
		},
		_setLanguageCodeAttr : function(n){
			if(n !== null)
				this._set('LanguageCode', n);
			else
				this._set('LanguageCode', '');
			this.updateLanguageData();
		},
		
		_setBirthDateAttr : function(n){
			if(n !== null){
				this._set('BirthDate', n);
			}else
				this._set('BirthDate', '');
			this.updatePersonDetails();
		},
		
		updatePersonDetails: function(){
			this.personalDataItem.labelNode.innerHTML = 
				this.FirstName + ' ' + this.LastName + ', ' +  this.messages.BORN + ' ' + this.BirthDate;
		},
		
		updateLanguageData: function(){
			dlg_language_select = dijitRegistry.byId('dlg_language_select');
			if(!dlg_language_select) return;
			var langName = '';
			if(this.LanguageCode !== null)
				 langName = dlg_language_select.map(this.LanguageCode);
			this.languageItem.labelNode.innerHTML = langName;
		},
		 
		postCreate: function(){
			var self = this;
			this.inherited(arguments);
			
			this.personalDetailsData.innerHTML = __t('Personal Details');
			this.languageData.innerHTML  = __t('Preferred Language');
			
			if(this.personalDataItem){
				if(this.personalDataItem.domNode._clickEvent)
					dojoConnect.disconnect(this.personalDataItem.domNode._clickEvent);
				this.personalDataItem.domNode._clickEvent = 
				dojoConnect.connect(this.personalDataItem.domNode, 'click', function(evt){
					var dlg_person_data = dijitRegistry.byId('dlg_person_data');
					if(!dlg_person_data) return false;
					
					dlg_person_data.set('FirstName', self.FirstName);
					dlg_person_data.set('LastName', self.LastName);
					dlg_person_data.set('BirthDate', self.BirthDate);
					
					dlg_person_data.show(
						function (first_name, last_name, birth_date){
							self.set('FirstName', first_name);
							self.set('LastName', last_name);
							self.set('BirthDate', birth_date);
							self.updateData();
						}
					);
					return true;
				});
			}
			
			if(this.languageItem){
				if(this.languageItem.domNode._clickEvent)
					dojoConnect.disconnect(this.languageItem.domNode._clickEvent);
				this.languageItem.domNode._clickEvent = dojoConnect.connect(this.languageItem.domNode, 'click', function(evt){
					var dlg_language_select = dijitRegistry.byId('dlg_language_select');
					if(!dlg_language_select) return false;
					
					dlg_language_select.set('LanguageCode', self.LanguageCode);
					
					dlg_language_select.show(
						function (code){
							self.set('LanguageCode', code);
							self.updateData();
						}
					);
					return true;
				});
			}
		},
		
		updateData: function(){
			var self = this;
			console.debug("updatign settings data against url:" + this.serviceUrl);
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[Person][per_first_name]":  this.FirstName,
					"data[Person][per_last_name]": this.LastName,
					"data[Person][per_birth_date]": this.BirthDate,
					"data[User][usr_lang_code]": this.LanguageCode
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("auth request returned");
				if(!response.personid){
					console.debug("there was an error during update:" + response.toString());
					Sys.message(response.error);
				}
				else{
					console.debug("update was successfull");
				}
			}, function(error){
				self.alert(self.errors.err101);
			});
		},
		
		/**
		 storeItem => {
			model => 'user',
			id,
			first_name,
			last_name,
			birth_date,
			language_code
		 }
		 */
		refresh: function (){
			this.inherited(arguments);
		
			var self = this;
			this.query = {id: '*', model: 'user'};
			dojoQuery(".header", this.domNode).forEach(function(element){
				self.headerNode = element;
			});
			if(!Sys.getUser())
				return; //user is not authenticated
				
			if(typeof (this.serviceUrl)  === 'function'){
				this.dataUrl = this.serviceUrl.call(this);
			}else{
				this.dataUrl = this.substitute(this.serviceUrl, this);
			}
			if(this.dataUrl === null)
				return;
			this.processDataParams();
			
			this._store = new ItemFileReadStore({url: this.dataUrl, hierarchical: true});
			
			if(!this._store || this._store === null)
				return;
			
			this._store.fetch({query: this.query, queryOptions: {deep: false}, onComplete: function(storeItems){
				for(var i in storeItems){
					var storeItem = storeItems[i];
					
					self.set('FirstName', self._store.getValue(storeItem, 'first_name'));
					
					self.set('LastName', self._store.getValue(storeItem, 'last_name'));
					
					self.set('BirthDate', self._store.getValue(storeItem, 'birth_date'));
					
					self.set('LanguageCode', self._store.getValue(storeItem, 'language_code'));
				}
			}, onError: function(error){
				self.alert(self.errors.err102);
				console.debug("error=" + error);
			}});
		}
	});
});
},
'edu/wohaoo/mobile/SignupView':function(){
define([
    "dijit/registry", "dojo/dom-class", "dojo/on", "dojo/_base/array", "dojo/keys", "dojo/_base/declare", 
    "dojo/parser", "dojo/dom",  "dojo/dom-geometry", "dojo/dom-construct", 
    "dojo/request", "dojo/query!css3", "dojo/dom-style",
     "dijit/_WidgetBase" , "dijit/_AttachMixin", 
    "dijit/_TemplatedMixin", "dijit/_WidgetsInTemplateMixin",
    "dojo/text!./templates/SignupView.html",
    "dojo/i18n!./nls/SignupView", "edu/wohaoo/mobile/_AppViewMixin", "dojox/mobile/ScrollableView", 
    "dojox/mobile/ContentPane", "dojox/mobile/Button", "dojox/mobile/TextBox",
    "dojo/domReady!"
], function(dijitRegistry, dojoClass, on, array, keys, declare, parser, dom, dojoGeom, domConstruct,  
				dojoRequest, dojoQuery, domStyle,
				_WidgetBase, _AttachMixin, _TemplatedMixin, _WidgetsInTemplateMixin, 
				template, i18n, _AppViewMixin, MobileScrollableView) {
	return declare([MobileScrollableView,  _AppViewMixin, _TemplatedMixin, _WidgetsInTemplateMixin],{
		templateString: template,
		messages: i18n,
		firstNameText: i18n.firstName,
		lastNameText: i18n.lastName,
		telephoneText: i18n.telephone,
		passwordText: i18n.password,
		logoText: __t("WOHAOO Education"),
		signupText: i18n.signup,
		delay: 1000,
		redirectTo: "",
		signupPane: null,
		firstName: null,
		lastName: null,
		telephone: null,
		password: null,
		signupButton: null,
		
		postCreate: function(){
			this.inherited(arguments);
			var self = this;
			on(this.signupButton.domNode, 'click', function(e){
				console.debug("signup clicked");
				var firstName = self.firstName.get('value');
				var lastName = self.lastName.get('value');
				var telephone = self.telephone.get('value');
				var password = self.password.get('value');
				
				if(!firstName || !lastName || !telephone || !password){
					dijitRegistry.byId('dlg_message').show(self.messages.INVALID_INPUT_PARAMETERS);
					e.stopPropagation();
					e.preventDefault(); 
					return false;
				}
				
				self.__doSignup(firstName, lastName, telephone, password);
				e.stopPropagation();
				e.preventDefault(); 
				return true;
			});
		},
		
		startup: function(){
			this.inherited(arguments);
		},
		
		__doSignup: function(firstName, lastName, telephone, password){
			var self = this;
			console.debug("creating new user");
			dojoRequest.post(this.serviceUrl, {
				data: {
					"data[User][usr_telephone]":  telephone,
					"data[User][usr_password]": password,
					"data[Person][per_first_name]":  firstName,
					"data[Person][per_last_name]":  lastName
				},
				handleAs: 'json'
			}).then(function(response){
				console.debug("signup request returned");
				if(!response.userid){
					console.debug("there was an error during signup:" + response.error);
					dijitRegistry.byId('dlg_message').show(response.error);
				}
				else{
					console.debug("signup was successfull");
					var redirectToWidget = dijitRegistry.byId(self.redirectTo);
					redirectToWidget.refresh();
					redirectToWidget.animate();
					self.moveTo(self.id, self.redirectTo);
				}
			}, function(error){
				self.alert(self.errors.err101);
				console.debug("error occurred:" + error.toString());
			});
		},
		
		animate: function(){
			var self = this;
			if(!dojoClass.contains(this.signupPane.domNode, 'mblHidden')){
				dojoClass.add(this.signupPane.domNode, 'mblHidden');
			}
			
			dojoQuery(".company-logo", this.domNode).forEach(function(element){
				if(!dojoClass.contains(element, 'center')){
					dojoClass.add(element, 'center');
				}
			});
			setTimeout(function(){
				if(dojoClass.contains(self.signupPane.domNode, 'mblHidden')){
					dojoClass.remove(self.signupPane.domNode, 'mblHidden');
				}
				
				dojoQuery(".company-logo", self.domNode).forEach(function(element){
					dojoClass.remove(element, 'center');
				});
			}, this.delay);
		},
		
		destroyDescendants: function(/*Boolean*/ preserveDom){
			this.inherited(arguments);
		},

		destroy: function(){
			this.inherited(arguments);
		}
	});

});
},
'url:edu/wohaoo/mobile/templates/EnrollView.html':"<div class=\"enrollContainer\">\n\t<div id=\"enrollDialog\" class=\"enrollDialog\">\n\t\t<div class=\"enrollHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"enrollPane mblHidden\" data-dojo-attach-point=\"enrollPane\">\n\t\t\t<div class=\"enrollDescPane\">\n\t\t\t\t<span data-dojo-type=\"dojox/mobile/ContentPane\" data-dojo-attach-point=\"enrollMessage\" \n\t\t\t\tclass=\"text center\">${descText}</span>\n\t\t\t</div>\n\t\t\t<div class=\"amountInputPane\">\n\t\t\t\t<input class='amountInput'  type='text'\n\t\t\t\t  data-dojo-props='readOnly:true, name:\"data[Payment][pay_amount]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${amountText}\" data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t\t\t  data-dojo-attach-point=\"amountInput\"/>\n\t\t\t</div>\n\t\t\t<div class=\"telephoneInputPane\">\n\t\t\t\t<input class='telephoneInput'  type='text'\n\t\t\t\t  data-dojo-props='readOnly:true, name:\"data[Payment][pay_medium]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${telephoneText}\" data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t\t\t  data-dojo-attach-point=\"telephoneInput\"/>\n\t\t\t</div>\n\t\t\t<div class=\"transactionCodeInputPane\">\n\t\t\t\t<input class='transactionCodeInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[Payment][pay_transaction_id]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${transactionCodeText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"transactionCode\"/>\n\t\t\t</div>\n\t\t\t<div class=\"enrollButtonPane\">\n\t\t\t\t<button  class='enrollButton mblBlueButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"enrollButton\">${enrollText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>",
'url:edu/wohaoo/mobile/templates/LanguageDialogView.html':"<div id=\"languageDialogContainer\">\n\t<div class=\"mblSimpleDialogTitle\">${LanguageDialogTitle}</div>\n\t<ul data-dojo-type=\"dojox.mobile.EdgeToEdgeList\"  data-dojo-attach-point=\"optionsList\" data-dojo-props='select:\"single\"'>\n\t\t<!--<li data-dojo-type=\"dojox.mobile.ListItem\" data-dojo-props='checkClass:\"mblDomButtonSilverCircleGreenButton\", uncheckClass:\"mblDomButtonSilverCircleGrayButton\"'>\n\t\t\tSample Item \n\t\t</li>-->\n\t</ul>\n\t<button data-dojo-type=\"dojox.mobile.Button\" \n\t\tclass=\"mblSimpleDialogButton\" \n\t\tdata-dojo-props='select:\"single\"'\n\t\tdata-dojo-attach-point=\"okButton\" \n\t\tstyle=\"width:60%;\">${OKButton}</button>\n</div>",
'url:edu/wohaoo/mobile/templates/LoginView.html':"<div class=\"loginContainer\">\n\t<div id=\"loginDialog\" class=\"loginDialog\">\n\t\t<div class=\"loginHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"loginPane mblHidden\" data-dojo-attach-point=\"loginPane\">\n\t\t\t<div class=\"emailInputPane\">\n\t\t\t\t<input class='emailInput'  type='email'\n\t\t\t\t  data-dojo-props='name:\"data[User][usr_telephone]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${telephoneText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"telephone\"/>\n\t\t\t</div> \n\t\t\t<div class=\"passwordInputPane\">\n\t\t\t\t<input class='passwordInput' placeholder=\"${passwordText}\" \n\t\t\t\t\t\t\tdata-dojo-props='name:\"data[User][usr_password]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t\t\t\tdata-dojo-type=\"dojox/mobile/TextBox\" type=\"password\" data-dojo-attach-point=\"password\"/>\n\t\t\t</div>\n\t\t\t<div class=\"loginButtonPane\">\n\t\t\t\t<button  class='loginButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"loginButton\">${loginText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>",
'url:edu/wohaoo/mobile/templates/LogoutView.html':"<div class=\"logoutContainer\">\n\t<div id=\"logoutDialog\" class=\"logoutDialog\">\n\t\t<div class=\"logoutHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"logoutPane mblHidden\" data-dojo-attach-point=\"logoutPane\">\n\t\t\t<div class=\"logoutButtonPane\">\n\t\t\t\t<button  class='logoutButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"logoutButton\">${logoutText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>",
'url:edu/wohaoo/mobile/templates/PersonDataView.html':"<div id=\"personDataViewContainer\">\n\t<div class=\"mblSimpleDialogTitle\">${SettingsHeaderText}</div>\n\t<div class=\"mblSimpleDialogText\">${FirstNameLabel}</div>\n\t<input data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t   value=\"${FisrtName}\"\n\t\t   data-dojo-attach-point=\"FirstNameInput\"\n\t\t   style=\"width:90%;\">\n\t<div class=\"mblSimpleDialogText\">${LastNameLabel}</div>\n\t<input data-dojo-type=\"dojox/mobile/TextBox\"\n\t\t   value=\"${LastName}\"\n\t\t   data-dojo-attach-point=\"LastNameInput\"\n\t\t   style=\"width:90%;\">\n\t<div class=\"mblSimpleDialogText\">${BirthDateLabel}</div>\n\t<input data-dojo-type=\"dojox/mobile/TextBox\"\n\t\tvalue=\"${BirthDate}\"\n\t\tdata-dojo-props='readOnly: true'\n\t\t data-dojo-attach-point=\"BirthDateInput\"\n\t\t data-dojo-attach-event=\"onClick: onShowBirthDatePicker\"\n\t\t   style=\"width:90%;\">\n\t<div id=\"birthDateOpener\" \n\t\t data-dojo-attach-point=\"BirthDateOpener\"\n\t\tdata-dojo-type=\"dojox/mobile/Opener\"\n\t\tdata-dojo-props='lazy:true'\n\t\t\tdata-dojo-attach-event=\"onShow: onBirthDateShow, onHide: onBirthDateHide\">\n\t</div>\n\t<button data-dojo-type=\"dojox/mobile/Button\"\n\t\t\tclass=\"mblSimpleDialogButton\" \n\t\t\tdata-dojo-attach-event=\"onClick: onOk\" \n\t\t\tdata-dojo-attach-point=\"okButton\">${OKButton}</button>\n\t<button data-dojo-type=\"dojox/mobile/Button\"\n\t\t\tclass=\"mblSimpleDialogButton\" \n\t\t\tdata-dojo-attach-event=\"onClick: onCancel\"\n\t\t\tdata-dojo-attach-point=\"cancelButton\">${CancelButton}</button>\n</div>",
'url:edu/wohaoo/mobile/templates/SettingsView.html':"<div id=\"settingsViewContainer\">\n\t<h2 class=\"settingsGeneralHeader\" data-dojo-type=\"dojox.mobile.EdgeToEdgeCategory\" data-dojo-props='label:\"General\"'></h2>\n\t<ul data-dojo-type=\"dojox.mobile.EdgeToEdgeList\">\n\t\t<li class=\"settingsPersonalDataItem propertyListItem\" data-dojo-type=\"dojox.mobile.ListItem\" \n\t\t\t\tdata-dojo-props='label:\"Personal Details\", variableHeight:true'\n\t\t\t\tdata-dojo-attach-point=\"personalDataItem\">\n\t\t\t\t<div class=\"personalDetailsData\" data-dojo-attach-point=\"personalDetailsData\"></div>\n\t\t</li>\n\t\t<li class=\"settingsLanguageItem propertyListItem\"  data-dojo-type=\"dojox.mobile.ListItem\" \n\t\t\t\tdata-dojo-props='label:\"Language\", variableHeight:true'\n\t\t\t\tdata-dojo-attach-point=\"languageItem\">\n\t\t\t\t<div class=\"languageData\" data-dojo-attach-point=\"languageData\"></div>\n\t\t</li>\n\t</ul>\n</div>",
'url:edu/wohaoo/mobile/templates/SignupView.html':"<div class=\"signupContainer\">\n\t<div id=\"signupDialog\" class=\"signupDialog\">\n\t\t<div class=\"signupHeaderPane\">\n\t\t\t<span class=\"company-logo center\">${logoText}</span>\n\t\t</div>\n\t\t<div data-dojo-type=\"dojox/mobile/ContentPane\" class=\"signupPane mblHidden\" data-dojo-attach-point=\"signupPane\">\n\t\t\t<div class=\"firstNameInputPane\">\n\t\t\t\t<input class='firstNameInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[Person][per_first_name]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${firstNameText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"firstName\"/>\n\t\t\t</div>\n\t\t\t<div class=\"lastNameInputPane\">\n\t\t\t\t<input class='lastNameInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[Person][per_last_name]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${lastNameText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"lastName\"/>\n\t\t\t</div>  \n\t\t\t<div class=\"telInputPane\">\n\t\t\t\t<input class='telInput'  type='text'\n\t\t\t\t  data-dojo-props='name:\"data[User][usr_telephone]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t  placeholder=\"${telephoneText}\" data-dojo-type=\"dojox/mobile/TextBox\" data-dojo-attach-point=\"telephone\"/>\n\t\t\t</div> \n\t\t\t<div class=\"passwordInputPane\">\n\t\t\t\t<input class='passwordInput' placeholder=\"${passwordText}\" \n\t\t\t\t\t\t\tdata-dojo-props='name:\"data[User][usr_password]\", value:\"\", textDir:\"ltr\"'\n\t\t\t\t\t\t\tdata-dojo-type=\"dojox/mobile/TextBox\" type=\"text\" data-dojo-attach-point=\"password\"/>\n\t\t\t</div>\n\t\t\t<div class=\"signupButtonPane\">\n\t\t\t\t<button  class='signupButton'\n\t\t\t\tdata-dojo-type=\"dojox/mobile/Button\" data-dojo-attach-point=\"signupButton\">${signupText}</button>\n\t\t\t</div> \n\t\t</div>\n\t</div>\n</div>",
'*now':function(r){r(['dojo/i18n!*preload*edu/nls/wohaoo*["ar","ca","cs","da","de","el","en-gb","en-us","es-es","fi-fi","fr-fr","he-il","hu","it-it","ja-jp","ko-kr","nl-nl","nb","pl","pt-br","pt-pt","ru","sk","sl","sv","th","tr","zh-tw","zh-cn","ROOT"]']);}
}});
define("edu/wohaoo", [], 1);
