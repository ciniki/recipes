<?php
//
// Description
// ===========
// This method will remove a recipe from the database.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id: 		The ID of the business to remove the recipe from.
// recipe_id:			The ID of the recipe to be removed.
// 
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_recipes_recipeDelete(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'recipe_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Recipe'), 
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['business_id'], 'ciniki.recipes.recipeDelete'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//  
	// Turn off autocommit
	// 
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDelete');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbAddModuleHistory');
	$rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) { 
		return $rc;
	}   

	//
	// Get the uuid of the recipes to be deleted
	//
	$strsql = "SELECT uuid, image_id FROM ciniki_recipes "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'recipe');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['recipe']) ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1360', 'msg'=>'Unable to find existing recipe'));
	}
	$uuid = $rc['recipe']['uuid'];
	$image_id = $rc['recipe']['image_id'];

	//
	// Remove any additional images
	//
//	$strsql = "SELECT id, uuid, image_id FROM ciniki_recipe_images "
//		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
//		. "AND recipe_id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
//		. "";
//	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'image');
//	if( $rc['stat'] != 'ok' ) {
//		return $rc;
//	}
//	if( isset($rc['rows']) ) {
//		ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'refClear');
//		foreach($rc['rows'] as $rid => $row) {
//			//
//			// Delete the reference to the image, and remove the image if no more references
//			//
//			$rc = ciniki_images_refClear($ciniki, $args['business_id'], array(
//				'object'=>'ciniki.recipes.image',
//				'object_id'=>$row['id']));
//			if( $rc['stat'] == 'fail' ) {
//				ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
//				return $rc;
//			}
//
//			//
//			// Remove the image from the database
//			//
//			$strsql = "DELETE FROM ciniki_recipe_images "
//				. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
//				. "AND id = '" . ciniki_core_dbQuote($ciniki, $row['id']) . "' ";
//			$rc = ciniki_core_dbDelete($ciniki, $strsql, 'ciniki.recipes');
//			if( $rc['stat'] != 'ok' ) { 
//				ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
//				return $rc;
//			}
//
//			ciniki_core_dbAddModuleHistory($ciniki, 'ciniki.recipes', 'ciniki_recipe_history', 
//				$args['business_id'], 1, 'ciniki_recipe_images', $row['id'], '*', '');
//			$ciniki['syncqueue'][] = array('push'=>'ciniki.recipes.image',
//				'args'=>array('delete_uuid'=>$row['uuid'], 'delete_id'=>$row['id']));
//		}
//	}

	//
	// FIXME: Delete any notes
	//

	//
	// Start building the delete SQL
	//
	$strsql = "DELETE FROM ciniki_recipes "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
		. "";

	$rc = ciniki_core_dbDelete($ciniki, $strsql, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) {
		ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
		return $rc;
	}
	if( !isset($rc['num_affected_rows']) || $rc['num_affected_rows'] != 1 ) {
		ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1361', 'msg'=>'Unable to delete recipe'));
	}

	$rc = ciniki_core_dbAddModuleHistory($ciniki, 'ciniki.recipes', 'ciniki_recipe_history', 
		$args['business_id'], 3, 'ciniki_recipes', $args['recipe_id'], '*', '');

	//
	// Remove the reference, and remove image if no more references
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

	$ciniki['syncqueue'][] = array('push'=>'ciniki.recipes.recipe', 
		'args'=>array('delete_uuid'=>$uuid, 'delete_id'=>$args['recipe_id']));

	return array('stat'=>'ok');
}
?>
