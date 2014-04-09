<?php
    class ItemFunctions {
        function add() {
            $list_id = getJson("list_id");
            $item_name = getJson("item_name");
            requirePermission(canUseList($list_id));
            if(sqlIsNameTaken($list_id, $item_name)) {
                $item = sqlItemByName($list_id, $item_name);
                sqlSetItemState(1, $item['id']);
                $item = sqlItemByName($list_id, $item_name);
                $item['message'] = "Item already exists. Unchecked Item.";
                okGetResponse($item);
            }
            requireValidItemName($item_name);
            $item_id = sqlAddItem($list_id, $item_name);
            userUpdateItem($item_id);
            okGetResponse(sqlGetItem($item_id));
        }
        
        function uncheck() {
            okGetResponse(setItemState(1));
        }
        
        function check() {
            okGetResponse(setItemState(2));
        }
        
        function archive() {
            okGetResponse(setItemState(3));
        }
        
        function setState() {
            okGetResponse(setItemState(getJson("state")));
        }
        
        function remove() {
            $item_id = getJson("item_id");
            requirePermission(canUseItem($item_id));
            sqlRemoveItem($item_id);
            userUpdateItem($item_id);
            okGetResponse(sqlGetItem($item_id));
        }
        
        function get() {
            $item_id = getJson("item_id");
            requirePermission(canUseItem($list_id));
            okGetResponse(sqlGetItem($item_id));
        }
        
        function getAll() {
            $list_id = getJson("list_id");
            requirePermission(canUseList($list_id));
            okGetResponse(sqlGetItems($list_id));
        }
        
        function getAllState() { 
            $list_id = getJson("list_id");
            $state = getJson("state");
            requirePermission(canUseList($list_id));
            okGetResponse(sqlGetItemstate($list_id, $state));
        }
    }
    
    function userUpdateItem($item_id) {
        userUpdateList(sqlGetListFromItem($item_id));
    }
    
    function setItemState($state) {
        $item_id = getJson("item_id");
        requirePermission(canUseItem($item_id));
        sqlSetItemState($state, $item_id);
        userUpdateItem($item_id);
        return sqlGetItem($item_id);
    }
    
    function canUseItem($item_id) {
        return sqlIsValidItem($item_id)
            && canUseList(sqlGetListFromItem($item_id));
    }
?>