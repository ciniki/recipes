<?php
//
// Description
// -----------
//
// Returns
// -------
//
function ciniki_recipes_searchField($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'field'=>array('required'=>'yes', 'blank'=>'no', 'validlist'=>array(), 'name'=>'Field'),
        'start_needle'=>array('required'=>'yes', 'blank'=>'yes', 'name'=>'Search String'), 
        'limit'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Limit'), 
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['tnid'], 'ciniki.recipes.searchField', 0, 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Get the number of faqs in each status for the tenant, 
    // if no rows found, then return empty array
    //
    $strsql = "SELECT " . $args['field'] . " AS name "
        . "FROM ciniki_recipes "
        . "WHERE ciniki_recipes.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND (" . $args['field']  . " LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
            . "AND " . $args['field'] . " <> '' "
            . ") "
        . "";
    $strsql .= "ORDER BY " . $args['field'] . " "
        . "";
    if( isset($args['limit']) && $args['limit'] != '' && $args['limit'] > 0 ) {
        $strsql .= "LIMIT " . ciniki_core_dbQuote($ciniki, $args['limit']) . " ";
    } else {
        $strsql .= "LIMIT 25 ";
    }
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.recipes', array(
        array('container'=>'results', 'fname'=>'name', 'name'=>'result', 'fields'=>array('name')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['results']) || !is_array($rc['results']) ) {
        return array('stat'=>'ok', 'results'=>array());
    }
    return array('stat'=>'ok', 'results'=>$rc['results']);
}
?>
