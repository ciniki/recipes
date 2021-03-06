<?php
//
// Description
// ===========
// This method updates one or more elements of an existing recipe.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:     The ID of the tenant to the recipe is a part of.
// recipe_id:       The ID of the recipe to update.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_recipes_recipeUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'recipe_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Recipe'), 
        'name'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Name'), 
        'primary_image_id'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Image'),
        'num_servings'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Number of Servings'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Web Flags'), 
        'prep_time'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Prep Time'), 
        'roast_time'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Roast Time'), 
        'cook_time'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Cook Time'), 
        'synopsis'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Synopsis'), 
        'description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Description'), 
        'ingredients'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Ingredients'), 
        'instructions'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Instructions'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Notes'), 
        'tag-10'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Meals'),
        'tag-20'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Ingredients'),
        'tag-30'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Cuisines'),
        'tag-40'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Methods'),
        'tag-50'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Occasions'),
        'tag-60'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Diets'),
        'tag-70'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Seasons'),
        'tag-80'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Collections'),
        'tag-90'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Products'),
        'tag-100'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Contributors'),
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
    $rc = ciniki_recipes_checkAccess($ciniki, $args['tnid'], 'ciniki.recipes.recipeUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    if( isset($args['name']) ) {
        $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args['name'])));
        //
        // Make sure the permalink is unique
        //
        $strsql = "SELECT id, name, permalink FROM ciniki_recipes "
            . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['recipe_id']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.recipes', 'recipe');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( $rc['num_rows'] > 0 ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.recipes.16', 'msg'=>'You already have a recipe with this name, please choose another name'));
        }
    }

    //  
    // Turn off autocommit
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    $rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.recipes');
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Update the recipe
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    $rc = ciniki_core_objectUpdate($ciniki, $args['tnid'], 'ciniki.recipes.recipe', 
        $args['recipe_id'], $args);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Update the tags
    //
    $tag_types = array(
        '10'=>'meals',
        '20'=>'mainingredients',
        '30'=>'cuisines',
        '40'=>'methods',
        '50'=>'occasions',
        '60'=>'diets',
        '70'=>'seasons',
        '80'=>'collections',
        '90'=>'products',
        '100'=>'contributors',
        );
    foreach($tag_types as $tag_type => $arg_name) {
        if( isset($args['tag-' . $tag_type]) ) {
            ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'tagsUpdate');
            $rc = ciniki_core_tagsUpdate($ciniki, 'ciniki.recipes', 'tag', $args['tnid'],
                'ciniki_recipe_tags', 'ciniki_recipe_history',
                'recipe_id', $args['recipe_id'], $tag_type, $args['tag-' . $tag_type]);
            if( $rc['stat'] != 'ok' ) {
                ciniki_core_dbTransactionRollback($ciniki, 'ciniki.recipes');
                return $rc;
            }
        }
    }

    //
    // Commit the database changes
    //
    $rc = ciniki_core_dbTransactionCommit($ciniki, 'ciniki.recipes');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Update the last_change date in the tenant modules
    // Ignore the result, as we don't want to stop user updates if this fails.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'updateModuleChangeDate');
    ciniki_tenants_updateModuleChangeDate($ciniki, $args['tnid'], 'ciniki', 'recipes');

    return array('stat'=>'ok');
}
?>
