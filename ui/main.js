//
// The recipes app to manage an artists collection
//
function ciniki_recipes_main() {
    this.webFlags = {
        '1':{'name':'Visible'},
//      '5':{'name':'Category Highlight'},
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

    //
    // Setup the main panel to list the collection
    //
    this.menu = new M.panel('Recipes',
        'ciniki_recipes_main', 'menu',
        'mc', 'medium narrowaside', 'sectioned', 'ciniki.recipes.main.menu');
    this.menu.data = {};
    this.menu.npList = [];
    this.menu.formtab = '10';
    this.menu.tag_name = '';
    this.menu.formtabs = {'label':'', 'tabs':{
        '10':{'label':'Meals', 'visible':'yes', 'fn':'M.ciniki_recipes_main.menu.open(null,10,\'\');'},
        '20':{'label':'Ingredients', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,20,\'\');'},
        '30':{'label':'Cuisines', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,30,\'\');'},
        '40':{'label':'Methods', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,40,\'\');'},
        '50':{'label':'Occasions', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,50,\'\');'},
        '60':{'label':'Diets', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,60,\'\');'},
        '70':{'label':'Seasons', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,70,\'\');'},
        '80':{'label':'Collections', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,80,\'\');'},
        '90':{'label':'Products', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,90,\'\');'},
        '100':{'label':'Contributors', 'visible':'no', 'fn':'M.ciniki_recipes_main.menu.open(null,100,\'\');'},
        }},
    this.menu.forms = {};
    this.menu.forms['10'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['20'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['30'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['40'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['50'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['60'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['70'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['80'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['90'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.forms['100'] = {
        'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
        'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'recipe name', 'noData':'No recipes found'},
        'recipes':{'label':'Latest Recipes', 'type':'simplegrid', 'num_cols':1,
            'addTxt':'Add Recipe',
            'addFn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0,null,null,null);',
            },
        };
    this.menu.sections = this.menu.forms['10']; 
//      this.menu.listby = 'category';
    this.menu.liveSearchCb = function(s, i, v) {
        if( v != '' ) {
            M.api.getJSONBgCb('ciniki.recipes.recipeSearch', {'business_id':M.curBusinessID, 'start_needle':v, 'limit':'15'},
                function(rsp) {
                    M.ciniki_recipes_main.menu.liveSearchShow(s, null, M.gE(M.ciniki_recipes_main.menu.panelUID + '_' + s), rsp.recipes);
                });
        }
        return true;
    };
    this.menu.liveSearchResultValue = function(s, f, i, j, d) {
        return d.name; 
    };
    this.menu.liveSearchResultRowFn = function(s, f, i, j, d) {
        return 'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', \'' + d.id + '\');'; 
    };
    this.menu.liveSearchResultRowStyle = function(s, f, i, d) { return ''; };
//      Currently not allowing full search
//      this.menu.liveSearchSubmitFn = function(s, search_str) {
//          M.ciniki_recipes_main.searchArtCatalog('M.ciniki_recipes_main.menu.open();', search_str);
//      };
    this.menu.cellValue = function(s, i, j, d) {
        if( s == 'tags' ) {
            return d.tag.tag_name + ' <span class="count">' + d.tag.num_recipes + '</span>';
        } 
        else if( s == 'recipes' ) {
            return '<span class="maintext">' + d.name + '</span>';
        }
    };
    this.menu.rowFn = function(s, i, d) {
        switch(s) {
            case 'tags': return 'M.ciniki_recipes_main.menu.open(null,null,\'' + escape(d.tag.tag_name) + '\');';
//            case 'recipes': return 'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', \'' + d.id + '\',M.ciniki_recipes_main.menu.data[unescape(\'' + escape(s) + '\')]);'; 
            case 'recipes': return 'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', \'' + d.id + '\',null,null,M.ciniki_recipes_main.menu.npList);';
        }
    };
    this.menu.sectionData = function(s) { 
        return this.data[s];
    };
    this.menu.open = function(cb, tag_type, tag_name) {
        if( tag_type != null ) { this.formtab = tag_type; }
        if( tag_name != null ) { this.tag_name = unescape(tag_name); }
        var args = {'business_id':M.curBusinessID};
        if( this.formtab != null ) { args.tag_type = this.formtab; }
        if( this.tag_name != null ) { 
            this.forms[this.formtab].recipes.label = this.tag_name;
            args.tag_name = this.tag_name; 
        }
        M.api.getJSONCb('ciniki.recipes.recipeList', args, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_recipes_main.menu;
            p.data = rsp;
            p.npList = (rsp.nplist != null ? rsp.nplist : []);
            p.refresh();
            p.show(cb);
        });
    };
    this.menu.addButton('add', 'Add', 'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.menu.open();\', 0);');
//      this.menu.addButton('tools', 'Tools', 'M.ciniki_recipes_main.tools.show(\'M.ciniki_recipes_main.menu.open();\');');
    this.menu.addClose('Back');

    //
    // Display information about a recipe
    //
/*    this.recipe = new M.panel('Recipe',
        'ciniki_recipes_main', 'recipe',
        'mc', 'medium mediumaside', 'sectioned', 'ciniki.recipes.main.recipe');
    this.recipe.next_recipe_id = 0;
    this.recipe.prev_recipe_id = 0;
    this.recipe.data = null;
    this.recipe.recipe_id = 0;
    this.recipe.sections = {
        '_image':{'label':'Image', 'aside':'yes', 'type':'imageform', 'fields':{
            'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'history':'no'},
        }},
        'info':{'label':'Public Information', 'aside':'yes', 'list':{
            'name':{'label':'Title', 'type':'text'},
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
            'addFn':'M.ciniki_recipes_main.image.open(\'M.ciniki_recipes_main.recipe.open();\',M.ciniki_recipes_main.recipe.recipe_id);',
            },
        '_buttons':{'label':'', 'buttons':{
            'edit':{'label':'Edit', 'fn':'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.recipe.open();\',M.ciniki_recipes_main.recipe.recipe_id);'},
//              'delete':{'label':'Delete', 'fn':'M.ciniki_recipes_main.recipe.remove();'},
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
            return 'M.ciniki_recipes_main.recipe.open(null,\'' + this.prev_recipe_id + '\');';
        }
        return null;
    };
    this.recipe.nextButtonFn = function() {
        if( this.next_recipe_id > 0 ) {
            return 'M.ciniki_recipes_main.recipe.open(null,\'' + this.next_recipe_id + '\');';
        }
        return null;
    };
    this.recipe.thumbFn = function(s, i, d) {
        return 'M.ciniki_recipes_main.image.open(\'M.ciniki_recipes_main.recipe.open();\',\'' + d.id + '\');';
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
    this.recipe.open = function(cb, rid, list) {
        this.reset();
        if( cb != null ) { this.cb = cb; }
        if( rid != null ) { this.recipe_id = rid; }
        if( list != null ) { this.list = list; }

        M.api.getJSONCb('ciniki.recipes.recipeGet', {'business_id':M.curBusinessID, 'recipe_id':M.ciniki_recipes_main.recipe.recipe_id, 'images':'yes'}, function(rsp) {
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
    this.recipe.addButton('edit', 'Edit', 'M.ciniki_recipes_main.recipe.open(\'M.ciniki_recipes_main.recipe.open();\',M.ciniki_recipes_main.recipe.recipe_id);');
    this.recipe.addButton('next', 'Next');
    this.recipe.addClose('Back');
    this.recipe.addLeftButton('prev', 'Prev');
*/
    //
    // The panel to display the edit form
    //
    this.recipe = new M.panel('Recipe', 'ciniki_recipes_main', 'recipe', 'mc', 'medium mediumaside', 'sectioned', 'ciniki.recipes.main.recipe');
    this.recipe.recipe_id = 0;
    this.recipe.data = null;
    this.recipe.npList = [];
    this.recipe.sections = {
        '_image':{'label':'Image', 'type':'imageform', 'aside':'yes', 'fields':{
            'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no',
                'addDropImage':function(iid) {
                    M.ciniki_recipes_main.recipe.setFieldValue('primary_image_id', iid, null, null);
                    return true;
                    },
                'addDropImageRefresh':'',
                'deleteImage':function(fid) {
                        M.ciniki_recipes_main.recipe.setFieldValue(fid, 0, null, null);
                        return true;
                    },
                },
            }},
        'info':{'label':'Public Information', 'aside':'yes', 'type':'simpleform', 'fields':{
            'name':{'label':'Title', 'required':'yes', 'type':'text'},
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
        'images':{'label':'Additional Images', 'type':'simplethumbs',
//            'visible':function() { return (M.ciniki_recipes_main.product.sections._tabs.selected == 'images' ? 'yes':'hidden');},
            },
        '_images':{'label':'', 'type':'simplegrid', 'num_cols':1,
//            'visible':function() { return (M.ciniki_recipes_main.product.sections._tabs.selected == 'images' ? 'yes':'hidden');},
            'addTxt':'Add Additional Image',
            'addFn':'M.ciniki_recipes_main.recipe.save("M.ciniki_recipes_main.image.open(\'M.ciniki_recipes_main.recipe.refreshImages();\',0,M.ciniki_recipes_main.recipe.recipe_id);");',
            },
        '_buttons':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_recipes_main.recipe.save();'},
            'delete':{'label':'Delete', 'fn':'M.ciniki_recipes_main.recipe.remove();'},
        }},
    };
    this.recipe.fieldValue = function(s, i, d) { 
        return this.data[i]; 
    }
    this.recipe.sectionData = function(s) {
        return this.data[s];
    };
/*      this.recipe.liveSearchCb = function(s, i, value) {
        if( i == 'category' || i == 'cuisine' ) {
            var rsp = M.api.getJSONBgCb('ciniki.recipes.searchField', {'business_id':M.curBusinessID, 'field':i, 'start_needle':value, 'limit':15},
                function(rsp) {
                    M.ciniki_recipes_main.recipe.liveSearchShow(s, i, M.gE(M.ciniki_recipes_main.recipe.panelUID + '_' + i), rsp.results);
                });
        }
    };
    this.recipe.liveSearchResultValue = function(s, f, i, j, d) {
        if( (f == 'category' || f == 'cuisine' ) && d.result != null ) { return d.result.name; }
        return '';
    };
    this.recipe.liveSearchResultRowFn = function(s, f, i, j, d) { 
        if( (f == 'category' || f == 'cuisine' )
            && d.result != null ) {
            return 'M.ciniki_recipes_main.recipe.updateField(\'' + s + '\',\'' + f + '\',\'' + escape(d.result.name) + '\');';
        }
    };
    this.recipe.updateField = function(s, fid, result) {
        M.gE(this.panelUID + '_' + fid).value = unescape(result);
        this.removeLiveSearch(s, fid);
    }; */
    this.recipe.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.recipes.recipeHistory', 'args':{'business_id':M.curBusinessID, 
            'recipe_id':this.recipe_id, 'field':i}};
    }
    this.recipe.addDropImage = function(iid) {
        if( this.recipe_id == 0 ) {
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.recipes.recipeAdd', {'business_id':M.curBusinessID, 'recipe_id':this.recipe_id, 'image_id':iid}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_recipes_main.recipe.recipe_id = rsp.id;
                    M.ciniki_recipes_main.recipe.refreshImages();
                });
        } else {
            M.api.getJSONCb('ciniki.recipes.imageAdd', {'business_id':M.curBusinessID, 'image_id':iid, 'name':'', 'recipe_id':this.recipe_id, 'webflags':1}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_recipes_main.recipe.refreshImages();
            });
        }
        return true;
    };
    this.recipe.thumbFn = function(s, i, d) {
        return 'M.ciniki_recipes_main.image.open(\'M.ciniki_recipes_main.recipe.refreshImages();\',\'' + d.id + '\');';
    };
    this.recipe.refreshImages = function() {
        if( M.ciniki_recipes_main.recipe.recipe_id > 0 ) {
            M.api.getJSONCb('ciniki.recipes.recipeGet', {'business_id':M.curBusinessID, 'recipe_id':this.recipe_id, 'images':'yes'}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_recipes_main.recipe;
                p.data.images = rsp.recipe.images;
                p.refreshSection('images');
                p.show();
            });
        }
    }
    this.recipe.open = function(cb, rid, type, type_name, list) {
        if( rid != null ) { this.recipe_id = rid; }
        if( this.recipe_id == 0 ) {
            this.reset();
            this.sections._buttons.buttons.delete.visible = 'no';
        } else {
            this.sections._buttons.buttons.delete.visible = 'yes';
        }
        if( list != null ) { this.npList = list; }
        M.api.getJSONCb('ciniki.recipes.recipeGet', {'business_id':M.curBusinessID, 'recipe_id':this.recipe_id, 'tags':'yes', 'images':'yes'}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_recipes_main.recipe;
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
    this.recipe.save = function(cb) {
        if( cb == null ) { cb = 'M.ciniki_recipes_main.recipe.close();'; }
        if( !this.checkForm() ) { return false; }
        if( this.recipe_id > 0 ) {
            var c = this.serializeForm('no');
            if( c != '' ) {
                M.api.postJSONCb('ciniki.recipes.recipeUpdate', {'business_id':M.curBusinessID, 'recipe_id':this.recipe_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } else {
                        eval(cb);
                    }
                });
            } else {
                eval(cb);
            }
        } else {
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.recipes.recipeAdd', {'business_id':M.curBusinessID}, c, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                } else {
                    M.ciniki_recipes_main.recipe.recipe_id = rsp.id;
                    eval(cb);
                }
            });
        }
    };
    this.recipe.remove = function() {
        if( confirm('Are you sure you want to delete \'' + this.data.name + '\'?  All information about it will be removed and unrecoverable.') ) {
            M.api.getJSONCb('ciniki.recipes.recipeDelete', {'business_id':M.curBusinessID, 'recipe_id':this.recipe_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_recipes_main.recipe.close();
            });
        }
    };
    this.recipe.nextButtonFn = function() {
        if( this.npList != null && this.npList.indexOf('' + this.recipe_id) < (this.npList.length - 1) ) {
            return 'M.ciniki_recipes_main.recipe.save(\'M.ciniki_recipes_main.recipe.open(null,' + this.npList[this.npList.indexOf('' + this.recipe_id) + 1] + ');\');';
        }
        return null;
    }
    this.recipe.prevButtonFn = function() {
        if( this.npList != null && this.npList.indexOf('' + this.recipe_id) > 0 ) {
            return 'M.ciniki_recipes_main.recipe.save(\'M.ciniki_recipes_main.recipe.open(null,' + this.npList[this.npList.indexOf('' + this.recipe_id) - 1] + ');\');';
        }
        return null;
    }
    this.recipe.addButton('save', 'Save', 'M.ciniki_recipes_main.recipe.save();');
    this.recipe.addButton('next', 'Next');
    this.recipe.addClose('Cancel');
    this.recipe.addLeftButton('prev', 'Prev');

    //
    // The panel to display the edit image form
    //
    this.image = new M.panel('Edit Image', 'ciniki_recipes_main', 'image', 'mc', 'medium mediumaside', 'sectioned', 'ciniki.recipes.main.image');
    this.image.default_data = {};
    this.image.data = {};
    this.image.recipe_id = 0;
    this.image.sections = {
        '_image':{'label':'Photo', 'type':'imageform', 'aside':'yes', 'fields':{
            'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
        }},
        'info':{'label':'Information', 'type':'simpleform', 'fields':{
            'name':{'label':'Title', 'type':'text'},
            'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':{'1':{'name':'Visible'}}},
        }},
        '_description':{'label':'Description', 'type':'simpleform', 'fields':{
            'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
        }},
        '_save':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_recipes_main.image.save();'},
            'delete':{'label':'Delete', 'fn':'M.ciniki_recipes_main.image.remove();'},
        }},
    };
    this.image.fieldValue = function(s, i, d) { 
        if( this.data[i] != null ) {
            return this.data[i]; 
        } 
        return ''; 
    };
    this.image.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.recipes.imageHistory', 'args':{'business_id':M.curBusinessID, 
            'recipe_image_id':this.recipe_image_id, 'field':i}};
    };
    this.image.addDropImage = function(iid) {
        M.ciniki_recipes_main.image.setFieldValue('image_id', iid, null, null);
        return true;
    };
    this.image.open = function(cb, iid, eid) {
        if( iid != null ) { this.recipe_image_id = iid; }
        if( eid != null ) { this.recipe_id = eid; }
        if( this.recipe_image_id > 0 ) {
            M.api.getJSONCb('ciniki.recipes.imageGet', {'business_id':M.curBusinessID, 'recipe_image_id':this.recipe_image_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_recipes_main.image.data = rsp.image;
                M.ciniki_recipes_main.image.refresh();
                M.ciniki_recipes_main.image.show(cb);
            });
        } else {
            this.reset();
            this.data = {'webflags':1};
            this.refresh();
            this.show(cb);
        }
    };
    this.image.save = function() {
        if( this.recipe_image_id > 0 ) {
            var c = this.serializeFormData('no');
            if( c != '' ) {
                M.api.postJSONFormData('ciniki.recipes.imageUpdate', {'business_id':M.curBusinessID, 'recipe_image_id':this.recipe_image_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } else {
                        M.ciniki_recipes_main.image.close();
                    }
                });
            } else {
                this.close();
            }
        } else {
            var c = this.serializeFormData('yes');
            var rsp = M.api.postJSONFormData('ciniki.recipes.imageAdd', {'business_id':M.curBusinessID, 'recipe_id':this.recipe_id}, c, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                } else {
                    M.ciniki_recipes_main.image.close();
                }
            });
        }
    };
    this.image.remove = function() {
        if( confirm('Are you sure you want to delete this image?') ) {
            M.api.getJSONCb('ciniki.recipes.imageDelete', {'business_id':M.curBusinessID, 'recipe_image_id':this.recipe_image_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_recipes_main.image.close();
            });
        }
    };
    this.image.addButton('save', 'Save', 'M.ciniki_recipes_main.image.save();');
    this.image.addClose('Cancel');
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

        this.tagTypes['10'].visible = M.modFlagSet('ciniki.recipes', 0x01);
        this.tagTypes['20'].visible = M.modFlagSet('ciniki.recipes', 0x02);
        this.tagTypes['30'].visible = M.modFlagSet('ciniki.recipes', 0x04);
        this.tagTypes['40'].visible = M.modFlagSet('ciniki.recipes', 0x08);
        this.tagTypes['50'].visible = M.modFlagSet('ciniki.recipes', 0x10);
        this.tagTypes['60'].visible = M.modFlagSet('ciniki.recipes', 0x20);
        this.tagTypes['70'].visible = M.modFlagSet('ciniki.recipes', 0x40);
        this.tagTypes['80'].visible = M.modFlagSet('ciniki.recipes', 0x80);
        this.tagTypes['90'].visible = M.modFlagSet('ciniki.recipes', 0x0100);
        this.tagTypes['100'].visible = M.modFlagSet('ciniki.recipes', 0x0200);
    
        this.menu.formtab = null;
        for(i in this.tagTypes) {
            this.menu.formtabs.tabs[i].visible = this.tagTypes[i].visible;
//            this.recipe.sections.info.list['tag-'+i].visible = this.tagTypes[i].visible;
            this.recipe.sections['_'+i].active = this.tagTypes[i].visible;
            if( this.menu.formtab == null && this.tagTypes[i].visible == 'yes' ) {
                this.menu.formtab = i;
            }
        }

        this.menu.tag_name = null;
        this.menu.open(cb);
    }
}
