<?php
//
// Description
// ===========
// This method will add a new recipe to the database.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to add the recipe to.  The user must
//					an owner of the business.
//
// 
// Returns
// -------
// <rsp stat='ok' id='34' />
//
function ciniki_recipes_recipeAdd(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'name'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Name'), 
		'image_id'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'0', 'name'=>'Image'),
        'num_servings'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Number of Servings'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Web Flags'), 
        'prep_time'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Prep Time'), 
        'roast_time'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Roast Time'), 
        'cook_time'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Cook Time'), 
        'synopsis'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Synopsis'), 
        'description'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Description'), 
        'ingredients'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Ingredients'), 
        'instructions'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Instructions'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Notes'), 
		'tag-10'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Meals'),
		'tag-20'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Ingredients'),
		'tag-30'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Cuisines'),
		'tag-40'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Methods'),
		'tag-50'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Occasions'),
		'tag-60'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Diets'),
		'tag-70'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Seasons'),
		'tag-80'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Collections'),
		'tag-90'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Products'),
		'tag-100'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Contributors'),
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
	$args['permalink'] = ciniki_core_makePermalink($ciniki, $args['name']);
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'private', 'checkAccess');
    $rc = ciniki_recipes_checkAccess($ciniki, $args['business_id'], 'ciniki.recipes.recipeAdd'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//
	// Check the permalink doesn't already exist
	//
	$strsql = "SELECT id, name, permalink FROM ciniki_recipes "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'recipe');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( $rc['num_rows'] > 0 ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1355', 'msg'=>'You already have a recipe with this name, please choose another name.'));
	}

	//  
	// Turn off autocommit
	//  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	$rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) { 
		return $rc;
	}   

	//
	// Add the recipe
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
	$rc = ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.recipes.recipe', $args);
	if( $rc['stat'] != 'ok' ) {	
		return $rc;
	}
	$recipe_id = $rc['id'];

	//
	// Update the tags
	//
	$tag_types = array(
		'10'=>'meals',
		'20'=>'mainingredients',
		'30'=>'cuisines',
		'40'=>'methods',
		'50'=>'occasions',
		'60'=>'diets',
		'70'=>'seasons',
		'80'=>'collections',
		'90'=>'products',
		'100'=>'contributors',
		);
	foreach($tag_types as $tag_type => $arg_name) {
		if( isset($args['tag-' . $tag_type]) ) {
			ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'tagsUpdate');
			$rc = ciniki_core_tagsUpdate($ciniki, 'ciniki.recipes', 'tag', $args['business_id'],
				'ciniki_recipe_tags', 'ciniki_recipe_history',
				'recipe_id', $recipe_id, $tag_type, $args['tag-' . $tag_type]);
			if( $rc['stat'] != 'ok' ) {
				ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
				return $rc;
			}
		}
	}

	//
	// Commit the database changes
	//
    $rc = ciniki_core_dbTransactionCommit($ciniki, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	//
	// Update the last_change date in the business modules
	// Ignore the result, as we don't want to stop user updates if this fails.
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'updateModuleChangeDate');
	ciniki_businesses_updateModuleChangeDate($ciniki, $args['business_id'], 'ciniki', 'recipes');

	return array('stat'=>'ok', 'id'=>$recipe_id);
}
?>
