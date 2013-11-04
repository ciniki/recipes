<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:			The ID of the business to add the recipe to.
// recipe_image_id:	The ID of the recipe image to get.
//
// Returns
// -------
//
function ciniki_recipes_imageGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'recipe_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Event Image'),
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['business_id'], 'ciniki.recipes.imageGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
	$date_format = ciniki_users_dateFormat($ciniki);

	//
	// Get the main information
	//
	$strsql = "SELECT ciniki_recipe_images.id, "
		. "ciniki_recipe_images.name, "
		. "ciniki_recipe_images.permalink, "
		. "ciniki_recipe_images.webflags, "
		. "ciniki_recipe_images.image_id, "
		. "ciniki_recipe_images.description "
		. "FROM ciniki_recipe_images "
		. "WHERE ciniki_recipe_images.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND ciniki_recipe_images.id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_image_id']) . "' "
		. "";
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.recipes', array(
		array('container'=>'images', 'fname'=>'id', 'name'=>'image',
			'fields'=>array('id', 'name', 'permalink', 'webflags', 'image_id', 'description')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['images']) ) {
		return array('stat'=>'ok', 'err'=>array('pkg'=>'ciniki', 'code'=>'1127', 'msg'=>'Unable to find image'));
	}
	$image = $rc['images'][0]['image'];
	
	return array('stat'=>'ok', 'image'=>$image);
}
?>
