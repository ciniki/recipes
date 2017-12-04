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
// tnid:         The ID of the tenant to remove the recipe from.
// recipe_id:           The ID of the recipe to be removed.
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
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'recipe_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Recipe'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'private', 'checkAccess');
    $rc = ciniki_recipes_checkAccess($ciniki, $args['tnid'], 'ciniki.recipes.recipeDelete'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $modules = $rc['modules'];

    //
    // Get the uuid of the recipes to be deleted
    //
    $strsql = "SELECT uuid FROM ciniki_recipes "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'recipe');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['recipe']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.recipes.14', 'msg'=>'Unable to find existing recipe'));
    }
    $uuid = $rc['recipe']['uuid'];

    //  
    // Turn off autocommit
    // 
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDelete');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbObjectDelete');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbAddModuleHistory');
    $rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.recipes');
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Remove any additional images
    //
    $strsql = "SELECT id, uuid, image_id FROM ciniki_recipe_images "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND recipe_id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'image');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['rows']) ) {
        $images = $rc['rows'];
        foreach($rc['rows'] as $rid => $row) {
            $rc = ciniki_core_objectDelete($ciniki, $args['tnid'], 'ciniki.recipes.image',
                $row['id'], $row['uuid'], 0x04);
            if( $rc['stat'] != 'ok' ) {
                ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
                return $rc;
            }
        }
    }

    //
    // run ciniki.recipes.recipeDelete hooks
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'methodFishHooks');
    $rc = ciniki_core_methodFishHooks($ciniki, $args['tnid'], 'ciniki.recipes.recipeDelete', array('recipe_id'=>$args['recipe_id']));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // FIXME: Delete any notes
    //

    //
    // Remove the recipe
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectDelete');
    $rc = ciniki_core_objectDelete($ciniki, $args['tnid'], 'ciniki.recipes.recipe', $args['recipe_id'], $uuid);
    if( $rc['stat'] != 'ok' ) {
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
    // Update the last_change date in the tenant modules
    // Ignore the result, as we don't want to stop user updates if this fails.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'updateModuleChangeDate');
    ciniki_tenants_updateModuleChangeDate($ciniki, $args['tnid'], 'ciniki', 'recipes');

    return array('stat'=>'ok');
}
?>
