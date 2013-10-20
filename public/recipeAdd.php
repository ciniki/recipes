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
	// Add the recipe
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
	return ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.recipes.recipe', $args);
}
?>
