//
// The recipes app to manage an artists collection
//
function ciniki_recipes_main() {
	this.webFlags = {
		'1':{'name':'Hidden'},
		'5':{'name':'Category Highlight'},
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
			'mc', 'medium', 'sectioned', 'ciniki.recipes.main.menu');
		this.menu.data = {};
		this.menu.sections = {};	// Sections are set in showPieces function
		this.menu.listby = 'category';
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
			if( j == 0 ) { 
				return '<span class="maintext">' + d.recipe.name + '</span>';
			}
		};
		this.menu.rowFn = function(s, i, d) {
			return 'M.ciniki_recipes_main.showRecipe(\'M.ciniki_recipes_main.showMenu();\', \'' + d.recipe.id + '\',M.ciniki_recipes_main.menu.data[unescape(\'' + escape(s) + '\')]);'; 
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
				'category':{'label':'Category'},
				'cuisine':{'label':'Cuisine'},
				'num_servings':{'label':'Servings'},
				'prep_time':{'label':'Prep Time'},
				'cook_time':{'label':'Cook Time'},
				'website':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
				'tags':{'label':'Tags'},
			}},
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
			if( s == 'description' || s == 'ingredients' || s == 'instructions' ) { 
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
		this.recipe.thumbSrc = function(s, i, d) {
			if( d.image.image_data != null && d.image.image_data != '' ) {
				return d.image.image_data;
			} else {
				return '/ciniki-manage-themes/default/img/noimage_75.jpg';
			}
		};
		this.recipe.thumbTitle = function(s, i, d) {
			if( d.image.name != null ) { return d.image.name; }
			return '';
		};
		this.recipe.thumbID = function(s, i, d) {
			if( d.image.id != null ) { return d.image.id; }
			return 0;
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
			'info':{'label':'Public Information', 'type':'simpleform', 'fields':{
				'name':{'label':'Title', 'type':'text'},
				'category':{'label':'Category', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
				'cuisine':{'label':'Cuisine', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
				'num_servings':{'label':'Servings', 'type':'text', 'size':'small'},
				'prep_time':{'label':'Prep Time', 'type':'text', 'size':'small'},
				'cook_time':{'label':'Cook Time', 'type':'text', 'size':'small'},
				'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
			}},
			'_tags':{'label':'Tags', 'fields':{
				'tags':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new tag:'},
				}},
			'_description':{'label':'Description', 'type':'simpleform', 'fields':{
				'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
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

		this.showMenu(cb);
	}

	this.showMenu = function(cb, listby, type, sec) {
		//
		// If there is not many 
		//
		this.menu.data = {};
		if( listby != null && (listby == 'category' || listby == 'cuisine' ) ) {
			this.menu.listby = listby;
		}
		this.menu.sections = {
//			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':3, 'hint':'search',
//				'noData':'No art found',
//				'headerValues':null,
//				'cellClasses':['thumbnail', 'multiline', 'multiline'],
//				},
			'tabs':{'label':'', 'type':'paneltabs', 'selected':this.menu.listby, 'tabs':{
				'category':{'label':'Category', 'fn':'M.ciniki_recipes_main.showMenu(null,\'category\');'},
				'cuisine':{'label':'Cuisine', 'fn':'M.ciniki_recipes_main.showMenu(null,\'cuisine\');'},
				}},
		};
		var rsp = M.api.getJSONCb('ciniki.recipes.recipeList', 
			{'business_id':M.curBusinessID, 'type':this.menu.listby}, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				}
				M.ciniki_recipes_main.menu.data = {};
				// 
				// Setup the menu to display the categories
				//
				M.ciniki_recipes_main.menu.data = {};
				var i = 0;
				for(i in rsp.types) {
					M.ciniki_recipes_main.menu.data[rsp.types[i].type.name + ' '] = rsp.types[i].type.recipes;
					M.ciniki_recipes_main.menu.sections[rsp.types[i].type.name + ' '] = {'label':rsp.types[i].type.name,
						'num_cols':1, 'type':'simplegrid', 'headerValues':null,
						'cellClasses':['multiline'],
						'noData':'No FAQs found',
						'addTxt':'Add',
						'addFn':'M.ciniki_recipes_main.showEdit(\'M.ciniki_recipes_main.showMenu();\',0,M.ciniki_recipes_main.menu.listby,\'' + rsp.types[i].type.name + '\');',
					};
				}
				if( rsp.types.length == 0 ) {
					M.ciniki_recipes_main.menu.data['_nodata'] = [{'label':'No recipes found.  '},];
					M.ciniki_recipes_main.menu.sections['_nodata'] = {'label':' ', 'type':'simplelist', 'list':{
						'nodata':{'label':'No recipes found'}}};
				}
				
				M.ciniki_recipes_main.menu.refresh();
				M.ciniki_recipes_main.menu.show(cb);
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
		if( this.edit.recipe_id > 0 ) {
			this.edit.sections._buttons.buttons.delete.visible = 'yes';
			var rsp = M.api.getJSONCb('ciniki.recipes.recipeGet', 
				{'business_id':M.curBusinessID, 'recipe_id':this.edit.recipe_id, 'tags':'yes'}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					var p = M.ciniki_recipes_main.edit;
					p.data = rsp.recipe;
					p.sections._tags.fields.tags.tags = [];
					if( rsp.tags != null ) {
						for(i in rsp.tags) {
							p.sections._tags.fields.tags.tags.push(rsp.tags[i].tag.name);
						}
					}
					p.refresh();
					p.show(cb);
				});
		} else {
			this.edit.reset();
			this.edit.sections._buttons.buttons.delete.visible = 'no';
			this.edit.data = {};
			if( type != null && type == 'category' && type_name != null ) {
				this.edit.data.category = type_name;
			} else if( type != null && type == 'cuisine' && type_name != null ) {
				this.edit.data.cuisine = type_name;
			}
			this.edit.refresh();
			this.edit.show(cb);
		}
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
