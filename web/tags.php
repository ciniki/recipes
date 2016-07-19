<?php
//
// Description
// -----------
// This function will return a list of categories for the web recipes page.
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure.
// business_id:     The ID of the business to get events for.
//
// Returns
// -------
// <categories>
//      <category name="Indian" image_id="349" />
//      <category name="Stews & Soups" image_id="418" />
//      ...
// </categories>
//
function ciniki_recipes_web_tags($ciniki, $settings, $business_id, $tag_type) {

    $strsql = "SELECT ciniki_recipe_tags.tag_name, "
        . "ciniki_recipe_tags.permalink, "
        . "COUNT(ciniki_recipe_tags.recipe_id) AS num_tags, "
        . "MAX(ciniki_recipes.primary_image_id) AS image_id "
        . "FROM ciniki_recipe_tags, ciniki_recipes "
        . "WHERE ciniki_recipe_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $tag_type) . "' "
        . "AND ciniki_recipe_tags.recipe_id = ciniki_recipes.id "
        . "AND ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "AND (ciniki_recipes.webflags&0x01) = 1 "
        . "GROUP BY ciniki_recipe_tags.tag_name "
        . "ORDER BY ciniki_recipe_tags.tag_name, ciniki_recipes.primary_image_id ASC, ciniki_recipes.date_added DESC "
        . "";

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.blog', array(
        array('container'=>'tags', 'fname'=>'permalink', 
            'fields'=>array('name'=>'tag_name', 'permalink', 'num_tags', 'image_id')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['tags']) ) {
        return array('stat'=>'ok');
    }
    $tags = $rc['tags'];

    return array('stat'=>'ok', 'tags'=>$tags);  
}
?>
