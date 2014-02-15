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
function ciniki_recipes_objects($ciniki) {
	$objects = array();
	$objects['recipe'] = array(
		'name'=>'Recipe',
		'sync'=>'yes',
		'table'=>'ciniki_recipes',
		'fields'=>array(
			'name'=>array(),
			'permalink'=>array(),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'category'=>array(),
			'cuisine'=>array(),
			'num_servings'=>array(),
			'webflags'=>array(),
			'prep_time'=>array(),
			'cook_time'=>array(),
			'description'=>array(),
			'ingredients'=>array(),
			'instructions'=>array(),
			),
		'history_table'=>'ciniki_recipe_history',
		);
	$objects['tag'] = array(
		'name'=>'Tag',
		'sync'=>'yes',
		'table'=>'ciniki_recipe_tags',
			'fields'=>array(
			'recipe_id'=>array('ref'=>'ciniki.recipes.recipe'),
			'tag_type'=>array(),
			'tag_name'=>array(),
			'permalink'=>array(),
			),
		'history_table'=>'ciniki_recipe_history',
		);
	$objects['image'] = array(
		'name'=>'Image',
		'sync'=>'yes',
		'table'=>'ciniki_recipe_images',
		'fields'=>array(
			'recipe_id'=>array('ref'=>'ciniki.recipes.recipe'),
			'name'=>array(),
			'permalink'=>array(),
			'webflags'=>array(),
			'sequence'=>array(),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array(),
			),
		'history_table'=>'ciniki_recipe_history',
		);
	$objects['note'] = array(
		'name'=>'Note',
		'sync'=>'yes',
		'table'=>'ciniki_recipe_notes',
		'fields'=>array(
			'parent_id'=>array(),
			'recipe_id'=>array('ref'=>'ciniki.recipes.recipe'),
			'user_id'=>array('ref'=>'ciniki.users.user'),
			'content'=>array(),
			),
		'history_table'=>'ciniki_recipe_history',
		);
	
	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
