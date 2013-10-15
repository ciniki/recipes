<?php
//
// Description
// ===========
// This method updates one or more elements of an existing recipe.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to the recipe is a part of.
// recipe_id:		The ID of the recipe to update.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_recipes_recipeUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'recipe_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Recipe'), 
        'name'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Name'), 
		'image_id'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Image'),
        'category'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Category'), 
        'cuisine'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Cuisine'), 
        'num_servings'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Number of Servings'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Web Flags'), 
        'prep_time'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Prep Time'), 
        'cook_time'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Cook Time'), 
        'description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Description'), 
        'ingredients'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Ingredients'), 
        'instructions'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Instructions'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Notes'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'private', 'checkAccess');
    $rc = ciniki_recipes_checkAccess($ciniki, $args['business_id'], 'ciniki.recipes.recipeUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	if( isset($args['name']) ) {
		$args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args['name'])));
		//
		// Make sure the permalink is unique
		//
		$strsql = "SELECT id, name, permalink FROM ciniki_recipes "
			. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
			. "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
			. "";
		$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'recipe');
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( $rc['num_rows'] > 0 ) {
			return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1357', 'msg'=>'You already have a recipe with this name, please choose another name'));
		}
	}

	//  
	// Turn off autocommit
	//  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbUpdate');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbAddModuleHistory');
	$rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) { 
		return $rc;
	}   

	// Get the existing image_id
	$strsql = "SELECT image_id FROM ciniki_recipes "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'recipe');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['recipe']) ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1358', 'msg'=>'Recipe not found'));
	}
	$recipe = $rc['recipe'];

	//
	// Keep track if anything has been updated
	//
	$updated = 0;

	//
	// Start building the update SQL
	//
	$strsql = "UPDATE ciniki_recipes SET last_updated = UTC_TIMESTAMP()";

	//
	// Add all the fields to the change log
	//
	$changelog_fields = array(
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
		if( isset($args[$field]) ) {
			$strsql .= ", $field = '" . ciniki_core_dbQuote($ciniki, $args[$field]) . "' ";
			$rc = ciniki_core_dbAddModuleHistory($ciniki, 'ciniki.recipes', 'ciniki_recipe_history', $args['business_id'], 
				2, 'ciniki_recipes', $args['recipe_id'], $field, $args[$field]);
			$updated = 1;
		}
	}

	//
	// Only update the record, and last_updated if there is something to update, or lists were updated
	//
	if( $updated > 0 ) {
		$strsql .= "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' ";
		$rc = ciniki_core_dbUpdate($ciniki, $strsql, 'ciniki.recipes');
		if( $rc['stat'] != 'ok' ) {
			ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
			return $rc;
		}
		if( !isset($rc['num_affected_rows']) || $rc['num_affected_rows'] != 1 ) {
			ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
			return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1359', 'msg'=>'Unable to update recipe'));
		}
	}

	//
	// Update image reference
	//
	if( isset($args['image_id']) && $recipe['image_id'] != $args['image_id']) {
		//
		// Remove the old reference, and remove image if no more references
		//
		ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'refClear');
		$rc = ciniki_images_refClear($ciniki, $args['business_id'], array(
			'object'=>'ciniki.recipes.recipe', 
			'object_id'=>$args['recipe_id']));
		if( $rc['stat'] == 'fail' ) {
			ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
			return $rc;
		}

		//
		// Add the new reference
		//
		ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'refAdd');
		$rc = ciniki_images_refAdd($ciniki, $args['business_id'], array(
			'image_id'=>$args['image_id'], 
			'object'=>'ciniki.recipes.recipe', 
			'object_id'=>$args['recipe_id'],
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
	if( $updated > 0 ) {
		ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'updateModuleChangeDate');
		ciniki_businesses_updateModuleChangeDate($ciniki, $args['business_id'], 'ciniki', 'recipes');

		//
		// Add to the sync queue so it will get pushed
		//
		$ciniki['syncqueue'][] = array('push'=>'ciniki.recipes.recipe', 
			'args'=>array('id'=>$args['recipe_id']));
	}

	return array('stat'=>'ok');
}
?>
