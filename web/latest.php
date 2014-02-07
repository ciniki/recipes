<?php
//
// Description
// -----------
// This function will return a list of categories for the recipes.
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
function ciniki_recipes_web_latest($ciniki, $settings, $business_id, $limit) {

	$strsql = "SELECT ciniki_recipes.id, "
		. "ciniki_recipes.name AS title, "
		. "ciniki_recipes.permalink, "
		. "ciniki_recipes.image_id, "
		. "ciniki_recipes.num_servings, "
		. "ciniki_recipes.prep_time, "
		. "ciniki_recipes.cook_time, "
		. "ciniki_recipes.description, "
		. "'yes' AS is_details, "
		. "IF(ciniki_images.last_updated > ciniki_recipes.last_updated, UNIX_TIMESTAMP(ciniki_images.last_updated), UNIX_TIMESTAMP(ciniki_recipes.last_updated)) AS last_updated "
		. "FROM ciniki_recipes "
		. "LEFT JOIN ciniki_images ON (ciniki_recipes.image_id = ciniki_images.id) "
		. "WHERE ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
		. "AND (ciniki_recipes.webflags&0x01) = 0 "
		. "ORDER BY ciniki_recipes.date_added DESC ";

	$strsql .= "LIMIT $limit ";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', '');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	$recipes = $rc['rows'];
	
	return array('stat'=>'ok', 'recipes'=>$recipes);
}
?>
