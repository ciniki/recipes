<?php
//
// Description
// ===========
//
// Arguments
// ---------
// 
// Returns
// -------
// <rsp stat='ok' id='34' />
//
function ciniki_recipes_imageAdd(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'recipe_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Event'), 
        'name'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Title'), 
        'permalink'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Permalink'), 
        'webflags'=>array('required'=>'no', 'default'=>'0', 'blank'=>'yes', 'name'=>'Website Flags'), 
        'sequence'=>array('required'=>'no', 'default'=>'0', 'blank'=>'yes', 'name'=>'Sequence'), 
		'image_id'=>array('required'=>'yes', 'blank'=>'yes', 'name'=>'Image'),
        'description'=>array('required'=>'no', 'default'=>'', 'blank'=>'yes', 'name'=>'Description'), 
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['business_id'], 'ciniki.recipes.imageAdd'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    } 

	//
	// Get a UUID for use in permalink
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbUUID');
	$rc = ciniki_core_dbUUID($ciniki, 'ciniki.recipes');
	if( $rc['stat'] != 'ok' ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1341', 'msg'=>'Unable to get a new UUID', 'err'=>$rc['err'])    );
	}
	$args['uuid'] = $rc['uuid'];

	//
	// Determine the permalink
	//
	if( !isset($args['permalink']) || $args['permalink'] == '' ) {
		if( isset($args['name']) && $args['name'] != '' ) {
			$args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 \-]/', '', strtolower($args['name'])));
		} else {
			$args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args['uuid'])));
		}
	}

	//
	// Check the permalink doesn't already exist
	//
	$strsql = "SELECT id, name, permalink FROM ciniki_recipe_images "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND recipe_id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
		. "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'image');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( $rc['num_rows'] > 0 ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1326', 'msg'=>'You already have an image with this name, please choose another name'));
	}


	if( $args['recipe_id'] <= 0 ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1323', 'msg'=>'No recipe specified'));
	}
   
	//
	// Add the recipe image to the database
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
	return ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.recipes.image', $args, 0x07);
}
?>
