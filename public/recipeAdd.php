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
        'category'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Category'), 
        'cuisine'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Cuisine'), 
        'num_servings'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Number of Servings'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Web Flags'), 
        'prep_time'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Prep Time'), 
        'cook_time'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Cook Time'), 
        'description'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Description'), 
        'ingredients'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Ingredients'), 
        'instructions'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Instructions'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Notes'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

	$args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args['name'])));
    
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
	// Turn off autocommit
	//  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbUUID');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbInsert');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbAddModuleHistory');
	$rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) { 
		return $rc;
	}   

	//
	// Get a new UUID
	//
	$rc = ciniki_core_dbUUID($ciniki, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$args['uuid'] = $rc['uuid'];

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
	// Add the recipes to the database
	//
	$strsql = "INSERT INTO ciniki_recipes (uuid, business_id, name, permalink, "
		. "image_id, category, cuisine, num_servings, webflags, prep_time, cook_time, "
		. "description, ingredients, instructions, "
		. "date_added, last_updated) VALUES ("
		. "'" . ciniki_core_dbQuote($ciniki, $args['uuid']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['name']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['image_id']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['category']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['cuisine']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['num_servings']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['webflags']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['prep_time']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['cook_time']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['description']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['ingredients']) . "', "
		. "'" . ciniki_core_dbQuote($ciniki, $args['instructions']) . "', "
		. "UTC_TIMESTAMP(), UTC_TIMESTAMP())"
		. "";
	$rc = ciniki_core_dbInsert($ciniki, $strsql, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) { 
		ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
		return $rc;
	}
	if( !isset($rc['insert_id']) || $rc['insert_id'] < 1 ) {
		ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1356', 'msg'=>'Unable to add recipe'));
	}
	$recipe_id = $rc['insert_id'];

	//
	// Add to the sync queue so it will get pushed.  This should be added
	// before the tags so it will be pushed ahead of tags and won't cause
	// extra sync calls.
	//
	$ciniki['syncqueue'][] = array('push'=>'ciniki.recipes.recipe', 
		'args'=>array('id'=>$recipe_id));

	//
	// Add all the fields to the change log
	//
	$changelog_fields = array(
		'uuid',
		'name',
		'permalink',
		'image_id',
		'category',
		'cuisine',
		'num_servings',
		'webflags',
		'prep_time',
		'cook_time',
		'description',
		'ingredients',
		'instructions',
		);
	foreach($changelog_fields as $field) {
		if( isset($args[$field]) && $args[$field] != '' ) {
			$rc = ciniki_core_dbAddModuleHistory($ciniki, 'ciniki.recipes', 
				'ciniki_recipe_history', $args['business_id'], 1, 'ciniki_recipes', 
				$recipe_id, $field, $args[$field]);
		}
	}

	//
	// Add image reference
	//
	if( $args['image_id'] > 0 ) {
		ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'refAdd');
		$rc = ciniki_images_refAdd($ciniki, $args['business_id'], array(
			'image_id'=>$args['image_id'], 
			'object'=>'ciniki.recipes.recipe', 
			'object_id'=>$recipe_id,
			'object_field'=>'image_id'));
		if( $rc['stat'] != 'ok' ) {
			ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
			return $rc;
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
