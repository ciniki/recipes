<?php
//
// Description
// ===========
// This method will return all information for a recipe.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id: 		The ID of the business to get the recipe from.
// recipe_id:			The ID of the recipe to get.
// 
// Returns
// -------
//
function ciniki_recipes_recipeGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'recipe_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Item'), 
		'images'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Images'),
		'tags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Tags'),
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['business_id'], 'ciniki.recipes.recipeGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'timezoneOffset');
	$utc_offset = ciniki_users_timezoneOffset($ciniki);

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'datetimeFormat');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');

	$datetime_format = ciniki_users_datetimeFormat($ciniki);
	$date_format = ciniki_users_dateFormat($ciniki);

	if( $args['recipe_id'] == 0 ) {
		$recipe = array(
			'id'=>'0',
			'name'=>'',
			'permalink'=>'',
			'num_servings'=>'',
			'webflags'=>'1',
			'prep_time'=>'',
			'roast_time'=>'',
			'cook_time'=>'',
			'synopsis'=>'',
			'description'=>'',
			'ingredients'=>'',
			'instructions'=>'',
			'website'=>'',
			);
	} else {
		$strsql = "SELECT ciniki_recipes.id, ciniki_recipes.name, ciniki_recipes.permalink, "
			. "ciniki_recipes.image_id, "
			. "ciniki_recipes.num_servings, "
			. "ciniki_recipes.webflags, "
			. "ciniki_recipes.prep_time, "
			. "ciniki_recipes.roast_time, "
			. "ciniki_recipes.cook_time, "
			. "ciniki_recipes.synopsis, "
			. "ciniki_recipes.description, "
			. "ciniki_recipes.ingredients, "
			. "ciniki_recipes.instructions, "
			. "CONCAT_WS('', IF((ciniki_recipes.webflags&0x01)=0, 'hidden', 'visible'), IF((ciniki_recipes.webflags&0x10)=0x02, ', highlight', '')) AS website "
			. "FROM ciniki_recipes "
			. "WHERE ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_recipes.id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
			. "";

		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.recipes', array(
			array('container'=>'recipes', 'fname'=>'id', 'name'=>'recipe',
				'fields'=>array('id', 'name', 'permalink', 'image_id', 
					'num_servings', 'webflags', 'prep_time', 'roast_time', 'cook_time', 
					'synopsis', 'description', 'ingredients', 'instructions', 'website'),
				),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( !isset($rc['recipes']) ) {
			return array('stat'=>'ok', 'err'=>array('pkg'=>'ciniki', 'code'=>'1363', 'msg'=>'Unable to find recipe'));
		}
		$recipe = $rc['recipes'][0]['recipe'];

		//
		// Get the categories and tags for the post
		//
		$strsql = "SELECT CONCAT('tag-', tag_type) AS tagtype, tag_type, tag_name AS lists "
			. "FROM ciniki_recipe_tags "
			. "WHERE recipe_id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
			. "AND business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY tag_type, tag_name "
			. "";
		$rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.recipes', array(
			array('container'=>'tags', 'fname'=>'tagtype', 'name'=>'tags',
				'fields'=>array('tag_type', 'lists'), 'dlists'=>array('lists'=>'::')),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['tags']) ) {
			foreach($rc['tags'] as $arg => $tag) {
				$recipe[$arg] = $tag['lists'];
			}
		}

		//
		// Get the additional images if requested
		//
		if( isset($args['images']) && $args['images'] == 'yes' ) {
			ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheThumbnail');
			$strsql = "SELECT id, image_id, name, sequence, webflags, description "
				. "FROM ciniki_recipe_images "
				. "WHERE recipe_id = '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
				. "AND business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. "ORDER BY sequence, date_added, name "
				. "";
			$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.recipes', array(
				array('container'=>'images', 'fname'=>'id', 'name'=>'image',
					'fields'=>array('id', 'image_id', 'name', 'sequence', 'webflags', 'description')),
				));
			if( $rc['stat'] != 'ok' ) {	
				return $rc;
			}
			if( isset($rc['images']) ) {
				$recipe['images'] = $rc['images'];
				foreach($recipe['images'] as $inum => $img) {
					if( isset($img['image']['image_id']) && $img['image']['image_id'] > 0 ) {
						$rc = ciniki_images_loadCacheThumbnail($ciniki, $args['business_id'], $img['image']['image_id'], 75);
						if( $rc['stat'] != 'ok' ) {
							return $rc;
						}
						$recipe['images'][$inum]['image']['image_data'] = 'data:image/jpg;base64,' . base64_encode($rc['image']);
					}
				}
			}
		}
	}

	//
	// Check if all tags should be returned
	//
	$tags = array();
	if( isset($args['tags']) && $args['tags'] == 'yes' ) {
		//
		// Get the available tags
		//
		$strsql = "SELECT DISTINCT CONCAT('tag-', tag_type) AS tagtype, tag_type, tag_name AS tag_names "
			. "FROM ciniki_recipe_tags "
			. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY tag_type, tag_name "
			. "";
		$rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.recipes', array(
			array('container'=>'tags', 'fname'=>'tagtype', 'name'=>'tag',
				'fields'=>array('tag_type', 'tag_names'), 'dlists'=>array('tag_names'=>'::')),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['tags']) ) {
			$tags = $rc['tags'];
		}
	}

	return array('stat'=>'ok', 'recipe'=>$recipe, 'tags'=>$tags);
}
?>
