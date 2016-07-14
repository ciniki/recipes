<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_recipes_web_recipeDetails($ciniki, $settings, $business_id, $permalink, $tag_permalink) {

    $modules = array();
    if( isset($ciniki['business']['modules']) ) {
        $modules = $ciniki['business']['modules'];
    }

    $strsql = "SELECT ciniki_recipes.id, "
        . "ciniki_recipes.name, "
        . "ciniki_recipes.permalink, "
        . "ciniki_recipes.num_servings, "
        . "ciniki_recipes.prep_time, "
        . "ciniki_recipes.roast_time, "
        . "ciniki_recipes.cook_time, "
        . "ciniki_recipes.description, "
        . "ciniki_recipes.ingredients, "
        . "ciniki_recipes.instructions, "
        . "ciniki_recipes.image_id AS primary_image_id, "
        . "ciniki_recipe_images.image_id, "
        . "ciniki_recipe_images.name AS image_name, "
        . "ciniki_recipe_images.permalink AS image_permalink, "
        . "ciniki_recipe_images.description AS image_description, "
        . "UNIX_TIMESTAMP(ciniki_recipe_images.last_updated) AS image_last_updated "
        . "FROM ciniki_recipes "
        . "LEFT JOIN ciniki_recipe_images ON ("
            . "ciniki_recipes.id = ciniki_recipe_images.recipe_id "
            . "AND (ciniki_recipe_images.webflags&0x01) = 1 "
            . ") "
        . "WHERE ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "AND ciniki_recipes.permalink = '" . ciniki_core_dbQuote($ciniki, $permalink) . "' "
        . "AND (ciniki_recipes.webflags&0x01) = 1 "
        . "ORDER BY ciniki_recipe_images.sequence, ciniki_recipe_images.date_added, ciniki_recipe_images.name "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.artclub', array(
        array('container'=>'recipes', 'fname'=>'id', 
            'fields'=>array('id', 'name', 'permalink', 'image_id'=>'primary_image_id', 
            'num_servings', 'prep_time', 'roast_time', 'cook_time',
            'description', 'ingredients', 'instructions')),
        array('container'=>'images', 'fname'=>'image_id', 
            'fields'=>array('image_id', 'title'=>'image_name', 'permalink'=>'image_permalink',
                'description'=>'image_description', 
                'last_updated'=>'image_last_updated')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['recipes']) || count($rc['recipes']) < 1 ) {
        return array('stat'=>'404', 'err'=>array('pkg'=>'ciniki', 'code'=>'1556', 'msg'=>"I'm sorry, but we can't find the recipe you requested."));
    }
    $recipe = array_pop($rc['recipes']);

    //
    // Get the categories and tags for the recipe
    //
    $strsql = "SELECT id, tag_type, tag_name, permalink "
        . "FROM ciniki_recipe_tags "
        . "WHERE recipe_id = '" . ciniki_core_dbQuote($ciniki, $recipe['id']) . "' "
        . "AND business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "ORDER BY tag_type, tag_name "
        . "";
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.recipes', array(
        array('container'=>'types', 'fname'=>'tag_type',
            'fields'=>array('type'=>'tag_type')),
        array('container'=>'tags', 'fname'=>'id',
            'fields'=>array('id', 'name'=>'tag_name', 'permalink')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['types']) ) {
        foreach($rc['types'] as $type) {
            if( $type['type'] == 10 ) {
                $recipe['categories'] = $type['tags'];
            } elseif( $type['type'] == 20 ) {
                $recipe['tags'] = $type['tags'];
            }
        }
    }

    //
    // Check if any similar recipes
    //
/*  if( isset($modules['ciniki.recipes']['flags']) 
        && ($modules['ciniki.recipes']['flags']&0x01) > 0
        ) {
        $strsql = "SELECT ciniki_recipes.id, "
            . "ciniki_recipes.name, "
            . "ciniki_recipes.permalink, "
            . "ciniki_recipes.short_description, "
            . "ciniki_recipes.long_description, "
            . "ciniki_recipes.primary_image_id, "
            . "ciniki_recipes.short_description, "
            . "'yes' AS is_details, "
            . "UNIX_TIMESTAMP(ciniki_recipes.last_updated) AS last_updated "
            . "FROM ciniki_recipe_relationships "
            . "LEFT JOIN ciniki_recipes ON ((ciniki_recipe_relationships.recipe_id = ciniki_recipes.id "
                    . "OR ciniki_recipe_relationships.related_id = ciniki_recipes.id) "
                . "AND ciniki_recipes.id <> '" . ciniki_core_dbQuote($ciniki, $recipe['id']) . "' "
                . "AND ciniki_recipes.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
                . ") "
            . "WHERE (ciniki_recipe_relationships.recipe_id = '" . ciniki_core_dbQuote($ciniki, $recipe['id']) . "' "
                . "OR ciniki_recipe_relationships.related_id = '" . ciniki_core_dbQuote($ciniki, $recipe['id']) . "' "
                . ") "
            . "AND ciniki_recipe_relationships.relationship_type = 10 "
            . "AND ciniki_recipe_relationships.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . ""; 
        $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.recipes', array(
            array('container'=>'recipes', 'fname'=>'id',
                'fields'=>array('id', 'image_id'=>'primary_image_id', 'title'=>'name', 'permalink', 
                    'description'=>'short_description', 'is_details', 'last_updated')),
            ));
        if( $rc['stat'] == 'ok' && isset($rc['recipes']) ) {
            $recipe['similar'] = $rc['recipes'];
        }
    } */

    $rsp = array('stat'=>'ok', 'recipe'=>$recipe);

    //
    // Check if we need to get the tag_name
    //
    if( $tag_permalink != '' ) {
        $strsql = "SELECT DISTINCT tag_name "
            . "FROM ciniki_recipe_tags "
            . "WHERE ciniki_recipe_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND ciniki_recipe_tags.recipe_id = '" . ciniki_core_dbQuote($ciniki, $recipe['id']) . "' "
            . "LIMIT 1 "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'tag');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['tag']) ) {
            $rsp['tag_name'] = $rc['tag']['tag_name'];
        }
    }

    return $rsp;
}
?>
