<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to add the recipe to.
// name:                The name of the recipe.  
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_recipes_imageUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'recipe_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Event Image'), 
        'image_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Image'),
        'name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Title'), 
        'permalink'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Permalink'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Website Flags'), 
        'sequence'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sequence'), 
        'description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Description'), 
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['tnid'], 'ciniki.recipes.imageUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //
    // Get the existing image details
    //
    $strsql = "SELECT uuid, recipe_id, image_id "
        . "FROM ciniki_recipe_images "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_image_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'item');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['item']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.recipes.10', 'msg'=>'Event image not found'));
    }
    $item = $rc['item'];

    if( isset($args['name']) ) {
        if( $args['name'] != '' ) {
            $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args['name'])));
        } else {
            $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($item['uuid'])));
        }
        //
        // Make sure the permalink is unique
        //
        $strsql = "SELECT id, name, permalink FROM ciniki_recipe_images "
            . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND recipe_id = '" . ciniki_core_dbQuote($ciniki, $item['recipe_id']) . "' "
            . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['recipe_image_id']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'image');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( $rc['num_rows'] > 0 ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.recipes.11', 'msg'=>'You already have an image with this name, please choose another name'));
        }
    }

    //
    // Update the recipe in the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    return ciniki_core_objectUpdate($ciniki, $args['tnid'], 'ciniki.recipes.image', $args['recipe_image_id'], $args);
}
?>
