UPDATE fjord_dynamic_values AS d_values
    JOIN fjord_dynamic_attributes as attribute ON d_values.attribute_id = attribute.id
    SET d_values.value = ? WHERE attribute.name = ?;