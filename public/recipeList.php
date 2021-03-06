<?php
//
// Description
// ===========
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:     The ID of the tenant to get the list from.
// 
// Returns
// -------
//
function ciniki_recipes_recipeList($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'tag_type'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Type'),
        'tag_name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Tag'),
        'limit'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Limit'), 
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['tnid'], 'ciniki.recipes.recipeList'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');

    //
    // Get the tag stats
    //
    $tags = array();
    if( isset($args['tag_type']) && $args['tag_type'] != '' ) {
        $strsql = "SELECT tag_type, tag_name, COUNT(recipe_id) AS num_recipes "
            . "FROM ciniki_recipe_tags "
            . "WHERE ciniki_recipe_tags.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
            . "AND ciniki_recipe_tags.recipe_id > 0 "
            . "GROUP BY tag_type, tag_name "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
        $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.recipes', array(
            array('container'=>'tags', 'fname'=>'tag_name', 'name'=>'tag',
                'fields'=>array('tag_name', 'num_recipes')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['tags']) ) {
            $tags = $rc['tags'];
        }

        //
        // Get untaged recipes
        //
        $strsql = "SELECT ciniki_recipe_tags.tag_name, "
            . "COUNT(ciniki_recipes.id) AS num_recipes "
            . "FROM ciniki_recipes "
            . "LEFT JOIN ciniki_recipe_tags ON ("
                . "ciniki_recipes.id = ciniki_recipe_tags.recipe_id "
                . "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
                . "AND ciniki_recipe_tags.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . ") "
            . "WHERE ciniki_recipes.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ISNULL(tag_name) "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'untagged');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['untagged']) && $rc['untagged']['num_recipes'] > 0 ) {
            $tags[] = array('tag'=>array('permalink'=>'--', 
                'tag_name'=>'Unknown', 
                'num_recipes'=>$rc['untagged']['num_recipes'],
                ));
        }

    }



    //
    // Get the list of recipes requested
    //
    $recipes = array();
    if( isset($args['tag_type']) && $args['tag_type'] != '' && isset($args['tag_name']) && $args['tag_name'] == 'Unknown' ) {
        $strsql = "SELECT ciniki_recipes.id, ciniki_recipes.name "  
            . "FROM ciniki_recipes "
            . "LEFT JOIN ciniki_recipe_tags ON ("
                . "ciniki_recipes.id = ciniki_recipe_tags.recipe_id "
                . "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
                . "AND ciniki_recipe_tags.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . ") "
            . "WHERE ciniki_recipes.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ISNULL(ciniki_recipe_tags.tag_name) "
            . "ORDER BY name "
            . "";
    } elseif( isset($args['tag_type']) && $args['tag_type'] != '' && isset($args['tag_name']) && $args['tag_name'] != '' ) {
        $strsql = "SELECT ciniki_recipes.id, ciniki_recipes.name "  
            . "FROM ciniki_recipe_tags, ciniki_recipes "
            . "WHERE ciniki_recipe_tags.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
            . "AND ciniki_recipe_tags.tag_name = '" . ciniki_core_dbQuote($ciniki, $args['tag_name']) . "' "
            . "AND ciniki_recipe_tags.recipe_id = ciniki_recipes.id "
            . "AND ciniki_recipes.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "ORDER BY name "
            . "";
    } elseif( isset($args['tag_type']) && $args['tag_type'] != '' ) {
        $strsql = "SELECT ciniki_recipes.id, ciniki_recipes.name "  
            . "FROM ciniki_recipe_tags, ciniki_recipes "
            . "WHERE ciniki_recipe_tags.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ciniki_recipe_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
            . "AND ciniki_recipe_tags.recipe_id = ciniki_recipes.id "
            . "AND ciniki_recipes.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "ORDER BY name "
            . "";
    } else {
        $strsql = "SELECT ciniki_recipes.id, ciniki_recipes.name "  
            . "FROM ciniki_recipes "
            . "WHERE ciniki_recipes.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "ORDER BY last_updated "
            . "";
        if( isset($args['limit']) && $args['limit'] != '' && $args['limit'] > 0 ) {
            $strsql .= "LIMIT " . ciniki_core_dbQuote($ciniki, $args['limit']) . " ";
        } else {
            $strsql .= "LIMIT 25 ";
        }
    }
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.recipes', array(
        array('container'=>'recipes', 'fname'=>'id', 'fields'=>array('id', 'name')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['recipes']) ) {
        $recipes = $rc['recipes'];
    }
    $recipe_ids = array();
    foreach($recipes as $recipe) {
        $recipe_ids[] = $recipe['id'];
    }

    return array('stat'=>'ok', 'tags'=>$tags, 'recipes'=>$recipes, 'nplist'=>$recipe_ids);
}
?>
