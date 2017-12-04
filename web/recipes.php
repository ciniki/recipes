<?php
//
// Description
// -----------
// This function will return a list of recipes for a category/cuisine/tag
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure.
// tnid:     The ID of the tenant to get events for.
//
// Returns
// -------
//
function ciniki_recipes_web_recipes($ciniki, $settings, $tnid, $args) {

    $strsql = "SELECT ciniki_recipes.id, "
        . "ciniki_recipes.name AS title, "
        . "ciniki_recipes.permalink, "
        . "ciniki_recipes.primary_image_id AS image_id, "
        . "ciniki_recipes.num_servings, "
        . "ciniki_recipes.prep_time, "
        . "ciniki_recipes.roast_time, "
        . "ciniki_recipes.cook_time, "
        . "ciniki_recipes.synopsis, "
        . "ciniki_recipes.description, "
        . "'yes' AS is_details, "
        . "UNIX_TIMESTAMP(ciniki_recipes.last_updated) AS last_updated "
        . "";
if( isset($args['tag_type']) && $args['tag_type'] != '' && isset($args['tag_permalink']) && $args['tag_permalink'] != '' ) {
        $strsql .= ", ciniki_recipe_tags.tag_name "
            . "FROM ciniki_recipe_tags, ciniki_recipes "
            . "WHERE ciniki_recipe_tags.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
            . "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
            . "AND ciniki_recipe_tags.permalink = '" . ciniki_core_dbQuote($ciniki, $args['tag_permalink']) . "' "
            . "AND ciniki_recipe_tags.recipe_id = ciniki_recipes.id "
            . "AND ciniki_recipes.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
            . "AND (ciniki_recipes.webflags&0x01) = 1 "
            . "";
    } else {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.recipes.23', 'msg'=>"Unable to find recipes."));
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
