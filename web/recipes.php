<?php
//
// Description
// -----------
// This function will return a list of recipes for a category/cuisine/tag
//
// Arguments
// ---------
// ciniki:
// settings:		The web settings structure.
// business_id:		The ID of the business to get events for.
// type:			The list to return, either by category or year.
//
//					- category
//					- year
//
// type_name:		The name of the category or year to list.
//
// Returns
// -------
// <images>
//		[title="Slow River" permalink="slow-river" image_id="431" 
//			caption="Based on a photograph taken near Slow River, Ontario, Pastel, size: 8x10" sold="yes"
//			last_updated="1342653769"],
//		[title="Open Field" permalink="open-field" image_id="217" 
//			caption="An open field in Ontario, Oil, size: 8x10" sold="yes"
//			last_updated="1342653769"],
//		...
// </images>
//
function ciniki_recipes_web_recipes($ciniki, $settings, $business_id, $args) {

	$strsql = "SELECT ciniki_recipes.id, "
		. "ciniki_recipes.name AS title, "
		. "ciniki_recipes.permalink, "
		. "ciniki_recipes.image_id, "
		. "ciniki_recipes.num_servings, "
		. "ciniki_recipes.prep_time, "
		. "ciniki_recipes.roast_time, "
		. "ciniki_recipes.cook_time, "
		. "ciniki_recipes.synopsis, "
		. "ciniki_recipes.description, "
		. "'yes' AS is_details, "
		. "UNIX_TIMESTAMP(ciniki_recipes.last_updated) AS last_updated "
		. "";
/*	if( isset($args['category']) && $args['category'] != '' ) {
		$strsql .= "FROM ciniki_recipes "
			. "WHERE ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
			. "AND ciniki_recipes.category = '" . ciniki_core_dbQuote($ciniki, $args['category']) . "' "
			. "AND (ciniki_recipes.webflags&0x01) = 1 "
			. "";
	} elseif( isset($args['cuisine']) && $args['cuisine'] != '' ) {
		$strsql .= "FROM ciniki_recipes "
			. "WHERE ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
			. "AND ciniki_recipes.cuisine = '" . ciniki_core_dbQuote($ciniki, $args['cuisine']) . "' "
			. "AND (ciniki_recipes.webflags&0x01) = 1 "
			. "";
	} else */
if( isset($args['tag_type']) && $args['tag_type'] != '' && isset($args['tag_permalink']) && $args['tag_permalink'] != '' ) {
		$strsql .= ", ciniki_recipe_tags.tag_name "
			. "FROM ciniki_recipe_tags, ciniki_recipes "
			. "WHERE ciniki_recipe_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
			. "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
			. "AND ciniki_recipe_tags.permalink = '" . ciniki_core_dbQuote($ciniki, $args['tag_permalink']) . "' "
			. "AND ciniki_recipe_tags.recipe_id = ciniki_recipes.id "
			. "AND ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
			. "AND (ciniki_recipes.webflags&0x01) = 1 "
			. "";
//	} elseif( isset($args['tag']) && $args['tag'] != '' ) {
//		$strsql .= ", ciniki_recipe_tags.tag_name "
//			. "FROM ciniki_recipe_tags, ciniki_recipes "
//			. "WHERE ciniki_recipe_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
//			. "AND ciniki_recipe_tags.permalink = '" . ciniki_core_dbQuote($ciniki, $args['tag']) . "' "
//			. "AND ciniki_recipe_tags.recipe_id = ciniki_recipes.id "
//			. "AND ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
//			. "AND (ciniki_recipes.webflags&0x01) = 1 "
//			. "";
	} else {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'1555', 'msg'=>"Unable to find recipes."));
	}
	$strsql .= "ORDER BY ciniki_recipes.name ASC ";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', '');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	$recipes = $rc['rows'];
	
	return array('stat'=>'ok', 'recipes'=>$recipes);
}
?>
