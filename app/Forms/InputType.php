<?php

namespace App\Forms;

interface InputType
{
    const TEXT = "text";
    const TEXTAREA = "textarea";
    const INTEGER = "number";
    const DATE_TIME = "date_time";
    const CHECKBOX = "checkbox";
    const CHECKBOX_LIST = "checkbox_list";
    const PASSWORD = "password";
    const RADIO_LIST = "radio_list";
    const SELECT = "select";
    const MULTISELECT = "multiselect";
    const UPLOAD = "upload"; // dont use in DB
    const MULTIUPLOAD = "multiupload"; // dont use in DB
    const EMAIL = "email";
    const URL = "URL";
}