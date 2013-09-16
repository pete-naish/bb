<?php
    
    $Form = new PerchForm('add');
    
    if ($Form->posted() && $Form->validate()) {
        $Item = $Region->add_new_item();   
        if (is_object($Item)) {
            PerchUtil::redirect(PERCH_LOGINPATH.'/core/apps/content/edit/?id='.$Region->id().'&itm='.$Item->itemID());
        }
    }

    $items = $Region->get_items_for_editing();

	if (!PerchUtil::count($items)) {
		// No items(!) so add a new one and edit it.
		$Item = $Region->add_new_item();   
        if (is_object($Item)) {
            PerchUtil::redirect(PERCH_LOGINPATH.'/core/apps/content/edit/?id='.$Region->id().'&itm='.$Item->itemID());
        }
	}

    $cols = $Region->get_edit_columns();
    //PerchUtil::debug($cols);
?>