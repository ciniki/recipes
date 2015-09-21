//
// The recipes app to manage an artists collection
//
function ciniki_recipes_main() {
	this.webFlags = {
		'1':{'name':'Hidden'},
		'5':{'name':'Category Highlight'},
		};
	this.tagTypes = {
		'10':{'name':'Meals & Courses', 'arg':'meals', 'visible':'no'},
		'20':{'name':'Main Ingredients', 'arg':'mainingredients', 'visible':'no'},
		'30':{'name':'Cuisines', 'arg':'cuisines', 'visible':'no'},
		'40':{'name':'Methods', 'arg':'methods', 'visible':'no'},
		'50':{'name':'Occasions', 'arg':'occasions', 'visible':'no'},
		'60':{'name':'Diets', 'arg':'diets', 'visible':'no'},
		'70':{'name':'Seasons', 'arg':'seasons', 'visible':'no'},
		'80':{'name':'Collections', 'arg':'collections', 'visible':'no'},
		'90':{'name':'Products', 'arg':'products', 'visible':'no'},
		'100':{'name':'Contributors', 'arg':'contributors', 'visible':'no'},
		};
	// Web flags for additional image
	this.webFlags2 = {
		'1':{'name':'Hidden'},
		};
	this.init = function() {
		//
		// Setup the main panel to list the collection
		//
		this.menu = new M.panel('Recipes',
			'ciniki_recipes_main', 'menu',
			'mc', 'medium narrowaside', 'sectioned', 'ciniki.recipes.main.menu');
		this.menu.data = {};
		this.menu.formtab = '10';
		this.menu.tag_name = '';
		this.menu.formtabs = {'label':'', 'tabs':{
			'10':{'label':'Meals', 'visible':'yes', 'fn':'M.ciniki_recipes_main.showMenu(null,10,\'\');'},
			'20':{'label':'Ingredients', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,20,\'\');'},
			'30':{'label':'Cuisines', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,30,\'\');'},
			'40':{'label':'Methods', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,40,\'\');'},
			'50':{'label':'Occasions', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,50,\'\');'},
			'60':{'label':'Diets', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,60,\'\');'},
			'70':{'label':'Seasons', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,70,\'\');'},
			'80':{'label':'Collections', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,80,\'\');'},
			'90':{'label':'Products', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,90,\'\');'},
			'100':{'label':'Contributors', 'visible':'no', 'fn':'M.ciniki_recipes_main.showMenu(null,100,\'\');'},
			}},
		this.menu.forms = {};
		this.menu.forms['10'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['20'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['30'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['40'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['50'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['60'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['70'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['80'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['90'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['100'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
			'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Recipe',
				'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);',
				},
			};
		this.menu.sections = this.menu.forms['10'];	
//		this.menu.listby = 'category';
//		this.menu.liveSearchCb = function(s, i, v) {
//			if( v != '' ) {
//				M.api.getJSONBgCb('ciniki.recipes.searchQuick', {'business_id':M.curBusinessID, 'start_needle':v, 'limit':'15'},
//					function(rsp) {
//						M.ciniki_recipes_main.menu.liveSearchShow(s, null, M.gE(M.ciniki_recipes_main.menu.panelUID + '_' + s), rsp.items);
//					});
//			}
//			return true;
//		};
//		this.menu.liveSearchResultValue = function(s, f, i, j, d) {
//			return this.cellValue(s, i, j, d);
//		};
//		this.menu.liveSearchResultRowFn = function(s, f, i, j, d) {
//			return 'M.ciniki_recipes_main.showItem(\'M.ciniki_recipes_main.showMenu(null);\', \'' + d.item.id + '\');'; 
//		};
//		this.menu.liveSearchResultRowStyle = function(s, f, i, d) { return ''; };
// 		Currently not allowing full search
//		this.menu.liveSearchSubmitFn = function(s, search_str) {
//			M.ciniki_recipes_main.searchArtCatalog('M.ciniki_recipes_main.showMenu();', search_str);
//		};
		this.menu.cellValue = function(s, i, j, d) {
			if( s == 'tags' ) {
				return d.tag.tag_name + ' <span class="count">' + d.tag.num_recipes + '</span>';
			} 
			else if( s == 'recipes' ) {
				return '<span class="maintext">' + d.recipe.name + '</span>';
			}
		};
		this.menu.rowFn = function(s, i, d) {
			switch(s) {
				case 'tags': return 'M.ciniki_recipes_main.showMenu(null,null,\'' + escape(d.tag.tag_name) + '\');';
				case 'recipes': return 'M.ciniki_recipes_main.showRecipe(\'M.ciniki_recipes_main.showMenu();\', \'' + d.recipe.id + '\',M.ciniki_recipes_main.menu.data[unescape(\'' + escape(s) + '\')]);'; 
			}
		};
		this.menu.sectionData = function(s) { 
			return this.data[s];
		};
		this.menu.addButton('add', 'Add', 'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\', 0);');
//		this.menu.addButton('tools', 'Tools', 'M.ciniki_recipes_main.tools.show(\'M.ciniki_recipes_main.showMenu();\');');
		this.menu.addClose('Back');

		//
		// Display information about a recipe
		//
		this.recipe = new M.panel('Recipe',
			'ciniki_recipes_main', 'recipe',
			'mc', 'medium mediumaside', 'sectioned', 'ciniki.recipes.main.recipe');
		this.recipe.next_recipe_id = 0;
		this.recipe.prev_recipe_id = 0;
		this.recipe.data = null;
		this.recipe.recipe_id = 0;
		this.recipe.sections = {
			'_image':{'label':'Image', 'aside':'yes', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'history':'no'},
			}},
			'info':{'label':'Public Information', 'aside':'yes', 'list':{
				'name':{'label':'Title', 'type':'text'},
//				'category':{'label':'Category'},
//				'cuisine':{'label':'Cuisine'},
				'num_servings':{'label':'Servings'},
				'prep_time':{'label':'Prep Time'},
				'roast_time':{'label':'Roast Time'},
				'cook_time':{'label':'Cook Time'},
				'website':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
				'tag-10':{'label':'Meals & Courses', 'visible':'no'},
				'tag-20':{'label':'Main Ingredients', 'visible':'no'},
				'tag-30':{'label':'Cuisines', 'visible':'no'},
				'tag-40':{'label':'Methods', 'visible':'no'},
				'tag-50':{'label':'Occasions', 'visible':'no'},
				'tag-60':{'label':'Diets', 'visible':'no'},
				'tag-70':{'label':'Seasons', 'visible':'no'},
				'tag-80':{'label':'Collections', 'visible':'no'},
				'tag-90':{'label':'Products', 'visible':'no'},
				'tag-100':{'label':'Contributors', 'visible':'no'},
			}},
			'synopsis':{'label':'Synopsis', 'type':'htmlcontent'},
			'description':{'label':'Description', 'type':'htmlcontent'},
			'ingredients':{'label':'Ingredients', 'type':'htmlcontent'},
			'instructions':{'label':'Directions', 'type':'htmlcontent'},
			'images':{'label':'Additional Images', 'type':'simplethumbs'},
			'_images':{'label':'', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Image',
				'addFn':'M.startApp(\'ciniki.recipes.images\',null,\'M.ciniki_recipes_main.showRecipe();\',\'mc\',{\'recipe_id\':M.ciniki_recipes_main.recipe.recipe_id,\'add\':\'yes\'})',
				},
			'_buttons':{'label':'', 'buttons':{
				'edit':{'label':'Edit', 'fn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showRecipe();\',M.ciniki_recipes_main.recipe.recipe_id);'},
//				'delete':{'label':'Delete', 'fn':'M.ciniki_recipes_main.deleteRecipe();'},
			}},
			};
		this.recipe.sectionData = function(s) {
			if( s == 'synopsis' || s == 'description' || s == 'ingredients' || s == 'instructions' ) { 
				return this.data[s].replace(/\n/g, '<br/>'); 
			}
			if( s == 'info' ) { return this.sections[s].list; }
			return this.data[s];
			};
		this.recipe.listLabel = function(s, i, d) {
			switch (s) {
				case 'info': return d.label;
			}
		};
		this.recipe.listValue = function(s, i, d) {
			if( i.match(/tag-/) ) { 
				if( this.data[i] != null ) {
					return this.data[i].replace(/::/g, ', '); 
				}
				return '';
			}
			return this.data[i];
		};
		this.recipe.fieldValue = function(s, i, d) {
			return this.data[i];
		};
		this.recipe.noData = function(s) {
			return '';
		};
		this.recipe.prevButtonFn = function() {
			if( this.prev_recipe_id > 0 ) {
				return 'M.ciniki_recipes_main.showRecipe(null,\'' + this.prev_recipe_id + '\');';
			}
			return null;
		};
		this.recipe.nextButtonFn = function() {
			if( this.next_recipe_id > 0 ) {
				return 'M.ciniki_recipes_main.showRecipe(null,\'' + this.next_recipe_id + '\');';
			}
			return null;
		};
		this.recipe.thumbFn = function(s, i, d) {
			return 'M.startApp(\'ciniki.recipes.images\',null,\'M.ciniki_recipes_main.showRecipe();\',\'mc\',{\'recipe_image_id\':\'' + d.image.id + '\'});';
		};
		this.recipe.addDropImage = function(iid) {
			var rsp = M.api.getJSON('ciniki.recipes.imageAdd',
				{'business_id':M.curBusinessID, 'image_id':iid,
					'recipe_id':M.ciniki_recipes_main.recipe.recipe_id});
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
			return true;
		};
		this.recipe.addDropImageRefresh = function() {
			if( M.ciniki_recipes_main.recipe.recipe_id > 0 ) {
				var rsp = M.api.getJSONCb('ciniki.recipes.recipeGet', {'business_id':M.curBusinessID,
					'recipe_id':M.ciniki_recipes_main.recipe.recipe_id, 'images':'yes'}, function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						}
						M.ciniki_recipes_main.recipe.data.images = rsp.recipe.images;
						M.ciniki_recipes_main.recipe.refreshSection('images');
					});
			}
		};

		this.recipe.addButton('edit', 'Edit', 'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showRecipe();\',M.ciniki_recipes_main.recipe.recipe_id);');
		this.recipe.addButton('next', 'Next');
		this.recipe.addClose('Back');
		this.recipe.addLeftButton('prev', 'Prev');

		//
		// The panel to display the edit form
		//
		this.edit = new M.panel('Recipe',
			'ciniki_recipes_main', 'edit',
			'mc', 'medium mediumaside', 'sectioned', 'ciniki.recipes.main.edit');
		this.edit.recipe_id = 0;
		this.edit.data = null;
		this.edit.sections = {
			'_image':{'label':'Image', 'aside':'yes', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'controls':'all', 'hidelabel':'yes', 'history':'no'},
			}},
			'info':{'label':'Public Information', 'aside':'yes', 'type':'simpleform', 'fields':{
				'name':{'label':'Title', 'type':'text'},
//				'category':{'label':'Category', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
//				'cuisine':{'label':'Cuisine', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
				'num_servings':{'label':'Servings', 'type':'text', 'size':'small'},
				'prep_time':{'label':'Prep Time', 'type':'text', 'size':'small'},
				'roast_time':{'label':'Roast Time', 'type':'text', 'size':'small'},
				'cook_time':{'label':'Cook Time', 'type':'text', 'size':'small'},
				'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
			}},
			'_10':{'label':'Meals & Courses', 'aside':'yes', 'fields':{
				'tag-10':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new meal:'},
				}},
			'_20':{'label':'Main Ingredients', 'aside':'yes', 'fields':{
				'tag-20':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new ingredient:'},
				}},
			'_30':{'label':'Cuisines', 'aside':'yes', 'fields':{
				'tag-30':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new cuisine:'},
				}},
			'_40':{'label':'Methods', 'aside':'yes', 'fields':{
				'tag-40':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new method:'},
				}},
			'_50':{'label':'Occasions', 'aside':'yes', 'fields':{
				'tag-50':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new occasion:'},
				}},
			'_60':{'label':'Diets', 'aside':'yes', 'fields':{
				'tag-60':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new diet:'},
				}},
			'_70':{'label':'Seasons', 'aside':'yes', 'fields':{
				'tag-70':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new season:'},
				}},
			'_80':{'label':'Collections', 'aside':'yes', 'fields':{
				'tag-80':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new collection:'},
				}},
			'_90':{'label':'Products', 'aside':'yes', 'fields':{
				'tag-90':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new product:'},
				}},
			'_100':{'label':'Contributors', 'aside':'yes', 'fields':{
				'tag-100':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new contributor:'},
				}},
			'_synopsis':{'label':'Synopsis', 'type':'simpleform', 'fields':{
				'synopsis':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_description':{'label':'Description', 'type':'simpleform', 'fields':{
				'description':{'label':'', 'type':'textarea', 'size':'medium', 'hidelabel':'yes'},
			}},
			'_ingredients':{'label':'Ingredients', 'type':'simpleform', 'fields':{
				'ingredients':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
			}},
			'_instructions':{'label':'Directions', 'type':'simpleform', 'fields':{
				'instructions':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
			}},
			'_buttons':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_recipes_main.saveRecipe();'},
				'delete':{'label':'Delete', 'fn':'M.ciniki_recipes_main.deleteRecipe();'},
			}},
		};
		this.edit.fieldValue = function(s, i, d) { 
			return this.data[i]; 
		}
		this.edit.sectionData = function(s) {
			return this.data[s];
		};
		this.edit.liveSearchCb = function(s, i, value) {
			if( i == 'category' || i == 'cuisine' ) {
				var rsp = M.api.getJSONBgCb('ciniki.recipes.searchField', {'business_id':M.curBusinessID, 'field':i, 'start_needle':value, 'limit':15},
					function(rsp) {
						M.ciniki_recipes_main.edit.liveSearchShow(s, i, M.gE(M.ciniki_recipes_main.edit.panelUID + '_' + i), rsp.results);
					});
			}
		};
		this.edit.liveSearchResultValue = function(s, f, i, j, d) {
			if( (f == 'category' || f == 'cuisine' ) && d.result != null ) { return d.result.name; }
			return '';
		};
		this.edit.liveSearchResultRowFn = function(s, f, i, j, d) { 
			if( (f == 'category' || f == 'cuisine' )
				&& d.result != null ) {
				return 'M.ciniki_recipes_main.edit.updateField(\'' + s + '\',\'' + f + '\',\'' + escape(d.result.name) + '\');';
			}
		};
		this.edit.updateField = function(s, fid, result) {
			M.gE(this.panelUID + '_' + fid).value = unescape(result);
			this.removeLiveSearch(s, fid);
		};
		this.edit.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.recipes.recipeHistory', 'args':{'business_id':M.curBusinessID, 
				'recipe_id':this.recipe_id, 'field':i}};
		}
		this.edit.addDropImage = function(iid) {
			M.ciniki_recipes_main.edit.setFieldValue('image_id', iid, null, null);
			return true;
		};
		this.edit.deleteImage = function() {
			this.setFieldValue('image_id', 0, null, null);
			return true;
		};
		this.edit.addButton('save', 'Save', 'M.ciniki_recipes_main.saveRecipe();');
		this.edit.addClose('Cancel');
	}

	this.start = function(cb, appPrefix, aG) {
		args = {};
		if( aG != null ) {
			args = eval(aG);
		}

		//
		// Create container
		//
		var appContainer = M.createContainer(appPrefix, 'ciniki_recipes_main', 'yes');
		if( appContainer == null ) {
			alert('App Error');
			return false;
		}

		this.tagTypes['10'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x01)>0?'yes':'no';
		this.tagTypes['20'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x02)>0?'yes':'no';
		this.tagTypes['30'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x04)>0?'yes':'no';
		this.tagTypes['40'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x08)>0?'yes':'no';
		this.tagTypes['50'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x10)>0?'yes':'no';
		this.tagTypes['60'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x20)>0?'yes':'no';
		this.tagTypes['70'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x40)>0?'yes':'no';
		this.tagTypes['80'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x80)>0?'yes':'no';
		this.tagTypes['90'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x0100)>0?'yes':'no';
		this.tagTypes['100'].visible = (M.curBusiness.modules['ciniki.recipes'].flags&0x0200)>0?'yes':'no';
	
		this.menu.formtab = null;
		for(i in this.tagTypes) {
			this.menu.formtabs.tabs[i].visible = this.tagTypes[i].visible;
			this.recipe.sections.info.list['tag-'+i].visible = this.tagTypes[i].visible;
			this.edit.sections['_'+i].active = this.tagTypes[i].visible;
			if( this.menu.formtab == null && this.tagTypes[i].visible == 'yes' ) {
				this.menu.formtab = i;
			}
		}

		this.menu.tag_name = null;
		this.showMenu(cb);
	}

	this.showMenu = function(cb, tag_type, tag_name) {
		if( tag_type != null ) { this.menu.formtab = tag_type; }
		if( tag_name != null ) { this.menu.tag_name = unescape(tag_name); }
		var args = {'business_id':M.curBusinessID};
		if( this.menu.formtab != null ) { args.tag_type = this.menu.formtab; }
		if( this.menu.tag_name != null ) { 
			this.menu.forms[this.menu.formtab].recipes.label = this.menu.tag_name;
			args.tag_name = this.menu.tag_name; 
		}
		M.api.getJSONCb('ciniki.recipes.recipeList', args, function(rsp) {
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
			var p = M.ciniki_recipes_main.menu;
			p.data = rsp;
			p.refresh();
			p.show(cb);
		});
	};

	this.showRecipe = function(cb, rid, list) {
		this.recipe.reset();
		if( cb != null ) { this.recipe.cb = cb; }
		if( rid != null ) { this.recipe.recipe_id = rid; }
		if( list != null ) { this.recipe.list = list; }

		var rsp = M.api.getJSONCb('ciniki.recipes.recipeGet', 
			{'business_id':M.curBusinessID, 'recipe_id':M.ciniki_recipes_main.recipe.recipe_id,
			'images':'yes'}, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				}
				var p = M.ciniki_recipes_main.recipe;
				p.data = rsp.recipe;

				if( rsp.recipe.tags != null && rsp.recipe.tags != '' ) {
					p.data.tags = rsp.recipe.tags.replace(/::/g, ', ');
				}
				// Setup next/prev buttons
				p.prev_recipe_id = 0;
				p.next_recipe_id = 0;
				if( p.list != null ) {
					for(i in p.list) {
						if( p.next_recipe_id == -1 ) {
							p.next_recipe_id = p.list[i].recipe.id;
							break;
						} else if( p.list[i].recipe.id == p.recipe_id ) {
							// Flag to pickup next recipe
							p.next_recipe_id = -1;
						} else {
							p.prev_recipe_id = p.list[i].recipe.id;
						}
					}
				}
				p.refresh();
				p.show();
			});
	};

	this.refreshRecipeImages = function() {
		if( M.ciniki_recipes_main.recipe.recipe_id > 0 ) {
			var rsp = M.api.getJSONCb('ciniki.recipes.recipeGet', 
				{'business_id':M.curBusinessID, 'recipe_id':M.ciniki_recipes_main.recipe.recipe_id, 
				'images':'yes'}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_recipes_main.recipe.data.images = rsp.recipe.images;
					M.ciniki_recipes_main.recipe.refreshSection('images');
					M.ciniki_recipes_main.recipe.show();
				});
		}
	};

	this.showEdit = function(cb, rid, type, type_name) {
		if( rid != null ) {
			this.edit.recipe_id = rid;
		}
		if( this.edit.recipe_id == 0 ) {
			this.edit.reset();
			this.edit.sections._buttons.buttons.delete.visible = 'no';
		} else {
			this.edit.sections._buttons.buttons.delete.visible = 'yes';
		}
		M.api.getJSONCb('ciniki.recipes.recipeGet', {'business_id':M.curBusinessID, 'recipe_id':this.edit.recipe_id, 'tags':'yes'}, function(rsp) {
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
			var p = M.ciniki_recipes_main.edit;
			p.data = rsp.recipe;
			for(i in M.ciniki_recipes_main.tagTypes) {
				p.sections['_'+i].fields['tag-'+i].tags = [];
			}
			if( rsp.tags != null ) {
				for(i in rsp.tags) {
					p.sections['_'+rsp.tags[i].tag_type].fields[i].tags = rsp.tags[i].tag_names.split(/::/);
				}
			}
			p.refresh();
			p.show(cb);
		});
	};

	this.saveRecipe = function() {
		// Check form values
		var nv = this.edit.formFieldValue(this.edit.sections.info.fields.name, 'name');
		if( nv != this.edit.fieldValue('info', 'name') && nv == '' ) {
			alert('You must specifiy a title');
			return false;
		}
		if( this.edit.recipe_id > 0 ) {
			var c = this.edit.serializeForm('no');
			if( c != '' ) {
				var rsp = M.api.postJSONFormData('ciniki.recipes.recipeUpdate', 
					{'business_id':M.curBusinessID, 'recipe_id':this.edit.recipe_id}, c,
						function(rsp) {
							if( rsp.stat != 'ok' ) {
								M.api.err(rsp);
								return false;
							} else {
								M.ciniki_recipes_main.edit.close();
							}
						});
			} else {
				M.ciniki_recipes_main.edit.close();
			}
		} else {
			var c = this.edit.serializeForm('yes');
			var rsp = M.api.postJSONFormData('ciniki.recipes.recipeAdd', 
				{'business_id':M.curBusinessID}, c,
					function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						} else {
							M.ciniki_recipes_main.edit.close();
						}
					});
		}
	};

	this.deleteRecipe = function() {
		if( confirm('Are you sure you want to delete \'' + this.recipe.data.name + '\'?  All information about it will be removed and unrecoverable.') ) {
			var rsp = M.api.getJSONCb('ciniki.recipes.deleteRecipe', 
				{'business_id':M.curBusinessID, 'recipe_id':this.edit.recipe_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_recipes_main.edit.close();
				});
		}
	};
}
