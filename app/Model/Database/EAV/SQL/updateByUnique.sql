UPDATE fjord_dynamic_values AS d_values
    JOIN fjord_dynamic_attributes as attribute ON d_values.attribute_id = attribute.id
    JOIN fjord_dynamic_ids AS ids ON d_values.row_id = ids.id
    SET d_values.value = ? WHERE attribute.id_name = ? AND ids.row_unique = ?;