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
function ciniki_recipes_web_categories($ciniki, $settings, $business_id) {

    $strsql = "SELECT DISTINCT category AS name  "
        . "FROM ciniki_recipes "
        . "WHERE ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "AND (ciniki_recipes.webflags&0x01) = 1 "
        . "AND category <> '' "
        . "ORDER BY category "
        . "";
    
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.recipes', array(
        array('container'=>'categories', 'fname'=>'name', 'name'=>'category',
            'fields'=>array('name')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['categories']) ) {
        return array('stat'=>'ok');
    }
    $categories = $rc['categories'];

    //
    // Load highlight images
    //
    foreach($categories as $cnum => $cat) {
        //
        // Look for the highlight image, or the most recently added image
        //
        $strsql = "SELECT ciniki_recipes.image_id AS primary_image_id, ciniki_images.image "
            . "FROM ciniki_recipes, ciniki_images "
            . "WHERE ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND category = '" . ciniki_core_dbQuote($ciniki, $cat['category']['name']) . "' "
            . "AND ciniki_recipes.image_id = ciniki_images.id "
            . "AND (ciniki_recipes.webflags&0x01) = 1 "
            . "ORDER BY (ciniki_recipes.webflags&0x10) DESC, "
            . "ciniki_recipes.date_added DESC "
            . "LIMIT 1";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'image');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['image']) ) {
            $categories[$cnum]['category']['image_id'] = $rc['image']['primary_image_id'];
        } else {
            $categories[$cnum]['category']['image_id'] = 0;
        }
    }

    return array('stat'=>'ok', 'categories'=>$categories);  
}
?>
