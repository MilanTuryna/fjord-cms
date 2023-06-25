SELECT value.entity_id as entity_id, attribute.id_name AS attribute, attribute.title AS attribute_title, value.value, ids.row_unique
FROM fjord_dynamic_ids AS ids
    RIGHT JOIN fjord_dynamic_attributes AS attribute ON ids.entity_id = ?
    RIGHT JOIN fjord_dynamic_values AS value ON value.attribute_id = attribute.id WHERE ids.id = value.row_id