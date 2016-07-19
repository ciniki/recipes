<?php
//
// Description
// ===========
// This method will output the recipe(s) in pdf format.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:     The ID of the business to get the list from.
// 
// Returns
// -------
//
function ciniki_recipes_web_downloadPDF($ciniki, $settings, $business_id, $permalink, $args) {

    //
    // Load the recipe
    //
    $strsql = "SELECT ciniki_recipes.id, "
        . "ciniki_recipes.name, "
        . "ciniki_recipes.primary_image_id, "
        . "ciniki_recipes.num_servings, "
        . "ciniki_recipes.prep_time, "
        . "ciniki_recipes.roast_time, "
        . "ciniki_recipes.cook_time, "
        . "ciniki_recipes.synopsis, "
        . "ciniki_recipes.description, "
        . "ciniki_recipes.ingredients, "
        . "ciniki_recipes.instructions "
        . "FROM ciniki_recipes "
        . "WHERE ciniki_recipes.permalink = '" . ciniki_core_dbQuote($ciniki, $permalink) . "' "
        . "AND ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.recipes', array(
        array('container'=>'recipes', 'fname'=>'id',
            'fields'=>array('id', 'name', 'primary_image_id', 'num_servings', 'prep_time', 'roast_time', 'cook_time', 'synopsis', 'description', 'ingredients', 'instructions')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['recipes']) ) {
        return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'2533', 'msg'=>'Unable to find recipe'));
    }
    $recipes = $rc['recipes'];

    if( count($recipes) < 1 ) {
        return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'2534', 'msg'=>'Unable to find recipe'));
    }

    //
    // Generate PDF
    //
    if( $args['layout'] != 'single' 
//      && $args['layout'] != 'double' 
//      && $args['layout'] != 'triple' 
        ) {
        return array('stat'=>'404', 'err'=>array('pkg'=>'ciniki', 'code'=>'2535', 'msg'=>"That recipe is not available in the format you requested."));
    }

    if( !isset($args['title']) || $args['title'] == '' ) {
        foreach($recipes as $recipe) {
            $args['title'] = $recipe['name'];
            break;
        }
    }

    ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'templates', $args['layout']);
    $function = 'ciniki_recipes_templates_' . $args['layout'];
    $rc = $function($ciniki, $business_id, array(array('name'=>'', 'recipes'=>$recipes)), $args);
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }
    $pdf = $rc['pdf'];

    $filename = preg_replace('/[^a-zA-Z0-9_]/', '', preg_replace('/ /', '_', $args['title']));

    return array('stat'=>'ok', 'file'=>array('filename'=>$filename, 'extension'=>'pdf', 'binary_content'=>$pdf->Output($filename, 'S')));
}
?>
